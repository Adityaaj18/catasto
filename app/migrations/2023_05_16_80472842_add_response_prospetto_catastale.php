<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class AddProspettoCatastale implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Schema::renameTable('prospeto_catastale', 'prospetto_catastale');

        $sc = new Schema('prospetto_catastale');
        $sc->json('response')->after('status')->nullable();
		$sc->alter();
		
    }

    /**
	* Run undo migration.
    *
    * @return void
    */
    public function down()
    {
        ### DOWN
    }
}

