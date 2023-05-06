<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class Research implements IMigration
{
    protected $table = 'ricerca';

    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $sc = new Schema($this->table);
        $sc->int('id')->pri()->auto();

        /*
            INPUT for Real State ("elenco_immobili")
        */
        $sc->varchar("tipo_catasto",20)->nullable();
        $sc->varchar("provincia", 2)->nullable();
        $sc->varchar("comune")->nullable();
        $sc->varchar("sezione")->nullable();
        $sc->varchar("foglio")->nullable();
        $sc->varchar("particella")->nullable();

        /*
            OUTPUT for Real State ("elenco_immobili")

            It can contains:

            "{numero}"                 <------- best case scenario
            ""
            "Soppressa" (supressed)
            "Bene comune non censibile"
        */
        $sc->varchar("partita")->nullable();  // partita_iva
        $sc->varchar("indirizzo", 200)->nullable(); // "CONTRADA LA VAGLIA n. SC Piano S1"

        /*
            INPUT {piva_cf_or_id}

            OUTPUT for company info

            {denominazione} = "XXXXXXXX S.R.L." (example)
            {nome}          = null
            {cognome}       = null
            {quota}         <= 100
            {cf_socio}      = xxxxx
        */
        $sc->varchar("cf_ditta", 30)->nullable(); 

        /*
            INPUT {piva_cf_or_id}

            OUTPUT for company info

            {denominazione} = "PAOLO BOZZOLO"
            {nome}          = "PAOLO"   (not null)
            {cognome}       = "BOZZOLO" (not null)
            {quota}         = 100
            {cf_socio}      = xxxxx  <--- this time it will contain "codice fiscale"
        */
        $sc->varchar("cf_socio", 30)->nullable();

        /*
            Telephone number of the natural person
        */
        $sc->varchar("telefono", 25)->nullable();

        /*
            Extras
        */

        $sc->varchar("last_req", 20)->nullable();
        $sc->varchar("status", 20)->nullable();

        $sc->datetime('created_at')->nullable();
        $sc->datetime('updated_at')->nullable();
        $sc->datetime('deleted_at')->nullable();

        $sc->create();		
    }

    /**
	* Run undo migration.
    *
    * @return void
    */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}

