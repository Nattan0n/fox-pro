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
        Schema::create('bktrn', function (Blueprint $table) {
            $table->id();
            $table->string('bktrntyp')->nullable();
            $table->string('trndat')->nullable();
            $table->string('chqnum')->nullable();
            $table->string('chqdat')->nullable();
            $table->string('bnkcod')->nullable();
            $table->string('branch')->nullable();
            $table->string('cuscod')->nullable();
            $table->string('name')->nullable();
            $table->string('depcod')->nullable();
            $table->string('postgl')->nullable();
            $table->string('getdat')->nullable();
            $table->string('payindat')->nullable();
            $table->string('amount')->nullable();
            $table->string('charge')->nullable();
            $table->string('vatamt')->nullable();
            $table->string('netamt')->nullable();
            $table->string('remamt')->nullable();
            $table->string('remcut')->nullable();
            $table->string('cmplapp')->nullable();
            $table->string('chqstat')->nullable();
            $table->string('bnkacc')->nullable();
            $table->string('jnltrntyp')->nullable();
            $table->string('remark')->nullable();
            $table->string('refdoc')->nullable();
            $table->string('refnum')->nullable();
            $table->string('vatdat')->nullable();
            $table->string('vatprd')->nullable();
            $table->string('vatlate')->nullable();
            $table->string('vattyp')->nullable();
            $table->string('voucher')->nullable();
            $table->string('userid')->nullable();
            $table->string('chgdat')->nullable();
            $table->string('authid')->nullable();
            $table->string('approve')->nullable();
            $table->string('taxid')->nullable();
            $table->string('orgnum')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bktrn');
    }
};
