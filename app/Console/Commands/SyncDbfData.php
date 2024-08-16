<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DbfDataFetcher;
class SyncDbfData extends Command
{
   protected $signature = 'dbf:sync';
    protected $description = 'Sync data from DBF files to MySQL database';

    protected $dbfDataService;

    public function __construct(DbfDataFetcher $dbfDataService)
    {
        parent::__construct();
        $this->dbfDataService = $dbfDataService;
    }

    public function handle()
    {
        // Fetch data from DBF files (similar to previous logic)
        $dataCollection = $this->dbfDataService->fetchDataFromDbfFiles();

        // Insert or update data
        $this->dbfDataService->syncData($dataCollection);

        $this->info('Data synchronized successfully.');
    } 
}
