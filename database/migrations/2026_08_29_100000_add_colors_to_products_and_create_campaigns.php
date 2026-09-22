<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'colors')) {
                $table->json('colors')->nullable()->after('color_hex');
            }
        });

        if (! Schema::hasTable('campaigns')) {
            Schema::create('campaigns', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('subtitle')->nullable();
                $table->text('body')->nullable();
                $table->string('image_path')->nullable();
                $table->string('image_url')->nullable();
                $table->string('cta_label')->nullable();
                $table->string('cta_url')->nullable();
                $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
                $table->boolean('is_active')->default(false)->index();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'colors')) {
                $table->dropColumn('colors');
            }
        });
    }
};
