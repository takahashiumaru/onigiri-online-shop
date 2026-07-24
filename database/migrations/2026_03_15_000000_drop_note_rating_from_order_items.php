<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropNoteRatingFromOrderItems extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('order_items', 'note_rating')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('note_rating');
            });
        }
    }

    public function down()
    {
        // restore column if needed (safe: add without position if dependent columns missing)
        if (! Schema::hasColumn('order_items', 'note_rating')) {
            if (Schema::hasColumn('order_items', 'rating_review')) {
                Schema::table('order_items', function (Blueprint $table) {
                    $table->string('note_rating')->nullable()->after('rating_review');
                });
            } elseif (Schema::hasColumn('order_items', 'rating')) {
                Schema::table('order_items', function (Blueprint $table) {
                    $table->string('note_rating')->nullable()->after('rating');
                });
            } else {
                Schema::table('order_items', function (Blueprint $table) {
                    $table->string('note_rating')->nullable();
                });
            }
        }
    }
}
