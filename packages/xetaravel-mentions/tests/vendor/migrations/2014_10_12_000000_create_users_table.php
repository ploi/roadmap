<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });

        $now = Carbon::now();

        $users = [
            [
                'username'   => 'Admin',
                'email'      => 'admin@xetaravel.com',
                'password'   => bcrypt('secret'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'Member',
                'email'      => 'member@xetaravel.com',
                'password'   => bcrypt('secret'),
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];

        DB::table('users')->insert($users);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
