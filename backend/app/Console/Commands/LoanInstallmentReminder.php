<?php

namespace App\Console\Commands;

use App\Models\ApplicationNotification;
use App\Models\Applications;
use App\Wrappers\MailWrapper;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LoanInstallmentReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remind';

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
        $today= Carbon::now();


        Applications::where('status', 'ACTIVE')->get()->map(function ($application) use($today) {
            try{
                DB::beginTransaction();
                $app_Date=Carbon::parse($application->start_date);
                $diff=$app_Date->format('d')-$today->format('d');

//                $newDate = $today->addDays(abs($diff));
//                $isNextMonth = $newDate->month > $currentDate->month;
//
//                if ($isNextMonth) {
//                    echo 'The new date is in the next month.';
//                }
                if(abs($diff)<=7){
                    $month=$today->format('M');
                    $exists=ApplicationNotification::where('month',$month)->where('application_id',$application->id)->first();
                    if(!$exists) {
                        MailWrapper::installmentReminder($application, abs($diff));
                        ApplicationNotification::create([
                            'application_id'=>$application->id,
                            'month'=>$month
                        ]);
                    }
                }
                DB::commit();
            }catch (\Exception $e){
//                dd($e->getMessage());
                DB::rollback();
            }
        });
    }
}
