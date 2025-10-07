<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use League\Flysystem\Filesystem;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;
use XBase\TableReader;

class DbfController extends Controller
{
    public function index(){

        $adapter = new FtpAdapter(
            FtpConnectionOptions::fromArray([
                'host' => '129.200.10.5', // FTP server address
                'username' => 'admin', // FTP username
                'password' => 'thairungall', // FTP password
                'root' => '/TVS67', // Path on the FTP server to the DBF directory
                'port' => 21, // FTP port (default is 21)
                'passive' => true, // Use passive mode
                'ssl' => false, // Use SSL if needed
                'timeout' => 30, // Timeout in seconds
            ])
        );

        $filesystem = new Filesystem($adapter);

       

        // Remote path to the DBF file on the FTP server
        $remoteFilePath = 'bktrn.dbf';

        // Download the DBF file
        $stream = $filesystem->readStream($remoteFilePath);
         // Local path to save the downloaded DBF file
        $filePath = storage_path('app/temp/bktrn.dbf');
        if ($stream === false) {
            return response()->json(['error' => 'Failed to download the DBF file'], 500);
        }

        // Save the file locally
        file_put_contents($filePath, stream_get_contents($stream));
        fclose($stream);

        // Open the DBF file
        $table = new TableReader($filePath);

        // Initialize an array to hold the data
        $data = [];
        $columns = [];

        // Fetch column names
        foreach ($table->getColumns() as $column) {
            $columns[] = $column->getName();
        }
        // Fetch all records
        while ($record = $table->nextRecord()) {
            if ($record->isDeleted()) {
                continue; // Skip deleted records
            }

            $row = [];
            foreach ($columns as $column) {
                $rawText = $record->get($column);
                $row[$column] = iconv('Windows-874', 'UTF-8//IGNORE', $rawText);
                // $row[$column] = iconv('CP850', 'UTF-8', $record->get($column));
            }
            $data[] = $row;
        }

        // Close the table
        $table->close();

        // Optionally, delete the local file after processing
        unlink($filePath);

        // Print debug information
        // error_log('Columns: ' . json_encode($columns));
        // error_log('Data: ' . json_encode($data));

        // Return the data as a JSON response
        return response()->json([
            'columns' => $columns,
            'data' => $data
        ]);
    }
}