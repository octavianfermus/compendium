<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

    public function up() {
        Schema::create('users', function($table)
        {
            $table->increments('id');
            $table->string('email', 100)->unique();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('password', 64);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::drop('users');
    }

}
