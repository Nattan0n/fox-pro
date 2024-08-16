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
        Schema::create('aptrn', function (Blueprint $table) {
            $table->id();
             $table->string('rectyp')->nullable();
            $table->string('docnum')->nullable();
            $table->string('docdata')->nullable();
            $table->string('refnum')->nullable();
            $table->string('vatprd')->nullable();
            $table->string('vatlate')->nullable();
            $table->string('vattyp')->nullable();
            $table->string('postgl')->nullable();
            $table->string('ponum')->nullable();
            $table->string('dntyp')->nullable();
            $table->string('depcod')->nullable();
            $table->string('flgvat')->nullable();
            $table->string('supcod')->nullable();
            $table->string('shipto')->nullable();
            $table->string('youref')->nullable();
            $table->string('paytrm')->nullable();
            $table->string('duedat')->nullable();
            $table->string('bilnum')->nullable();
            $table->string('dlvby')->nullable();
            $table->string('nxtsqy')->nullable();
            $table->string('amount')->nullable();
            $table->string('disc')->nullable();
            $table->string('discamt')->nullable();
            $table->string('aftdisc')->nullable();
            $table->string('advnum')->nullable();
            $table->string('advamt')->nullable();
            $table->string('total')->nullable();
            $table->string('amtrat0')->nullable();
            $table->string('vatrat')->nullable();
            $table->string('vatamt')->nullable();
            $table->string('netamt')->nullable();
            $table->string('netval')->nullable();
            $table->string('payamt')->nullable();
            $table->string('remamt')->nullable();
            $table->string('cmplapp')->nullable();
            $table->string('cmpldat')->nullable();
            $table->string('docstat')->nullable();
            $table->string('cshpay')->nullable();
            $table->string('chqpay')->nullable();
            $table->string('intpay')->nullable();
            $table->string('tax')->nullable();
            $table->string('rcvamt')->nullable();
            $table->string('chqpas')->nullable();
            $table->string('vatdat')->nullable();
            $table->string('srv_vattyp')->nullable();
            $table->string('pvatprorat')->nullable();
            $table->string('pvat_rf')->nullable();
            $table->string('pvat_nrf')->nullable();
            $table->string('userid')->nullable();
            $table->string('chgdat')->nullable();
            $table->string('userprn')->nullable();
            $table->string('prndat')->nullable();
            $table->string('prncnt')->nullable();
            $table->string('prntim')->nullable();
            $table->string('authid')->nullable();
            $table->string('approve')->nullable();
            $table->string('billbe')->nullable();
            $table->string('orgnum')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aptrn');
    }
};
