<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateProjectSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_settings', function (Blueprint $table) {
            $table->id()->index('idx_id');
            $table->foreignId('project_id')->index('idx_project_setting_id');
            $table->integer('billable_rate');
            $table->integer('amount');
            $table->integer('budget');
            $table->integer('no_of_hours');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::unprepared(
            'ALTER TABLE ' . config('database.connections.mysql.database') . '.`project_settings`
            ADD CONSTRAINT `fk_project_setting`
                FOREIGN KEY (`project_id`)
                REFERENCES ' . config('database.connections.mysql.database') . '.`projects` (`id`)
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
        Schema::dropIfExists('project_settings');
    }
}
