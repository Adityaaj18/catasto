<?php

namespace simplerest\controllers\api;

use simplerest\libs\OpenApiIT;
use simplerest\controllers\MyApiController; 

class RicercaPersona extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		
	];

    static protected $hidden = [

    ];

    static protected $hide_in_response = false;

    function onPostingAfterCheck($id, Array &$data)
    {       
        $url = 'https://catasto.openapi.it/richiesta/ricerca_nazionale/';

        $res = OpenApiIT::makeRequest($data, $url, "?r=realstate&sub=ricerca_nazionale");

        if ($res['error'] !== null){
            response()->error("OpenAPI error", $res['error'], $res['message']);
        }
    }              
} 
