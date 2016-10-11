<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use TenantSync\Models\Device;
use TenantSync\Models\Company;
use TenantSync\Models\User;
use TenantSync\Models\AutoPayment;
use TenantSync\Models\Transaction;
use Mail;

class UpdatePendingTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:UpdatePendingTransactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will check for updated status on pending transactions.';

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
        $companies = Company::get();
        foreach ($companies as $company) {
            $updatedTransactions = array();
            $transactions=Transaction::where('company_id',$company->id)->where('status','Pending')->get();
            foreach($transactions as $transaction) {
                $device=Device::find($transaction->payable_id);
                $response = $device->getTransactionStatus($transaction->reference_number);

                echo "\nUpdatePendingTransactions Starting\n";

                $paymentResponse = array(
                    'RefNum' => $response->RefNum, 
                    'Error' => $response->Error, 
                    'ErrorCode' => $response->ErrorCode,
                    'AuthCode' => $response->AuthCode,
                    'AuthAmount' => $response->AuthAmount,
                    'Status' => $response->Status,
                    'Result' => $response->Result,
                    ); 
                echo "----------------------------------\n";
                echo "RefNum: " . $response->RefNum . "\n";
                echo "Error: " . $response->Error . "\n";
                echo "ErrorCode: " . $paymentResponse['ErrorCode'] . "\n";
                echo "AuthCode: " . $paymentResponse['AuthCode'] . "\n";
                echo "AuthAmount: " . $paymentResponse['AuthAmount'] . "\n";
                echo "Status: " . $paymentResponse['Status'] . "\n";
                echo "Result: " . $paymentResponse['Result'] . "\n";
                echo "----------------------------------\n";

                if($response->Status == 'Settled') {
                    echo $transaction->reference_number . "is now" .  $response->Status . "\n";
                    $transaction->status = $response->Status;
                    $transaction->save();
                    array_push($updatedTransactions,$transaction);
                } else {
                    echo $transaction->reference_number . " is still Pending.\n";
                }
                echo "UpdatePendingTransactions Complete!\n";
            }
            $landlords=User::where('company_id',$company->id)->get();
            foreach($landlords as $landlord) {
                echo "UpdatePendingTransactions checking " . $landlord->id . "\n";
                echo "Number of transactions: " . count($updatedTransactions)  . "\n";
                if(count($updatedTransactions)>0) {
                    Mail::send('emails.landlordpayments', ['transactions' => $updatedTransactions], function ($m) use ($landlord) {
                        $m->from(env('SEND_EMAIL', 'admin@tenantsyncdev.com'), 'TenantSync');
                        $m->to($landlord->email)->subject('Payments Received');
                    });
                }
            }
        }
        return;
    }
}