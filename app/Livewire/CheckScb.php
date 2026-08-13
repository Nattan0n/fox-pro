<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\ConnectionInterface;
use Exception;

class CheckScb extends Component
{
    public string $company    = 'TRU';
    public string $checkDate  = '';
    public array  $results    = [];
    public bool   $isLoading  = false;
    public bool   $hashModuleReady = false;
    public ?string $errorMessage   = null;
    public ?string $successMessage = null;

    private array $companyAccounts = [
        'TRU' => '1152079993',
        'TRT' => '1152175537',
        'TUC' => '1152297478',
        'TAP' => '1152729297',
    ];


    public array $companyLabels = [
        'TRU' => 'TRU — Thai Rung Union (TRUNION)',
        'TRT' => 'TRT — Thai Rung Tools (TRTOOLS)',
        'TUC' => 'TUC — Thai Ultimate Car (THAIULTIMATE)',
        'TAP' => 'TAP — Thai Autopart (THAIAUT)',
    ];

    public function mount(): void
    {
        $this->checkDate      = now()->format('Y-m-d');
        $this->hashModuleReady = $this->detectHashModule() !== null;
    }

    /** Returns the module dir path if all required JARs/properties exist, null otherwise. */
    private function detectHashModule(): ?string
    {
        $dir = rtrim(env('SCB_MODULE_DIR', ''), '/\\');
        if (empty($dir)) {
            return null;
        }
        $sep = DIRECTORY_SEPARATOR;
        $ok  = file_exists($dir . $sep . 'lib' . $sep . 'SCBBCMHash.jar')
            && file_exists($dir . $sep . 'lib' . $sep . 'iText-5.0.6.jar')
            && file_exists($dir . $sep . 'pickup.properties');
        return $ok ? $dir : null;
    }

    public function search(): void
    {
        $this->errorMessage   = null;
        $this->successMessage = null;
        $this->results        = [];
        $this->isLoading      = true;

        if (empty($this->checkDate)) {
            $this->dispatch('scb-notify', type: 'error', html: 'กรุณาเลือกวันที่จ่าย');
            $this->isLoading = false;
            return;
        }

        $bankAccountNum = $this->companyAccounts[$this->company] ?? null;
        if (!$bankAccountNum) {
            $this->dispatch('scb-notify', type: 'error', html: 'ไม่พบบัญชีธนาคารสำหรับบริษัทที่เลือก');
            $this->isLoading = false;
            return;
        }

        try {
            $db       = DB::connection('oracle');
            $bindings = ['bank_account_num' => $bankAccountNum, 'check_date' => $this->checkDate];

            // ── TIER 1: Full SQL (original) — requires HZ VPD context ────────────────
            // Same SQL that works in Oracle Reports / SQL Developer.
            // ce_banks_v and ce_bank_branches_v join hz_parties internally,
            // which requires FND_GLOBAL.APPS_INITIALIZE to be called first.
            $sqlFull = "
                SELECT DISTINCT
                    DECODE(ac.org_id,
                        '84','TRUNION     ',
                        '83','TRTOOLS     ',
                        '85','THAIULTIMATE',
                        '82','THAIAUT     ',
                        null
                    ) AS company,
                    TO_CHAR(ac.check_date,'YYYY-MM-DD') AS check_date,
                    ac.check_number,
                    aps.vendor_name,
                    aps.segment1 AS vendor_code,
                    NVL(ieba.bank_account_num,'0') AS payee_bank_account,
                    ac.amount AS check_amount,
                    NVL(ac.attribute10,' ') AS chq_desc,
                    RPAD(TO_CHAR(ac.check_date,'YYYYMMDD')||'  '||ac.check_number,32,' ')
                        ||TO_CHAR(SYSDATE,'YYYYMMDDHH24MMSS')
                        ||'BCM'||LPAD(' ',31,' ')||'.'                                        AS a3_6,
                    'PA2'||TO_CHAR(ac.check_date,'YYYYMMDD')
                        ||RPAD(cba.bank_account_num,25,' ')
                        ||LPAD(SUBSTR(cba.bank_account_num,4,1),2,'0')
                        ||LPAD(SUBSTR(cba.bank_account_num,1,3),4,'0')||'THB'                AS b2_7,
                    RPAD(cba.bank_account_num,15,' ')
                        ||RPAD(' ',9,' ')||RPAD(' ',1,' ')
                        ||LPAD(SUBSTR(cba.bank_account_num,4,1),2,'0')
                        ||LPAD(SUBSTR(cba.bank_account_num,1,3),4,'0')                       AS b11_15,
                    RPAD(NVL(ieba.bank_account_num,' '),25,' ')                               AS payee_account_no,
                    'N'||'N'||'Y'||'S'
                        ||RPAD(' ',4,' ')||RPAD(' ',2,' ')||RPAD(' ',22,' ')
                        ||LPAD(' ',17,'0')||RPAD(' ',5,' ')||LPAD(' ',17,'0')||RPAD(' ',48,' ')
                        ||'014'||RPAD(NVL(cbv.bank_name,' '),35,' ')
                        ||LPAD(SUBSTR(NVL(ieba.bank_account_num,'000'),1,3),4,'0')
                        ||RPAD(NVL(cbbv.bank_branch_name,' '),35,' ')
                        ||'B'||'N'||LPAD(' ',26,' ')                                         AS c7_31,
                    RPAD(NVL(ac.attribute10,' '),50,' ')||RPAD(' ',18,' ')                   AS c32_chq_desc,
                    RPAD(' ',15,' ')||RPAD(
                        DECODE(ac.attribute11, null,
                            DECODE(ac.attribute1, null,
                                DECODE(INSTR(aps.vendor_name,aps.attribute1,-1),0,
                                    '*.'||aps.vendor_name,
                                    RTRIM(LTRIM(aps.attribute1,'.'))||' '
                                        ||RTRIM(SUBSTR(aps.vendor_name,1,INSTR(aps.vendor_name,aps.attribute1,-1)-1))
                                ),
                                ac.attribute1
                            ),
                            ac.attribute11
                        )
                        ||' [ '||aps.segment1||' ]'
                        ||' CHQ NO : '||ac.check_number
                        ||' - PV NO : '||ac.check_voucher_num,
                    100,' ')||LPAD(' ',683,' ')||'.'                                          AS d4_5
                FROM
                    AP.ap_invoices_all           i,
                    AP.ap_suppliers              aps,
                    AP.ap_supplier_sites_all     apss,
                    ap_invoice_payments_all      aip,
                    ap_checks_all                ac,
                    apps.iby_external_payees_all iepa,
                    apps.iby_pmt_instr_uses_all  ipiua,
                    apps.iby_ext_bank_accounts   ieba,
                    apps.ce_banks_v              cbv,
                    apps.ce_bank_branches_v      cbbv,
                    ce_bank_accounts             cba
                WHERE 1=1
                AND aps.VENDOR_ID               = apss.VENDOR_ID
                AND iepa.PAYEE_PARTY_ID         = aps.PARTY_ID
                AND iepa.PARTY_SITE_ID          IS NULL
                AND iepa.SUPPLIER_SITE_ID       IS NULL
                AND IPIUA.EXT_PMT_PARTY_ID(+)  = iepa.EXT_PAYEE_ID
                AND ieba.EXT_BANK_ACCOUNT_ID(+) = IPIUA.INSTRUMENT_ID
                AND ieba.bank_id                = cbv.bank_party_id(+)
                AND ieba.branch_id              = cbbv.branch_party_id(+)
                AND i.invoice_id                = aip.invoice_id
                AND i.vendor_id                 = aps.vendor_id
                AND i.vendor_site_id            = apss.vendor_site_id
                AND i.cancelled_date            IS NULL
                AND i.payment_status_flag       = 'Y'
                AND aip.check_id                = ac.check_id
                AND aip.posted_flag             = 'Y'
                AND ac.bank_account_name        = cba.bank_account_name
                AND ieba.bank_account_num       IS NOT NULL
                AND cba.bank_account_num        = :bank_account_num
                AND TRUNC(ac.check_date)        = TO_DATE(:check_date,'YYYY-MM-DD')
                ORDER BY 2, 3
            ";

            // ── TIER 2: Safe SQL (no HZ-dependent APPS views) ────────────────────────
            // Uses ANSI JOIN syntax + subquery for iby_ext_bank_accounts (base table, no VPD).
            // ORA-01799 fix: (+ ) outer join to subquery is invalid — use ANSI LEFT JOIN instead.
            // Bank name / branch name = spaces (safe for SCB format).
            $sqlSafe = "
                SELECT DISTINCT
                    DECODE(ac.org_id,
                        '84','TRUNION     ',
                        '83','TRTOOLS     ',
                        '85','THAIULTIMATE',
                        '82','THAIAUT     ',
                        null
                    ) AS company,
                    TO_CHAR(ac.check_date,'YYYY-MM-DD') AS check_date,
                    ac.check_number,
                    aps.vendor_name,
                    aps.segment1 AS vendor_code,
                    NVL(ieba.bank_account_num,'0') AS payee_bank_account,
                    ac.amount AS check_amount,
                    NVL(ac.attribute10,' ') AS chq_desc,
                    RPAD(TO_CHAR(ac.check_date,'YYYYMMDD')||'  '||ac.check_number,32,' ')
                        ||TO_CHAR(SYSDATE,'YYYYMMDDHH24MMSS')
                        ||'BCM'||LPAD(' ',31,' ')||'.'                                        AS a3_6,
                    'PA2'||TO_CHAR(ac.check_date,'YYYYMMDD')
                        ||RPAD(cba.bank_account_num,25,' ')
                        ||LPAD(SUBSTR(cba.bank_account_num,4,1),2,'0')
                        ||LPAD(SUBSTR(cba.bank_account_num,1,3),4,'0')||'THB'                AS b2_7,
                    RPAD(cba.bank_account_num,15,' ')
                        ||RPAD(' ',9,' ')||RPAD(' ',1,' ')
                        ||LPAD(SUBSTR(cba.bank_account_num,4,1),2,'0')
                        ||LPAD(SUBSTR(cba.bank_account_num,1,3),4,'0')                       AS b11_15,
                    RPAD(NVL(ieba.bank_account_num,' '),25,' ')                               AS payee_account_no,
                    'N'||'N'||'Y'||'S'
                        ||RPAD(' ',4,' ')||RPAD(' ',2,' ')||RPAD(' ',22,' ')
                        ||LPAD(' ',17,'0')||RPAD(' ',5,' ')||LPAD(' ',17,'0')||RPAD(' ',48,' ')
                        ||'014'||RPAD(' ',35,' ')
                        ||LPAD(SUBSTR(NVL(ieba.bank_account_num,'000'),1,3),4,'0')
                        ||RPAD(' ',35,' ')
                        ||'B'||'N'||LPAD(' ',26,' ')                                         AS c7_31,
                    RPAD(NVL(ac.attribute10,' '),50,' ')||RPAD(' ',18,' ')                   AS c32_chq_desc,
                    RPAD(' ',15,' ')||RPAD(
                        DECODE(ac.attribute11, null,
                            DECODE(ac.attribute1, null,
                                DECODE(INSTR(aps.vendor_name,aps.attribute1,-1),0,
                                    '*.'||aps.vendor_name,
                                    RTRIM(LTRIM(aps.attribute1,'.'))||' '
                                        ||RTRIM(SUBSTR(aps.vendor_name,1,INSTR(aps.vendor_name,aps.attribute1,-1)-1))
                                ),
                                ac.attribute1
                            ),
                            ac.attribute11
                        )
                        ||' [ '||aps.segment1||' ]'
                        ||' CHQ NO : '||ac.check_number
                        ||' - PV NO : '||ac.check_voucher_num,
                    100,' ')||LPAD(' ',683,' ')||'.'                                          AS d4_5
                FROM ap_checks_all ac
                JOIN ce_bank_accounts cba
                    ON cba.bank_account_name = ac.bank_account_name
                JOIN ap_invoice_payments_all aip
                    ON aip.check_id = ac.check_id AND aip.posted_flag = 'Y'
                JOIN AP.ap_invoices_all i
                    ON i.invoice_id = aip.invoice_id
                    AND i.cancelled_date IS NULL
                    AND i.payment_status_flag = 'Y'
                JOIN AP.ap_suppliers aps
                    ON aps.vendor_id = i.vendor_id
                JOIN AP.ap_supplier_sites_all apss
                    ON apss.vendor_site_id = i.vendor_site_id
                    AND apss.vendor_id = aps.vendor_id
                JOIN (
                    SELECT iao.ACCOUNT_OWNER_PARTY_ID,
                           MAX(ieba2.bank_account_num) AS bank_account_num
                    FROM iby_account_owners iao
                    JOIN iby_ext_bank_accounts ieba2
                        ON ieba2.ext_bank_account_id = iao.ext_bank_account_id
                    GROUP BY iao.ACCOUNT_OWNER_PARTY_ID
                ) ieba ON ieba.ACCOUNT_OWNER_PARTY_ID = aps.PARTY_ID
                WHERE cba.bank_account_num     = :bank_account_num
                AND TRUNC(ac.check_date)       = TO_DATE(:check_date,'YYYY-MM-DD')
                ORDER BY 2, 3
            ";

            // ── Try Tier 1 first (EBS context + full SQL) ────────────────────────────
            $usedTier2 = false;
            $contextOk = $this->initOracleEBSContext($db);
            try {
                $rows = $db->select($sqlFull, $bindings);
                Log::info('SCB: Tier-1 query succeeded', ['context_ok' => $contextOk]);
            } catch (Exception $e1) {
                if (str_contains($e1->getMessage(), '28110') || str_contains($e1->getMessage(), 'HZ_COMMON_PUB')) {
                    // ORA-28110: HZ VPD still blocked → fall back to Tier 2
                    Log::warning('SCB: Tier-1 ORA-28110, switching to Tier-2 safe query');
                    try {
                        $rows = $db->select($sqlSafe, $bindings);
                        $usedTier2 = true;
                    } catch (Exception $e2) {
                        throw $e2;
                    }
                } else {
                    throw $e1;
                }
            }

            $this->results = array_map(fn($row) => (array) $row, $rows);

            if (empty($this->results)) {
                $this->dispatch('scb-notify', type: 'error', html: 'ไม่พบข้อมูลการจ่ายเงิน SCB สำหรับ '
                    . $this->company . ' วันที่ ' . $this->checkDate);
            } elseif ($usedTier2) {
                // Only notify Tier-2 fallback when results actually exist (avoids dual-dispatch)
                $this->dispatch('scb-notify', type: 'success', html: 'ข้อมูล bank name / branch ไม่แสดง (Oracle EBS context ไม่พร้อม) — ยังสามารถ Export Pay.txt ได้');
            }

        } catch (Exception $e) {
            Log::error('SCB Oracle query error: ' . $e->getMessage(), [
                'company'   => $this->company,
                'checkDate' => $this->checkDate,
            ]);
            $this->dispatch('scb-notify', type: 'error', html: 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }

        $this->isLoading = false;
    }

    public function exportToFile()
    {
        $this->errorMessage   = null;
        $this->successMessage = null;

        if (empty($this->results)) {
            $this->dispatch('scb-notify', type: 'error', html: 'ไม่มีข้อมูล กรุณากด Search ก่อน');
            return;
        }

        try {
            $payTxtContent = $this->generatePayTxt($this->results);

            $moduleDir = $this->detectHashModule();
            $tempDir   = rtrim(env('SCB_TEMP_DIR', storage_path('scb-temp')), '/\\');

            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            $pdfDir = $tempDir . DIRECTORY_SEPARATOR . 'pdf';
            if (!is_dir($pdfDir)) {
                mkdir($pdfDir, 0755, true);
            }

            $uniqueId   = uniqid('scb_', true);
            $inputFile  = $tempDir . DIRECTORY_SEPARATOR . $uniqueId . '_pay.txt';
            $outputFile = $tempDir . DIRECTORY_SEPARATOR . $uniqueId . '_pay-hash.txt';

            // Write Pay.txt (TIS-620)
            $encoded = iconv('UTF-8', 'TIS-620//TRANSLIT//IGNORE', $payTxtContent);
            file_put_contents($inputFile, $encoded !== false ? $encoded : $payTxtContent);

            $dateForFileName = str_replace('-', '', $this->checkDate);
            $downloadFile    = $outputFile;
            $downloadName    = $this->company . '_SCB_Pay-Hash_' . $dateForFileName . '.txt';

            if ($moduleDir !== null) {
                // SCB Hash Module requires Value Date >= today
                $today         = new \DateTime('today');
                $checkDateObj  = new \DateTime($this->checkDate);
                if ($checkDateObj < $today) {
                    @unlink($inputFile);
                    $this->dispatch('scb-notify', type: 'error', html:
                        'SCB Hash Module ไม่รองรับวันที่ย้อนหลัง<br>'
                        . '<span style="font-size:0.75rem">Check Date <b>' . $this->checkDate . '</b> '
                        . 'น้อยกว่าวันนี้ (' . (new \DateTime())->format('Y-m-d') . ')<br>'
                        . 'SCB กำหนดให้ Value Date ≥ วันที่ปัจจุบัน</span>');
                    return;
                }

                // ── Run Java Hash Module directly (no external bat/sh needed) ─────────
                // -Dfile.encoding=Cp874: Java 18+ defaults to UTF-8; SCB files are TIS-620/Cp874.
                // Windows: exec() uses cmd.exe /c which mangles multi-quoted args → write a
                //          one-shot wrapper .bat so exec() only passes a single quoted path.
                // Linux: use escapeshellarg directly.
                $sep   = DIRECTORY_SEPARATOR;
                $jar1  = $moduleDir . $sep . 'lib' . $sep . 'iText-5.0.6.jar';
                $jar2  = $moduleDir . $sep . 'lib' . $sep . 'SCBBCMHash.jar';
                $props = $moduleDir . $sep . 'pickup.properties';

                if (PHP_OS_FAMILY === 'Windows') {
                    $wrapperBat = $tempDir . $sep . $uniqueId . '_run.bat';
                    file_put_contents($wrapperBat,
                        "@echo off\r\n"
                        . 'java -Dfile.encoding=Cp874'
                        . ' -Dpickup.properties="' . $props . '"'
                        . ' -cp "' . $jar1 . ';' . $jar2 . '"'
                        . ' com.scb.hashAllSys.validations.HashValidation'
                        . ' "' . $inputFile  . '"'
                        . ' "' . $outputFile . '"'
                        . ' "' . $pdfDir     . '"'
                        . "\r\n"
                    );
                    $cmd = '"' . $wrapperBat . '"';
                } else {
                    $cmd = 'java -Dfile.encoding=Cp874'
                         . ' -Dpickup.properties=' . escapeshellarg($props)
                         . ' -cp '                 . escapeshellarg($jar1 . ':' . $jar2)
                         . ' com.scb.hashAllSys.validations.HashValidation'
                         . ' '                     . escapeshellarg($inputFile)
                         . ' '                     . escapeshellarg($outputFile)
                         . ' '                     . escapeshellarg(rtrim($pdfDir, '/') . '/');
                }

                $cmdOutput  = [];
                $returnCode = 0;
                exec($cmd . ' 2>&1', $cmdOutput, $returnCode);

                if (PHP_OS_FAMILY === 'Windows' && isset($wrapperBat)) {
                    @unlink($wrapperBat);
                }

                Log::info('SCB Hash Module executed', [
                    'cmd'         => $cmd,
                    'return_code' => $returnCode,
                    'output'      => implode("\n", $cmdOutput),
                ]);

                if (!file_exists($outputFile)) {
                    $errorFile = $pdfDir . $sep . 'Error_' . basename($inputFile);
                    if (file_exists($errorFile)) {
                        $errRaw     = iconv('TIS-620', 'UTF-8//IGNORE', file_get_contents($errorFile)) ?: '';
                        $errLines   = array_slice(array_filter(explode("\n", $errRaw)), 0, 6);
                        $errDisplay = implode("\n", $errLines);
                        $msg = 'SCB Hash Module: Validation Failed<br>'
                            . '<pre style="font-size:0.7rem;max-height:6rem;overflow-y:auto;margin-top:0.4rem;white-space:pre-wrap">'
                            . htmlspecialchars($errDisplay)
                            . '</pre>';
                    } else {
                        $msg = 'Hash Module ไม่สามารถสร้างไฟล์ output ได้ (code: '
                            . $returnCode . ')<br>'
                            . implode('<br>', array_map('htmlspecialchars', array_slice($cmdOutput, 0, 5)));
                    }
                    $this->dispatch('scb-notify', type: 'error', html: $msg);
                    @unlink($inputFile);
                    return;
                }

                $this->dispatch('scb-notify', type: 'success', html: 'Hash สำเร็จ — ' . $downloadName . ' พร้อม Upload SCB Business Net');

            } else {
                // SCB_MODULE_DIR not configured → download plain Pay.txt for review
                $downloadFile = $inputFile;
                $downloadName = $this->company . '_Pay_' . $dateForFileName . '.txt';
                $this->dispatch('scb-notify', type: 'success', html: 'Dev mode: ดาวน์โหลด Pay.txt (ยังไม่ได้ตั้งค่า SCB_MODULE_DIR)');
            }

            $fileContent = file_get_contents($downloadFile);
            @unlink($inputFile);
            if ($downloadFile !== $inputFile) {
                @unlink($outputFile);
            }

            return response()->streamDownload(function () use ($fileContent) {
                echo $fileContent;
            }, $downloadName, [
                'Content-Type'        => 'text/plain; charset=tis-620',
                'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
            ]);

        } catch (Exception $e) {
            Log::error('SCB export error: ' . $e->getMessage());
            $this->dispatch('scb-notify', type: 'error', html: 'เกิดข้อผิดพลาดในการ Export: ' . $e->getMessage());
        }
    }

    private function generatePayTxt(array $rows): string
    {
        if (empty($rows)) {
            return '';
        }

        $lines       = [];
        $first       = $rows[0];
        $crCount     = count($rows);

        // Sum amounts (in satang = amount × 100)
        $totalSatang = 0;
        foreach ($rows as $row) {
            $totalSatang += (int) round((float) $row['check_amount'] * 100);
        }

        $totalAmountFmt = str_pad((string) $totalSatang, 16, '0', STR_PAD_LEFT);
        $crCountFmt     = str_pad((string) $crCount, 6, '0', STR_PAD_LEFT);

        // 001 — header (one per file): RT1(3) + COMPANY(12) + A3_6(81) = 96 chars
        $lines[] = '001' . $first['company'] . $first['a3_6'];

        // 002 — debit (one per file): RT2(3) + B2_7(45) + amount(16) + '00000001'(8) + items(6) + B11_15(31) = 109 chars
        $lines[] = '002' . $first['b2_7'] . $totalAmountFmt . '00000001' . $crCountFmt . $first['b11_15'];

        // 003 + 004 — one pair per vendor payment
        foreach ($rows as $i => $row) {
            $seq         = str_pad((string) ($i + 1), 6, '0', STR_PAD_LEFT);
            $amtSatang   = str_pad((string) ((int) round((float) $row['check_amount'] * 100)), 16, '0', STR_PAD_LEFT);

            // 003 — credit: RT3(3) + seq(6) + payee_account(25) + amount(16) + 'THB'(3) + '00000001'(8) + C7_31(224) + C32(68) + C34(2) = 355 chars
            $lines[] = '003' . $seq . $row['payee_account_no'] . $amtSatang . 'THB' . '00000001' . $row['c7_31'] . $row['c32_chq_desc'] . '  ';

            // 004 — detail: RT4(3) + '00000001'(8) + seq(6) + D4_5(799) = 816 chars
            $lines[] = '004' . '00000001' . $seq . $row['d4_5'];
        }

        // 999 — footer: RT9(3) + DR_count(6) + CR_count(6) + total_amount(16) = 31 chars
        $lines[] = '999' . '000001' . $crCountFmt . $totalAmountFmt;

        return implode("\r\n", $lines) . "\r\n";
    }

    /**
     * Try to initialize Oracle EBS session context so HZ VPD policies pass.
     * Silently fails — main query will then catch any remaining ORA-28110.
     */
    private function initOracleEBSContext(ConnectionInterface $db): bool
    {
        // fnd_responsibility itself triggers HZ_COMMON_PUB VPD (ORA-28110) —
        // do NOT query it. Query only fnd_user (no VPD policy), then call
        // APPS_INITIALIZE with resp_id=0 which is enough to set FND_GLOBAL.USER_ID
        // so HZ_COMMON_PUB policy function stops erroring.
        try {
            $userId = 0; // SYSADMIN default
            try {
                $users  = $db->select(
                    "SELECT user_id FROM fnd_user
                     WHERE user_name IN ('SYSADMIN','OPERATIONS')
                       AND (end_date IS NULL OR end_date > SYSDATE)
                       AND ROWNUM = 1"
                );
                $userId = (int) ($users[0]->user_id ?? 0);
            } catch (Exception $eu) {
                Log::warning('SCB: fnd_user query failed (using user_id=0): ' . $eu->getMessage());
            }

            $db->statement(
                "BEGIN FND_GLOBAL.APPS_INITIALIZE(:uid, 0, 200); END;",
                ['uid' => $userId]
            );

            Log::info('SCB: Oracle EBS context initialized', ['user_id' => $userId]);
            return true;

        } catch (Exception $e) {
            Log::warning('SCB: Oracle EBS context init failed: ' . $e->getMessage());
            return false;
        }
    }

    public function render()
    {
        return view('livewire.check-scb');
    }
}
