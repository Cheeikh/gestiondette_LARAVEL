<?php

namespace App\Interfaces;

interface SmsServiceInterface
{
    public function sendSms($to, $message);
}
