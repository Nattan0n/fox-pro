<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

trait SimpleAWTMapping
{
    // 🔥 Direct mapping of AWT names to tax rates - Add all your AWT names here
    private $awtNameToRate = [
        // Transportation - 1%
        '01-Transport-c' => 1.0,
        '01-Transportation' => 1.0,
        '01-Transport' => 1.0,
        // Services - 3%
        '03-Service' => 3.0,
        '03-Professional' => 3.0,
        '03-Services' => 3.0,
        '03-Consulting' => 3.0,
        '03-HineofWork-c' => 3.0,
        // Rental/Contractor - 5%
        '05-Rental' => 5.0,
        '05-Contractor' => 5.0,
        '05-Lease' => 5.0,
        // Construction/Real Estate - 2%
        '02-Construction' => 2.0,
        '02-RealEstate' => 2.0,
        // Interest/Dividend - 1%
        '01-Interest' => 1.0,
        '01-Dividend' => 1.0,
        // 🔥 Commission - 0% (ไม่หัก WHT)
        '10-Commission' => 0.0,
        '10-Comm' => 0.0,
        '10-นายหน้า' => 0.0,
        '10-Commission-c' => 0.0,
        '10-Broker' => 0.0,
        '10-Brokerage' => 0.0,
    ];

    // Cache for AWT descriptions from database
    private $awtDescriptionCache = [];

    // 🔥 Commission keywords for detection
    private $commissionKeywords = [
        'commission',
        'comm',
        'นายหน้า',
        'ค่านายหน้า',
        'คอมมิชชั่น',
        'คอมมิสชั่น',
        'broker',
        'brokerage',
        'ค่าธรรมเนียม',
        'fee',
        'agent',
        'ค่าตอบแทน'
    ];

    // 🔥 Check if AWT is commission type
    private function isCommissionType($awtName, $awtDescription = '')
    {
        if (empty($awtName) && empty($awtDescription)) {
            return false;
        }

        // Check AWT name
        $awtNameLower = strtolower(trim($awtName));
        foreach ($this->commissionKeywords as $keyword) {
            if (strpos($awtNameLower, strtolower($keyword)) !== false) {
                Log::info("🚫 Commission detected in AWT name", [
                    'awt_name' => $awtName,
                    'matched_keyword' => $keyword,
                    'reason' => 'AWT name contains commission keyword'
                ]);
                return true;
            }
        }

        // Check AWT description if available
        if (!empty($awtDescription)) {
            $descriptionLower = strtolower(trim($awtDescription));
            foreach ($this->commissionKeywords as $keyword) {
                if (strpos($descriptionLower, strtolower($keyword)) !== false) {
                    Log::info("🚫 Commission detected in AWT description", [
                        'awt_name' => $awtName,
                        'awt_description' => $awtDescription,
                        'matched_keyword' => $keyword,
                        'reason' => 'AWT description contains commission keyword'
                    ]);
                    return true;
                }
            }
        }

        return false;
    }

    // 🔥 Get complete AWT details from database
    private function getAWTDetailsFromDatabase($awtName)
    {
        // Check cache first
        $cacheKey = 'awt_details_' . $awtName;
        if (isset($this->awtDescriptionCache[$cacheKey])) {
            return $this->awtDescriptionCache[$cacheKey];
        }

        try {
            $oracleConnection = DB::connection('oracle');

            // Query ap_awt_groups table for full details
            $result = $oracleConnection->select("
                SELECT 
                    ag.group_id,
                    ag.name,
                    ag.description,
                    ag.inactive_date,
                    ag.org_id,
                    -- Get tax details if available
                    (SELECT aagt.tax_rate 
                     FROM ap_awt_group_taxes_all aagt 
                     WHERE aagt.group_id = ag.group_id 
                     AND aagt.org_id = ag.org_id
                     AND NVL(aagt.inactive_date, SYSDATE + 1) > SYSDATE
                     ORDER BY aagt.tax_rate DESC
                     FETCH FIRST 1 ROWS ONLY) as tax_rate,
                    (SELECT aagt.tax_name 
                     FROM ap_awt_group_taxes_all aagt 
                     WHERE aagt.group_id = ag.group_id 
                     AND aagt.org_id = ag.org_id
                     AND NVL(aagt.inactive_date, SYSDATE + 1) > SYSDATE
                     ORDER BY aagt.tax_rate DESC
                     FETCH FIRST 1 ROWS ONLY) as tax_name,
                    (SELECT aagt.tax_code 
                     FROM ap_awt_group_taxes_all aagt 
                     WHERE aagt.group_id = ag.group_id 
                     AND aagt.org_id = ag.org_id
                     AND NVL(aagt.inactive_date, SYSDATE + 1) > SYSDATE
                     ORDER BY aagt.tax_rate DESC
                     FETCH FIRST 1 ROWS ONLY) as tax_code
                FROM ap_awt_groups ag
                WHERE UPPER(ag.name) = UPPER(:awt_name)
                AND (ag.inactive_date IS NULL OR ag.inactive_date > SYSDATE)
                ORDER BY ag.group_id DESC
                FETCH FIRST 1 ROWS ONLY
            ", ['awt_name' => $awtName]);

            if (!empty($result) && isset($result[0])) {
                $awtDetails = [
                    'group_id' => $result[0]->group_id ?? 0,
                    'name' => trim($result[0]->name ?? ''),
                    'description' => trim($result[0]->description ?? ''),
                    'tax_rate' => (float)($result[0]->tax_rate ?? 0),
                    'tax_name' => trim($result[0]->tax_name ?? ''),
                    'tax_code' => trim($result[0]->tax_code ?? ''),
                    'org_id' => $result[0]->org_id ?? 0
                ];

                // Cache the result
                $this->awtDescriptionCache[$cacheKey] = $awtDetails;

                Log::info("📋 AWT details retrieved from database", [
                    'awt_name' => $awtName,
                    'details' => $awtDetails
                ]);

                return $awtDetails;
            }
        } catch (Exception $e) {
            Log::warning("Failed to get AWT details from database: " . $e->getMessage(), [
                'awt_name' => $awtName
            ]);
        }

        // Return empty details if not found
        return [
            'group_id' => 0,
            'name' => '',
            'description' => '',
            'tax_rate' => 0,
            'tax_name' => '',
            'tax_code' => '',
            'org_id' => 0
        ];
    }

    // 🔥 ENHANCED: getWHTRateFromAWTName with commission check
    private function getWHTRateFromAWTName($awtName)
    {
        if (empty($awtName)) {
            return 0;
        }

        // 🔥 STEP 1: Get AWT details from database first
        $awtDetails = $this->getAWTDetailsFromDatabase($awtName);

        // 🔥 STEP 2: Check if it's commission type (ไม่หัก WHT)
        if ($this->isCommissionType($awtName, $awtDetails['description'])) {
            Log::info("🚫 Commission detected - WHT rate set to 0%", [
                'awt_name' => $awtName,
                'awt_description' => $awtDetails['description'],
                'reason' => 'Commission type does not require WHT deduction'
            ]);

            // Add to mapping for future use
            $this->awtNameToRate[$awtName] = 0.0;
            return 0.0; // ไม่หัก WHT สำหรับค่านายหน้า
        }

        // 🔥 STEP 3: Try exact match from mapping first
        if (isset($this->awtNameToRate[$awtName])) {
            Log::info("✅ Exact AWT mapping found", [
                'awt_name' => $awtName,
                'mapped_rate' => $this->awtNameToRate[$awtName]
            ]);
            return $this->awtNameToRate[$awtName];
        }

        // 🔥 STEP 4: Use database rate if available
        if ($awtDetails['tax_rate'] > 0) {
            Log::info("📊 Using tax rate from database", [
                'awt_name' => $awtName,
                'database_rate' => $awtDetails['tax_rate'],
                'tax_name' => $awtDetails['tax_name'],
                'tax_code' => $awtDetails['tax_code']
            ]);

            // Add to mapping for future use
            $this->awtNameToRate[$awtName] = $awtDetails['tax_rate'];
            return $awtDetails['tax_rate'];
        }

        // 🔥 STEP 5: Try pattern matching for dynamic names
        if (preg_match('/^(\d{2})-/', $awtName, $matches)) {
            $rateCode = (int)$matches[1];

            // Map rate codes to percentages
            $rateCodeMapping = [
                1 => 1.0,   // 01 = 1%
                2 => 2.0,   // 02 = 2%
                3 => 3.0,   // 03 = 3%
                5 => 5.0,   // 05 = 5%
                10 => 10.0  // 10 = 10% (but check for commission first)
            ];

            if (isset($rateCodeMapping[$rateCode])) {
                // 🔥 Special check for rate code 10 - might be commission
                if ($rateCode === 10) {
                    Log::warning("⚠️ Rate code 10 detected - verify if commission", [
                        'awt_name' => $awtName,
                        'awt_description' => $awtDetails['description'],
                        'suggested_action' => 'Manual verification recommended'
                    ]);
                }

                // Add this to our mapping for future use
                $this->awtNameToRate[$awtName] = $rateCodeMapping[$rateCode];

                // Enhanced logging with person type detection
                $isJuristicPerson = (bool) preg_match('/-c$/i', trim($awtName));
                $personType = $isJuristicPerson ? 'Juristic Person (ง.ด.53)' : 'Individual (ง.ด.3)';

                Log::info("🔄 Auto-mapped new AWT name with person type detection", [
                    'awt_name' => $awtName,
                    'rate_code' => $rateCode,
                    'mapped_rate' => $rateCodeMapping[$rateCode],
                    'person_type' => $personType,
                    'is_juristic_person' => $isJuristicPerson
                ]);

                return $rateCodeMapping[$rateCode];
            }
        }

        // Log unknown AWT name for future mapping
        Log::warning("❓ Unknown AWT name - needs manual mapping", [
            'awt_name' => $awtName,
            'awt_description' => $awtDetails['description'],
            'suggestion' => 'Please add to awtNameToRate mapping or check database configuration'
        ]);

        return 0;
    }

    // Fallback rate based on vendor type
    private function getFallbackRateFromVendorType($vendorType)
    {
        $vendorTypeRates = [
            'TRANSPORTATION' => 1.0,
            'EMPLOYEE' => 1.0,
            'SERVICE' => 3.0,
            'VENDOR' => 3.0,
            'PROFESSIONAL' => 3.0,
            'CONTRACTOR' => 5.0,
            'RENTAL' => 5.0,
            'CONSTRUCTION' => 2.0
        ];

        return $vendorTypeRates[strtoupper($vendorType)] ?? 0;
    }

    // 🔥 Get tax type code from rate with AWT_NAME detection
    private function getTaxTypeFromRateSimple($rate, $awtName = '')
    {
        // Map rates to rate codes (not tax types)
        $rateToCode = [
            1.0 => '01',   // 1% = 01
            2.0 => '02',   // 2% = 02  
            3.0 => '03',   // 3% = 03
            5.0 => '05',   // 5% = 05
            10.0 => '10',  // 10% = 10
        ];

        $taxType = $rateToCode[$rate] ?? '53'; // default fallback

        Log::info("Tax Type (Rate Code) Mapping", [
            'awt_name' => $awtName,
            'rate' => $rate,
            'tax_type_code' => $taxType
        ]);

        return $taxType;
    }

    // Get tax description from database using AWT name
    private function getTaxDescriptionFromDatabase($awtName)
    {
        // Check cache first
        if (isset($this->awtDescriptionCache[$awtName])) {
            return $this->awtDescriptionCache[$awtName];
        }

        try {
            $oracleConnection = DB::connection('oracle');
            // Query ap_awt_groups table for description
            $result = $oracleConnection->select("
                SELECT 
                    ag.name,
                    ag.description,
                    ag.inactive_date
                FROM ap_awt_groups ag
                WHERE UPPER(ag.name) = UPPER(:awt_name)
                AND (ag.inactive_date IS NULL OR ag.inactive_date > SYSDATE)
                FETCH FIRST 1 ROWS ONLY
            ", ['awt_name' => $awtName]);

            if (!empty($result) && isset($result[0]->description)) {
                $description = trim($result[0]->description);
                // Cache the result
                $this->awtDescriptionCache[$awtName] = $description;
                Log::info("Found tax description in database", [
                    'awt_name' => $awtName,
                    'description' => $description
                ]);
                return $description;
            }
        } catch (Exception $e) {
            Log::warning("Failed to get tax description from database: " . $e->getMessage(), [
                'awt_name' => $awtName
            ]);
        }
        // Return empty if not found
        return '';
    }

    // Get tax description from rate ENHANCED: พยายามดึงจาก database ก่อน ถ้าไม่เจอค่อย fallback
    private function getTaxDescriptionFromRate($rate, $awtName = '')
    {
        // ถ้ามี AWT name ให้ลองดึงจาก database ก่อน
        if (!empty($awtName)) {
            $dbDescription = $this->getTaxDescriptionFromDatabase($awtName);
            if (!empty($dbDescription)) {
                return $dbDescription;
            }
        }

        // Fallback to default descriptions based on rate
        $rateToDescription = [
            1.0 => 'ค่าขนส่ง',        // Transportation
            2.0 => 'ค่าก่อสร้าง',      // Construction
            3.0 => 'ค่าบริการ',       // Services
            5.0 => 'ค่าเช่า',         // Rental
            10.0 => 'ค่านายหน้า'      // Commission
        ];

        return $rateToDescription[$rate] ?? 'หัก ณ ที่จ่าย';
    }

    // 🔥 ENHANCED: Simplified invoice amount calculation with commission handling
    private function calculateCorrectInvoiceAmountsSimple($records)
    {
        $invoicesByNumber = $records->groupBy('invoice_num')->map(function ($invoiceRecords) {
            $firstRecord = $invoiceRecords->first();
            $invoiceNum = trim($firstRecord->invoice_num ?? '');
            $invoiceDate = trim($firstRecord->invoice_date ?? '');

            // Get base amount (sum of all ITEM/ACCRUAL lines)
            $baseAmount = $invoiceRecords->sum('base_amount_per_line');

            // Get AWT name from record
            $awtName = trim($firstRecord->awt_name ?? '');

            // Get VAT from DB (if any)
            $vatAmountFromDB = $firstRecord->vat_amount_per_invoice ?? 0;

            // 🔥 Enhanced commission check with database lookup
            $awtDetails = $this->getAWTDetailsFromDatabase($awtName);
            $isCommission = $this->isCommissionType($awtName, $awtDetails['description']);

            $whtRate = 0;
            $whtAmount = 0;

            if ($isCommission) {
                // 🚫 Commission - NO WHT calculation
                Log::info("🚫 Commission detected - no WHT calculation", [
                    'invoice_num' => $invoiceNum,
                    'awt_name' => $awtName,
                    'awt_description' => $awtDetails['description'],
                    'base_amount' => $baseAmount,
                    'wht_amount' => 0,
                    'reason' => 'Commission type - WHT exempt'
                ]);
            } else {
                // Regular WHT calculation using enhanced AWT mapping
                $whtRate = $this->getWHTRateFromAWTName($awtName);

                if ($whtRate > 0) {
                    // Calculate WHT using the correct rate from mapping
                    $whtAmount = round($baseAmount * ($whtRate / 100), 2);
                    Log::info("💰 WHT Calculation using enhanced AWT mapping", [
                        'invoice_num' => $invoiceNum,
                        'awt_name' => $awtName,
                        'awt_description' => $awtDetails['description'],
                        'mapped_rate' => $whtRate,
                        'base_amount' => $baseAmount,
                        'calculated_wht' => $whtAmount,
                        'source' => $awtDetails['tax_rate'] > 0 ? 'database' : 'mapping'
                    ]);
                } else {
                    // No AWT name or unknown AWT name - log warning
                    Log::warning("❓ Unknown or missing AWT name", [
                        'invoice_num' => $invoiceNum,
                        'awt_name' => $awtName,
                        'base_amount' => $baseAmount
                    ]);

                    // Fallback: Use vendor type if available
                    $vendorType = $firstRecord->vendor_type ?? '';
                    $whtRate = $this->getFallbackRateFromVendorType($vendorType);
                    if ($whtRate > 0) {
                        $whtAmount = round($baseAmount * ($whtRate / 100), 2);
                        Log::info("🔄 Fallback WHT calculation from vendor type", [
                            'invoice_num' => $invoiceNum,
                            'vendor_type' => $vendorType,
                            'fallback_rate' => $whtRate,
                            'calculated_wht' => $whtAmount
                        ]);
                    }
                }
            }

            // Calculate VAT (standard 7% if exists) - but not for commission
            $vatAmount = 0;
            if ($vatAmountFromDB > 0 && !$isCommission) {
                $vatAmount = round($baseAmount * 0.07, 2);
            }

            return [
                'invoice_num' => $invoiceNum,
                'invoice_date' => $invoiceDate,
                'invoice_amount' => $baseAmount,
                'vat_amount' => $vatAmount,
                'wht_amount' => $whtAmount,
                'wht_amount_for_txn' => $whtAmount,
                'awt_name' => $awtName,
                'wht_rate' => $whtRate,
                'is_commission' => $isCommission, // 🔥 Flag for commission
                'awt_details' => $awtDetails, // 🔥 Complete AWT details
                'your_reference' => $firstRecord->your_reference ?? '',
                'check_id' => $firstRecord->check_id ?? null
            ];
        });

        // Calculate totals (excluding commission from WHT totals)
        $regularInvoices = $invoicesByNumber->filter(function ($invoice) {
            return !($invoice['is_commission'] ?? false);
        });

        $commissionInvoices = $invoicesByNumber->filter(function ($invoice) {
            return $invoice['is_commission'] ?? false;
        });

        $totalWHT = $regularInvoices->sum('wht_amount_for_txn');
        $totalBase = $invoicesByNumber->sum('invoice_amount'); // Include all base amounts
        $whtEligibleBase = $regularInvoices->sum('invoice_amount'); // Only WHT-eligible base
        $commissionBase = $commissionInvoices->sum('invoice_amount');
        $weightedRate = $whtEligibleBase > 0 ? ($totalWHT / $whtEligibleBase) * 100 : 0;

        $commissionSummary = [
            'commission_count' => $commissionInvoices->count(),
            'commission_amount' => $commissionBase,
            'regular_wht_count' => $regularInvoices->count(),
            'regular_wht_amount' => $whtEligibleBase,
            'wht_exempt_amount' => $commissionBase
        ];

        Log::info("📊 Enhanced invoice calculation summary", [
            'total_invoices' => $invoicesByNumber->count(),
            'total_wht' => $totalWHT,
            'total_base' => $totalBase,
            'wht_eligible_base' => $whtEligibleBase,
            'weighted_rate' => $weightedRate,
            'commission_summary' => $commissionSummary
        ]);

        return [
            'invoices' => $invoicesByNumber,
            'total_wht' => $totalWHT,
            'total_base' => $totalBase,
            'wht_eligible_base' => $whtEligibleBase,
            'weighted_rate' => $weightedRate,
            'invoice_count' => $invoicesByNumber->count(),
            'commission_summary' => $commissionSummary // 🔥 Enhanced commission info
        ];
    }

    // 🔥 ENHANCED: Enhanced extractWHTRatesFromInvoices with commission exclusion
    private function extractWHTRatesFromInvoicesSimple($invoicesData)
    {
        $ratesData = [
            'rate1' => ['rate' => 0, 'taxable_amount' => 0, 'tax_amount' => 0, 'tax_type' => '', 'description' => '', 'awt_name' => ''],
            'rate2' => ['rate' => 0, 'taxable_amount' => 0, 'tax_amount' => 0, 'tax_type' => '', 'description' => '', 'awt_name' => ''],
            'rate3' => ['rate' => 0, 'taxable_amount' => 0, 'tax_amount' => 0, 'tax_type' => '', 'description' => '', 'awt_name' => ''],
            'rate4' => ['rate' => 0, 'taxable_amount' => 0, 'tax_amount' => 0, 'tax_type' => '', 'description' => '', 'awt_name' => ''],
            'rate5' => ['rate' => 0, 'taxable_amount' => 0, 'tax_amount' => 0, 'tax_type' => '', 'description' => '', 'awt_name' => '']
        ];

        // Group invoices by WHT rate and AWT name (EXCLUDE commission)
        $rateGroups = [];
        $excludedCommissionCount = 0;

        foreach ($invoicesData as $invoice) {
            // 🚫 Skip commission invoices for WHT rate extraction
            if ($invoice['is_commission'] ?? false) {
                $excludedCommissionCount++;
                Log::debug("⏭️ Skipping commission invoice from WHT rates", [
                    'invoice_num' => $invoice['invoice_num'] ?? '',
                    'awt_name' => $invoice['awt_name'] ?? '',
                    'amount' => $invoice['invoice_amount'] ?? 0
                ]);
                continue;
            }

            if ($invoice['wht_amount_for_txn'] > 0) {
                $rate = $invoice['wht_rate'] ?? 0;
                $awtName = $invoice['awt_name'] ?? '';

                if ($rate > 0) {
                    // 🔥 KEY FIX: Pass AWT_NAME to tax type detection
                    $taxType = $this->getTaxTypeFromRateSimple($rate, $awtName);

                    // Use combination of tax type and awt name as key for uniqueness
                    $groupKey = $taxType . '|' . $awtName;

                    if (!isset($rateGroups[$groupKey])) {
                        $rateGroups[$groupKey] = [
                            'rate' => $rate,
                            'tax_type' => $taxType,
                            'awt_name' => $awtName,
                            'description' => $this->getTaxDescriptionFromRate($rate, $awtName),
                            'taxable_amount' => 0,
                            'tax_amount' => 0
                        ];
                    }

                    $rateGroups[$groupKey]['taxable_amount'] += $invoice['invoice_amount'];
                    $rateGroups[$groupKey]['tax_amount'] += $invoice['wht_amount_for_txn'];
                }
            }
        }

        // Sort by tax type and assign to rate1-5
        ksort($rateGroups);
        $rateIndex = 1;
        foreach ($rateGroups as $groupKey => $rateData) {
            if ($rateIndex <= 5) {
                $ratesData['rate' . $rateIndex] = $rateData;
                $rateIndex++;
            }
        }

        Log::info("📊 WHT rates extraction completed", [
            'total_rate_groups' => count($rateGroups),
            'excluded_commission_invoices' => $excludedCommissionCount,
            'assigned_rates' => min(count($rateGroups), 5)
        ]);

        return $ratesData;
    }

    // 🔥 NEW: Method to manually mark AWT as commission
    public function markAsCommission($awtName)
    {
        $this->awtNameToRate[$awtName] = 0.0;
        Log::info("🚫 Manually marked AWT as commission (0% WHT)", [
            'awt_name' => $awtName
        ]);
    }

    // 🔥 NEW: Method to remove commission marking
    public function removeCommissionMarking($awtName)
    {
        if (isset($this->awtNameToRate[$awtName])) {
            unset($this->awtNameToRate[$awtName]);
            // Clear cache too
            $cacheKey = 'awt_details_' . $awtName;
            if (isset($this->awtDescriptionCache[$cacheKey])) {
                unset($this->awtDescriptionCache[$cacheKey]);
            }
            Log::info("🔄 Removed commission marking for AWT", [
                'awt_name' => $awtName,
                'action' => 'Will recalculate from database/pattern matching'
            ]);
        }
    }

    // 🔥 NEW: Method to get commission status summary
    public function getCommissionSummary($records)
    {
        $summary = [
            'total_records' => $records->count(),
            'commission_records' => 0,
            'regular_wht_records' => 0,
            'commission_awt_names' => [],
            'regular_awt_names' => [],
            'total_commission_amount' => 0,
            'total_regular_amount' => 0,
            'commission_details' => []
        ];

        foreach ($records as $record) {
            $awtName = trim($record->awt_name ?? '');
            $baseAmount = $record->base_amount_per_line ?? 0;

            if (!empty($awtName)) {
                $awtDetails = $this->getAWTDetailsFromDatabase($awtName);

                if ($this->isCommissionType($awtName, $awtDetails['description'])) {
                    $summary['commission_records']++;
                    $summary['commission_awt_names'][] = $awtName;
                    $summary['total_commission_amount'] += $baseAmount;

                    // Store commission details
                    $summary['commission_details'][] = [
                        'awt_name' => $awtName,
                        'description' => $awtDetails['description'],
                        'amount' => $baseAmount,
                        'invoice_num' => $record->invoice_num ?? '',
                        'check_number' => $record->your_reference ?? ''
                    ];
                } else {
                    $whtRate = $this->getWHTRateFromAWTName($awtName);
                    if ($whtRate > 0) {
                        $summary['regular_wht_records']++;
                        $summary['regular_awt_names'][] = $awtName;
                        $summary['total_regular_amount'] += $baseAmount;
                    }
                }
            }
        }

        $summary['commission_awt_names'] = array_unique($summary['commission_awt_names']);
        $summary['regular_awt_names'] = array_unique($summary['regular_awt_names']);
        $summary['commission_percentage'] = $summary['total_records'] > 0 ?
            round(($summary['commission_records'] / $summary['total_records']) * 100, 2) : 0;

        Log::info("📊 Commission Summary Report", $summary);

        return $summary;
    }

    // 🔥 NEW: Check if check has only commission invoices
    private function isCheckOnlyCommission($invoicesData)
    {
        if (is_array($invoicesData) || is_object($invoicesData)) {
            $invoicesArray = is_array($invoicesData) ? $invoicesData : $invoicesData->toArray();
        } else {
            return false;
        }

        $totalInvoices = count($invoicesArray);
        $commissionInvoices = 0;

        foreach ($invoicesArray as $invoice) {
            if ($invoice['is_commission'] ?? false) {
                $commissionInvoices++;
            }
        }

        $isOnlyCommission = $totalInvoices > 0 && $commissionInvoices === $totalInvoices;

        if ($isOnlyCommission) {
            Log::info("💼 Check identified as commission-only", [
                'total_invoices' => $totalInvoices,
                'commission_invoices' => $commissionInvoices
            ]);
        }

        return $isOnlyCommission;
    }

    // 🔥 NEW: Get all commission AWT names from current dataset
    public function getCommissionAWTNames($records)
    {
        $commissionAWTs = [];

        foreach ($records as $record) {
            $awtName = trim($record->awt_name ?? '');
            if (!empty($awtName)) {
                $awtDetails = $this->getAWTDetailsFromDatabase($awtName);
                if ($this->isCommissionType($awtName, $awtDetails['description'])) {
                    if (!isset($commissionAWTs[$awtName])) {
                        $commissionAWTs[$awtName] = [
                            'awt_name' => $awtName,
                            'description' => $awtDetails['description'],
                            'count' => 0,
                            'total_amount' => 0
                        ];
                    }
                    $commissionAWTs[$awtName]['count']++;
                    $commissionAWTs[$awtName]['total_amount'] += ($record->base_amount_per_line ?? 0);
                }
            }
        }

        return array_values($commissionAWTs);
    }

    // 🔥 NEW: Validate and clean commission keywords
    public function validateCommissionKeywords()
    {
        $validKeywords = [];
        foreach ($this->commissionKeywords as $keyword) {
            $trimmed = trim(strtolower($keyword));
            if (!empty($trimmed) && strlen($trimmed) >= 3) {
                $validKeywords[] = $trimmed;
            }
        }

        $this->commissionKeywords = array_unique($validKeywords);

        Log::info("✅ Commission keywords validated", [
            'total_keywords' => count($this->commissionKeywords),
            'keywords' => $this->commissionKeywords
        ]);

        return $this->commissionKeywords;
    }

    // 🔥 NEW: Add new commission keyword
    public function addCommissionKeyword($keyword)
    {
        $trimmed = trim(strtolower($keyword));
        if (!empty($trimmed) && strlen($trimmed) >= 3) {
            if (!in_array($trimmed, $this->commissionKeywords)) {
                $this->commissionKeywords[] = $trimmed;
                Log::info("➕ Added new commission keyword", [
                    'keyword' => $trimmed,
                    'total_keywords' => count($this->commissionKeywords)
                ]);
                return true;
            }
        }
        return false;
    }

    // 🔥 NEW: Clear all cached AWT data (useful for testing)
    public function clearAWTCache()
    {
        $cacheCount = count($this->awtDescriptionCache);
        $this->awtDescriptionCache = [];

        Log::info("🧹 Cleared AWT description cache", [
            'cleared_items' => $cacheCount
        ]);
    }

    // 🔥 Enhanced version of original methods (keeping same interface)
    private function calculateCorrectInvoiceAmounts($records)
    {
        return $this->calculateCorrectInvoiceAmountsSimple($records);
    }

    private function extractWHTRatesFromInvoices($invoicesData)
    {
        return $this->extractWHTRatesFromInvoicesSimple($invoicesData);
    }
}

class CheckOracle extends Component
{
    use SimpleAWTMapping; // ✅ ต้องมีบรรทัดนี้!
    // Override these methods to use the simple versions
    private function calculateCorrectInvoiceAmounts($records)
    {
        return $this->calculateCorrectInvoiceAmountsSimple($records);
    }
    private function extractWHTRatesFromInvoices($invoicesData)
    {
        return $this->extractWHTRatesFromInvoicesSimple($invoicesData);
    }
    // Computed property สำหรับตรวจสอบว่าสามารถ search ได้หรือไม่
    public function getCanSearchProperty()
    {
        // ต้องมี checkStart หรือ checkEnd อย่างน้อยหนึ่งอย่าง
        return !empty($this->checkStart) || !empty($this->checkEnd);
    }
    private function getStandardRateFromActual($actualRate)
    {
        // Not needed anymore - we use direct mapping
        return round($actualRate, 2);
    }
    private function getTaxTypeFromRate($rate)
    {
        return $this->getTaxTypeFromRateSimple($rate);
    }
    private function getWHTDescription($rate)
    {
        return $this->getTaxDescriptionFromRate($rate);
    }
    public $oracleData;
    public $selectedDate; // ✅ เปลี่ยนจาก dateFrom, dateTo เป็น selectedDate เดียว
    public $orgId = '84'; // Default org id
    public $userId = '';
    public $userOptions = []; // เก็บ user list จาก Oracle
    public $orgOptions = []; // จะถูก populate ใน mount()
    public $maxRecordLimit = 5000; // จำกัดไม่เกิน 5,000 records
    public $warningRecordLimit = 1000; // แจ้งเตือนเมื่อเกิน 1,000 records
    public $selectedChecks = [];
    public $selectAllChecks = false; // สถานะ Select All checkbox
    public $checkStart; // ✅ เพิ่มตัวแปรนี้
    public $checkEnd; // ✅ เพิ่มตัวแปรนี้
    // เพิ่ม property สำหรับควบคุม polling
    public $isProcessing = false;
    public $shouldContinue = true;
    public $loadingStep = 0;
    public $loadingData = [];
    public $tempQueryParams = [];
    // เพิ่ม properties สำหรับควบคุมการแสดงผล
    public $dataReady = false;
    public $pollingActive = false;
    public $maxRetries = 3;
    public $currentRetries = 0;
    public $isLoading = false;
    public $loadingProgress = 0;
    public $loadingMessage = '';
    public $loadingSteps = [
        10 => 'Validating parameters...',
        20 => 'Connecting to Oracle database...',
        30 => 'Estimating record count...',
        40 => 'Preparing query...',
        50 => 'Fetching data from Oracle...',
        60 => 'Processing invoice records...',
        70 => 'Calculating WHT amounts...',
        80 => 'Grouping check data...',
        90 => 'Finalizing results...',
        100 => 'Complete!'
    ];
    public $strictWHTMode = false;
    public function mount()
    {
        $this->oracleData = collect();
        // ✅ Initialize with today's date
        $this->selectedDate = now()->format('Y-m-d');
        // ✅ Load organizations จาก Oracle ก่อน
        $this->loadOrgOptionsFromOracle();
        // Load user options from Oracle
        $this->loadUserOptions();
        // ตรวจสอบ parameters จาก ERP ที่ส่งมาผ่าน URL หรือ request
        $this->initializeFromERP();
    }
    public function selectAllChecks()
    {
        $uniqueChecks = $this->oracleData
            ->unique('your_reference')
            ->pluck('your_reference')
            ->values()
            ->toArray();
        $this->selectedChecks = $uniqueChecks;
        $this->selectAllChecks = true;
        // ✅ เพิ่ม dispatch event
        $this->dispatch('checkboxesUpdated', [
            'selectAll' => true,
            'selectedChecks' => $this->selectedChecks
        ]);
        session()->flash('success', 'Selected all ' . count($uniqueChecks) . ' checks.');
    }
    // ✅ เพิ่มฟังก์ชัน Deselect All แยกต่างหาก
    public function deselectAllChecks()
    {
        $this->selectedChecks = [];
        $this->selectAllChecks = false;

        // ✅ เพิ่ม dispatch event
        $this->dispatch('checkboxesUpdated', [
            'selectAll' => false,
            'selectedChecks' => []
        ]);

        session()->flash('info', 'All checks deselected.');

        Log::info('Deselect All Checks');
    }
    //🔥 ENHANCED: Toggle Check Selection with Better Debugging
    public function toggleCheckSelection($checkNumber)
    {
        // 🔥 Enhanced validation and logging
        $checkNumber = trim($checkNumber);
        // ✅ Validate check number exists in current data
        $exists = $this->oracleData->contains(function ($item) use ($checkNumber) {
            return $item->your_reference === $checkNumber;
        });
        if (!$exists) {
            session()->flash('warning', "Check number {$checkNumber} not found in current data.");
            return;
        }
        // 🔥 Toggle logic with array_values to reset keys
        if (in_array($checkNumber, $this->selectedChecks)) {
            // Remove from selection
            $this->selectedChecks = array_values(
                array_filter($this->selectedChecks, function ($item) use ($checkNumber) {
                    return $item !== $checkNumber;
                })
            );
        } else {
            // Add to selection
            $this->selectedChecks[] = $checkNumber;
            $this->selectedChecks = array_values(array_unique($this->selectedChecks));
        }
        // ✅ Update selectAllChecks status
        $uniqueChecks = $this->oracleData->unique('your_reference')->count();
        $selectedCount = count($this->selectedChecks);
        $this->selectAllChecks = $selectedCount === $uniqueChecks && $uniqueChecks > 0;
        // 🔥 IMPORTANT: Dispatch with detailed info
        $this->dispatch('checkboxesUpdated', [
            'selectAll' => $this->selectAllChecks,
            'selectedChecks' => $this->selectedChecks,
            'toggledCheck' => $checkNumber,
            'action' => in_array($checkNumber, $this->selectedChecks) ? 'selected' : 'deselected',
            'totalSelected' => $selectedCount,
            'totalUnique' => $uniqueChecks
        ]);
        // 🔥 Flash message for user feedback
        if (in_array($checkNumber, $this->selectedChecks)) {
            session()->flash('success', "Check {$checkNumber} selected. Total selected: {$selectedCount}");
        } else {
            session()->flash('info', "Check {$checkNumber} deselected. Total selected: {$selectedCount}");
        }
    }
    // ✅ สร้างเมธอดใหม่เพื่อรวมการทำงาน
    public function loadDataAndClear()
    {
        // ✅ ขั้นตอนที่ 1: ล้างการเลือกทั้งหมด
        $this->clearSelection();
        // ✅ ขั้นตอนที่ 2: ดึงข้อมูลใหม่
        $this->getOracleData();
    }
    // Clears all selected checkboxes and selection state.
    public function clearSelection()
    {
        $this->selectedChecks = [];
        $this->selectAllChecks = false;
        // ✅ ส่ง Event เพื่อบอกให้ frontend (Alpine.js) อัปเดตสถานะ
        $this->dispatch('checkboxesUpdated', [
            'selectAll' => false,
            'selectedChecks' => []
        ]);
        $this->dispatch('info', 'Selection has been cleared.');
    }
    // ✅ เพิ่มฟังก์ชัน isCheckSelected สำหรับตรวจสอบสถานะ checkbox
    public function isCheckSelected($checkNumber)
    {
        return in_array($checkNumber, $this->selectedChecks);
    }
    // ✅ เพิ่มฟังก์ชัน Get Selected Data
    public function getSelectedData()
    {
        if (empty($this->selectedChecks)) {
            return collect();
        }
        return $this->oracleData->filter(function ($item) {
            return in_array($item->your_reference, $this->selectedChecks);
        });
    }
    // ✅ เพิ่ม Date validation สำหรับ single date
    public function validateDate()
    {
        if (empty($this->selectedDate)) {
            return [
                'valid' => false,
                'message' => 'Please select a date'
            ];
        }
        try {
            $selectedDate = Carbon::parse($this->selectedDate);
            // ตรวจสอบว่าไม่ย้อนหลังเกิน 2 ปี
            $maxPastDate = now()->subYears(2);
            if ($selectedDate->lt($maxPastDate)) {
                return [
                    'valid' => false,
                    'message' => 'Selected date cannot be more than 2 years ago. Please select a more recent date.'
                ];
            }
            return [
                'valid' => true
            ];
        } catch (Exception $e) {
            return [
                'valid' => false,
                'message' => 'Invalid date format. Please select a valid date.'
            ];
        }
    }
    // ✅ เพิ่ม Estimate record count สำหรับ single date
    public function estimateRecordCount()
    {
        try {
            $selectedDate = Carbon::parse($this->selectedDate)->format('d-M-y');
            $oracleConnection = DB::connection('oracle');
            $countQuery = "
                SELECT COUNT(*) as estimated_count
                FROM ap_checks_all apc
                WHERE apc.org_id = :org_id
                AND apc.check_date = to_date(:selected_date,'DD-MON-RR')
                AND (apc.void_date < to_date(:selected_date,'DD-MON-RR') or
                    apc.void_date > to_date(:selected_date,'DD-MON-RR') or apc.void_date is NULL)
            ";
            $params = [
                'org_id' => $this->orgId,
                'selected_date' => $selectedDate
            ];
            if (!empty($this->userId)) {
                $countQuery .= " AND apc.created_by = :user_id";
                $params['user_id'] = $this->userId;
            }
            $result = $oracleConnection->select($countQuery, $params);
            $estimatedCount = $result[0]->estimated_count ?? 0;
            Log::info("Estimated record count: {$estimatedCount} for date {$this->selectedDate}");
            return $estimatedCount;
        } catch (Exception $e) {
            Log::error('Failed to estimate record count: ' . $e->getMessage());
            return 0;
        }
    }
    // ✅ เพิ่ม Memory management functions
    private function setMemoryLimits($estimatedRecords)
    {
        // คำนวณ memory ที่ต้องการประมาณ (แต่ละ record ใช้ประมาณ 2KB)
        $estimatedMemory = $estimatedRecords * 2048; // 2KB per record
        $currentLimit = $this->parseMemoryLimit(ini_get('memory_limit'));
        $recommendedLimit = max($estimatedMemory * 2, 256 * 1024 * 1024); // อย่างน้อย 256MB
        // ถ้า current limit น้อยกว่าที่แนะนำ ให้ขยาย (ถ้าเป็นไปได้)
        if ($currentLimit < $recommendedLimit && $recommendedLimit <= 1024 * 1024 * 1024) { // ไม่เกิน 1GB
            $newLimit = $this->formatBytes($recommendedLimit);
            @ini_set('memory_limit', $newLimit);
            Log::info("Temporarily increased memory limit to {$newLimit} for {$estimatedRecords} records");
        }
        // เพิ่ม execution time
        if ($estimatedRecords > 1000) {
            $executionTime = min(300, max(60, $estimatedRecords / 10)); // 60-300 seconds
            @set_time_limit($executionTime);
            Log::info("Set execution time limit to {$executionTime} seconds");
        }
    }
    private function parseMemoryLimit($limit)
    {
        $limit = trim($limit);
        $last = strtolower($limit[strlen($limit) - 1]);
        $limit = (int) $limit;

        switch ($last) {
            case 'g':
                $limit *= 1024 * 1024 * 1024;
                break;
            case 'm':
                $limit *= 1024 * 1024;
                break;
            case 'k':
                $limit *= 1024;
                break;
        }
        return $limit;
    }
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    // ✅ เพิ่มฟังก์ชันใหม่: Load org options จาก Oracle database
    private function loadOrgOptionsFromOracle()
    {
        try {
            $oracleConnection = DB::connection('oracle');
            // ✅ ใช้ query จริงจาก Oracle HR_OPERATING_UNITS
            $orgs = $oracleConnection->select("
                SELECT 
                    organization_id as org_id,
                    name as org_name,
                    short_code
                FROM hr_operating_units
                WHERE (date_to IS NULL OR date_to > SYSDATE)
                AND organization_id IN (
                    SELECT DISTINCT org_id
                    FROM ap_checks_all
                    WHERE check_date >= SYSDATE - 365
                )
                ORDER BY organization_id
            ");
            if (!empty($orgs)) {
                $orgMap = [];
                foreach ($orgs as $org) {
                    // ✅ Format: "KTR - KTR Operating Unit"
                    $orgMap[$org->org_id] = $org->org_name;
                }
                $this->orgOptions = $orgMap;
                // ✅ ถ้ายังไม่มี orgId หรือ orgId ที่เลือกไม่อยู่ในรายการ ให้เลือกอันแรก
                if (empty($this->orgId) || !array_key_exists($this->orgId, $this->orgOptions)) {
                    $this->orgId = array_key_first($this->orgOptions);
                }
            } else {
                // ✅ Fallback: ถ้าไม่มีข้อมูลจาก HR_OPERATING_UNITS ให้ดึงจาก AP_CHECKS_ALL
                $this->loadOrgOptionsFromAPChecks();
            }
        } catch (Exception $e) {
            // ✅ Fallback: ดึงจาก AP_CHECKS_ALL แทน
            $this->loadOrgOptionsFromAPChecks();
        }
    }
    //✅ Fallback method: ดึง org options จาก AP_CHECKS_ALL
    private function loadOrgOptionsFromAPChecks()
    {
        try {
            $oracleConnection = DB::connection('oracle');

            $orgs = $oracleConnection->select("
                SELECT 
                    org_id,
                    COUNT(DISTINCT check_id) as check_count,
                    COUNT(DISTINCT vendor_name) as vendor_count,
                    MAX(check_date) as latest_check
                FROM ap_checks_all
                WHERE check_date >= SYSDATE - 365
                GROUP BY org_id
                HAVING COUNT(DISTINCT check_id) > 0
                ORDER BY org_id
            ");
            if (!empty($orgs)) {
                $orgMap = [];
                foreach ($orgs as $org) {
                    // ✅ Format: "ORG 81 (1,234 checks)"
                    $orgMap[$org->org_id] = 'ORG ' . $org->org_id . ' (' . number_format($org->check_count) . ' checks)';
                }
                $this->orgOptions = $orgMap;
                // ✅ เลือก org แรกถ้ายังไม่ได้เลือก
                if (empty($this->orgId) || !array_key_exists($this->orgId, $this->orgOptions)) {
                    $this->orgId = array_key_first($this->orgOptions);
                }
            } else {
                // ✅ Last resort: ใช้ค่าเริ่มต้นที่ hardcode
                $this->orgOptions = [
                    '81' => 'ORG 81 (No data available)',
                    '82' => 'ORG 82 (No data available)',
                    '83' => 'ORG 83 (No data available)',
                    '84' => 'ORG 84 (No data available)'
                ];
                $this->orgId = '81';
            }
        } catch (Exception $e) {
            // ✅ Final fallback
            $this->orgOptions = [
                '81' => 'ORG 81 (Connection Error)',
                '82' => 'ORG 82 (Connection Error)',
                '83' => 'ORG 83 (Connection Error)',
                '84' => 'ORG 84 (Connection Error)'
            ];
            $this->orgId = '81';
        }
    }
    // ✅ อัปเดต mapOrganization function ให้ใช้ข้อมูลจาก Oracle
    private function mapOrganization($erpOrg)
    {
        // ✅ สร้าง mapping จาก orgOptions ที่โหลดมาจาก Oracle
        $orgMapping = [];

        foreach ($this->orgOptions as $orgId => $orgName) {
            // ดึง short_code จากชื่อ org (เช่น "KTR - KTR Operating Unit" -> "KTR")
            $shortCode = strtoupper(explode(' - ', $orgName)[0]);
            $orgMapping[$shortCode] = $orgId;
            $orgMapping[$orgId] = $orgId; // Map org ID to itself
        }
        // ✅ เพิ่ม common mappings
        $commonMappings = [
            'TRU' => '84',  // Thai Rung Union
            'TAP' => '82',  // Thai Auto Parts
            'TRT' => '83',  // Thai Rung Tapanakorn
            'KTR' => '81'   // KTR
        ];
        $orgMapping = array_merge($orgMapping, $commonMappings);
        $result = $orgMapping[strtoupper($erpOrg)] ?? array_key_first($this->orgOptions);
        Log::info('Mapped ERP org "' . $erpOrg . '" to org ID "' . $result . '"');
        return $result;
    }
    // Load user options from Oracle database
    private function loadUserOptions()
    {
        try {
            $oracleConnection = DB::connection('oracle');
            $users = $oracleConnection->select("
                SELECT
                    ac.created_by,
                    fu.user_name,
                    fu.description
                FROM ap_checks_all ac
                LEFT JOIN fnd_user fu ON ac.created_by = fu.user_id
                WHERE ac.created_by IS NOT NULL
                AND ac.check_date >= SYSDATE - 365
                GROUP BY ac.created_by, fu.user_name, fu.description
                ORDER BY fu.user_name
            ");
            $userMap = collect($users)->mapWithKeys(function ($user) {
                $displayName = ($user->user_name ?? 'User' . $user->created_by) .
                    ' (' . ($user->description ?? 'No Description') . ')';
                return [$user->created_by => $displayName];
            });
            $this->userOptions = ['' => 'All Users'] + $userMap->toArray();
            Log::info('Loaded ' . count($users) . ' user options from Oracle');
        } catch (Exception $e) {
            Log::error('Failed to load user options: ' . $e->getMessage());
            $this->userOptions = ['' => 'All Users'];
        }
    }
    // ✅ อัปเดต Initialize parameters from ERP request สำหรับ single date
    private function initializeFromERP()
    {
        $request = request();
        // Organization parameter
        if ($request->has('organization') && !empty($request->get('organization'))) {
            $orgValue = $request->get('organization');
            $this->orgId = $this->mapOrganization($orgValue);
        }
        // ✅ เปลี่ยนจากการรับ from/to date เป็นรับ single date
        if ($request->has('check_date') && !empty($request->get('check_date'))) {
            $checkDate = $request->get('check_date');
            try {
                $this->selectedDate = $this->parseERPDate($checkDate);
            } catch (Exception $e) {
                Log::warning('Invalid date format from ERP for check_date: ' . $checkDate);
                $this->selectedDate = now()->format('Y-m-d');
            }
        }
        // ✅ ยังสามารถรับ from_date หรือ to_date ได้ (เอาอันใดอันหนึ่ง)
        if ($request->has('check_date_from') && !empty($request->get('check_date_from'))) {
            $dateFrom = $request->get('check_date_from');
            try {
                $this->selectedDate = $this->parseERPDate($dateFrom);
            } catch (Exception $e) {
                Log::warning('Invalid date format from ERP for check_date_from: ' . $dateFrom);
                $this->selectedDate = now()->format('Y-m-d');
            }
        }
        if ($request->has('check_date_to') && !empty($request->get('check_date_to'))) {
            $dateTo = $request->get('check_date_to');
            try {
                $this->selectedDate = $this->parseERPDate($dateTo);
            } catch (Exception $e) {
                Log::warning('Invalid date format from ERP for check_date_to: ' . $dateTo);
                $this->selectedDate = now()->format('Y-m-d');
            }
        }
        // User parameter
        if ($request->has('user') && !empty($request->get('user'))) {
            $this->userId = $request->get('user');
        }
        // ถ้ามีการส่ง parameters มาจาก ERP ให้ทำการ search อัตโนมัติ
        if ($request->hasAny(['organization', 'check_date', 'check_date_from', 'check_date_to', 'user'])) {
            Log::info('ERP parameters detected, auto-searching Oracle data', [
                'organization' => $this->orgId,
                'selected_date' => $this->selectedDate,
                'user' => $this->userId
            ]);
            // ใช้ preview แทน full query สำหรับ auto-search
            $this->getDataPreview();
        }
    }
    // Parse date from ERP in various formats
    private function parseERPDate($dateString)
    {
        $formats = [
            'Y-m-d',        // 2025-07-30
            'd-m-Y',        // 30-07-2025
            'Y/m/d',        // 2025/07/30
            'd/m/Y',        // 30/07/2025
            'Ymd',          // 20250730
            'd-M-Y',        // 30-Jul-2025
            'd-M-y',        // 30-Jul-25
        ];
        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $dateString);
                if ($date && $date->format($format) === $dateString) {
                    return $date->format('Y-m-d');
                }
            } catch (Exception $e) {
                continue;
            }
        }
        throw new Exception('Invalid date format: ' . $dateString);
    }
    // ✅ เพิ่มฟังก์ชันรีเฟรช organizations
    public function refreshOrganizations()
    {
        Log::info('Refreshing organizations from Oracle...');
        // ✅ Clear current options
        $this->orgOptions = [];
        // ✅ Reload from Oracle
        $this->loadOrgOptionsFromOracle();
        if (!empty($this->orgOptions)) {
            session()->flash('success', 'Organizations refreshed successfully. Found ' . count($this->orgOptions) . ' organizations.');
        } else {
            session()->flash('error', 'Failed to refresh organizations from Oracle.');
        }
    }
    // 🎯 แก้ไขแล้ว: getOracleData() ใน CheckOracle.php
    public function getOracleData()
    {
        // ตรวจสอบก่อนว่าสามารถ search ได้หรือไม่
        if (!$this->canSearch) {
            session()->flash('error', 'Please enter either Check Start or Check End number before searching.');
            return;
        }

        // เปิดใช้ strict mode เพื่อแสดงเฉพาะ records ที่มี WHT
        $this->strictWHTMode = true;

        // Reset all states
        $this->dataReady = false;
        $this->oracleData = collect();
        $this->loadingStep = 1;
        $this->isLoading = true;
        $this->isProcessing = true;
        $this->shouldContinue = true;
        $this->pollingActive = true;
        $this->loadingProgress = 0;
        $this->loadingMessage = 'Initializing...';
        $this->loadingData = [];
        $this->tempQueryParams = [];
        $this->currentRetries = 0;

        // Clear selections
        $this->selectedChecks = [];
        $this->selectAllChecks = false;

        // Start processing with JavaScript backup
        $this->dispatch('startLoadingProcess');
        $this->processDataStep();
    }
    // 🔥 NEW: Helper method to reset loading state
    private function resetLoadingState()
    {
        $this->isLoading = false;
        $this->isProcessing = false;
        $this->shouldContinue = false;
        $this->loadingProgress = 0;
        $this->loadingMessage = '';
        $this->loadingStep = 0;
        $this->loadingData = [];
        $this->tempQueryParams = [];
    }
    // 🎯 แก้ไขฟังก์ชัน exportOracleCU27() ให้เรียบง่ายขึ้น
    public function exportOracleCU27()
    {
        if ($this->oracleData->isEmpty()) {
            session()->flash('error', 'No Oracle data to export. Please search for data first.');
            return;
        }
        if (empty($this->selectedChecks)) {
            session()->flash('error', 'No checks selected for export. Please select at least one check number.');
            return;
        }
        $dataToExport = $this->getSelectedData();
        if ($dataToExport->isEmpty()) {
            session()->flash('error', 'No data found for selected check numbers.');
            return;
        }
        if ($dataToExport->count() > $this->maxRecordLimit) {
            session()->flash('error', 'Too many records to export (' . number_format($dataToExport->count()) . '). Maximum allowed: ' . number_format($this->maxRecordLimit) . '. Please reduce your selection.');
            return;
        }
        try {
            $this->setMemoryLimits($dataToExport->count());
            $selectedDate = Carbon::parse($this->selectedDate);
            $output = '';
            // Helper Functions
            $handleThaiText = function ($text, $maxLength = null) {
                if (empty($text)) return '';
                $text = trim($text);
                $text = str_replace(["\r", "\n", "\t"], ' ', $text);
                $text = preg_replace('/\s+/', ' ', $text);
                return $maxLength ? mb_substr($text, 0, $maxLength, 'UTF-8') : $text;
            };
            $formatAmount = function ($amount, $length = 20) {
                $numericValue = (float)str_replace(',', '', $amount ?? 0);
                $formatted = number_format($numericValue, 2, '.', '');
                return str_pad($formatted, $length, '0', STR_PAD_LEFT);
            };
            $padText = function ($text, $length, $padChar = ' ', $padType = STR_PAD_RIGHT) {
                if (empty($text)) $text = '';
                $currentLength = mb_strlen($text, 'UTF-8');
                if ($currentLength >= $length) {
                    return mb_substr($text, 0, $length, 'UTF-8');
                }
                $padLength = $length - $currentLength;
                $padding = str_repeat($padChar, $padLength);
                return $padType === STR_PAD_RIGHT ? $text . $padding : $padding . $text;
            };
            $validateEmail = function ($email) {
                if (empty($email)) return '';
                $email = trim($email);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return $email;
                }
                return '';
            };
            // GROUP DATA BY CHECK NUMBER
            $groupedData = $dataToExport->groupBy('your_reference');
            foreach ($groupedData as $checkNumber => $records) {
                $mainRecordForCheck = $records->first();
                //การคำนวณ WHT ที่ถูกต้องตาม CU27 Spec
                $invoicesData = $this->calculateCorrectInvoiceAmounts($records);
                $totalWHTForCheck = $invoicesData['total_wht'];
                $totalBaseForCheck = $invoicesData['total_base'];
                $correctedWHTRate = $invoicesData['weighted_rate'];
                //ดึงข้อมูล WHT Rates ทั้งหมดสำหรับ CU27
                $whtRatesData = $this->extractWHTRatesFromInvoices($invoicesData['invoices']);
                // Process Email & Address
                $address1 = trim($mainRecordForCheck->address_line1 ?? '');
                $email_mixed = trim($mainRecordForCheck->email_address ?? '');
                $email_vs_only = trim($mainRecordForCheck->vs_email_address_only ?? '');
                $validEmailFor1337 = $validateEmail($email_vs_only);
                $hasRealAddress = !empty($address1) && $address1 !== '' && $address1 !== '.';
                $mailAddr1Value = $hasRealAddress ? $address1 : $email_mixed;
                // CU27 Fields สำหรับ Transaction Line
                $F_rec_iden = $padText('TXN', 3);
                $F_rec_iden1 = $padText('CH', 10);
                $F_benefi_name = $padText($handleThaiText($mainRecordForCheck->benefi_name ?? '', 150), 150);
                $F_benefi_addr1 = $padText('', 70);
                $F_benefi_addr2 = $padText('', 70);
                $F_benefi_addr3 = $padText('', 70);
                $F_benefi_addr4 = $padText('', 70);
                $F_benefi_site = $padText('', 70);
                $F_mail_to_name = $padText($handleThaiText($mainRecordForCheck->benefi_name ?? '', 150), 150);
                $F_mail_addr1 = $padText($handleThaiText($mailAddr1Value, 70), 70);
                $F_mail_addr2 = $padText($handleThaiText($mainRecordForCheck->address_line2 ?? '', 70), 70);
                $F_mail_addr3 = $padText($handleThaiText($mainRecordForCheck->address_line3 ?? '', 70), 70);
                $F_mail_addr4 = $padText('', 70);
                $F_zip_code = $padText($handleThaiText($mainRecordForCheck->zip_code ?? '', 10), 10);
                $F_invoice_amount = $padText('', 20);
                $F_vat_amount = $padText('', 20);
                $F_wht_amount = $padText('', 20); // 🔥 บังคับให้เป็นค่าว่างเสมอ
                $F_ben_charged = $padText('OUR', 20);
                $F_check_amt = $padText($formatAmount($mainRecordForCheck->check_amt ?? 0, 20), 20);
                $F_currency = $padText('THB', 10);
                $F_your_reference = $padText($handleThaiText($mainRecordForCheck->your_reference ?? '', 35), 35);
                $F_check_date = $padText($mainRecordForCheck->check_date ?? '', 8);
                $paymentDetail = $handleThaiText($mainRecordForCheck->payment_details1 ?? '', 35);
                $F_payment_details1 = $padText($paymentDetail, 35);
                $F_payment_details2 = $padText('', 35);
                $F_payment_details3 = $padText('', 35);
                $F_payment_details4 = $padText('', 35);
                // 🎯 ใช้ค่าจาก Oracle โดยตรง แทนการ hardcode
                $exchangeDoc = trim($mainRecordForCheck->payment_details1 ?? '');
                if (empty($exchangeDoc)) {
                    // ถ้าไม่มีข้อมูลใน Oracle ให้ใช้ default
                    $exchangeDoc = '';
                }
                $F_exchange_document = $padText($exchangeDoc, 35);
                $deliveryMethod = trim($mainRecordForCheck->delivery_method ?? 'RT');
                $F_delivery_method = $padText($deliveryMethod, 5);
                $paymentLocation = trim($mainRecordForCheck->payment_location ?? '');
                if ($deliveryMethod === 'OC' && empty($paymentLocation)) {
                    $paymentLocation = '';
                }
                $F_payment_location = $padText($handleThaiText($paymentLocation, 35), 35);
                $F_preadvise_method = $padText('', 5);
                $F_fax_number = $padText('', 10);
                $F_email_address = $padText($validEmailFor1337, 120);
                $F_ivr_code = $padText('', 10);
                $F_sms_number = $padText('', 20);
                $F_charge_indicator = $padText('OUR', 12);
                $F_bene_tax_id = $padText($handleThaiText($mainRecordForCheck->vendor_tax_id ?? '', 20), 20);
                $F_personal_id = $padText('', 20);
                //WHT Type Field (Pos: 1539-1543) 
                $whtType = '';

                if ($totalWHTForCheck > 0) {
                    $firstInvoice = $invoicesData['invoices']->first();
                    if ($firstInvoice && !empty($firstInvoice['awt_name'])) {
                        $awtName = $firstInvoice['awt_name'];
                        $whtRate = $firstInvoice['wht_rate'] ?? 0;

                        // Check if AWT_NAME has '-c' suffix for company/juristic person
                        $isJuristicPerson = (bool) preg_match('/-c$/i', trim($awtName));

                        // Set WHT Type based on person type (not rate)
                        $whtType = $isJuristicPerson ? '53' : '03';  // Company = 53, Individual = 03

                        Log::info("WHT Type Detection in Export", [
                            'check_number' => $checkNumber,
                            'awt_name' => $awtName,
                            'wht_rate' => $whtRate,
                            'determined_wht_type' => $whtType,
                            'total_wht' => $totalWHTForCheck
                        ]);
                    } else {
                        // Fallback: if no AWT name, default to 53 (company)
                        $whtType = '53';
                        Log::warning("No AWT name found, defaulting to WHT type 53", [
                            'check_number' => $checkNumber,
                            'total_wht' => $totalWHTForCheck
                        ]);
                    }
                }

                $F_wht_type = $padText($whtType, 5);
                // Add logging after the whtRatesData calculation  
                Log::info("Tax Types in CU27 Export", [
                    'check_number' => $checkNumber,
                    'main_wht_type' => $whtType,
                    'rate1_tax_type' => $whtRatesData['rate1']['tax_type'] ?? '',
                    'rate2_tax_type' => $whtRatesData['rate2']['tax_type'] ?? '',
                    'rate3_tax_type' => $whtRatesData['rate3']['tax_type'] ?? '',
                    'awt_names' => [
                        'rate1' => $whtRatesData['rate1']['awt_name'] ?? '',
                        'rate2' => $whtRatesData['rate2']['awt_name'] ?? '',
                        'rate3' => $whtRatesData['rate3']['awt_name'] ?? ''
                    ]
                ]);
                // ✅ Tax Rate 1 Fields (Positions 1544-1625) - แก้ไขแล้ว
                $F_taxable_amount1 = $padText($this->formatWHTAmount($whtRatesData['rate1']['taxable_amount'] ?? 0), 20); // ✅ ใช้ taxable_amount ที่ถูกต้อง
                $F_tax_type1 = $padText($whtRatesData['rate1']['tax_type'] ?? '', 2);
                $F_tax_desc1 = $padText($handleThaiText($whtRatesData['rate1']['description'] ?? '', 35), 35);
                $F_tax_rate1 = $padText($this->formatWHTRate($whtRatesData['rate1']['rate'] ?? 0), 5);
                $F_tax_amount1 = $padText($this->formatWHTAmount($whtRatesData['rate1']['tax_amount'] ?? 0), 20);
                // 🔥 Tax Rate 2 Fields (Positions 1626-1707) - แก้ไขจาก tax_amount เป็น taxable_amount
                $F_taxable_amount2 = $padText($this->formatWHTAmount($whtRatesData['rate2']['taxable_amount'] ?? 0), 20); // 🔥 แก้ไขตรงนี้
                $F_tax_type2 = $padText($whtRatesData['rate2']['tax_type'] ?? '', 2);
                $F_tax_desc2 = $padText($handleThaiText($whtRatesData['rate2']['description'] ?? '', 35), 35);
                $F_tax_rate2 = $padText($this->formatWHTRate($whtRatesData['rate2']['rate'] ?? 0), 5);
                $F_tax_amount2 = $padText($this->formatWHTAmount($whtRatesData['rate2']['tax_amount'] ?? 0), 20);
                // 🔥 Tax Rate 3 Fields (Positions 1708-1789) - แก้ไขจาก tax_amount เป็น taxable_amount
                $F_taxable_amount3 = $padText($this->formatWHTAmount($whtRatesData['rate3']['taxable_amount'] ?? 0), 20); // 🔥 แก้ไขตรงนี้
                $F_tax_type3 = $padText($whtRatesData['rate3']['tax_type'] ?? '', 2);
                $F_tax_desc3 = $padText($handleThaiText($whtRatesData['rate3']['description'] ?? '', 35), 35);
                $F_tax_rate3 = $padText($this->formatWHTRate($whtRatesData['rate3']['rate'] ?? 0), 5);
                $F_tax_amount3 = $padText($this->formatWHTAmount($whtRatesData['rate3']['tax_amount'] ?? 0), 20);
                // 🔥 Tax Rate 4 Fields (Positions 1790-1871) - แก้ไขจาก tax_amount เป็น taxable_amount
                $F_taxable_amount4 = $padText($this->formatWHTAmount($whtRatesData['rate4']['taxable_amount'] ?? 0), 20); // 🔥 แก้ไขตรงนี้
                $F_tax_type4 = $padText($whtRatesData['rate4']['tax_type'] ?? '', 2);
                $F_tax_desc4 = $padText($handleThaiText($whtRatesData['rate4']['description'] ?? '', 35), 35);
                $F_tax_rate4 = $padText($this->formatWHTRate($whtRatesData['rate4']['rate'] ?? 0), 5);
                $F_tax_amount4 = $padText($this->formatWHTAmount($whtRatesData['rate4']['tax_amount'] ?? 0), 20);
                // 🔥 Tax Rate 5 Fields (Positions 1872-1953) - แก้ไขจาก tax_amount เป็น taxable_amount
                $F_taxable_amount5 = $padText($this->formatWHTAmount($whtRatesData['rate5']['taxable_amount'] ?? 0), 20); // 🔥 แก้ไขตรงนี้
                $F_tax_type5 = $padText($whtRatesData['rate5']['tax_type'] ?? '', 2);
                $F_tax_desc5 = $padText($handleThaiText($whtRatesData['rate5']['description'] ?? '', 35), 35);
                $F_tax_rate5 = $padText($this->formatWHTRate($whtRatesData['rate5']['rate'] ?? 0), 5);
                $F_tax_amount5 = $padText($this->formatWHTAmount($whtRatesData['rate5']['tax_amount'] ?? 0), 20);
                $F_wht_doc_no = $padText('', 20);
                // $F_client_name = $padText($handleThaiText(mb_substr($mainRecordForCheck->benefi_name ?? '', 0, 35, 'UTF-8'), 35), 35);
                $F_client_name = $padText('', 35);
                $clientAddress = $handleThaiText(($mainRecordForCheck->address_line1 ?? '') . ' ' . ($mainRecordForCheck->address_line2 ?? '') . ' ' . ($mainRecordForCheck->address_line3 ?? ''), 105);
                // $F_client_address = $padText($clientAddress, 105);
                $F_client_address = $padText('', 105);
                $F_wht_seq_no = $padText('', 10);
                $F_payment_condition = $padText('', 1);
                // $F_third_party_name = $padText($handleThaiText($mainRecordForCheck->benefi_name ?? '', 150), 150);
                $F_third_party_name = $padText('', 150);
                $F_third_party_addr1 = $padText('', 70);
                $F_third_party_addr2 = $padText('', 70);
                $F_third_party_addr3 = $padText('', 35);
                $F_third_party_addr4 = $padText('', 35);
                $F_debit_bank_code = $padText('', 10);
                $F_debit_branch_code = $padText('', 10);
                $F_debit_ac_no = $padText('', 20);
                $F_debit_country_code = $padText('', 2);
                $F_transaction_type = $padText('', 3);
                $F_customer_code = $padText('', 20);
                $F_customer_name = $padText('', 150);
                $F_customer_acro = $padText('', 20);
                $F_customer_addr1 = $padText('', 70);
                $F_customer_addr2 = $padText('', 70);
                $F_customer_addr3 = $padText('', 35);
                $F_customer_addr4 = $padText('', 35);
                $F_cust_ref1 = $padText('', 35);
                $F_cust_ref2 = $padText('', 35);
                $F_cust_ref3 = $padText('', 35);
                $F_cust_ref4 = $padText('', 35);
                $F_cust_ref5 = $padText('', 35);
                $F_credit_bank_code = $padText('', 10);
                $F_credit_branch_code = $padText('', 10);
                $F_credit_ac_no = $padText('', 20);
                $F_credit_country_code = $padText('', 2);
                $F_optional_service = $padText('', 2);
                $F_sender_name = $padText('', 70);
                $F_sender_correspondent = $padText('', 70);
                $F_receiving_bank_name = $padText('', 70);
                $F_trx_ref1 = $padText('', 35);
                $F_trx_ref2 = $padText('', 35);
                $F_trx_ref3 = $padText('', 35);
                $F_trx_ref4 = $padText('', 35);
                $F_trx_ref5 = $padText('', 35);
                $F_trx_ref6 = $padText('', 35);
                $F_on_behalf_tax_id = $padText('', 20);
                $F_on_behalf_name = $padText('', 150);
                $F_on_behalf_address = $padText('', 210);
                //Build complete CU27 line with CORRECTED WHT Rates (total 3948 characters exactly)
                $cu27Line =
                    $F_rec_iden .             // 1-3
                    $F_rec_iden1 .            // 4-13
                    $F_benefi_name .          // 14-163
                    $F_benefi_addr1 .         // 164-233
                    $F_benefi_addr2 .         // 234-303
                    $F_benefi_addr3 .         // 304-373
                    $F_benefi_addr4 .         // 374-443
                    $F_benefi_site .          // 444-513
                    $F_mail_to_name .         // 514-663
                    $F_mail_addr1 .           // 664-733
                    $F_mail_addr2 .           // 734-803
                    $F_mail_addr3 .           // 804-873
                    $F_mail_addr4 .           // 874-943
                    $F_zip_code .             // 944-953
                    $F_invoice_amount .       // 954-973
                    $F_vat_amount .           // 974-993
                    $F_wht_amount .           // 994-1013 ✅ รวม WHT ทั้งหมด
                    $F_ben_charged .          // 1014-1033
                    $F_check_amt .            // 1034-1053
                    $F_currency .             // 1054-1063
                    $F_your_reference .       // 1064-1098
                    $F_check_date .           // 1099-1106
                    $F_payment_details1 .     // 1107-1141
                    $F_payment_details2 .     // 1142-1176
                    $F_payment_details3 .     // 1177-1211
                    $F_payment_details4 .     // 1212-1246
                    $F_exchange_document .    // 1247-1281
                    $F_delivery_method .      // 1282-1286
                    $F_payment_location .     // 1287-1321
                    $F_preadvise_method .     // 1322-1326
                    $F_fax_number .           // 1327-1336
                    $F_email_address .        // 1337-1456
                    $F_ivr_code .             // 1457-1466
                    $F_sms_number .           // 1467-1486
                    $F_charge_indicator .     // 1487-1498
                    $F_bene_tax_id .          // 1499-1518
                    $F_personal_id .          // 1519-1538
                    $F_wht_type .             // 1539-1543 ✅ 53
                    //Tax Rate 1 (Positions 1544-1625) - WHT Rate แรก
                    $F_taxable_amount1 .      // 1544-1563 ✅ Taxable Amount 1
                    $F_tax_type1 .            // 1564-1565 ✅ Tax Type 1 (03, 05, etc.)
                    $F_tax_desc1 .            // 1566-1600 ✅ Tax Description 1
                    $F_tax_rate1 .            // 1601-1605 ✅ Tax Rate 1 (03.00, 05.00, etc.)
                    $F_tax_amount1 .          // 1606-1625 ✅ Tax Amount 1
                    //Tax Rate 2 (Positions 1626-1707) - WHT Rate ที่สอง
                    $F_taxable_amount2 .      // 1626-1645 ✅ Taxable Amount 2
                    $F_tax_type2 .            // 1646-1647 ✅ Tax Type 2
                    $F_tax_desc2 .            // 1648-1682 ✅ Tax Description 2
                    $F_tax_rate2 .            // 1683-1687 ✅ Tax Rate 2
                    $F_tax_amount2 .          // 1688-1707 ✅ Tax Amount 2
                    //Tax Rate 3 (Positions 1708-1789) - WHT Rate ที่สาม
                    $F_taxable_amount3 .      // 1708-1727 ✅ Taxable Amount 3
                    $F_tax_type3 .            // 1728-1729 ✅ Tax Type 3
                    $F_tax_desc3 .            // 1730-1764 ✅ Tax Description 3
                    $F_tax_rate3 .            // 1765-1769 ✅ Tax Rate 3
                    $F_tax_amount3 .          // 1770-1789 ✅ Tax Amount 3
                    //Tax Rate 4 (Positions 1790-1871) - WHT Rate ที่สี่
                    $F_taxable_amount4 .      // 1790-1809 ✅ Taxable Amount 4
                    $F_tax_type4 .            // 1810-1811 ✅ Tax Type 4
                    $F_tax_desc4 .            // 1812-1846 ✅ Tax Description 4
                    $F_tax_rate4 .            // 1847-1851 ✅ Tax Rate 4
                    $F_tax_amount4 .          // 1852-1871 ✅ Tax Amount 4
                    //Tax Rate 5 (Positions 1872-1953) - WHT Rate ที่ห้า
                    $F_taxable_amount5 .      // 1872-1891 ✅ Taxable Amount 5
                    $F_tax_type5 .            // 1892-1893 ✅ Tax Type 5
                    $F_tax_desc5 .            // 1894-1928 ✅ Tax Description 5
                    $F_tax_rate5 .            // 1929-1933 ✅ Tax Rate 5
                    $F_tax_amount5 .          // 1934-1953 ✅ Tax Amount 5
                    $F_wht_doc_no .           // 1954-1973
                    $F_client_name .          // 1974-2008
                    $F_client_address .       // 2009-2113
                    $F_wht_seq_no .           // 2114-2123
                    $F_payment_condition .    // 2124-2124
                    $F_third_party_name .     // 2125-2274
                    $F_third_party_addr1 .    // 2275-2344
                    $F_third_party_addr2 .    // 2345-2414
                    $F_third_party_addr3 .    // 2415-2449
                    $F_third_party_addr4 .    // 2450-2484
                    $F_debit_bank_code .      // 2485-2494
                    $F_debit_branch_code .    // 2495-2504
                    $F_debit_ac_no .          // 2505-2524
                    $F_debit_country_code .   // 2525-2526
                    $F_transaction_type .     // 2527-2529
                    $F_customer_code .        // 2530-2549
                    $F_customer_name .        // 2550-2699
                    $F_customer_acro .        // 2700-2719
                    $F_customer_addr1 .       // 2720-2789
                    $F_customer_addr2 .       // 2790-2859
                    $F_customer_addr3 .       // 2860-2894
                    $F_customer_addr4 .       // 2895-2929
                    $F_cust_ref1 .            // 2930-2964
                    $F_cust_ref2 .            // 2965-2999
                    $F_cust_ref3 .            // 3000-3034
                    $F_cust_ref4 .            // 3035-3069
                    $F_cust_ref5 .            // 3070-3104
                    $F_credit_bank_code .     // 3105-3114
                    $F_credit_branch_code .   // 3115-3124
                    $F_credit_ac_no .         // 3125-3144
                    $F_credit_country_code .  // 3145-3146
                    $F_optional_service .     // 3147-3148
                    $F_sender_name .          // 3149-3218
                    $F_sender_correspondent . // 3219-3288
                    $F_receiving_bank_name .  // 3289-3358
                    $F_trx_ref1 .             // 3359-3393
                    $F_trx_ref2 .             // 3394-3428
                    $F_trx_ref3 .             // 3429-3463
                    $F_trx_ref4 .             // 3464-3498
                    $F_trx_ref5 .             // 3499-3533
                    $F_trx_ref6 .             // 3534-3568
                    $F_on_behalf_tax_id .     // 3569-3588
                    $F_on_behalf_name .       // 3589-3738
                    $F_on_behalf_address;     // 3739-3948
                $output .= $cu27Line . "\n";
                //Process invoices และแสดง amounts ที่ถูกต้อง (ใช้ฟังก์ชันที่ฉลาด)
                $invoiceLines = $this->generateInvoiceLinesFromCalculatedData($invoicesData['invoices']);
                if (!empty($invoiceLines)) {
                    $output .= $invoiceLines;
                }
            }
            // Thai encoding conversion
            try {
                if (mb_check_encoding($output, 'UTF-8')) {
                    $ansiData = @iconv('UTF-8', 'Windows-874//IGNORE', $output);
                    if ($ansiData === false) {
                        $ansiData = @iconv('UTF-8', 'TIS-620//IGNORE', $output);
                    }
                    if ($ansiData === false) {
                        $ansiData = "\xEF\xBB\xBF" . $output;
                    }
                } else {
                    $ansiData = $output;
                }
            } catch (Exception $e) {
                Log::error('Thai encoding conversion failed: ' . $e->getMessage());
                $ansiData = $output;
            }

            $fileData = str_replace(["\r\n", "\r", "\n"], "\r\n", $ansiData);
            $fileSize = strlen($fileData);

            // 🎯 ใส่ตรงนี้ - Generate filename with date
            $dateFormatted = Carbon::parse($this->selectedDate)->format('Ymd');
            // ✅ โค้ดใหม่ (ถูกต้อง)
            $orgName = $this->orgOptions[$this->orgId] ?? 'ORG' . $this->orgId;
            $orgParts = preg_split('/[\s-]+/', trim($orgName));
            $shortOrgName = strtoupper($orgParts[0]); // เอาแค่คำแรก เช่น TRT, TRU, TAP
            $dateFormatted = Carbon::parse($this->selectedDate)->format('Ymd');
            $fileName = $shortOrgName . '_CU27_' . $dateFormatted . '.txt';

            // Clear selection after successful export
            $originalSelectedCount = count($this->selectedChecks);
            $this->selectedChecks = [];
            $this->selectAllChecks = false;

            $successMessage = '✅ FIXED CU27 file exported successfully! ' .
                $originalSelectedCount . ' checks exported to ' . $fileName;
            session()->flash('success', $successMessage);

            return response()->streamDownload(function () use ($fileData) {
                echo $fileData;
            }, $fileName, [
                'Content-Type' => 'text/plain; charset=windows-874',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '\"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'memory') !== false || strpos($e->getMessage(), 'exhausted') !== false) {
                session()->flash('error', 'Export failed due to insufficient memory. Please reduce your selection.');
            } elseif (strpos($e->getMessage(), 'timeout') !== false) {
                session()->flash('error', 'Export timed out. Please reduce your selection.');
            } else {
                session()->flash('error', 'Export failed: ' . $e->getMessage());
            }
        }
    }
    // 🔥 NEW: Force component re-render
    private function forceRender()
    {
        // ✅ Force Livewire to re-evaluate all computed properties
        $this->dispatch('$refresh');

        // ✅ Trigger specific events for frontend
        $this->dispatch('countersUpdated', [
            'uniqueChecks' => $this->oracleData->unique('your_reference')->count(),
            'totalRecords' => $this->oracleData->count(),
            'selectedChecks' => count($this->selectedChecks),
            'timestamp' => now()->timestamp
        ]);
    }
    // 🔥 ENHANCED: Better updatedSelectAllChecks
    public function updatedSelectAllChecks($value)
    {
        if ($this->oracleData->isEmpty()) {
            $this->selectAllChecks = false;
            session()->flash('warning', 'No data available to select.');
            return;
        }
        if ($value) {
            $uniqueChecks = $this->oracleData
                ->unique('your_reference')
                ->pluck('your_reference')
                ->filter()
                ->values()
                ->toArray();
            $this->selectedChecks = $uniqueChecks;
            session()->flash('success', '✅ Selected all ' . count($uniqueChecks) . ' unique checks.');
        } else {
            $this->selectedChecks = [];
            session()->flash('info', 'All checks deselected.');
        }
        // ✅ Force refresh counters
        $this->forceRender();
    }
    // 🎯 คำนวณ WHT จาก AWT Group ใน Oracle
    private function calculateWHTFromAWTGroup($record, $baseAmount)
    {
        $awtName = trim($record->awt_name ?? '');
        if (empty($awtName)) {
            return 0;
        }
        // 🎯 Method 1: Extract rate from AWT Group name
        if (preg_match('/(\d+(?:\.\d+)?)%?/', $awtName, $matches)) {
            $rate = (float)$matches[1];
            // ✅ Validate reasonable rates
            if ($rate >= 0.5 && $rate <= 10.0) {
                $whtAmount = $baseAmount * ($rate / 100.0);
                return $whtAmount;
            }
        }
        // 🎯 Method 2: Query Oracle AWT tables
        $awtGroupId = $record->awt_group_id ?? 0;
        if ($awtGroupId > 0) {
            $whtAmount = $this->getWHTRateFromOracleAWTGroup($awtGroupId, $baseAmount, $record->org_id);
            if ($whtAmount > 0) {
                return $whtAmount;
            }
        }
        // 🎯 Method 3: Standard rates based on vendor type
        $vendorType = $record->vendor_type ?? '';
        $standardRate = $this->getStandardWHTRateByVendorType($vendorType);
        if ($standardRate > 0) {
            $whtAmount = $baseAmount * ($standardRate / 100.0);
            return $whtAmount;
        }
        return 0;
    }
    //🔥 NEW: Standard WHT rates by vendor type
    private function getStandardWHTRateByVendorType($vendorType)
    {
        $standardRates = [
            'VENDOR' => 3.0,     // Service providers: 3%
            'CONTRACTOR' => 5.0,  // Contractors: 5%
            'EMPLOYEE' => 1.0,    // Employees: 1%
            'INDIVIDUAL' => 5.0,  // Individuals: 5%
        ];
        return $standardRates[strtoupper($vendorType)] ?? 0;
    }
    // 🎯 Query WHT rate จาก Oracle AWT Group tables
    private function getWHTRateFromOracleAWTGroup($awtGroupId, $baseAmount, $orgId)
    {
        try {
            $oracleConnection = DB::connection('oracle');
            $awtRate = $oracleConnection->select("
            SELECT 
                NVL(aagt.tax_rate, 0) as tax_rate,
                aagt.tax_name,
                aagt.tax_code
            FROM ap.ap_awt_group_taxes_all aagt
            WHERE aagt.group_id = :awt_group_id
            AND aagt.org_id = :org_id
            AND NVL(aagt.inactive_date, SYSDATE + 1) > SYSDATE
            ORDER BY aagt.tax_rate DESC
            FETCH FIRST 1 ROWS ONLY
        ", [
                'awt_group_id' => $awtGroupId,
                'org_id' => $orgId
            ]);
            if (!empty($awtRate) && isset($awtRate[0]->tax_rate)) {
                $rate = (float)$awtRate[0]->tax_rate;
                $taxName = $awtRate[0]->tax_name ?? '';
                $taxCode = $awtRate[0]->tax_code ?? '';
                if ($rate > 0 && $rate <= 10.0) {
                    $whtAmount = $baseAmount * ($rate / 100.0);
                    return $whtAmount;
                }
            }
        } catch (Exception $e) {
            Log::error("Failed to query AWT rate from Oracle: " . $e->getMessage());
        }
        return 0;
    }
    // 🔥 ENHANCED: Better invoice lines generation
    private function generateInvoiceLinesFromCalculatedData($invoicesData)
    {
        $output = '';
        foreach ($invoicesData as $invoice) {
            if (empty($invoice['invoice_num'])) continue;
            // 🎯 KEY FIX: Show actual amounts in invoice lines
            $invoiceData = sprintf(
                "%s %s %.2f %.2f %.2f",
                $invoice['invoice_num'],
                $invoice['invoice_date'],
                $invoice['invoice_amount'],
                $invoice['vat_amount'],
                $invoice['wht_amount'] // 🔥 Show actual WHT, not wht_amount_for_txn
            );
            // 🎯 Truncate if too long
            if (strlen($invoiceData) > 72) {
                $fixedPart = sprintf(
                    " %s %.2f %.2f %.2f",
                    $invoice['invoice_date'],
                    $invoice['invoice_amount'],
                    $invoice['vat_amount'],
                    $invoice['wht_amount']
                );
                $maxInvoiceNumLength = 72 - strlen($fixedPart);

                if ($maxInvoiceNumLength > 0) {
                    $truncatedInvoiceNum = substr($invoice['invoice_num'], 0, $maxInvoiceNumLength);
                    $invoiceData = $truncatedInvoiceNum . $fixedPart;
                } else {
                    $invoiceData = substr($invoiceData, 0, 72);
                }
            }
            $invoiceDataPadded = str_pad($invoiceData, 72, ' ', STR_PAD_RIGHT);
            $invLine = "INV" . $invoiceDataPadded;
            $output .= $invLine . "\n";
        }
        return $output;
    }
    // 🎯 Format WHT Amount สำหรับ CU27
    private function formatWHTAmount($amount)
    {
        if ($amount <= 0) return '00000000000000000.00';
        return sprintf('%020.2f', $amount);
    }
    // 5. 🔥 ENHANCED formatWHTRate() method - don't change the rate, just format it
    private function formatWHTRate($rate)
    {
        if ($rate <= 0) return '00.00';
        // 🔥 KEY FIX: Format the rate exactly as provided, don't modify it
        return sprintf('%05.2f', min($rate, 99.99));
    }
    // 🎯 Enhanced WHT Description - Query from tr_uob_wht table
    private function getWHTDescriptionFromDatabase($checkId, $rate)
    {
        try {
            $oracleConnection = DB::connection('oracle');

            // Query tr_uob_wht table for actual tax descriptions
            $whtDescriptions = $oracleConnection->select("
            SELECT DISTINCT 
                NVL(TRIM(tax_desc1), '') as tax_desc1,
                NVL(TRIM(tax_desc2), '') as tax_desc2, 
                NVL(TRIM(tax_desc3), '') as tax_desc3,
                NVL(TRIM(tax_desc4), '') as tax_desc4,
                NVL(TRIM(tax_desc5), '') as tax_desc5,
                NVL(tax_rate1, 0) as tax_rate1,
                NVL(tax_rate2, 0) as tax_rate2,
                NVL(tax_rate3, 0) as tax_rate3,
                NVL(tax_rate4, 0) as tax_rate4,
                NVL(tax_rate5, 0) as tax_rate5
            FROM tr_uob_wht 
            WHERE check_id = :check_id
            ORDER BY ROWNUM
            FETCH FIRST 1 ROWS ONLY
        ", ['check_id' => $checkId]);

            if (!empty($whtDescriptions)) {
                $whtData = $whtDescriptions[0];

                // 🎯 Match rate with description
                $tolerance = 0.25; // Allow ±0.25% tolerance

                if (abs($whtData->tax_rate1 - $rate) <= $tolerance && !empty($whtData->tax_desc1)) {
                    return $whtData->tax_desc1;
                }
                if (abs($whtData->tax_rate2 - $rate) <= $tolerance && !empty($whtData->tax_desc2)) {
                    return $whtData->tax_desc2;
                }
                if (abs($whtData->tax_rate3 - $rate) <= $tolerance && !empty($whtData->tax_desc3)) {
                    return $whtData->tax_desc3;
                }
                if (abs($whtData->tax_rate4 - $rate) <= $tolerance && !empty($whtData->tax_desc4)) {
                    return $whtData->tax_desc4;
                }
                if (abs($whtData->tax_rate5 - $rate) <= $tolerance && !empty($whtData->tax_desc5)) {
                    return $whtData->tax_desc5;
                }
            }
        } catch (Exception $e) {
            Log::warning('Failed to get WHT description from database: ' . $e->getMessage());
        }
        // 🔄 Fallback to default descriptions
        return $this->getDefaultWHTDescription($rate);
    }
    // 🎯 Renamed original function to default descriptions
    private function getDefaultWHTDescription($rate)
    {
        if ($rate <= 0) return '';
        // Default Thai descriptions according to tax regulations
        if ($rate >= 4.75 && $rate <= 5.25) return 'ค่าเช่า'; // 5% - Rental services
        if ($rate >= 2.75 && $rate <= 3.25) return 'ค่าบริการ'; // 3% - Professional services  
        if ($rate >= 1.75 && $rate <= 2.25) return 'ค่าขนส่ง'; // 2% - Transportation
        if ($rate >= 0.75 && $rate <= 1.25) return 'ดอกเบี้ย'; // 1% - Interest
        return 'หักภาษี ณ ที่จ่าย'; // Default description
    }
    // 🎯 Add helper method to validate tr_uob_wht table existence
    private function isWHTTableAvailable($checkDate = null)
    {
        try {
            $oracleConnection = DB::connection('oracle');

            // Check if table exists and has data for the date range
            $checkDate = $checkDate ?? $this->selectedDate;
            $dateFrom = Carbon::parse($checkDate)->subDays(7)->format('Y-m-d');
            $dateTo = Carbon::parse($checkDate)->addDays(1)->format('Y-m-d');

            $result = $oracleConnection->select("
            SELECT COUNT(*) as record_count
            FROM tr_uob_wht 
            WHERE created_date BETWEEN TO_DATE(:date_from, 'YYYY-MM-DD') 
                                   AND TO_DATE(:date_to, 'YYYY-MM-DD')
        ", [
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ]);
            $recordCount = $result[0]->record_count ?? 0;
            Log::info('WHT Table availability check', [
                'date_range' => $dateFrom . ' to ' . $dateTo,
                'record_count' => $recordCount,
                'available' => $recordCount > 0
            ]);
            return $recordCount > 0;
        } catch (Exception $e) {
            Log::warning('WHT Table not available: ' . $e->getMessage());
            return false;
        }
    }
    // 🔥 NEW: Get all WHT descriptions from tr_uob_wht table
    private function getWHTDescriptionsFromTable($checkId)
    {
        try {
            $oracleConnection = DB::connection('oracle');

            $whtData = $oracleConnection->select("
            SELECT 
                NVL(TRIM(tax_desc1), '') as tax_desc1,
                NVL(TRIM(tax_desc2), '') as tax_desc2, 
                NVL(TRIM(tax_desc3), '') as tax_desc3,
                NVL(tax_rate1, 0) as tax_rate1,
                NVL(tax_rate2, 0) as tax_rate2,
                NVL(tax_rate3, 0) as tax_rate3,
                NVL(tax_type1, '') as tax_type1,
                NVL(tax_type2, '') as tax_type2,
                NVL(tax_type3, '') as tax_type3
            FROM tr_uob_wht 
            WHERE check_id = :check_id
            FETCH FIRST 1 ROWS ONLY
        ", ['check_id' => $checkId]);

            if (!empty($whtData)) {
                return [
                    'rate1' => [
                        'rate' => (float)$whtData[0]->tax_rate1,
                        'description' => trim($whtData[0]->tax_desc1),
                        'tax_type' => trim($whtData[0]->tax_type1)
                    ],
                    'rate2' => [
                        'rate' => (float)$whtData[0]->tax_rate2,
                        'description' => trim($whtData[0]->tax_desc2),
                        'tax_type' => trim($whtData[0]->tax_type2)
                    ],
                    'rate3' => [
                        'rate' => (float)$whtData[0]->tax_rate3,
                        'description' => trim($whtData[0]->tax_desc3),
                        'tax_type' => trim($whtData[0]->tax_type3)
                    ]
                ];
            }
        } catch (Exception $e) {
            Log::warning('Failed to get WHT descriptions from tr_uob_wht: ' . $e->getMessage());
        }

        return [];
    }
    // 🔥 NEW: Find matching description based on rate
    private function findMatchingDescriptionFromTable($whtDescriptionsFromDB, $targetRate)
    {
        $tolerance = 0.25; // ±0.25% tolerance
        // Try to match with rate1, rate2, rate3
        foreach ($whtDescriptionsFromDB as $rateKey => $rateInfo) {
            if ($rateInfo['rate'] > 0 && !empty($rateInfo['description'])) {
                // Check if rates match within tolerance
                if (abs($rateInfo['rate'] - $targetRate) <= $tolerance) {
                    Log::info("🎯 Found matching rate in tr_uob_wht", [
                        'target_rate' => $targetRate,
                        'table_rate' => $rateInfo['rate'],
                        'description' => $rateInfo['description'],
                        'rate_key' => $rateKey
                    ]);
                    return $rateInfo['description'];
                }
            }
        }
        // If no exact match, try to find the closest rate with description
        $closestMatch = null;
        $smallestDifference = PHP_FLOAT_MAX;
        foreach ($whtDescriptionsFromDB as $rateKey => $rateInfo) {
            if ($rateInfo['rate'] > 0 && !empty($rateInfo['description'])) {
                $difference = abs($rateInfo['rate'] - $targetRate);
                if ($difference < $smallestDifference && $difference <= 1.0) { // Within 1% difference
                    $smallestDifference = $difference;
                    $closestMatch = $rateInfo['description'];
                }
            }
        }
        if ($closestMatch) {
            Log::info("🔍 Using closest match from tr_uob_wht", [
                'target_rate' => $targetRate,
                'closest_description' => $closestMatch,
                'difference' => $smallestDifference
            ]);
        }
        return $closestMatch ?? '';
    }
    // 🔥 ENHANCED: Better progress update method with forced refresh
    // Override updateProgress from trait ถ้าจำเป็น
    public function updateProgress($percentage, $message = null)
    {
        $this->loadingProgress = max(0, min(100, $percentage));
        $this->loadingMessage = $message ?? 'Processing...';
    }
    // Extract query building to separate method for cleaner code
    private function buildOracleQuery()
    {
        // Your existing query here
        $query = "
        SELECT * FROM (
            -- Your existing query
        ) WHERE ROWNUM <= " . $this->maxRecordLimit;
        return $query;
    }
    private function buildQueryParams($selectedDate)
    {
        $params = [
            'org_id' => $this->orgId,
            'selected_date' => $selectedDate
        ];
        if (!empty($this->userId)) {
            $params['user_id'] = $this->userId;
        }
        if (!empty($this->checkStart)) {
            $params['check_start'] = $this->checkStart;
        }
        if (!empty($this->checkEnd)) {
            $params['check_end'] = $this->checkEnd;
        }
        return $params;
    }
    public function processDataStep()
    {
        if (!$this->shouldContinue || !$this->isProcessing) {
            return;
        }
        try {
            Log::info("Processing step {$this->loadingStep}, Progress: {$this->loadingProgress}%");

            switch ($this->loadingStep) {
                case 1:
                    $this->step1Validate();
                    break;
                case 2:
                    $this->step2EstimateCount();
                    break;
                case 3:
                    $this->step3PrepareConnection();
                    break;
                case 4:
                    $this->step4BuildQuery();
                    break;
                case 5:
                    $this->step5ExecuteQuery();
                    break;
                case 6:
                    $this->step6ProcessData();
                    break;
                case 7:
                    $this->step7Finalize();
                    break;
                default:
                    $this->completeLoading();
                    break;
            }
            // Auto advance to next step
            if ($this->loadingStep <= 7 && $this->shouldContinue) {
                $this->dispatch('continueProcessing');
            }
        } catch (Exception $e) {
            Log::error("Error in step {$this->loadingStep}: " . $e->getMessage());
            // Retry logic
            if ($this->currentRetries < $this->maxRetries) {
                $this->currentRetries++;
                Log::info("Retrying step {$this->loadingStep}, attempt {$this->currentRetries}");
                $this->dispatch('retryProcessing');
            } else {
                $this->handleProcessingError($e);
            }
        }
    }
    // เพิ่ม error handler
    private function handleProcessingError($exception)
    {
        $this->shouldContinue = false;
        $this->isProcessing = false;
        $this->pollingActive = false;
        $this->resetLoadingState();
        $this->dataReady = false;
        session()->flash('error', 'Processing error: ' . $exception->getMessage());
        Log::error("Processing failed at step {$this->loadingStep}: " . $exception->getMessage());
    }
    private function step1Validate()
    {
        $this->updateProgress(10, 'Validating date and parameters...');
        $validation = $this->validateDate();
        if (!$validation['valid']) {
            $this->shouldContinue = false;
            $this->resetLoadingState();
            session()->flash('error', $validation['message']);
            return;
        }
        $this->loadingStep = 2;
    }
    private function step2EstimateCount()
    {
        $this->updateProgress(20, 'Estimating record count...');
        try {
            $estimatedCount = $this->estimateRecordCount();
            if ($estimatedCount > $this->maxRecordLimit) {
                $this->shouldContinue = false;
                $this->resetLoadingState();
                session()->flash('error', "Too many records found (" . number_format($estimatedCount) . ")");
                return;
            }
            if ($estimatedCount == 0) {
                $this->shouldContinue = false;
                $this->resetLoadingState();
                session()->flash('info', 'No records found.');
                return;
            }
            $this->loadingData['estimatedCount'] = $estimatedCount;
            $this->updateProgress(30, "Found " . number_format($estimatedCount) . " records...");

            $this->loadingStep = 3;
        } catch (Exception $e) {
            $this->handleProcessingError($e);
        }
    }
    private function step3PrepareConnection()
    {
        $this->updateProgressAndContinue(40, 'Preparing system resources...');
        $estimatedCount = $this->loadingData['estimatedCount'] ?? 0;
        $this->setMemoryLimits($estimatedCount);
        $this->loadingStep = 4;
    }
    private function step4BuildQuery()
    {
        $this->updateProgressAndContinue(50, 'Building database query...');
        $selectedDate = Carbon::parse($this->selectedDate)->format('d-M-y');
        // Store query params
        $this->tempQueryParams = [
            'org_id' => $this->orgId,
            'selected_date' => $selectedDate
        ];
        if (!empty($this->userId)) {
            $this->tempQueryParams['user_id'] = $this->userId;
        }
        if (!empty($this->checkStart)) {
            $this->tempQueryParams['check_start'] = $this->checkStart;
        }
        if (!empty($this->checkEnd)) {
            $this->tempQueryParams['check_end'] = $this->checkEnd;
        }
        $this->loadingStep = 5;
    }
    // ตัวอย่างการปรับปรุง step5ExecuteQuery()
    private function step5ExecuteQuery()
    {
        $this->updateProgress(60, 'Connecting to Oracle database...');
        try {
            $oracleConnection = DB::connection('oracle');
            $this->updateProgress(65, 'Executing query...');
            $query = $this->buildFullOracleQuery();
            $rawData = collect($oracleConnection->select($query, $this->tempQueryParams));
            $this->updateProgress(70, 'Query completed, processing results...');
            if ($rawData->isEmpty()) {
                $this->resetLoadingState();
                session()->flash('info', 'No data found for the selected date.');
                return;
            }
            $this->loadingData['rawData'] = $rawData;
            $this->loadingStep = 6;
        } catch (Exception $e) {
            throw $e; // Let processDataStep handle retry
        }
    }
    private function step6ProcessData()
    {
        $rawData = $this->loadingData['rawData'] ?? collect();
        $totalRecords = $rawData->count();
        $processedCount = 0;

        $this->updateProgressAndContinue(75, "Processing {$totalRecords} records...");

        // ถ้าใช้ strict mode ข้อมูลจะ filtered แล้วจาก query
        if ($this->strictWHTMode) {
            Log::info("Using pre-filtered data from strict query");
            $filteredData = $rawData;
        } else {
            // SMART FILTER: กรองแบบฉลาดขึ้นสำหรับ regular mode
            $filteredData = $rawData->filter(function ($record) {
                // ต้องเป็น ITEM หรือ ACCRUAL
                $isItemOrAccrual = in_array($record->line_type_lookup_code ?? '', ['ITEM', 'ACCRUAL']);

                // ต้องมี WHT ใน invoice นี้
                $hasWHTInInvoice = !empty($record->wht_amount_per_invoice) && $record->wht_amount_per_invoice > 0;

                // เพิ่มเงื่อนไข: ถ้ามี AWT name หรือ AWT group ID ก็โอเค
                $hasAWTInfo = (!empty($record->awt_name) && $record->awt_name !== '')
                    || (!empty($record->awt_group_id) && $record->awt_group_id > 0);

                // สำหรับ debug
                if ($record->your_reference === '5013666') {
                    Log::info("WHT Filter Debug for 5013666", [
                        'invoice_num' => $record->invoice_num ?? '',
                        'line_type' => $record->line_type_lookup_code ?? '',
                        'invoice_amount' => $record->invoice_amount ?? 0,
                        'wht_amount_per_invoice' => $record->wht_amount_per_invoice ?? 0,
                        'awt_name' => $record->awt_name ?? '',
                        'awt_group_id' => $record->awt_group_id ?? 0,
                        'is_item_accrual' => $isItemOrAccrual,
                        'has_wht_in_invoice' => $hasWHTInInvoice,
                        'has_awt_info' => $hasAWTInfo,
                        'will_show' => $isItemOrAccrual && $hasWHTInInvoice && $hasAWTInfo
                    ]);
                }

                return $isItemOrAccrual && $hasWHTInInvoice && $hasAWTInfo;
            });
        }

        Log::info("WHT Filter Results", [
            'mode' => $this->strictWHTMode ? 'STRICT' : 'REGULAR',
            'original_count' => $totalRecords,
            'filtered_count' => $filteredData->count(),
            'removed_count' => $totalRecords - $filteredData->count()
        ]);

        // ถ้าไม่มีข้อมูลหลัง filter ให้แสดงข้อมูลดิบแทน (เฉพาะ regular mode)
        if ($filteredData->isEmpty() && !$this->strictWHTMode) {
            Log::warning("No WHT records found after filtering, showing original data");
            $filteredData = $rawData;
        }

        // Process filtered data
        $processedData = $filteredData->map(function ($record) use (&$processedCount, $filteredData) {
            $processedCount++;
            $totalFiltered = $filteredData->count();

            // Update progress every 50 records
            if ($processedCount % 50 == 0 || $processedCount == $totalFiltered) {
                $progress = 75 + floor(($processedCount / $totalFiltered) * 10); // 75-85%
                $this->updateProgressAndContinue($progress, "Processing record {$processedCount} of {$totalFiltered}...");
            }

            // Your existing processing logic
            $record->net_amount = $record->invoice_amount - $record->wht_amount_per_invoice;
            $record->wht_amount_raw = $record->wht_amount_per_invoice;
            $record->wht_base_amount_raw = $record->invoice_amount;
            $record->wht_rate_raw = $record->invoice_amount > 0 ?
                round(($record->wht_amount_per_invoice / $record->invoice_amount) * 100, 2) : 0;

            $record->col22_26 = $record->invoice_amount > 0 ?
                sprintf('%020.2f', $record->invoice_amount) : '00000000000000000.00';
            $record->col27_31 = $record->wht_amount_per_invoice > 0 ?
                sprintf('%020.2f', $record->wht_amount_per_invoice) : '00000000000000000.00';

            return $record;
        })->sortBy([
            ['your_reference', 'asc'],
            ['invoice_num', 'asc'],
            ['distribution_line_number', 'asc']
        ])->values();

        $this->oracleData = $processedData;
        $this->updateProgressAndContinue(90, 'Calculating summary...');
        $this->loadingStep = 7;
    }
    private function step7Finalize()
    {
        $uniqueChecks = $this->oracleData->unique('your_reference');
        $uniqueInvoices = $this->oracleData->unique('invoice_num');
        $whtRecords = $this->oracleData->filter(function ($record) {
            return !empty($record->wht_amount_raw) && $record->wht_amount_raw > 0;
        });
        $message = 'Found ' . number_format($this->oracleData->count()) . ' complete records from ' .
            ($this->orgOptions[$this->orgId] ?? 'Org ' . $this->orgId) . ' on ' .
            Carbon::parse($this->selectedDate)->format('d/m/Y');
        if ($whtRecords->count() > 0) {
            $totalWHT = $whtRecords->sum('wht_amount_raw');
            $message .= ' (Including ' . $whtRecords->count() . ' records with WHT, Total: ฿' .
                number_format($totalWHT, 2) . ')';
        }
        session()->flash('success', $message);
        $this->updateProgressAndContinue(100, 'Data loaded successfully!');
        $this->loadingStep = 8;
    }
    private function completeLoading()
    {
        $this->loadingProgress = 100;
        $this->loadingMessage = 'Complete! Preparing to display data...';
        // Delay before showing data
        $this->dispatch('loadingCompleted');
        // Set data ready after short delay
        $this->dispatch('prepareDataDisplay');
    }
    // เพิ่ม method สำหรับ finalize data display
    public function finalizeDataDisplay()
    {
        $this->dataReady = true;
        $this->pollingActive = false;
        $this->isLoading = false;
        $this->isProcessing = false;
        $this->shouldContinue = false;
        $this->loadingProgress = 0;
        $this->loadingMessage = '';
        $this->loadingStep = 0;
        Log::info("Data display finalized, records: " . $this->oracleData->count());
    }
    // เพิ่ม method สำหรับ force continue
    public function forceContinue()
    {
        if ($this->isProcessing && $this->loadingStep <= 7) {
            Log::info("Force continuing from step {$this->loadingStep}");
            $this->processDataStep();
        }
    }
    // Helper method to update progress and trigger next step
    private function updateProgressAndContinue($percentage, $message)
    {
        $this->loadingProgress = $percentage;
        $this->loadingMessage = $message;
        $this->dispatch('loadingProgressUpdated', [
            'progress' => $this->loadingProgress,
            'message' => $this->loadingMessage,
            'step' => $this->loadingStep
        ]);
        // Force refresh
        $this->dispatch('$refresh');
    }
    // Helper to build the full Oracle query
    private function buildFullOracleQuery()
    {
        if ($this->strictWHTMode) {
            Log::info("Using STRICT WHT-only query");
            return $this->buildFullOracleQueryItemsWithWHTOnly();
        } else {
            Log::info("Using REGULAR query with PHP filtering");
            return $this->buildFullOracleQueryRegular();
        }
    }
    // 4. เพิ่ม method สำหรับเปิด strict mode
    public function useStrictWHTFilter()
    {
        $this->strictWHTMode = true;
        Log::info("Switched to strict WHT-only mode");
    }

    public function useRegularFilter()
    {
        $this->strictWHTMode = false;
        Log::info("Switched to regular filter mode");
    }
    // 🔥 ALTERNATIVE OPTION: ถ้าอยากแสดงเฉพาะ ITEM/ACCRUAL ที่มี WHT เท่านั้น
    private function buildFullOracleQueryItemsWithWHTOnly()
    {
        $query = "
    SELECT * FROM (
        SELECT  
            -- CU27 Required Fields
            'TXN' as rec_iden,
            'CH' as rec_iden1,
            
            -- Beneficiary Name
            NVL(apc.attribute1, 
                DECODE(v.attribute1, NULL, '*ERROR => ' || apc.vendor_name,
                    DECODE(INSTR(apc.vendor_name, v.attribute1, -1), 0, '*ERROR => ' || apc.vendor_name,
                        DECODE(v.attribute1, '.', 
                            RTRIM(SUBSTR(apc.vendor_name, 1, INSTR(apc.vendor_name, v.attribute1, -1) - 1)),
                            RTRIM(LTRIM(v.attribute1, '.')) || ' ' || 
                            RTRIM(SUBSTR(apc.vendor_name, 1, INSTR(apc.vendor_name, v.attribute1, -1) - 1))
                        )
                    )
                )
            ) as benefi_name,
            
            -- Raw Data for Debug
            apc.attribute1 as apc_attribute1,
            apc.attribute3 as apc_attribute3,
            apc.attribute4 as apc_attribute4,
            v.attribute1 as v_attribute1,
            apc.vendor_name as apc_vendor_name,
            v.vendor_name as v_vendor_name,
            
            apc.check_number as your_reference,
            apc.amount as check_amt,
            to_char(apc.check_date,'DDMMYYYY') as check_date,
            
            NVL(TRIM(vs.attribute3), '') as payment_details1,
            '' as col12_13,
            NVL(TRIM(vs.attribute3), '') as payment_details2,
            
            -- Delivery Method
            DECODE(apc.bank_account_name,
                'TRU-Dummy UOB - SATHORN1', 'RT', 
                'TRT-Dummy UOB - SATHORN1', 'RT',
                'TUC-Dummy UOB - SATHORN1', 'RT',
                'TAP-Dummy UOB - SATHORN1', 'RT',
                'Dummy UOB - EASE', 'RT',
                'OC') as delivery_method,
                
            -- Payment Location
            DECODE(apc.bank_account_name,
                'TRU-Dummy UOB - SATHORN1', 'SATHORN1', 
                'TRT-Dummy UOB - SATHORN1', 'SATHORN1',
                'TUC-Dummy UOB - SATHORN1', 'SATHORN1',
                'TAP-Dummy UOB - SATHORN1', 'SATHORN1',
                'Dummy UOB - EASE', 'EASE',
                NVL(TRIM(vs.attribute4), '')) as payment_location,
            
            '' as col16_18,
            'OUR' as charges1,
            'OUR' as charges,
            '53' as CF_wht_type,
            '' as cf_personal_id,
            '' as col37_39,
            '1' as payment_condition1,
            
            -- Address Info
            NVL(TRIM(vs.address_line1), '') as address_line1,
            NVL(TRIM(vs.address_line2), '') as address_line2,
            NVL(TRIM(vs.address_line3), '') as address_line3,
            NVL(TRIM(vs.city), '') as city,
            NVL(TRIM(vs.state), '') as state,
            NVL(TRIM(vs.zip), '') as zip_code,
            NVL(TRIM(vs.country), '') as country,
            
            -- Email Fields
            NVL(NVL(TRIM(apc.attribute3), NVL(TRIM(apc.attribute4), TRIM(vs.email_address))), '') as email_address,
            NVL(TRIM(vs.email_address), '') as vs_email_address_only,
            
            NVL(TRIM(vs.phone), '') as phone_number,
            NVL(TRIM(vs.fax), '') as fax_number,
            
            -- Vendor Tax ID
            decode( substr(apc.attribute5,1,13), null, substr(p.num_1099,1,13), substr(apc.attribute5,1,13) ) as vendor_tax_id,
            
            -- WHT Calculation
            NVL(aid.pay_awt_group_id, 0) as awt_group_id,
            NVL(aag.tax_name, '') as awt_name,
            
            -- WHT amount from distributions - เฉพาะ distribution line นี้
            NVL(ABS(DECODE(aid.line_type_lookup_code, 'AWT', aid.amount, 'NONREC_TAX', aid.amount, 0)), 0) as wht_amount_per_line,
            
            -- WHT amount per invoice (รวมทั้ง invoice)
            (SELECT NVL(SUM(ABS(aid_awt.amount)), 0)
             FROM ap.ap_invoice_distributions_all aid_awt
             WHERE aid_awt.invoice_id = aid.invoice_id
               AND aid_awt.line_type_lookup_code IN ('AWT', 'NONREC_TAX')
               AND aid_awt.amount < 0
               AND aid_awt.org_id = aid.org_id) as wht_amount_per_invoice,
            
            -- VAT amount
            (SELECT NVL(SUM(aid_vat.amount), 0)
             FROM ap.ap_invoice_distributions_all aid_vat
             WHERE aid_vat.invoice_id = aid.invoice_id
               AND aid_vat.line_type_lookup_code IN ('TAX', 'REC_TAX')
               AND aid_vat.amount > 0
               AND aid_vat.org_id = aid.org_id) as vat_amount_per_invoice,
            
            -- Base amount for this distribution line
            DECODE(aid.line_type_lookup_code, 'ITEM', aid.amount, 'ACCRUAL', aid.amount, 0) as base_amount_per_line,
            
            -- Invoice info
            aid.invoice_id,
            aid.distribution_line_number,
            aid.line_type_lookup_code,
            
            -- Legacy WHT fields
            '00000000000000000.00' as col22_26,
            '00000000000000000.00' as col27_31,
            '00000000000000000.00' as col32_36,
            '00000000000000000.00' as col22,
            '00000000000000000.00' as col26,
            '00000000000000000.00' as col27,
            '00000000000000000.00' as col31,
            '00000000000000000.00' as col32,
            '00000000000000000.00' as col36,
            
            -- Invoice Information
            NVL(TRIM(api.invoice_num), '') as invoice_num,
            to_char(api.invoice_date,'DD/MM/YYYY') as invoice_date,
            DECODE(aid.line_type_lookup_code, 'ITEM', aid.amount, 'ACCRUAL', aid.amount, 0) as invoice_amount,
            
            -- Supplier Information
            NVL(v.vendor_type_lookup_code, '') as vendor_type,
            NVL(v.organization_type_lookup_code, '') as organization_type,
            CASE 
                WHEN v.vendor_type_lookup_code = 'EMPLOYEE' THEN 'Employee'
                WHEN v.vendor_type_lookup_code = 'VENDOR' THEN 'Vendor' 
                WHEN v.vendor_type_lookup_code = 'CONTRACTOR' THEN 'Contractor'
                WHEN v.vendor_type_lookup_code = 'CUSTOMER' THEN 'Customer'
                ELSE NVL(v.vendor_type_lookup_code, 'Other')
            END as vendor_type_desc,
            
            NVL(TRIM(v.attribute1), '') as supplier_attribute1,
            NVL(TRIM(v.attribute2), '') as supplier_attribute2,
            NVL(TRIM(vs.attribute1), '') as site_attribute1,
            NVL(TRIM(vs.attribute2), '') as site_attribute2,
            NVL(TRIM(vs.vendor_site_code), '') as vendor_site_code,
            NVL(vs.pay_site_flag, 'N') as pay_site_flag,
            
            apc.created_by,
            apc.org_id,
            apc.check_id,
            v.vendor_id,
            vs.vendor_site_id,
            
            -- User Info
            NVL(fu.user_name, '') as created_by_username,
            NVL(fu.description, '') as created_by_description
            
        FROM    
            ap.ap_checks_all apc,
            ap.ap_invoices_all api,
            ap.ap_invoice_payments_all aip,
            ap.ap_invoice_distributions_all aid,
            ap.ap_suppliers v,
            ap.ap_supplier_sites_all vs,
            ap.ap_awt_group_taxes_all aag,
            ap.ap_suppliers p,
            applsys.fnd_user fu
        WHERE    
            apc.check_id   = aip.check_id
            AND aip.invoice_id = api.invoice_id
            AND api.invoice_id = aid.invoice_id
            
            -- JOIN conditions
            AND apc.vendor_name = v.vendor_name
            AND api.vendor_id   = v.vendor_id
            AND api.vendor_site_id = vs.vendor_site_id
            AND v.vendor_id     = vs.vendor_id
            AND aid.pay_awt_group_id = aag.group_id(+)
            AND apc.created_by  = fu.user_id(+)
            AND api.vendor_id   = p.vendor_id(+)
            
            -- REVISED FILTER (3 เงื่อนไข):
            -- 1) แถวพื้นฐานต้องเป็น ITEM/ACCRUAL
            -- 2) ถ้า INV นี้มี WHT → ต้องผ่านและบังคับ group/tax ให้ครบ
            -- 3) ถ้า 'ใบแรกของเช็ค' ไม่มี WHT → ให้เห็นทุก INV ของเช็คนั้น
            AND aid.line_type_lookup_code IN ('ITEM','ACCRUAL')
            AND (
                (
                    -- Branch A: invoice ปัจจุบันมี WHT → ผ่าน (และต้องมี group/tax)
                    EXISTS (
                        SELECT 1
                        FROM ap.ap_invoice_distributions_all aid_wht
                        WHERE aid_wht.invoice_id = aid.invoice_id
                          AND aid_wht.org_id     = aid.org_id
                          AND aid_wht.line_type_lookup_code IN ('AWT','NONREC_TAX')
                          AND aid_wht.amount < 0
                          AND ABS(aid_wht.amount) > 0
                    )
                    AND aid.pay_awt_group_id IS NOT NULL
                    AND aid.pay_awt_group_id > 0
                    AND aag.tax_name IS NOT NULL
                )
                OR
                (
                    -- Branch B (NEW):
                    -- หาก 'ใบแรกของเช็คนี้' ไม่มี WHT → อนุญาตให้เห็นทุก INV ของเช็คนั้น
                    EXISTS (
                        SELECT 1
                        FROM ap.ap_invoice_payments_all aip_first
                        JOIN ap.ap_invoices_all api_first
                          ON api_first.invoice_id = aip_first.invoice_id
                         AND api_first.org_id     = api.org_id
                        WHERE aip_first.check_id = apc.check_id
                          -- หา 'ใบแรก' ตาม (invoice_date, invoice_num)
                          AND NOT EXISTS (
                              SELECT 1
                              FROM ap.ap_invoice_payments_all aip_prev
                              JOIN ap.ap_invoices_all api_prev
                                ON api_prev.invoice_id = aip_prev.invoice_id
                               AND api_prev.org_id     = api.org_id
                              WHERE aip_prev.check_id = apc.check_id
                                AND (
                                      api_prev.invoice_date <  api_first.invoice_date
                                   OR (api_prev.invoice_date = api_first.invoice_date
                                       AND NVL(api_prev.invoice_num,'') < NVL(api_first.invoice_num,''))
                                )
                          )
                          -- และใบแรกนั้น 'ไม่มี WHT'
                          AND NOT EXISTS (
                              SELECT 1
                              FROM ap.ap_invoice_distributions_all aid_wht_first
                              WHERE aid_wht_first.invoice_id = api_first.invoice_id
                                AND aid_wht_first.org_id     = aid.org_id
                                AND aid_wht_first.line_type_lookup_code IN ('AWT','NONREC_TAX')
                                AND aid_wht_first.amount < 0
                                AND ABS(aid_wht_first.amount) > 0
                          )
                    )
                )
            )
            
            -- Basic filters
            AND apc.org_id = :org_id
            AND api.org_id = :org_id
            AND aip.org_id = :org_id
            AND aid.org_id = :org_id
            AND vs.org_id  = :org_id
            AND aag.org_id(+) = :org_id
            AND apc.check_date = to_date(:selected_date,'DD-MON-RR')
            AND (apc.void_date < to_date(:selected_date,'DD-MON-RR')
                 OR apc.void_date > to_date(:selected_date,'DD-MON-RR')
                 OR apc.void_date IS NULL)
                
            -- FOREIGN vendors filter
            AND (v.vendor_type_lookup_code IS NULL 
                 OR v.vendor_type_lookup_code != 'FOREIGN')
                
            -- Active vendor filter
            AND NVL(v.enabled_flag, 'Y') = 'Y'
            AND NVL(vs.inactive_date, SYSDATE + 1) > SYSDATE
    ";

        // Add optional conditions based on parameters
        if (!empty($this->userId)) {
            $query .= " AND apc.created_by = :user_id";
        }

        if (!empty($this->checkStart)) {
            $query .= " AND apc.check_number >= :check_start";
        }

        if (!empty($this->checkEnd)) {
            $query .= " AND apc.check_number <= :check_end";
        }

        $query .= " ORDER BY apc.check_number, api.invoice_num, aid.distribution_line_number
    ) WHERE ROWNUM <= " . $this->maxRecordLimit;

        return $query;
    }


    // 🔥 ENHANCED: ปรับ calculateCorrectInvoiceAmountsSimple เพื่อจัดการกับ filtered data
    private function calculateCorrectInvoiceAmountsSimple($records)
    {
        $invoicesByNumber = $records->groupBy('invoice_num')->map(function ($invoiceRecords) {
            $firstRecord = $invoiceRecords->first();
            $invoiceNum = trim($firstRecord->invoice_num ?? '');
            $invoiceDate = trim($firstRecord->invoice_date ?? '');

            // คำนวณ base amount จาก ITEM/ACCRUAL ที่มี WHT
            $baseAmount = $invoiceRecords
                ->filter(function ($record) {
                    return in_array($record->line_type_lookup_code ?? '', ['ITEM', 'ACCRUAL']);
                })
                ->sum('base_amount_per_line');

            // ถ้าไม่มี base amount ข้าม invoice นี้
            if ($baseAmount <= 0) {
                return null;
            }

            // Get AWT name from record
            $awtName = trim($firstRecord->awt_name ?? '');

            // Get VAT from DB (if any)
            $vatAmountFromDB = $firstRecord->vat_amount_per_invoice ?? 0;

            // Calculate WHT using AWT mapping
            $whtRate = $this->getWHTRateFromAWTName($awtName);
            $whtAmount = 0;

            if ($whtRate > 0) {
                $whtAmount = round($baseAmount * ($whtRate / 100), 2);
                Log::info("WHT Calculation for Invoice", [
                    'invoice_num' => $invoiceNum,
                    'awt_name' => $awtName,
                    'base_amount' => $baseAmount,
                    'wht_rate' => $whtRate,
                    'calculated_wht' => $whtAmount,
                    'filter_mode' => $this->strictWHTMode ? 'STRICT' : 'REGULAR'
                ]);
            }

            // Calculate VAT (standard 7% if exists)
            $vatAmount = 0;
            if ($vatAmountFromDB > 0) {
                $vatAmount = round($baseAmount * 0.07, 2);
            }

            return [
                'invoice_num' => $invoiceNum,
                'invoice_date' => $invoiceDate,
                'invoice_amount' => $baseAmount,
                'vat_amount' => $vatAmount,
                'wht_amount' => $whtAmount,
                'wht_amount_for_txn' => $whtAmount,
                'awt_name' => $awtName,
                'wht_rate' => $whtRate,
                'your_reference' => $firstRecord->your_reference ?? '',
                'check_id' => $firstRecord->check_id ?? null
            ];
        })->filter(); // Remove null values

        // Calculate totals
        $totalWHT = $invoicesByNumber->sum('wht_amount_for_txn');
        $totalBase = $invoicesByNumber->sum('invoice_amount');
        $weightedRate = $totalBase > 0 ? ($totalWHT / $totalBase) * 100 : 0;

        return [
            'invoices' => $invoicesByNumber,
            'total_wht' => $totalWHT,
            'total_base' => $totalBase,
            'weighted_rate' => $weightedRate,
            'invoice_count' => $invoicesByNumber->count()
        ];
    }

    // 9. เพิ่ม method สำหรับเปิด/ปิด mode จาก frontend
    public function toggleFilterMode()
    {
        $this->strictWHTMode = !$this->strictWHTMode;

        $mode = $this->strictWHTMode ? 'STRICT' : 'REGULAR';
        session()->flash('info', "Switched to {$mode} filter mode. Please search again to see results.");

        Log::info("Filter mode toggled", [
            'new_mode' => $mode,
            'strict_enabled' => $this->strictWHTMode
        ]);
    }
    public function render()
    {
        return view('livewire.check-oracle');
    }
}
