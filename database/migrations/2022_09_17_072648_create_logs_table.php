<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id()->index('idx_id');
            $table->unsignedBigInteger('logable_id')->nullable();
            $table->string('logable_type')->nullable();
            $table->string('status')->nullable();
            $table->string('status_description')->nullable();
            $table->longText('request_details')->nullable();
            $table->longText('response_details')->nullable();
            $table->string('sql_details')->nullable();
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
        Schema::dropIfExists('logs');
    }
}
