<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlgorithmRequestVotes extends Migration {

	public function up()
	{
		Schema::create('algorithm_request_votes', function($table)
        {
            $table->increments('id');
            $table->string('user_id');
            $table->string('request_id');
            $table->timestamps();      
        });
	}

	public function down()
	{
		Schema::drop('algorithm_request_votes');
	}

}
