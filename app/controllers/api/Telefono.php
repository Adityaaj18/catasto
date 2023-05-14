<?php

namespace simplerest\controllers\api;

use simplerest\libs\OpenApi;
use simplerest\core\libs\Logger;
use simplerest\core\libs\Strings;
use simplerest\controllers\MyApiController; 

class Telefono extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		
	];

    static protected $hidden = [

    ];

    static protected $hide_in_response = false;

    function onPostingAfterCheck($id, Array &$data)
    {       
        $url  = "https://rintraccio.openapi.it/telefoni/";

        $res  = OpenApi::makeRequest($data, $url, "?r=rintracio&sub=telefoni");

        /*
            La respuesta puede ser variada, incluyendo:

            Array
            (
                [success] => false
                [message] => Insufficient Credit in Wallet: 0.7 > 0.6
                [error] => 223
                [data] =>
                [trace] => WyJpbmRleC5waHBAMjY2IiwiY2xhc3MuQXZXcy5waHBAMjE3IiwiaW5kZXgucGhwQDQ0MiJd
            )

            o...

            array (
                'success' => false,
                'message' => 'cf_piva not valid',
                'error' => 219,
                'data' => NULL,
                'trace' => 'WyJpbmRleC5waHBAMjE0IiwiY2xhc3MuQXZXcy5waHBAMjE3IiwiaW5kZXgucGhwQDQ0MiJd',
            )
        */

        /*
            Ej:

            {
                "status": 219,
                "error": {
                    "type": null,
                    "code": null,
                    "message": "cf_piva not valid",
                    "detail": null,
                    "location": null
                }
            }
        */

    
        if ($res['error'] !== null){
            response()->error("OpenAPI error", $res['error'], $res['message']);
        }
    }     
} 
