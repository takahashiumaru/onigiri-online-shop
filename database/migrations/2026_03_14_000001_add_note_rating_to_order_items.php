<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNoteRatingToOrderItems extends Migration
{
    public function up()
    {
        // safe-guard: only add column if it doesn't already exist
        if (!Schema::hasColumn('order_items', 'note_rating')) {
            // decide position depending on existing columns to avoid "unknown column" errors
            if (Schema::hasColumn('order_items', 'rating_review')) {
                Schema::table('order_items', function (Blueprint $table) {
                    $table->string('note_rating')->nullable()->after('rating_review');
                });
            } elseif (Schema::hasColumn('order_items', 'rating')) {
                Schema::table('order_items', function (Blueprint $table) {
                    $table->string('note_rating')->nullable()->after('rating');
                });
            } else {
                // fallback: add column without specifying position
                Schema::table('order_items', function (Blueprint $table) {
                    $table->string('note_rating')->nullable();
                });
            }
        }
    }

    public function down()
    {
        if (Schema::hasColumn('order_items', 'note_rating')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('note_rating');
            });
        }
    }
}
