<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateProjectTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_tasks', function (Blueprint $table) {
            $table->id()->index('idx_id');
            $table->foreignId('project_id')->index('idx_project_id');
            $table->string('name');
            $table->longText('observation');
            $table->string('is_done', 3)->default('0');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::unprepared(
            'ALTER TABLE ' . config('database.connections.mysql.database') . '.`project_tasks`
            ADD CONSTRAINT `fk_project_tasks`
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
        Schema::dropIfExists('project_tasks');
    }
}
