<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViewsTable extends Migration {

	public function up() {
        Schema::create('algorithm_views', function($table)
        {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('algorithm_id');
            $table->timestamps();
        });
    }
	public function down()
	{
		Schema::drop('algorithm_views');
	}

}
