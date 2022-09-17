<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\ErrorAndNotificationSystem;

class ErrorAndNotificationSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ErrorAndNotificationSystem::truncate();
        $records = [
            [
                'id'                       => '1',
                'notify_code'              => 'INFO_00001',
                'notify_short_description' => 'The record(s) was(were) successfully fetched from the database!',
                'notify_reference'         => config('app.url') . '/documentation/information#INFO_00001',
                'created_at'               => '2021-09-26 00:00:00',
                'updated_at'               => '2021-09-26 00:00:00',
            ],
            [
                'id'                       => '2',
                'notify_code'              => 'INFO_00002',
                'notify_short_description' => 'The record(s) was (were) successfully inserted in the database!',
                'notify_reference'         => config('app.url') . '/documentation/information#INFO_00002',
                'created_at'               => '2021-09-26 00:00:00',
                'updated_at'               => '2021-09-26 00:00:00',
            ],
            [
                'id'                       => '3',
                'notify_code'              => 'INFO_00003',
                'notify_short_description' => 'The record(s) you have selected was (were) successfully deleted from the database!',
                'notify_reference'         => config('app.url') . '/documentation/information#INFO_00003',
                'created_at'               => '2021-09-26 00:00:00',
                'updated_at'               => '2021-09-26 00:00:00',
            ],
            [
                'id'                       => '4',
                'notify_code'              => 'INFO_00004',
                'notify_short_description' => 'The records were successfully deleted from the database!',
                'notify_reference'         => config('app.url') . '/documentation/information#INFO_00004',
                'created_at'               => '2021-09-26 00:00:00',
                'updated_at'               => '2021-09-26 00:00:00',
            ],
            [
                'id'                       => '5',
                'notify_code'              => 'INFO_00005',
                'notify_short_description' => 'The record(s) you have selected was (were) successfully updated in the database!',
                'notify_reference'         => config('app.url') . '/documentation/information#INFO_00005',
                'created_at'               => '2021-09-26 00:00:00',
                'updated_at'               => '2021-09-26 00:00:00',
            ],
            [
                'id'                       => '6',
                'notify_code'              => 'INFO_00006',
                'notify_short_description' => 'The record(s) you are looking for does not exist in the database!',
                'notify_reference'         => config('app.url') . '/documentation/information#INFO_00006',
                'created_at'               => '2021-09-26 00:00:00',
                'updated_at'               => '2021-09-26 00:00:00',
            ],
            [
                'id'                       => '7',
                'notify_code'              => 'WAR_00001',
                'notify_short_description' => 'The table you are looking for is empty!',
                'notify_reference'         => config('app.url') . '/documentation/warning#WAR_00001',
                'created_at'               => '2021-09-26 00:00:00',
                'updated_at'               => '2021-09-26 00:00:00',
            ],
            [
                'id'                       => '8',
                'notify_code'              => 'WAR_00002',
                'notify_short_description' => 'The record(s) you are trying to delete does not exist in the database!',
                'notify_reference'         => config('app.url') . '/documentation/warning#WAR_00002',
                'created_at'               => '2021-09-26 00:00:00',
                'updated_at'               => '2021-09-26 00:00:00',
            ],
            [
                'id'                       => '9',
                'notify_code'              => 'WAR_00003',
                'notify_short_description' => 'You do not have sufficient rights to view this resource(s)',
                'notify_reference'         => config('app.url') . '/documentation/warning#WAR_00003',
                'created_at'               => '2021-09-26 00:00:00',
                'updated_at'               => '2021-09-26 00:00:00',
            ],
            [
                'id'                       => '10',
                'notify_code'              => 'ERR_00001',
                'notify_short_description' => 'The table you are looking for does not exist in the database!',
                'notify_reference'         => config('app.url') . '/documentation/error#ERR_00001',
                'created_at'               => '2021-09-26 00:00:00',
                'updated_at'               => '2021-09-26 00:00:00',
            ],
            [
                'id'                       => '11',
                'notify_code'              => 'ERR_00002',
                'notify_short_description' => 'One or more column(s) is (are) missing from the table!',
                'notify_reference'         => config('app.url') . '/documentation/error#ERR_00002',
                'created_at'               => '2021-09-26 00:00:00',
                'updated_at'               => '2021-09-26 00:00:00',
            ],
            [
                'id'                       => '12',
                'notify_code'              => 'ERR_00003',
                'notify_short_description' => 'The record(s) already exists in the database!',
                'notify_reference'         => config('app.url') . '/documentation/error#ERR_00003',
                'created_at'               => '2021-09-26 00:00:00',
                'updated_at'               => '2021-09-26 00:00:00',
            ],
            [
                'id'                       => '13',
                'notify_code'              => 'ERR_00004',
                'notify_short_description' => 'The requested API route does not exist!',
                'notify_reference'         => config('app.url') . '/documentation/error#ERR_00004',
                'created_at'               => '2021-09-26 00:00:00',
                'updated_at'               => '2021-09-26 00:00:00',
            ],
        ];
        ErrorAndNotificationSystem::insert($records);
    }
}
