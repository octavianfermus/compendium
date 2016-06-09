<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCommendationsTable extends Migration {

	public function up()
	{
		Schema::create('user_commendations', function($table)
        {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('commendator');
            $table->timestamps();
        });
	}

	public function down()
	{
		Schema::drop('user_commendations');
	}

}
