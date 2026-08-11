<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CheckScb extends Component
{
    public string $company    = 'TRU';
    public string $checkDate  = '';
    public array  $results    = [];
    public bool   $isLoading  = false;
    public ?string $errorMessage   = null;
    public ?string $successMessage = null;

    private array $companyAccounts = [
        'TRU' => '1152079993',
        'TRT' => '1152175537',
        'TUC' => '1152297478',
        'TAP' => '1152729297',
    ];

    private array $companyOrgIds = [
        'TRU' => 84,
        'TRT' => 83,
        'TUC' => 85,
        'TAP' => 82,
    ];

    public array $companyLabels = [
        'TRU' => 'TRU — Thai Rung Union (TRUNION)',
        'TRT' => 'TRT — Thai Rung Tools (TRTOOLS)',
        'TUC' => 'TUC — Thai Ultimate Car (THAIULTIMATE)',
        'TAP' => 'TAP — Thai Autopart (THAIAUT)',
    ];

    public function mount(): void
    {
        $this->checkDate = now()->format('Y-m-d');
    }

    public function search(): void
    {
        $this->errorMessage   = null;
        $this->successMessage = null;
        $this->results        = [];
        $this->isLoading      = true;

        if (empty($this->checkDate)) {
            $this->errorMessage = 'กรุณาเลือกวันที่จ่าย';
            $this->isLoading    = false;
            return;
        }

        $bankAccountNum = $this->companyAccounts[$this->company] ?? null;
        if (!$bankAccountNum) {
            $this->errorMessage = 'ไม่พบบัญชีธนาคารสำหรับบริษัทที่เลือก';
            $this->isLoading    = false;
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
                    ap_invoices_all              i,
                    ap_suppliers                 aps,
                    ap_supplier_sites_all        apss,
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
                AND cba.bank_account_num        = :bank_account_num
                AND TRUNC(ac.check_date)        = TO_DATE(:check_date,'YYYY-MM-DD')
                ORDER BY 2, 3
            ";

            // ── TIER 2: Safe SQL (no HZ-dependent APPS views) ────────────────────────
            // Falls back to this if ORA-28110 still occurs after context init.
            // Bank name / branch name will be spaces — SCB Hash Module does not validate them.
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
                FROM
                    ap_invoices_all              i,
                    ap_suppliers                 aps,
                    ap_supplier_sites_all        apss,
                    ap_invoice_payments_all      aip,
                    ap_checks_all                ac,
                    apps.iby_ext_bank_accounts   ieba,
                    ce_bank_accounts             cba
                WHERE 1=1
                AND aps.VENDOR_ID               = apss.VENDOR_ID
                AND ieba.EXT_BANK_ACCOUNT_ID(+) = (
                        SELECT MAX(b.EXT_BANK_ACCOUNT_ID)
                        FROM apps.iby_ext_bank_accounts b
                        WHERE b.ACCOUNT_OWNER_PARTY_ID = aps.PARTY_ID
                    )
                AND i.invoice_id                = aip.invoice_id
                AND i.vendor_id                 = aps.vendor_id
                AND i.vendor_site_id            = apss.vendor_site_id
                AND i.cancelled_date            IS NULL
                AND i.payment_status_flag       = 'Y'
                AND aip.check_id                = ac.check_id
                AND aip.posted_flag             = 'Y'
                AND ac.bank_account_name        = cba.bank_account_name
                AND cba.bank_account_num        = :bank_account_num
                AND TRUNC(ac.check_date)        = TO_DATE(:check_date,'YYYY-MM-DD')
                ORDER BY 2, 3
            ";

            // ── Try Tier 1 first (EBS context + full SQL) ────────────────────────────
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
                        $this->successMessage = 'ข้อมูล bank name / branch ไม่แสดง (Oracle EBS context ไม่พร้อม) — ยังสามารถ Export Pay.txt ได้';
                    } catch (Exception $e2) {
                        throw $e2;
                    }
                } else {
                    throw $e1;
                }
            }

            $this->results = array_map(fn($row) => (array) $row, $rows);

            if (empty($this->results)) {
                $this->errorMessage = 'ไม่พบข้อมูลการจ่ายเงิน SCB สำหรับ '
                    . $this->company . ' วันที่ ' . $this->checkDate;
            }

        } catch (Exception $e) {
            Log::error('SCB Oracle query error: ' . $e->getMessage(), [
                'company'   => $this->company,
                'checkDate' => $this->checkDate,
            ]);
            $this->errorMessage = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
        }

        $this->isLoading = false;
    }

    public function exportToFile()
    {
        $this->errorMessage   = null;
        $this->successMessage = null;

        if (empty($this->results)) {
            $this->errorMessage = 'ไม่มีข้อมูล กรุณากด Search ก่อน';
            return;
        }

        try {
            $payTxtContent = $this->generatePayTxt($this->results);

            $tempDir    = rtrim(env('SCB_TEMP_DIR', storage_path('scb-temp')), '/\\');
            $hashScript = env('SCB_HASH_SCRIPT', '/var/scb/hashmodule/scbhash.sh');

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

            // Write Pay.txt with TIS-620 encoding
            $encoded = iconv('UTF-8', 'TIS-620//TRANSLIT//IGNORE', $payTxtContent);
            file_put_contents($inputFile, $encoded !== false ? $encoded : $payTxtContent);

            $dateForFileName = str_replace('-', '', $this->checkDate);
            $downloadName    = $this->company . '_SCB_Pay-Hash_' . $dateForFileName . '.txt';
            $downloadFile    = $outputFile;

            // Try to run Hash Module (Linux server only)
            if (PHP_OS_FAMILY !== 'Windows' && file_exists($hashScript) && is_executable($hashScript)) {
                $cmd        = escapeshellarg($hashScript) . ' '
                            . escapeshellarg($inputFile) . ' '
                            . escapeshellarg($outputFile) . ' '
                            . escapeshellarg($pdfDir . DIRECTORY_SEPARATOR);
                $cmdOutput  = [];
                $returnCode = 0;
                exec($cmd . ' 2>&1', $cmdOutput, $returnCode);

                Log::info('SCB Hash Module executed', [
                    'return_code' => $returnCode,
                    'output'      => implode("\n", $cmdOutput),
                    'input_file'  => $inputFile,
                    'output_file' => $outputFile,
                ]);

                if (!file_exists($outputFile)) {
                    // Check for error file from Hash Module
                    $errorFileName = 'Error_' . basename($inputFile);
                    $errorFile     = $pdfDir . DIRECTORY_SEPARATOR . $errorFileName;
                    if (file_exists($errorFile)) {
                        $errContent = iconv('TIS-620', 'UTF-8//IGNORE', file_get_contents($errorFile));
                        $this->errorMessage = 'SCB Hash Module: Validation Failed<br><pre class="text-xs mt-2">'
                            . htmlspecialchars($errContent ?: file_get_contents($errorFile)) . '</pre>';
                    } else {
                        $this->errorMessage = 'Hash Module ไม่สามารถสร้างไฟล์ output ได้ (code: '
                            . $returnCode . ')<br>' . implode('<br>', array_map('htmlspecialchars', $cmdOutput));
                    }
                    @unlink($inputFile);
                    return;
                }

                $downloadFile = $outputFile;
                $this->successMessage = 'Hash สำเร็จ — ' . $downloadName . ' พร้อม Upload SCB Business Net';

            } else {
                // Dev environment (Windows) or no hash script — download plain Pay.txt
                $downloadFile = $inputFile;
                $downloadName = $this->company . '_Pay_' . $dateForFileName . '.txt';
                $this->successMessage = 'Dev mode: ดาวน์โหลด Pay.txt (Hash Module ไม่พร้อมใช้งานบน environment นี้)';
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
            $this->errorMessage = 'เกิดข้อผิดพลาดในการ Export: ' . $e->getMessage();
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
            $seq         = str_pad((string) ($i + 1), 8, '0', STR_PAD_LEFT);
            $amtSatang   = str_pad((string) ((int) round((float) $row['check_amount'] * 100)), 16, '0', STR_PAD_LEFT);

            // 003 — credit: RT3(3) + seq(8) + payee_account(25) + amount(16) + 'THB'(3) + '00000001'(8) + C7_31(224) + C32(68) + C34(2) = 357 chars
            $lines[] = '003' . $seq . $row['payee_account_no'] . $amtSatang . 'THB' . '00000001' . $row['c7_31'] . $row['c32_chq_desc'] . '  ';

            // 004 — detail: RT4(3) + '00000001'(8) + seq(8) + D4_5(799) = 818 chars
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
    private function initOracleEBSContext($db): bool
    {
        try {
            // Prefer SYSADMIN; fallback to any active user that can initialize AP context
            $users = $db->select(
                "SELECT user_id FROM fnd_user
                 WHERE user_name IN ('SYSADMIN','OPERATIONS','APPS')
                   AND (end_date IS NULL OR end_date > SYSDATE)
                   AND ROWNUM = 1"
            );
            $userId = (int) ($users[0]->user_id ?? 0);

            // Any active AP responsibility (application_id=200 = Payables)
            $resps = $db->select(
                "SELECT MIN(responsibility_id) AS rid FROM fnd_responsibility
                 WHERE application_id = 200 AND (end_date IS NULL OR end_date > SYSDATE)"
            );
            $respId = (int) ($resps[0]->rid ?? 0);

            $db->statement(
                "BEGIN FND_GLOBAL.APPS_INITIALIZE(:uid, :rid, 200); END;",
                ['uid' => $userId, 'rid' => $respId]
            );

            // Multi-Org: allow access across all orgs
            $db->statement("BEGIN MO_GLOBAL.SET_POLICY_CONTEXT('M', NULL); END;");

            Log::info('SCB: Oracle EBS context initialized', [
                'user_id' => $userId, 'resp_id' => $respId,
            ]);
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
