<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        User::truncate();
        $records = [
            [
                'id'                => '1',
                'name'              => 'Webmaster',
                'nickname'          => 'webmaster',
                'email'             => 'webmaster@localhost.com',
                'email_verified_at' => '2021-10-01 00:00:00',
                'user_role_type_id' => '1',
                'password'          => bcrypt('123@UserWebmaster'),
            ],
            [
                'id'                => '2',
                'name'              => 'Administrator',
                'nickname'          => 'administrator',
                'email'             => 'administrator@localhost.com',
                'email_verified_at' => '2021-10-01 00:00:00',
                'user_role_type_id' => '2',
                'password'          => bcrypt('123@UserAdministrator'),
            ],
            [
                'id'                => '3',
                'name'              => 'Author',
                'nickname'          => 'author',
                'email'             => 'author@localhost.com',
                'email_verified_at' => '2021-10-01 00:00:00',
                'user_role_type_id' => '3',
                'password'          => bcrypt('123@UserAuthor'),
            ],
            [
                'id'                => '4',
                'name'              => 'Editor',
                'nickname'          => 'editor',
                'email'             => 'editor@localhost.com',
                'email_verified_at' => '2021-10-01 00:00:00',
                'user_role_type_id' => '4',
                'password'          => bcrypt('123@UserEditor'),
            ],
            [
                'id'                => '5',
                'name'              => 'Contributor',
                'nickname'          => 'contributor',
                'email'             => 'contributor@localhost.com',
                'email_verified_at' => '2021-10-01 00:00:00',
                'user_role_type_id' => '5',
                'password'          => bcrypt('123@UserContributor'),
            ],
            [
                'id'                => '6',
                'name'              => 'Subscriber',
                'nickname'          => 'subscriber',
                'email'             => 'subscriber@localhost.com',
                'email_verified_at' => '2021-10-01 00:00:00',
                'user_role_type_id' => '6',
                'password'          => bcrypt('123@UserSubscriber'),
            ],
        ];
        User::insert($records);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
