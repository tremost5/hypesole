<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('site_settings', function(Blueprint $t){$t->id();$t->string('key')->unique();$t->longText('value')->nullable();$t->timestamps();});
  Schema::create('banners', function(Blueprint $t){$t->id();$t->string('title');$t->string('subtitle')->nullable();$t->string('image_path');$t->string('button_text')->nullable();$t->string('button_url')->nullable();$t->unsignedInteger('sort_order')->default(0);$t->boolean('is_active')->default(true);$t->dateTime('starts_at')->nullable();$t->dateTime('ends_at')->nullable();$t->timestamps();});
 }
 public function down(): void {Schema::dropIfExists('banners');Schema::dropIfExists('site_settings');}
};
