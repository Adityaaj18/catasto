<?php

namespace simplerest\controllers\api;

use simplerest\core\libs\DB;
use simplerest\libs\OpenApi;
use simplerest\core\libs\Logger;
use simplerest\core\libs\Strings;
use simplerest\controllers\MyApiController; 

class Soci extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		
	];

    static protected $hidden = [

    ];

    static protected $hide_in_response = false;

     /*
        Este no requiere callback
    */
    function onPost($id, Array &$data)
    {       
        
    }         
} 
