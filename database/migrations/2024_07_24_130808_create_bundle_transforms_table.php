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
        Schema::create('bundle_transforms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("student_id")->nullable();
            $table->unsignedInteger("from_bundle_id")->nullable();
            $table->unsignedInteger("to_bundle_id")->nullable();
            $table->unsignedBigInteger("service_request_id")->nullable();
            $table->boolean('certificate')->default(false);

            $table->foreign('student_id')->references('id')->on('students')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('from_bundle_id')->references('id')->on('bundles')->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('to_bundle_id')->references('id')->on('bundles')->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('service_request_id')->references('id')->on('service_user')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('status')->default("pending");
            $table->string('type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bundle_transforms');
    }
};
