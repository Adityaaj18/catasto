<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class ElencoImmobili implements IMigration
{
    protected $table = 'visura_catastale';

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
            INPUT for Real State ("visura_catastale")
        */

        $sc->enum("entita", ["immobile", "soggetto"]);
        $sc->varchar("id_immobile", 240)->nullable();
        $sc->varchar("tipo_catasto",2); // ["T","F"] ?
        $sc->varchar("provincia", 60)->nullable();
        $sc->varchar("comune")->nullable();
        $sc->varchar("foglio")->nullable();
        $sc->varchar("particella")->nullable();
        $sc->int("subalterno")->nullable();
        $sc->enum("tipo_visura", ["ordinaria", "storica"]); 
        $sc->varchar("richiedente");

        $sc->json("result")->nullable();  
        $sc->varchar("status", 20)->nullable();
        $sc->json('response')->after('status')->nullable();
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

