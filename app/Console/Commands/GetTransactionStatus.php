<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use TenantSync\Models\Device;
use TenantSync\Models\AutoPayment;
use TenantSync\Models\Transaction;
use Mail;

class GetTransactionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:GetTransactionStatus {incomingTransaction}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will print out the status of a transaction';

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
        $transaction=Transaction::where('reference_number',$this->argument('incomingTransaction'))->first();
        $device=Device::find($transaction->payable_id);
        $response = $device->getTransactionStatus($this->argument('incomingTransaction'));

        echo "hello Mike\n";

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
        echo "\nGetTransactionStatus Complete!\n";
        return;
    }
}