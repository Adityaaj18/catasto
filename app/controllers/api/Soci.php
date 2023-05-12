<?php

namespace simplerest\controllers\api;

use simplerest\libs\OpenApiIT;
use simplerest\core\libs\Logger;
use simplerest\core\api\v1\ApiController;

class Soci extends ApiController
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
    function onPostingAfterCheck($id, Array &$data)
    {       
        $piva_cf_or_id = $data['piva_cf_or_id'];

        $url = 'https://imprese.openapi.it/soci/' . $piva_cf_or_id;

        $res = OpenApiIT::makeRequest($data, $url);

        if ($res['error'] !== null){
            response()->error("OpenAPI error", $res['error'], $res['message']);
        }
    }        
    
    
} 
