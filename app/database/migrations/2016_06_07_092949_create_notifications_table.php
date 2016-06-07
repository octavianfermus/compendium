<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration {

	public function up()
	{
		Schema::create('notifications', function($table)
        {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('who_said');
            $table->string('url');
            $table->string('title');
            $table->string('text');
            $table->integer('seen');
            $table->timestamps();
        });
	}
    
	public function down()
	{
		Schema::drop('notifications');
	}

}
