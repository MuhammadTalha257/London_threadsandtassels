<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations. Guarded so it can be re-run safely if a previous
     * attempt only partially applied.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'gallery')) {
                $table->json('gallery')->nullable()->after('image_path');
            }
            if (! Schema::hasColumn('products', 'sale_price')) {
                $table->decimal('sale_price', 10, 2)->nullable()->after('price');
            }
            if (! Schema::hasColumn('products', 'is_on_sale')) {
                $table->boolean('is_on_sale')->default(false)->index()->after('is_new_arrival');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            foreach (['gallery', 'is_on_sale', 'sale_price'] as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
