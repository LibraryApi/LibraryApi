<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Mail\AdminNotificationEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendAdminNotification
{
    public function handle(UserRegistered $event)
    {
        Mail::to(env('MAIL_USERNAME'))->send(new AdminNotificationEmail($event->user));
    }
}
