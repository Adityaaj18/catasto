<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class RicercaPersona implements IMigration
{
    protected $table = 'ricerca_persona';

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
            INPUT for Real State
        */
        $sc->varchar("cf_piva", 30);
        $sc->varchar("tipo_catasto", 5);
        $sc->varchar("provincia", 2);  // "RM"


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

