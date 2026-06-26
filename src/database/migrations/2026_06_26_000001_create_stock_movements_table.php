<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('sku')->index();
            $table->string('product_name');
            $table->enum('type', ['in', 'out'])->index();
            $table->integer('quantity');
            $table->integer('before_quantity');
            $table->integer('after_quantity');
            $table->string('reference_type')->nullable(); // 'purchase', 'sale', 'adjustment'
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_number')->nullable(); // Invoice/PO number
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['reference_type', 'reference_id']);
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_movements');
    }
};
