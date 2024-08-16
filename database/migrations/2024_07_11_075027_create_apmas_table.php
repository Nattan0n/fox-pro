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
        Schema::create('apmas', function (Blueprint $table) {
            $table->id();
            $table->string('supcod')->nullable();
            $table->string('suptyp')->nullable();
            $table->string('onhold')->nullable();
            $table->string('prenam')->nullable();
            $table->string('supnam')->nullable();
            $table->string('addr01')->nullable();
            $table->string('addr02')->nullable();
            $table->string('addr03')->nullable();
            $table->string('zipcod')->nullable();
            $table->string('telnum')->nullable();
            $table->string('contact')->nullable();
            $table->string('supnam2')->nullable();
            $table->string('paytrm')->nullable();
            $table->string('paycond')->nullable();
            $table->string('dlvby')->nullable();
            $table->string('vatrat')->nullable();
            $table->string('flgvat')->nullable();
            $table->string('disc')->nullable();
            $table->string('balance')->nullable();
            $table->string('chqpay')->nullable();
            $table->string('crline')->nullable();
            $table->string('lasrcv')->nullable();
            $table->string('accnum')->nullable();
            $table->string('remark')->nullable();
            $table->string('taxid')->nullable();
            $table->string('orgnum')->nullable();
            $table->string('taxdes')->nullable();
            $table->string('taxrat')->nullable();
            $table->string('taxtyp')->nullable();
            $table->string('taxcond')->nullable();
            $table->string('creby')->nullable();
            $table->string('credat')->nullable();
            $table->string('userid')->nullable();
            $table->string('chgdat')->nullable();
            $table->string('status')->nullable();
            $table->string('inactdat')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apmas');
    }
};
