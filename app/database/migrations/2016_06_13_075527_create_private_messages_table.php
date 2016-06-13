<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrivateMessagesTable extends Migration {

	public function up()
	{
		Schema::create('private_messages', function($table)
        {
            $table->increments('id');
            $table->integer('from');
            $table->integer('to');
            $table->text('message');
            $table->boolean('seen');
            $table->timestamps();
        });
	}

	public function down()
	{
		Schema::drop('private_messages');
	}

}
