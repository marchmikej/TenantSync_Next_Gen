<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use TenantSync\Models\User;
use TenantSync\Models\Transaction;
use Mail;
use Carbon\Carbon;

class SendLandlordDailyTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:SendLandlordDailyTransactions {incomingDate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send out daily email showing previous days transactions or given dates transactions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $incomingDate = $this->argument('incomingDate');

        if($incomingDate=="yesterday") {
            $incomingDate = Carbon::yesterday()->toDateString();
            echo $incomingDate;
            echo "\n";
        }
        $landlords=User::where('company_id','>',0)->get();
        foreach($landlords as $landlord) {
            $transactions=Transaction::where('date',$incomingDate)->where('company_id',$landlord->company_id)->get();
            echo "SendLandlordDailyTransactions checking " . $landlord->id . "\n";
            echo "Number of transactions: " . count($transactions)  . "\n";
            if(count($transactions)>0) {
                Mail::send('emails.landlordpayments', ['transactions' => $transactions], function ($m) use ($landlord,$incomingDate) {
                    $m->from(env('SEND_EMAIL', 'admin@tenantsyncdev.com'), 'TenantSync');
                    $m->to($landlord->email)->subject('Payments Received ' . $incomingDate);
                });
            }
        }
        echo "SendLandlordDailyTransactions Complete\n";
        return;
    }
}