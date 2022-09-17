<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\UserRoleType;

class UserRoleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        UserRoleType::truncate();
        $records = [
            [
                'id'                    => '1',
                'user_role_name'        => 'Webmaster',
                'user_role_description' => 'A user with access to the website network administration features and all other features that are included for the rest of the user role types (administrator, author, editor, contributor and subscriber).',
                'user_role_slug'        => 'webmaster',
                'user_role_is_active'   => '1',
                'created_at'            => '2021-09-01 00:00:00',
                'updated_at'            => '2021-09-01 00:00:00',
            ],
            [
                'id'                    => '2',
                'user_role_name'        => 'Administrator',
                'user_role_description' => 'A user with access to all the administration features withing a single website and all other features that are included for the rest of the user role types (author, editor, contributor and subscriber).',
                'user_role_slug'        => 'administrator',
                'user_role_is_active'   => '1',
                'created_at'            => '2021-09-01 00:00:00',
                'updated_at'            => '2021-09-01 00:00:00',
            ],
            [
                'id'                    => '3',
                'user_role_name'        => 'Author',
                'user_role_description' => 'A user who can publish and manage their own posts and content.',
                'user_role_slug'        => 'author',
                'user_role_is_active'   => '1',
                'created_at'            => '2021-09-01 00:00:00',
                'updated_at'            => '2021-09-01 00:00:00',
            ],
            [
                'id'                    => '4',
                'user_role_name'        => 'Editor',
                'user_role_description' => 'A user with access to the website network administration features and all other features included for the rest of the users.',
                'user_role_slug'        => 'editor',
                'user_role_is_active'   => '1',
                'created_at'            => '2021-09-01 00:00:00',
                'updated_at'            => '2021-09-01 00:00:00',
            ],
            [
                'id'                    => '5',
                'user_role_name'        => 'Contributor',
                'user_role_description' => 'A user with access to the website network administration features and all other features included for the rest of the users.',
                'user_role_slug'        => 'contributor',
                'user_role_is_active'   => '1',
                'created_at'            => '2021-09-01 00:00:00',
                'updated_at'            => '2021-09-01 00:00:00',
            ],
            [
                'id'                    => '6',
                'user_role_name'        => 'Subscriber',
                'user_role_description' => 'A user with access to the website network administration features and all other features included for the rest of the users.',
                'user_role_slug'        => 'subscriber',
                'user_role_is_active'   => '1',
                'created_at'            => '2021-09-01 00:00:00',
                'updated_at'            => '2021-09-01 00:00:00',
            ],
        ];
        UserRoleType::insert($records);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
