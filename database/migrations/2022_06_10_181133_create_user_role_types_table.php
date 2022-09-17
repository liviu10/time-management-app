<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateUserRoleTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_role_types', function (Blueprint $table) {
            $table->id()->index('idx_id');
            $table->string('user_role_name');
            $table->string('user_role_description');
            $table->string('user_role_slug');
            $table->string('user_role_is_active', 3);
            $table->timestamps();
        });

        DB::unprepared(
            'ALTER TABLE ' . config('database.connections.mysql.database') . '.`users` 
            ADD CONSTRAINT `fk_user_role_type_id`
                FOREIGN KEY (`user_role_type_id`)
                REFERENCES ' . config('database.connections.mysql.database') . '.`user_role_types` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_role_types');
    }
}
