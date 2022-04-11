<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banner_file', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Banner::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\File::class)->constrained()->onDelete('cascade');

            $table->index(['banner_id', 'file_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banner_file_pivot');
    }
};
