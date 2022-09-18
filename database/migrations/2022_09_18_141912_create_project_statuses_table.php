<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateProjectStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_statuses', function (Blueprint $table) {
            $table->id()->index('idx_id');
            $table->foreignId('project_id')->index('idx_project_status_id');
            $table->string('name');
            $table->integer('tracked_time');
            $table->integer('amount');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::unprepared(
            'ALTER TABLE ' . config('database.connections.mysql.database') . '.`project_statuses`
            ADD CONSTRAINT `fk_project_status`
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
        Schema::dropIfExists('project_statuses');
    }
}
