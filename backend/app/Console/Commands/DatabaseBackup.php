<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use CURLFile;
use Illuminate\Console\Command;

use Illuminate\Http\File;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;


class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */

    public function handle()
    {
        $startTime = microtime(true);

        /*
         * Backup database
         */
        $dbuser = env('DB_USERNAME');
        $dbpass = env('DB_PASSWORD');
        $dbname = env('DB_DATABASE');
        $dbhost = env('DB_HOST');

        $location = $this->backupLocation('database');

        $backup_file = date("Y-m-d") . '.sql';

        $command = "/usr/bin/mysqldump -h $dbhost -u " . $dbuser . " -p'" . $dbpass . "' " . $dbname . " > " . $location . "/" . $backup_file . ' --no-tablespaces';

        system($command);
        $file = Storage::get($location . "/" . $backup_file);
        dd($file);
        $this->uploadDbBackupToBucket($file);
//        Storage::disk('s3')->putFileAs('databases', new File(), $backup_file);

        @unlink($location . '/' . $backup_file);


        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $executionTime = number_format($executionTime / 60, 2);

        dd('daily backup completed in ( ' . $executionTime . ' minutes ) successfully');

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['example', InputArgument::OPTIONAL, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }

    public function backupLocation($dir = '')
    {
        $location = storage_path() . '/' . $dir;
        if (!file_exists($location)) {
            mkdir($location, 0755, true);
        }
        return $location;
    }

    public function uploadDbBackupToBucket($file)
    {

        $apiEndpoint = env('BUCKET_API') . 'storage/upload';
// Send the file content to the API
        $response = Http::post($apiEndpoint, [
            'file' => $file,
            'bucket' => env('BUCKET'),
        ]);

// Check the response from the API
        if ($response->successful()) {
            return response()->json(['message' => 'File sent successfully', 'data' => $response->json()]);
        } else {
            return response()->json(['error' => 'Failed to send file', 'status' => $response->status()], $response->status());
        }

    }


}
