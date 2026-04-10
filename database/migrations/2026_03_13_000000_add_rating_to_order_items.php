<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRatingToOrderItems extends Migration
{
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->tinyInteger('rating')->unsigned()->nullable()->after('quantity');
            $table->text('rating_review')->nullable()->after('rating');
        });
    }

    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['rating', 'rating_review']);
        });
    }
}
