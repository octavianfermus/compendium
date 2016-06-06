<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReplyVotesTable extends Migration {

	public function up()
	{
		Schema::create('reply_votes', function($table)
        {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('comment_id');
            $table->integer('algorithm_id');
            $table->integer('vote');
            $table->timestamps();
        });
	}

	public function down()
	{
		Schema::drop('reply_votes');
	}

}
