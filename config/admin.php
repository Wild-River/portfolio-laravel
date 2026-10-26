<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Administrator Account
    |--------------------------------------------------------------------------
    |
    | AdminUserSeeder uses these values to create the single admin user.
    | Set ADMIN_EMAIL and ADMIN_PASSWORD in .env before running the seeder.
    |
    */

    'name' => env('ADMIN_NAME', 'admin'),

    'email' => env('ADMIN_EMAIL'),

    'password' => env('ADMIN_PASSWORD'),

];
