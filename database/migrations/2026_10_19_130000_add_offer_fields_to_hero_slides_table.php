<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->string('kicker', 60)->nullable()->after('id');
            $table->string('discount_label', 40)->nullable()->after('subhead');
            $table->string('cta_label', 40)->nullable()->after('discount_label');
            $table->string('cta_url')->nullable()->after('cta_label');
        });
    }

    public function down(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->dropColumn(['kicker', 'discount_label', 'cta_label', 'cta_url']);
        });
    }
};
