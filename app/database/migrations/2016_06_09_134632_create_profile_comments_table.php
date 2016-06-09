<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfileCommentsTable extends Migration {

	public function up()
	{
		Schema::create('profile_discussion', function($table)
        {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('profile_id');
            $table->string('text');
            $table->integer('upvotes');
            $table->integer('downvotes');
            $table->boolean('deleted');
            $table->timestamps();
        });
	}

	public function down()
	{
		Schema::drop('profile_discussion');
	}

}
