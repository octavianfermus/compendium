<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportsTable extends Migration {

	public function up()
	{
        Schema::create('reports', function($table)
        {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('reported_id');
            $table->string('tbl');
            $table->integer('reported_user_id');
            $table->boolean('answered');
            $table->integer('answered_by');
            $table->string('user_reason');
            $table->string('user_description');
            $table->string('action');
            $table->timestamps();
        });
    }

	public function down()
	{
	   Schema::drop('reports');
    }
}
