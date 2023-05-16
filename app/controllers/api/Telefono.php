<?php

namespace simplerest\controllers\api;

use simplerest\libs\OpenApi;
use simplerest\core\libs\Url;
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
        //throw new \Exception("FOO");

        $url  = "https://rintraccio.openapi.it/telefoni/";

        if (config()['mock_responses']){
            OpenApi::mock(ETC_PATH . 'mocks/telefono.json'); // <------- especifico del endpoint
        }
        
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

        $data      = $res['data'];

        $status    = $data['status'] ?? $data['stato'] ?? null;
        $callback  = $data['callback']['url'] ?? null;
        
        $cb_params = Url::getQueryParams($callback);
        $r_sub     = 'r='.$cb_params['r'] .'&sub='. $cb_params['sub'];

        switch($r_sub){
            case 'r=realstate&sub=elenco_immobili':
                $endpoint = 'elenco_immobili';
            break;
        
            case 'r=realstate&sub=prospetto_catastale':
                $endpoint = 'prospetto_catastale';
            break;
        
            case 'r=realstate&sub=ricerca_persona':
                $endpoint = 'ricerca_persona';
            break;
        
            case 'r=realstate&sub=ricerca_nazionale':
                $endpoint = 'ricerca_nazionale';
            break;
        
            case '=realstate&sub=indirizzo':
                $endpoint = 'indirizzo';
            break;
        
            case 'r=company_info&sub=soci':
                $endpoint = 'soci';
            break;
        
            case 'r=rintracio&sub=telefoni':
                $endpoint = 'telefono';
            break;
        
            default:
                throw new \Exception("Invalid callback for '$callback'");            
        }

        // $s_eq   = [
        //     'evasa' => 'SENT' // 'PENDING'
        // ];

        dd($status, 'STATUS');
        dd($callback, 'CALLBACK');
        dd($endpoint, 'ENDPOINT');

        if ($res['error'] !== null){
            response()->error("OpenAPI error", $res['error'] ?? "Error", $res['message'] ?? null);
        } 
    }     
} 
