<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MirrorDatabase extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mirror';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mirror the remote database to the local database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {



        $this->dbBackup();
        return 0;
    }

    function dbBackup()
    {
        for ($i = 1; $i <=10; $i++){
             $this->info("Mirroring remote database to local database...$i");

        $remoteConnection = DB::connection('remote');
        $tables = $remoteConnection->getDoctrineSchemaManager()->listTableNames();
//        $migrationIds = [];
//        foreach ($tables as $table) {
//            $migrationId = $remoteConnection->table($tables[13])
//                ->where('migration', 'like', '%_create_' . $table . '_table%')
//                ->value('id');
//            $migrationIds[$table] = $migrationId;
//        }
//        asort($migrationIds);
//        $reorderedTables = array_keys($migrationIds);

        $this->populateTable($remoteConnection,$tables);
        $this->info('Database mirroring complete.');
        }

    }

    function populateTable($con,$tables)
    {
        $failedTables = [];

        foreach ($tables as $table) {
            $records = $con->table($table)->get();

            foreach ($records as $record) {
                try {
                    if($table!='migrationsss'||$table!='audits'){
                        $record = json_decode(json_encode($record), true);
                        DB::table($table)->insert($record);
                        $this->info("Mirrored table: {$table}");
                    }
                } catch (\Exception $e) {
                    if (!in_array($table, $failedTables )and stripos($e->getMessage(),'23000')== false) {
                        $failedTables[] = $table;
                        $this->error("Table Has Error: {$table}");
                        $this->error($e->getMessage());
                        break; // Stop processing the current table if an error occurs
                    }

                }
            }

        }

        if (!empty($failedTables)) {
            $this->populateTable($con, $failedTables);
        }
    }
}
