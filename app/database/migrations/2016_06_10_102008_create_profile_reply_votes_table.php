<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfileReplyVotesTable extends Migration {

	public function up()
	{
		Schema::create('profile_reply_votes', function($table)
        {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('comment_id');
            $table->integer('profile_id');
            $table->integer('vote');
            $table->timestamps();
        });
	}
    
	public function down()
	{
		Schema::drop('profile_reply_votes');
	}

}
