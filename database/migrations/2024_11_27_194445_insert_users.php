<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $user = new \App\Models\User();
        $user->name = 'Admin';
        $user->email = 'admin@teste.com.br';
        $user->email_verified_at = now()->getTimestamp();
        $user->password = bcrypt('12345678');
        $user->save();

        $user = new \App\Models\User();
        $user->name = 'Desafio Dev';
        $user->email = 'desafiodev88@gmail.com';
        $user->email_verified_at = now()->getTimestamp();
        $user->password = bcrypt('12345678');
        $user->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('users')->truncate();
    }
};
