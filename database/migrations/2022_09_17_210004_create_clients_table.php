<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id()->index('idx_id');
            $table->string('name');
            $table->string('fiscal_code')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('pin_number')->nullable();
            $table->string('country');
            $table->string('county')->nullable();
            $table->string('city');
            $table->string('address');
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('phone_number');
            $table->string('email');
            $table->string('is_active', 3)->default('0');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
