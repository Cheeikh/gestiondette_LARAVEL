<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendPaymentRemindersJob;
use App\Interfaces\SmsServiceInterface;

class SendPaymentRemindersCommand extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Send payment reminders to clients with overdue debts.';
    protected $smsService;

    // Inject the SMS service
    public function __construct(SmsServiceInterface $smsService)
    {
        parent::__construct();
        $this->smsService = $smsService;
    }

    public function handle()
    {
        dispatch(new SendPaymentRemindersJob($this->smsService));
        $this->info('Payment reminders have been sent.');
    }
}
