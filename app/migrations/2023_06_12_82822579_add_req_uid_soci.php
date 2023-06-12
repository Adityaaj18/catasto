<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class AddReqUidSoci implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $sc = new Schema('soci');
        $sc->varchar('req_uid', 240)->nullable();
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

