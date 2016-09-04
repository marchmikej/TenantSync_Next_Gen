<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use TenantSync\Models\Device;
use TenantSync\Models\AutoPayment;
use TenantSync\Models\Transaction;
use Mail;

class AutoPaymentsGetNew extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:AutoPaymentsGetNew';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull in autopayments not yet in database';

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
        $autoPayments=AutoPayment::where('num_payments','<>',0)->get();
        foreach($autoPayments as $autoPayment) {
            echo "AutoPaymentsGetNew checking " . $autoPayment->id . "\n";
            $device=Device::find($autoPayment->device_id);

            $numLeft = $device->getCustomer($autoPayment->customer_number);
            echo "Payment remaining: " . $numLeft->NumLeft . "\n";

            $autoPayment->num_payments=$numLeft->NumLeft;
            $autoPayment->save();

            $response = $device->getCustomerHistory($autoPayment->customer_number);

            $transactionSearchResult = array(
                'TransactionsMatched' => $response->TransactionsMatched, 
                'TransactionsReturned' => $response->TransactionsReturned, 
                'ErrorsCount' => $response->ErrorsCount,
                'DeclinesCount' => $response->DeclinesCount,
                'SalesCount' => $response->SalesCount,
                'CreditsCount' => $response->CreditsCount,
                'AuthOnlyCount' => $response->AuthOnlyCount,
                'VoidsCount' => $response->VoidsCount,
                'SalesAmount' => $response->SalesAmount); 

            echo "Sales Count: " . $response->SalesCount . "\n";

            //return $response->Transactions;
            foreach($response->Transactions as $transaction) {
                $currentTransaction = Transaction::where('reference_number',$transaction->Response->RefNum)->first();
                echo 'RefNum: ' . $transaction->Response->RefNum . "\n";
                //If count==0 then transaction is not in database.  If greater than 0 move onto next transaction
                if(count($currentTransaction)==0) {
                    $transactionResult = array(
                        'user_id' => $device->user_id,
                        'amount' => $autoPayment->amount,
                        'description' => $autoPayment->description,
                        'reference_number' => $transaction->Response->RefNum, 
                        'date' => $transaction->DateTime,
                        'payable_type' => "device",
                        'payable_id' => $device->id,
                        'payment_from_id' => $autoPayment->user_id,
                        'payment_from_source' => 0,
                        'status' => $transaction->Response->Status, 
                        'payment_type' => $autoPayment->payment_type,
                        'transaction_fee' => $autoPayment->transaction_fee,
                        'auto_payment_id' => $autoPayment->id,
                        'propery_id' => $device->property_id,
                        'company_id' => $device->getCompany(),
                        //'Result' => $transaction->Response->Result,
                        //'ErrorCode' => $transaction->Response->ErrorCode,
                        //'Error' => $transaction->Response->Error,
                        //'ResponseStatus' => $transaction->Response->Status
                        //'Amount' => $transaction->Details->Amount,
                    ); 
                    $createTransaction=Transaction::create($transactionResult);
                    $emailTransaction=array($createTransaction);
                    $user=$autoPayment->user();
                    Mail::send('emails.propertyreceipt', ['transactions' => $emailTransaction], function ($m) use ($user) {
                        $m->from(env('SEND_EMAIL', 'admin@tenantsyncdev.com'), 'TenantSync');

                        $m->to($user->email)->subject('Payment Received');
                    });
                }
            }
        }
        echo "AutoPaymentsGetNew Complete\n";
        return;
    }
}