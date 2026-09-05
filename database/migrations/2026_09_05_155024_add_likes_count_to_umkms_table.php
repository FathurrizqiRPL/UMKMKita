<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('umkms', function (Blueprint $table) {
        $table->integer('likes_count')->default(0)->after('slug');
    });
}

public function down()
{
    Schema::table('umkms', function (Blueprint $table) {
        $table->dropColumn('likes_count');
    });
}
};
