<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class Soci implements IMigration
{
    protected $table = 'soci';

    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $sc = new Schema($this->table);
        $sc->int('id')->pri()->auto();


        $sc->varchar("piva_cf_or_id");


        /*
            Extras
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

