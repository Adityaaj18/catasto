<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class ProspettoCatastale implements IMigration
{
   protected $table = 'prospeto_catastale';

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
        $sc->enum("tipo_catasto",["T","F"]);
        $sc->varchar("provincia", 2);  // "RM"
        $sc->varchar("comune");
        $sc->varchar("sezione")->nullable();
        $sc->varchar("sezione_urbana")->nullable();
        $sc->varchar("foglio");
        $sc->varchar("particella"); 
        $sc->varchar("subalterno");

        /*
            OUTPUT for Real State ("elenco_immobili")

            It can contains:

            "{numero}"                 <------- best case scenario
            ""
            "Soppressa" (supressed)
            "Bene comune non censibile"
        */

        $sc->json("result")->nullable();  
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

