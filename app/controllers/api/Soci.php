<?php

namespace simplerest\controllers\api;

use simplerest\core\libs\Logger;
use simplerest\controllers\MyApiController; 

class Soci extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		
	];

    static protected $hidden = [

    ];

    static protected $hide_in_response = false;

    function __construct()
    {       
        parent::__construct();
    }     
    
    function onPost($id, Array &$data){
        Logger::log($data);
    }
} 
