<?php

namespace App\Livewire;

use App\Exports\CheckLists;
use App\Models\Aprcpit;
use App\Models\Aptrn;
use App\Models\Bktrn;
use League\Flysystem\Filesystem;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;
use XBase\TableReader;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use App\Services\DbfDataFetcher;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan as FacadesArtisan;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CheckUob extends Component
{
    public $linkedData;
    public $dateCheck;
    public $date;
    public $ebill_to = [];

    public function mount()
    {
        $this->linkedData = collect();
    }

    public function synD()
    {
        FacadesArtisan::call('dbf:sync');
    }

    public function getData()
    {
        $this->ebill_to = [];
        $selectedDate = Carbon::parse($this->dateCheck);
        $cmpdate = $selectedDate->format('Y') . $selectedDate->format('m') . $selectedDate->format('d');
        $this->linkedData = Bktrn::where('chqdat', $cmpdate)
            ->where('voucher', 'like', 'PS' . '%')
            ->with('aprcpit')->get();

        foreach ($this->linkedData as $data) {
            $Amount = 0;
            $total = 0;
            foreach ($data->aprcpit as $aprcpit) {
                $Amount += $aprcpit->aptrn->netamt ?? 0;
                $total += $aprcpit->aptrn->amount ?? 0;
            }
            $this->ebill_to['taxid'][$data->id] = $data->apmas->taxid ?? null;
            $this->ebill_to['name'][$data->id] = ($data->apmas->prenam ?? null) . ($data->apmas->supnam ?? null);
            if ($data->amount == $Amount) {
                $this->ebill_to['addr1'][$data->id] = null;
                $this->ebill_to['addr2'][$data->id] = null;
                $this->ebill_to['addr3'][$data->id] = null;
            } else {
                $this->ebill_to['addr1'][$data->id] = $data->apmas->addr01 ?? null;
                $this->ebill_to['addr2'][$data->id] = $data->apmas->addr02 ?? null;
                $this->ebill_to['addr3'][$data->id] = $data->apmas->addr03 ?? null;
            }
        }
    }

    public function txtFile()
    {
        $selectedDate = Carbon::parse($this->dateCheck);
        $date = $selectedDate->format('d') . $selectedDate->format('m') . $selectedDate->format('Y');
        $output = null;
        foreach ($this->linkedData as $data) {
            $Amount = 0;
            $total = 0;
            foreach ($data->aprcpit as $aprcpit) {
                $Amount += $aprcpit->aptrn->netamt ?? 0;
                $total += $aprcpit->aptrn->amount ?? 0;
            }

            $fullAddress = mb_substr($this->ebill_to['addr1'][$data->id]
                . $this->ebill_to['addr2'][$data->id]
                . $this->ebill_to['addr3'][$data->id], 0, 105);
            $totalAmount = 0;
            $totalVat = 0;
            $totalNet = 0;
            $nameCom = mb_substr($this->ebill_to['name'][$data->id], 0, 70);
            $nameCom35 = mb_substr($this->ebill_to['name'][$data->id], 0, 35);
            $address1 = mb_substr($this->ebill_to['addr1'][$data->id], 0, 35);
            $address2 = mb_substr($this->ebill_to['addr2'][$data->id], 0, 35);
            $address3 = mb_substr($this->ebill_to['addr3'][$data->id], 0, 35);
            $taxDes = mb_substr($data->apmas->taxdes ?? 0, 0, 35);

            $output .=
                str_pad('TXN', 3)
                . $nameCom
                . str_pad("", 70 - mb_strlen($nameCom))
                . $nameCom35
                . str_pad("", 35 - mb_strlen($nameCom35))
                . $address1
                . str_pad("", 35 - mb_strlen($address1))
                . $address2
                . str_pad("", 35 - mb_strlen($address2))
                . $address3
                . str_pad("", 35 - mb_strlen($address3))
                . str_pad($data->apmas->zipcod ?? null, 10)
                . str_pad(sprintf('%015.2f', $data->netamt ?? null), 15)
                . str_pad($data->chqnum ?? null, 15)
                . str_pad($date, 8)
                . str_pad("REC+TAX", 35)
                . str_pad("", 35)
                . str_pad("", 35)
                . str_pad("RT", 2)
                . str_pad("SATHORN1", 15)
                . str_pad("", 5)
                . str_pad('', 10)
                . str_pad('', 70)
                . str_pad('OUR', 12)
                . str_pad(sprintf('%013.0f', $this->ebill_to['taxid'][$data->id]), 13)
                . str_pad('53', 2)
                . str_pad(sprintf('%015.2f', $total), 15)
                . str_pad($data->apmas->suptyp ?? null, 2)
                . $taxDes
                . str_pad('', 35 - mb_strlen($taxDes));

            if ($data->amount == $Amount) {
                $output .= str_pad('', 20);
            } else {
                $output .= str_pad(sprintf('%05.2f', $data->apmas->taxrat ?? null), 5)
                    . str_pad(sprintf('%015.2f', round($Amount - $data->amount ?? 0, 2), 15), 15);
            }

            $output  .=
                str_pad(sprintf('%015.2f', ''), 15)
                . str_pad('', 35)
                . str_pad('', 2)
                . str_pad(sprintf('%05.2f', ''), 5)
                . str_pad(sprintf('%015.2f', ''), 15)
                . str_pad(sprintf('%015.2f', ''), 15)
                . str_pad('', 35)
                . str_pad('', 2)
                . str_pad(sprintf('%05.2f', ''), 5)
                . str_pad(sprintf('%015.2f', ''), 15)
                . str_pad("", 15)
                . $nameCom35
                . str_pad("", 35 - mb_strlen($nameCom35))
                . $fullAddress
                . str_pad("", 105 - mb_strlen($fullAddress))
                . str_pad($data->apmas->taxcond ?? null, 1)
                . "\n" .
                str_pad('INV No.', 14) .
                str_pad('', 1) .
                str_pad('Inv. Date', 10) .
                str_pad('', 2) .
                str_pad('Inv.Amt', 13, ' ', STR_PAD_LEFT) .
                str_pad('', 2) .
                str_pad('Vat', 14, ' ', STR_PAD_LEFT) .
                str_pad('', 6) .
                str_pad('Net', 11, ' ', STR_PAD_LEFT)
                . "\n";

            foreach ($data->aprcpit as $aprcpit) {
                $totalAmount += $aprcpit->aptrn->amount ?? 0;
                $totalVat += $aprcpit->aptrn->vatamt ?? 0;
                $totalNet += $aprcpit->aptrn->netamt ?? 0;
                $dateinv = substr($aprcpit->aptrn->duedat, 6, 2) . "/" . substr($aprcpit->aptrn->duedat, 4, 2) . "/" . substr($aprcpit->aptrn->duedat, 0, 4);
                $output .=
                    str_pad('INV' . ($aprcpit->aptrn->refnum ?? null), 18) .
                    str_pad('', 1) .
                    str_pad($dateinv, 10) .
                    str_pad('', 2) .
                    str_pad(sprintf('%.2f', $aprcpit->aptrn->amount ?? 0), 11, ' ', STR_PAD_LEFT) .
                    str_pad('', 2) .
                    str_pad(sprintf('%.2f', $aprcpit->aptrn->vatamt ?? 0), 9, ' ', STR_PAD_LEFT) .
                    str_pad(sprintf('%.2f', $aprcpit->aptrn->netamt ?? 0), 13, ' ', STR_PAD_LEFT)
                    . "\n";
            }

            $output .=
                str_pad('INV', 15) .
                "\n" .
                str_pad('INV Total', 15) .
                str_pad('', 1) .
                str_pad('', 10) .
                str_pad('', 2) .
                str_pad(sprintf('%.2f', $totalAmount), 14, ' ', STR_PAD_LEFT) .
                str_pad('', 2) .
                str_pad(sprintf('%.2f', $totalVat), 9, ' ', STR_PAD_LEFT) .
                str_pad(sprintf('%.2f', $totalNet), 13, ' ', STR_PAD_LEFT)
                . "\n" .
                str_pad('INV =====================================================================', 73) . "\n";
        }

        // Convert encoding and save file
        $ansiData = iconv('UTF-8', 'Windows-874//TRANSLIT', $output);
        $data = str_replace("\n", "\r\n", $ansiData);
        Storage::disk('local')->put('test.txt', $data);
        return response()->download(storage_path('app/test.txt'), 'cu27.txt');
    }

    public function exportExcel()
    {
        return Excel::download(new CheckLists($this->linkedData, $this->ebill_to), 'report.xlsx');
    }

    public function cu27()
    {
        // Initialize variables
        $selectedDate = Carbon::parse($this->dateCheck);
        $date = $selectedDate->format('d') . $selectedDate->format('m') . $selectedDate->format('Y');
        $output = null;

        // Oracle Enhancement - ดึงข้อมูลเฉพาะฟิลด์ที่ต้องการ
        $oracleEnhancement = [];
        try {
            $fromDate = $selectedDate->format('d-M-y');
            $toDate = $selectedDate->format('d-M-y');

            $oracleConnection = DB::connection('oracle');

            $oracleData = $oracleConnection->select("
                select  
                    'TXN' as rec_iden,
                    NVL(apc.attribute1, DECODE(v.attribute1,NULL,'*ERROR => ' || apc.vendor_name,
                        DECODE(INSTR(apc.vendor_name,v.attribute1,-1),0,'*ERROR => ' || apc.vendor_name,
                        DECODE(v.attribute1,'.', RTRIM(SUBSTR(apc.vendor_name,1,INSTR(apc.vendor_name,v.attribute1,-1)-1)),
                        RTRIM(LTRIM(v.attribute1,'.')) || ' ' || RTRIM(SUBSTR(apc.vendor_name,1,
                        INSTR(apc.vendor_name,v.attribute1,-1)-1)))) ) ) as benefi_name,
                    apc.check_number as your_reference,
                    (substr(lpad(to_char(apc.amount *100),14,'0'),1,12)||'.'||substr(lpad(to_char(apc.amount *100),14,'0'),13,2)) as check_amt,
                    to_char(apc.check_date,'DDMMYYYY') as check_date,
                    vs.attribute3 as payment_details1,
                    decode(vs.email_address,null,'           ','EMAIL') as preadvise_method_16_17,
                    vs.email_address as email_address,
                    decode(apc.bank_account_name,'TRU-Dummy UOB - SATHORN1','RT', 
                        decode(apc.bank_account_name,'TRT-Dummy UOB - SATHORN1','RT',
                        decode(apc.bank_account_name,'TUC-Dummy UOB - SATHORN1','RT',
                        decode(apc.bank_account_name,'TAP-Dummy UOB - SATHORN1','RT',
                        decode(apc.bank_account_name,'Dummy UOB - EASE','RT','OC'))))) as delivery_method,
                    decode(apc.bank_account_name,'TRU-Dummy UOB - SATHORN1','SATHORN1', 
                        decode(apc.bank_account_name,'TRT-Dummy UOB - SATHORN1','SATHORN1',
                        decode(apc.bank_account_name,'TUC-Dummy UOB - SATHORN1','SATHORN1',
                        decode(apc.bank_account_name,'TAP-Dummy UOB - SATHORN1','SATHORN1',
                        decode(apc.bank_account_name,'Dummy UOB - EASE','EASE', vs.attribute4))))) as payemnt_location,
                    '00000000000000000.00' as col22,  -- Taxable Amount 1 
                    '00000000000000000.00' as col26,  -- Tax Amount 1
                    '00000000000000000.00' as col27,  -- Taxable Amount 2
                    '00000000000000000.00' as col31,  -- Tax Amount 2
                    '00000000000000000.00' as col32,  -- Taxable Amount 3
                    '00000000000000000.00' as col36,  -- Tax Amount 3
                    api.invoice_num,
                    to_char(api.invoice_date,'DD/MM/YYYY') as invoice_date,
                    aid.amount as invoice_amount,
                    (aid.amount - NVL(aid.awt_gross_amount, 0)) as net_amount
                from    
                    ap_checks_all apc,
                    ap_invoices_all api,
                    ap_invoice_payments_all aip,
                    ap_invoice_distributions_all aid,
                    ap_suppliers v,
                    ap_supplier_sites_all vs
                where    
                    apc.check_id = aip.check_id
                    and aip.invoice_id = api.invoice_id
                    and api.invoice_id = aid.invoice_id
                    and apc.vendor_name = v.vendor_name
                    and aid.line_type_lookup_code in ( 'ITEM' , 'ACCRUAL' )
                    and api.vendor_id = v.vendor_id
                    and api.vendor_site_id = vs.vendor_site_id
                    and v.vendor_id = vs.vendor_id
                    and apc.org_id = 81
                    and api.org_id = 81
                    and aip.org_id = 81
                    and aid.org_id = 81
                    and vs.org_id = 81
                    and apc.check_date between to_date(:from_payment_date,'DD-MON-RR') 
                                            and to_date(:to_payment_date,'DD-MON-RR')
                    and (apc.void_date < to_date(:from_payment_date,'DD-MON-RR') or
                        apc.void_date > to_date(:to_payment_date,'DD-MON-RR') or apc.void_date is NULL)
                order by apc.check_number, api.invoice_num
            ", [
                'from_payment_date' => $fromDate,
                'to_payment_date' => $toDate
            ]);

            // จัดกลุ่มข้อมูล Oracle
            foreach ($oracleData as $record) {
                if (!isset($oracleEnhancement[$record->your_reference])) {
                    $oracleEnhancement[$record->your_reference] = [
                        'main' => $record,
                        'invoices' => [],
                        'total_amount' => 0,
                        'total_wht' => 0,
                        'total_net' => 0
                    ];
                }

                $oracleEnhancement[$record->your_reference]['invoices'][] = $record;
                $oracleEnhancement[$record->your_reference]['total_amount'] += $record->invoice_amount;
                $oracleEnhancement[$record->your_reference]['total_net'] += $record->net_amount;
            }

            Log::info('Oracle AP Schema query successful. Found ' . count($oracleData) . ' records.');
        } catch (Exception $e) {
            Log::error('Oracle AP Schema enhancement failed: ' . $e->getMessage());
        }

        foreach ($this->linkedData as $index => $data) {
            // เช็คว่ามีข้อมูลจาก Oracle หรือไม่
            $checkNumber = $data->chqnum;
            $oracleInfo = $oracleEnhancement[$checkNumber] ?? null;
            $useOracle = !is_null($oracleInfo);

            // คำนวณ totals จาก DBF
            $Amount = 0;
            $total = 0;
            foreach ($data->aprcpit as $aprcpit) {
                $Amount += $aprcpit->aptrn->netamt ?? 0;
                $total += $aprcpit->aptrn->amount ?? 0;
            }

            // ✅ Function to handle Thai text properly
            $handleThaiText = function ($text, $maxLength = null) {
                if (empty($text)) return '';

                if (!mb_check_encoding($text, 'UTF-8')) {
                    $text = mb_convert_encoding($text, 'UTF-8', 'auto');
                }

                $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
                $text = str_replace(["\r", "\n", "\t"], ' ', $text);
                $text = trim($text);

                return $maxLength ? mb_substr($text, 0, $maxLength, 'UTF-8') : $text;
            };

            // ✅ แก้ไข: Function สำหรับ format number ให้ความยาวถูกต้อง
            $formatAmount = function ($amount, $length = 20) {
                $numericValue = (float)$amount;
                $formatted = number_format($numericValue, 2, '.', '');
                $result = str_pad($formatted, $length, '0', STR_PAD_LEFT);

                // ✅ สำคัญ: ตรวจสอบความยาว ถ้าเกินให้ตัดให้พอดี
                if (strlen($result) > $length) {
                    $result = substr($result, -$length);
                }

                return $result;
            };

            // ✅ แก้ไข: Function สำหรับ format rate ให้ความยาวถูกต้อง
            $formatRate = function ($rate, $length = 5) {
                $numericValue = (float)$rate;
                $formatted = number_format($numericValue, 2, '.', '');
                $result = str_pad($formatted, $length, '0', STR_PAD_LEFT);

                if (strlen($result) > $length) {
                    $result = substr($result, -$length);
                }

                return $result;
            };

            // ✅ Function สำหรับเช็คว่า amount มีค่าจริงหรือไม่
            $hasValue = function ($amount) {
                $numericValue = (float)$amount;
                return $numericValue > 0;
            };

            // ✅ Function สำหรับเช็คว่า formatted amount string มีค่าจริงหรือไม่
            $hasValueFromFormattedString = function ($formattedAmount) {
                $cleaned = ltrim($formattedAmount, '0');
                return !empty($cleaned) && $cleaned !== '.00';
            };

            if ($useOracle) {
                $mainRecord = $oracleInfo['main'];

                $F_benefi_name = $handleThaiText($mainRecord->benefi_name, 150);
                $F_mail_address1 = $handleThaiText('', 70);
                $F_mail_address2 = $handleThaiText('', 70);
                $F_mail_address3 = $handleThaiText('', 70);
                $F_zip_code = $handleThaiText('', 10);
                $F_check_amt = $mainRecord->check_amt;
                $F_your_reference = $handleThaiText($mainRecord->your_reference, 35);
                $F_check_date = $mainRecord->check_date;
                $F_payment_details1 = $handleThaiText($mainRecord->payment_details1 ?? 'REC+TAX', 35);
                $F_delivery_method = $handleThaiText($mainRecord->delivery_method, 5);
                $F_payment_location = $handleThaiText($mainRecord->payemnt_location, 35);
                $F_email_address = $handleThaiText($mainRecord->email_address ?? '', 120);
                $F_tax_id = str_pad('0000000000000', 20, '0', STR_PAD_LEFT);
            } else {
                $F_benefi_name = $handleThaiText($this->ebill_to['name'][$data->id], 150);
                $F_mail_address1 = $handleThaiText($this->ebill_to['addr1'][$data->id] ?? '', 70);
                $F_mail_address2 = $handleThaiText($this->ebill_to['addr2'][$data->id] ?? '', 70);
                $F_mail_address3 = $handleThaiText($this->ebill_to['addr3'][$data->id] ?? '', 70);
                $F_zip_code = $handleThaiText($data->apmas->zipcod ?? '', 10);
                $F_check_amt = sprintf('%020.2f', $data->netamt);
                $F_your_reference = $handleThaiText($data->chqnum, 35);
                $F_check_date = $date;
                $F_payment_details1 = 'REC+TAX';
                $F_delivery_method = 'RT';
                $F_payment_location = 'SATHORN1';
                $F_email_address = '';

                $taxId = $this->ebill_to['taxid'][$data->id] ?? '';
                $taxId = preg_replace('/\D/', '', $taxId);
                $F_tax_id = str_pad($taxId, 20, '0', STR_PAD_LEFT);
            }

            // Check if this is a WHT transaction
            $isWhtTransaction = !($data->amount == $Amount);

            // ✅ WHT Information with corrected logic
            if ($isWhtTransaction) {
                if ($useOracle) {
                    $mainRecord = $oracleInfo['main'];
                    $taxableAmount = $mainRecord->col22; // จาก Oracle

                    // ✅ แก้ไขเงื่อนไข: ถ้า taxableAmount ไม่มีค่าให้ taxType เป็นค่าว่าง
                    $taxType = $hasValueFromFormattedString($taxableAmount)
                        ? str_pad($data->apmas->suptyp ?? '', 2)
                        : '  '; // 2 spaces แทน '00'

                    $taxDescription = $hasValueFromFormattedString($taxableAmount)
                        ? $handleThaiText($data->apmas->taxdes ?? '', 35)
                        : '';

                    $taxRate = $hasValueFromFormattedString($taxableAmount)
                        ? $formatRate($data->apmas->taxrat ?? 3.00, 5)
                        : '00.00';

                    $whtAmount = $mainRecord->col26;
                } else {
                    // ✅ ใช้ formatAmount สำหรับ DBF
                    $taxableAmount = $formatAmount($total, 20);

                    // ✅ แก้ไขเงื่อนไข: ถ้า total ไม่มีค่าให้ taxType เป็นค่าว่าง
                    $taxType = $hasValue($total)
                        ? str_pad($data->apmas->suptyp ?? '', 2)
                        : '  '; // 2 spaces แทน '00'

                    $taxDescription = $hasValue($total)
                        ? $handleThaiText($data->apmas->taxdes ?? '', 35)
                        : '';

                    $taxRate = $hasValue($total)
                        ? $formatRate($data->apmas->taxrat ?? 3.00, 5)
                        : '00.00';

                    $whtAmount = $formatAmount(abs(round($Amount - $data->amount, 2)), 20);
                }
            } else {
                if ($useOracle) {
                    $mainRecord = $oracleInfo['main'];
                    $taxableAmount = $mainRecord->col22; // '00000000000000000.00'
                    $taxType = '  '; // 2 spaces เพราะไม่มี WHT
                    $taxDescription = '';
                    $taxRate = '00.00';
                    $whtAmount = $mainRecord->col26; // '00000000000000000.00'
                } else {
                    $taxableAmount = '00000000000000000.00';
                    $taxType = '  '; // 2 spaces เพราะไม่มี WHT
                    $taxDescription = '';
                    $taxRate = '00.00';
                    $whtAmount = '00000000000000000.00';
                }
            }

            // ✅ Debug logging
            Log::info('=== Field Length Validation ===');
            Log::info('Check Number: ' . $checkNumber);
            Log::info('Using Oracle data: ' . ($useOracle ? 'YES' : 'NO'));
            Log::info('Is WHT Transaction: ' . ($isWhtTransaction ? 'YES' : 'NO'));
            Log::info('Original total: ' . $total);
            Log::info('Taxable Amount: "' . $taxableAmount . '" (length: ' . strlen($taxableAmount) . ')');
            Log::info('Has Value Check: ' . ($useOracle ?
                ($hasValueFromFormattedString($taxableAmount) ? 'YES' : 'NO') : ($hasValue($total) ? 'YES' : 'NO')));
            Log::info('Tax Type: "' . $taxType . '" (length: ' . strlen($taxType) . ')');
            Log::info('Tax Rate: "' . $taxRate . '" (length: ' . strlen($taxRate) . ')');
            Log::info('WHT Amount: "' . $whtAmount . '" (length: ' . strlen($whtAmount) . ')');

            // ✅ เพิ่ม validation ว่า Tax Type เป็นค่าว่างหรือไม่
            if (trim($taxType) === '') {
                Log::info('✅ Tax Type is empty (spaces only) because taxable amount is zero');
            } else {
                Log::info('✅ Tax Type has value: "' . trim($taxType) . '"');
            }

            // ✅ Function สำหรับ pad ข้อมูลภาษาไทยอย่างถูกต้อง
            $padThaiText = function ($text, $length, $padString = ' ', $padType = STR_PAD_RIGHT) {
                $currentLength = mb_strlen($text, 'UTF-8');

                if ($currentLength >= $length) {
                    return mb_substr($text, 0, $length, 'UTF-8');
                }

                $padLength = $length - $currentLength;
                $padding = str_repeat($padString, $padLength);

                return $padType === STR_PAD_RIGHT ? $text . $padding : $padding . $text;
            };

            // Build output string with EXACT positions per CU27 spec
            $output .=
                // 1-3: Transaction Identifier (3)
                $padThaiText('TXN', 3) .

                // 4-13: Product Code (10)
                $padThaiText('CH', 10) .

                // 14-163: Beneficiary Name (150)
                $padThaiText($F_benefi_name, 150) .

                // 164-233: Beneficiary Address 1 (70)
                $padThaiText('', 70) .

                // 234-303: Beneficiary Address 2 (70)
                $padThaiText('', 70) .

                // 304-373: Beneficiary Address 3 (70)
                $padThaiText('', 70) .

                // 374-443: Beneficiary Address 4 (70)
                $padThaiText('', 70) .

                // 444-513: Beneficiary Code/Site (70)
                $padThaiText('', 70) .

                // 514-663: Mail-To-Name (150)
                $padThaiText($F_benefi_name, 150) .

                // 664-733: Mail-To-Address 1 (70)
                $padThaiText($F_mail_address1, 70) .

                // 734-803: Mail-To-Address 2 (70)
                $padThaiText($F_mail_address2, 70) .

                // 804-873: Mail-To-Address 3 (70)
                $padThaiText($F_mail_address3, 70) .

                // 874-943: Mail-To-Address 4 (70)
                $padThaiText('', 70) .

                // 944-953: Zip Code (10)
                $padThaiText($F_zip_code, 10) .

                // 954-973: Invoice Amount (20)
                $padThaiText('', 20) .

                // 974-993: VAT Amount (20)
                $padThaiText('', 20) .

                // 994-1013: WHT Amount (20)
                $padThaiText('', 20) .

                // 1014-1033: Ben Charged (20)
                $padThaiText('OUR', 20) .

                // 1034-1053: Cheque Amount (20)
                $padThaiText($formatAmount(str_replace(',', '', $F_check_amt), 20), 20) .

                // 1054-1063: Currency (10)
                $padThaiText('THB', 10) .

                // 1064-1098: Your Reference (35)
                $padThaiText($F_your_reference, 35) .

                // 1099-1106: Cheque Date (8)
                $padThaiText($F_check_date, 8) .

                // 1107-1141: Payment Details1 (35)
                $padThaiText($F_payment_details1, 35) .

                // 1142-1176: Payment Details2 (35)
                $padThaiText('', 35) .

                // 1177-1211: Payment Details3 (35)
                $padThaiText('', 35) .

                // 1212-1246: Payment Details4 (35)
                $padThaiText('', 35) .

                // 1247-1281: Exchange Document (35)
                $padThaiText('REC+TAX', 35) .

                // 1282-1286: Delivery Method (5)
                $padThaiText($F_delivery_method, 5) .

                // 1287-1321: Payment Location (35)
                $padThaiText($F_payment_location, 35) .

                // 1322-1326: Preadvise Method (5)
                $padThaiText('', 5) .

                // 1327-1336: Fax Number (10)
                $padThaiText('', 10) .

                // 1337-1456: Email Address (120)
                $padThaiText($F_email_address, 120) .

                // 1457-1466: IVR Code (10)
                $padThaiText('', 10) .

                // 1467-1486: SMS Number (20)
                $padThaiText('', 20) .

                // 1487-1498: Charge Indicator (12)
                $padThaiText('OUR', 12) .

                // 1499-1518: Beneficiary Tax ID (20)
                $padThaiText($F_tax_id, 20) .

                // 1519-1538: Personal ID (20)
                $padThaiText('', 20) .

                // 1539-1543: WHT Type (5)
                $padThaiText('53', 5) .

                // 1544-1563: Taxable Amount 1 (20)
                $padThaiText($taxableAmount, 20) .

                // ✅ 1564-1565: Tax Type 1 (2) - ใช้ $taxType ที่คำนวณใหม่แล้ว
                $padThaiText($taxType, 2) .

                // 1566-1600: Tax Type Description 1 (35)
                $padThaiText($taxDescription, 35) .

                // 1601-1605: Tax Rate 1 (5)
                $padThaiText($taxRate, 5) .

                // 1606-1625: Tax Amount 1 (20)
                $padThaiText($whtAmount, 20) .

                // 1626-1645: Taxable Amount 2 (20)
                $padThaiText($useOracle ? $oracleInfo['main']->col27 : '00000000000000000.00', 20) .

                // 1646-1647: Tax Type 2 (2)
                $padThaiText('', 2) .

                // 1648-1682: Tax Type Description 2 (35)
                $padThaiText('', 35) .

                // 1683-1687: Tax Rate 2 (5)
                $padThaiText('00.00', 5) .

                // 1688-1707: Tax Amount 2 (20)
                $padThaiText($useOracle ? $oracleInfo['main']->col31 : '00000000000000000.00', 20) .

                // 1708-1727: Taxable Amount 3 (20)
                $padThaiText($useOracle ? $oracleInfo['main']->col32 : '00000000000000000.00', 20) .

                // 1728-1729: Tax Type 3 (2)
                $padThaiText('', 2) .

                // 1730-1764: Tax Type Description 3 (35)
                $padThaiText('', 35) .

                // 1765-1769: Tax Rate 3 (5)
                $padThaiText('00.00', 5) .

                // 1770-1789: Tax Amount 3 (20)
                $padThaiText($useOracle ? $oracleInfo['main']->col36 : '00000000000000000.00', 20) .

                // 1790-1809: Taxable Amount 4 (20)
                $padThaiText('00000000000000000.00', 20) .

                // 1810-1811: Tax Type 4 (2)
                $padThaiText('', 2) .

                // 1812-1846: Tax Type Description 4 (35)
                $padThaiText('', 35) .

                // 1847-1851: Tax Rate 4 (5)
                $padThaiText('00.00', 5) .

                // 1852-1871: Tax Amount 4 (20)
                $padThaiText('00000000000000000.00', 20) .

                // 1872-1891: Taxable Amount 5 (20)
                $padThaiText('00000000000000000.00', 20) .

                // 1892-1893: Tax Type 5 (2)
                $padThaiText('', 2) .

                // 1894-1928: Tax Type Description 5 (35)
                $padThaiText('', 35) .

                // 1929-1933: Tax Rate 5 (5)
                $padThaiText('00.00', 5) .

                // 1934-1953: Tax Amount 5 (20)
                $padThaiText('00000000000000000.00', 20) .

                // 1954-1973: WHT Document No. (20)
                $padThaiText('', 20) .

                // 1974-2008: Client Name (35)
                $padThaiText(mb_substr($F_benefi_name, 0, 35, 'UTF-8'), 35) .

                // 2009-2113: Client Address (105)
                $padThaiText(mb_substr($F_mail_address1 . $F_mail_address2 . $F_mail_address3, 0, 105, 'UTF-8'), 105) .

                // 2114-2123: WHT Sequence No. (10)
                $padThaiText('', 10) .

                // 2124-2124: Payment Condition (1)
                $padThaiText('1', 1) .

                // 2125-2274: 3rd Party Name (150)
                $padThaiText($F_benefi_name, 150) .

                // 2275-2344: 3rd Party Address 1 (70)
                $padThaiText('', 70) .

                // 2345-2414: 3rd Party Address 2 (70)
                $padThaiText('', 70) .

                // 2415-2449: 3rd Party Address 3 (35)
                $padThaiText('', 35) .

                // 2450-2484: 3rd Party Address 4 (35)
                $padThaiText('', 35) .

                // 2485-2494: (Sending) Debit Bank Code (10)
                $padThaiText('', 10) .

                // 2495-2504: (Sending) Debit Branch Code (10)
                $padThaiText('', 10) .

                // 2505-2524: (Sending) Debit A/C no (20)
                $padThaiText('', 20) .

                // 2525-2526: (Sending) Debit A/C Country Code (2)
                $padThaiText('', 2) .

                // 2527-2529: Transaction Type (3)
                $padThaiText('', 3) .

                // 2530-2549: Customer Code (20)
                $padThaiText('', 20) .

                // 2550-2699: Customer Name (150)
                $padThaiText('', 150) .

                // 2700-2719: Customer Acro (20)
                $padThaiText('', 20) .

                // 2720-2789: Customer Address 1 (70)
                $padThaiText('', 70) .

                // 2790-2859: Customer Address 2 (70)
                $padThaiText('', 70) .

                // 2860-2894: Customer Address 3 (35)
                $padThaiText('', 35) .

                // 2895-2929: Customer Address 4 (35)
                $padThaiText('', 35) .

                // 2930-2964: Cust.Ref.1/Shipping Code (35)
                $padThaiText('', 35) .

                // 2965-2999: Cust.Ref.2/Collector Code (35)
                $padThaiText('', 35) .

                // 3000-3034: Customer Reference 3 (35)
                $padThaiText('', 35) .

                // 3035-3069: Customer Reference 4 (35)
                $padThaiText('', 35) .

                // 3070-3104: Customer Reference 5 (35)
                $padThaiText('', 35) .

                // 3105-3114: (Receiving) Credit Bank Code (10)
                $padThaiText('', 10) .

                // 3115-3124: (Receiving) Credit Branch Code (10)
                $padThaiText('', 10) .

                // 3125-3144: (Receiving) Credit A/C No. (20)
                $padThaiText('', 20) .

                // 3145-3146: (Receiving) Credit A/C Country Code (2)
                $padThaiText('', 2) .

                // 3147-3148: Optional Service (2)
                $padThaiText('', 2) .

                // 3149-3218: Sender Name (70)
                $padThaiText('', 70) .

                // 3219-3288: Sender Correspondent Name (70)
                $padThaiText('', 70) .

                // 3289-3358: Receiving Bank Name (70)
                $padThaiText('', 70) .

                // 3359-3393: Transaction Reference 1 (35)
                $padThaiText('', 35) .

                // 3394-3428: Transaction Reference 2 (35)
                $padThaiText('', 35) .

                // 3429-3463: Transaction Reference 3 (35)
                $padThaiText('', 35) .

                // 3464-3498: Transaction Reference 4 (35)
                $padThaiText('', 35) .

                // 3499-3533: Transaction Reference 5 (35)
                $padThaiText('', 35) .

                // 3534-3568: Transaction Reference 6 (35)
                $padThaiText('', 35) .

                // 3569-3588: On Behalf of TaxId (20)
                $padThaiText('', 20) .

                // 3589-3738: On Behalf of Name (150)
                $padThaiText('', 150) .

                // 3739-3948: On Behalf of Address (210)
                $padThaiText('', 210) .

                "\n";

            // Process Invoice Details with Thai text support
            if ($useOracle && !empty($oracleInfo['invoices'])) {
                // ใช้ข้อมูล Invoice จาก Oracle
                foreach ($oracleInfo['invoices'] as $invoiceRecord) {
                    $invoiceData = 'INV' . ($invoiceRecord->invoice_num ?? '')
                        . ' ' . ($invoiceRecord->invoice_date ?? '')
                        . ' ' . sprintf('%.2f', $invoiceRecord->invoice_amount ?? 0)
                        . ' ' . sprintf('%.2f', 0) // VAT = 0
                        . ' ' . sprintf('%.2f', $invoiceRecord->net_amount ?? 0);

                    $output .= $padThaiText($invoiceData, 75) . "\n";
                }
            } else {
                // ใช้ข้อมูล Invoice จาก DBF
                foreach ($data->aprcpit as $aprcpit) {
                    $dateinv = substr($aprcpit->aptrn->duedat, 6, 2) . "/"
                        . substr($aprcpit->aptrn->duedat, 4, 2) . "/"
                        . substr($aprcpit->aptrn->duedat, 0, 4);

                    $invoiceData = 'INV' . ($aprcpit->aptrn->refnum ?? '')
                        . ' ' . $dateinv
                        . ' ' . sprintf('%.2f', $aprcpit->aptrn->amount ?? 0)
                        . ' ' . sprintf('%.2f', $aprcpit->aptrn->vatamt ?? 0)
                        . ' ' . sprintf('%.2f', $aprcpit->aptrn->netamt ?? 0);

                    $output .= $padThaiText($invoiceData, 75) . "\n";
                }
            }
        }

        // ✅ การ convert encoding ที่รองรับภาษาไทยอย่างถูกต้อง
        try {
            if (mb_check_encoding($output, 'UTF-8')) {
                $ansiData = @iconv('UTF-8', 'Windows-874//IGNORE', $output);

                if ($ansiData === false) {
                    $ansiData = @mb_convert_encoding($output, 'Windows-874', 'UTF-8');
                }

                if ($ansiData === false) {
                    $ansiData = @iconv('UTF-8', 'TIS-620//IGNORE', $output);
                }

                if ($ansiData === false) {
                    $ansiData = @mb_convert_encoding($output, 'CP874', 'UTF-8');
                }

                if ($ansiData === false) {
                    Log::warning('All Thai encoding methods failed, saving as UTF-8 with BOM');
                    $ansiData = "\xEF\xBB\xBF" . $output;
                }
            } else {
                $utf8Output = @mb_convert_encoding($output, 'UTF-8', 'auto');
                if ($utf8Output !== false) {
                    $ansiData = @iconv('UTF-8', 'Windows-874//IGNORE', $utf8Output);
                    if ($ansiData === false) {
                        $ansiData = $utf8Output;
                    }
                } else {
                    $ansiData = $output;
                }
            }

            Log::info('Thai encoding conversion completed successfully');
        } catch (Exception $e) {
            Log::error('Thai encoding conversion failed: ' . $e->getMessage());
            $ansiData = $output;
        }

        // Ensure CRLF line endings for PC format
        $fileData = str_replace(["\r\n", "\r", "\n"], "\r\n", $ansiData);

        // เพิ่ม validation ขนาดไฟล์
        $fileSize = strlen($fileData);
        Log::info("CU27 file generated. Size: {$fileSize} bytes, Records: " . count($this->linkedData));

        // ตรวจสอบว่าแต่ละบรรทัดมีความยาวถูกต้องหรือไม่
        $lines = explode("\r\n", $fileData);
        foreach ($lines as $lineNum => $line) {
            if (!empty(trim($line)) && !str_starts_with($line, 'INV')) {
                $lineLength = mb_strlen($line, 'UTF-8');
                if ($lineLength != 3948) {
                    Log::warning("Line " . ($lineNum + 1) . " has incorrect length: {$lineLength} (expected: 3948)");
                }
            }
        }

        Storage::disk('local')->put('cu27_fixed.txt', $fileData);
        return response()->download(storage_path('app/cu27_fixed.txt'), 'cu27_fixed.txt');
    }
    public function render()
    {
        return view('livewire.check-uob');
    }
}