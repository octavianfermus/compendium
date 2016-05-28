<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestTable extends Migration {

	public function up() {
        Schema::create('algorithm_requests', function($table)
        {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('name');
            $table->string('language');
            $table->string('description');
            $table->timestamps();
        });
    }

	public function down() {
        Schema::drop('algorithm_requests');
    }

}
