<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@addweb.com',
                'type' => 1,
                'password' => Hash::make('addweb@123'),
                'contact' => '9821348989',
                'address' => 'mansarovar',
                'city' => 'jaipur',
                'state' => 'rajasthan',
                'country' => 'india',
                'postal_code' => '302020',
                'username' => 'admin',
                'status' => '1',
                'longitude' => '4324',
                'latitude' => '4324'
            ],
            [
                'name' => 'Agent',
                'email' => 'agent@addweb.com',
                'type' => 2,
                'password' => Hash::make('addweb@123'),
                'contact' => '9821348989',
                'address' => 'mansarovar',
                'city' => 'jaipur',
                'state' => 'rajasthan',
                'country' => 'india',
                'postal_code' => '302020',
                'username' => 'admin',
                'status' => '1',
                'longitude' => '4324',
                'latitude' => '4324'
            ],
            [
                'name' => 'User',
                'email' => 'user@addweb.com',
                'type' => 0,
                'password' => Hash::make('addweb@123'),
                'contact' => '9821348989',
                'address' => 'mansarovar',
                'city' => 'jaipur',
                'state' => 'rajasthan',
                'country' => 'india',
                'postal_code' => '302020',
                'username' => 'admin',
                'status' => '1',
                'longitude' => '4324',
                'latitude' => '4324'
            ],
        ];

        foreach ($users as $key => $user) {
            User::create($user);
        }
    }
}
