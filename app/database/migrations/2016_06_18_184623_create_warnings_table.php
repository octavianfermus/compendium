<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWarningsTable extends Migration {

	public function up()
	{
        Schema::create('warnings', function($table)
        {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('report_id');
            $table->timestamps();
        });
    }

	public function down()
	{
	   Schema::drop('warnings');
	}

}
