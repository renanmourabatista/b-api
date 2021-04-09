<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();

            $table->decimal('value');
            $table->integer('status');

            $table->unsignedBigInteger('wallet_sender_id');
            $table->unsignedBigInteger('wallet_receiver_id');

            $table->foreign('wallet_sender_id')->references('id')->on('wallets');
            $table->foreign('wallet_receiver_id')->references('id')->on('wallets');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->dropForeign(['wallet_sender_id']);
            $table->dropForeign(['wallet_receiver_id']);
        });

        Schema::dropIfExists('transfers');
    }
}
