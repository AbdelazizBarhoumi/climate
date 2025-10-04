<?php

use App\Models\Tour;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Tag;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tour_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Tour::class)->constrained('tours')->cascadeOnDelete();
            $table->foreignIdFor(Tag::class)->constrained('tags')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_tag');
    }
};
