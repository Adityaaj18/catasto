<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class Indirizzo implements IMigration
{
    protected $table = 'indirizzo';

    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $sc = new Schema($this->table);
        $sc->int('id')->pri()->auto();

        $sc->varchar("id_indirizzo");
        $sc->varchar("dal_civico", 30);
        $sc->varchar("al_civico", 30); 

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

