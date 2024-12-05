<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $phone = new \App\Models\Phone();
        $phone->num = '+55 (99) 99999-9999';
        $phone->user_id = 1;
        $phone->save();

        $phone = new \App\Models\Phone();
        $phone->num = '+55 (99) 98888-9999';
        $phone->user_id = 2;
        $phone->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('phones')->truncate();
    }
};
