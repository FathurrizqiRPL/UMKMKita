<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'is_favorite')) {
                $table->boolean('is_favorite')->default(false)->after('description');
            }
            if (!Schema::hasColumn('items', 'is_best_seller')) {
                $table->boolean('is_best_seller')->default(false)->after('is_favorite');
            }
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['is_favorite', 'is_best_seller']);
        });
    }
};
