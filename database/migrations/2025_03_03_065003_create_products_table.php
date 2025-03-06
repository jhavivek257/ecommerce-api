<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('sku')->unique()->index();
            $table->decimal('mrp', 10, 2)->index();
            $table->decimal('salePrice', 10, 2)->index();
            $table->unsignedInteger('stock')->index();
            $table->text('description')->nullable();
            $table->string('thumbnail');
            $table->json('images')->nullable();
            $table->string('slug')->unique();
            $table->foreignId('catId')->constrained('categories')->onDelete('cascade')->index();
            $table->boolean('status')->default(1)->index();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
