<?php

namespace simplerest\controllers\api;

use simplerest\libs\OpenApi;
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
        $url = 'https://catasto.openapi.it/richiesta/ricerca_persona/';

        $res = OpenApi::makeRequest($data, $url, "?r=realstate&sub=ricerca_persona");

        if ($res['error'] !== null){
            response()->error("OpenAPI error", $res['error'], $res['message']);
        }
    }              
} 
