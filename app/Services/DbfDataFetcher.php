<?php

namespace App\Services;
use League\Flysystem\Filesystem;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;
use XBase\TableReader;
use App\Models\Apmas;
use App\Models\Aprcpit;
use App\Models\Bktrn;
use App\Models\Aptrn;

class DbfDataFetcher
{
     protected $filesystem;

    public function __construct()
    {
        $adapter = new FtpAdapter(FtpConnectionOptions::fromArray([
            'host' => env('FTP_HOST'),
            'username' => env('FTP_USERNAME'),
            'password' => env('FTP_PASSWORD'),
            'root' => env('FTP_ROOT'),
            'port' => (int) env('FTP_PORT', 21),
            'passive' => filter_var(env('FTP_PASSIVE', true), FILTER_VALIDATE_BOOLEAN),
            'ssl' => filter_var(env('FTP_SSL', false), FILTER_VALIDATE_BOOLEAN),
            'timeout' => (int) env('FTP_TIMEOUT', 30),
            ])
        );

        $this->filesystem = new Filesystem($adapter);
    }

    public function fetchDataFromDbf($filePath)
    {
        $remoteFilePath = $filePath;
        $stream = $this->filesystem->readStream($remoteFilePath);
        $localFilePath = storage_path('app/temp/' . $filePath);

        if ($stream === false) {
            throw new \Exception("Failed to download the DBF file: $filePath");
        }

        file_put_contents($localFilePath, stream_get_contents($stream));
        fclose($stream);

        $table = new TableReader($localFilePath);
        $data = [];
        $columns = [];

        foreach ($table->getColumns() as $column) {
            $columns[] = $column->getName();
        }

        while ($record = $table->nextRecord()) {
            if ($record->isDeleted()) {
                continue;
            }

            $row = [];
            foreach ($columns as $column) {
                $rawText = $record->get($column);
                $row[$column] = iconv('Windows-874', 'UTF-8//IGNORE', $rawText);
            }
            $data[] = $row;
        }

        $table->close();
        unlink($localFilePath);

        return $data;
    }

    public function fetchDataFromDbfFiles()
    {
        $filePaths = ['apmas.dbf', 'aprcpit.dbf', 'bktrn.dbf', 'aptrn.dbf'];
        $dataCollection = [];

        foreach ($filePaths as $filePath) {
            $dataCollection[$filePath] = $this->fetchDataFromDbf($filePath);
        }

        return $dataCollection;
    }

    public function syncData(array $data)
    {
        $this->syncTable(Apmas::class , $data['apmas.dbf'],['supcod']);
        $this->syncTable(Aprcpit::class, $data['aprcpit.dbf'], ['rcpnum', 'docnum']);
        $this->syncTable(Bktrn::class, $data['bktrn.dbf'], ['chqnum']);
        $this->syncTable(Aptrn::class, $data['aptrn.dbf'], ['refnum']);
    }
    
    protected function syncTable($modelClass, array $data, array $uniqueKeys)
    {
        // Truncate the table
        $modelClass::truncate();

        // Insert data into the table
        foreach ($data as $record) {
            $modelClass::create($record);
        }
    }
}