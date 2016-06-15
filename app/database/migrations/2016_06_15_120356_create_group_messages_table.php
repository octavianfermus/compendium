<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupMessagesTable extends Migration {

	public function up()
	{
        Schema::create('group_messages', function($table)
        {
            $table->increments('id');
            $table->integer('group_id');
            $table->integer('user_id');
            $table->string('message');
            $table->timestamps();
        });
	}

	public function down()
	{
        Schema::drop('group_messages');
	}

}
