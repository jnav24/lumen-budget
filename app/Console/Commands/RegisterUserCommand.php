<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class RegisterUserCommand extends Command
{
    protected $signature = 'create:user {--F|first=} {--L|last=} {--E|email=} {--P|password=}';
    protected $description = 'Creates a user in the DB';

    public function handle()
    {
        try {
            DB::transaction(function () {
                $this->createUser();
            });
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    public function createUser() {
        $faker = Faker::create();

        if (empty($this->option('email'))) {
            $this->error('Email is required');
            exit;
        }

        if (empty($this->option('password'))) {
            $this->error('Password is required');
            exit;
        }

        if (User::where('username', $this->option('email'))->exists()) {
            $this->error('User already exists');
            exit;
        }

        $user = new User();
        $profile = new UserProfile();

        $user->username = $this->option('email');
        $user->password = app('hash')->make($this->option('password'));
        $user->save();

        $profile->user_id = $user->id;
        $profile->image = '';
        $profile->first_name = $this->option('first') ?? $faker->firstName;
        $profile->last_name = $this->option('last') ?? $faker->lastName;
        $profile->save();

        $this->info('User created');
    }
}