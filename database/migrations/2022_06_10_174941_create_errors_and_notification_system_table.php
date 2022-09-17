<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateErrorsAndNotificationSystemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('errors_and_notification_system', function (Blueprint $table) {
            $table->id()->index('idx_id');
            $table->string('notify_code', 10)->unique('notify_code');
            $table->string('notify_short_description');
            $table->string('notify_reference');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('errors_and_notification_system');
    }
}
