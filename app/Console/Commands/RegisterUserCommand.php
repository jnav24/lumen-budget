<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RegisterUserCommand extends Command
{
    protected $signature = 'create:user {--F|first=} {--L|last=} {--E|email=} {--P|password=}';
    protected $description = 'Creates a user in the DB';

    public function handle()
    {}
}