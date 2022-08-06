<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('postal_code');
            $table->string('street');
            $table->string('number');
            $table->string('district');
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('city_id')->constrained('cities');
            $table->foreignId('state_id')->constrained('states');
            $table->timestamps();
        });
    }
    
    // model Address {
    //     city      City      @relation(fields: [cityId], references: [id])
    //     state     State     @relation(fields: [stateId], references: [id])
    //   }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
