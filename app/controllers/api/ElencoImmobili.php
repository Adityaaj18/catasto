<?php

namespace simplerest\controllers\api;

use simplerest\core\libs\DB;
use simplerest\libs\OpenApi;
use simplerest\core\libs\Strings;
use simplerest\controllers\MyApiController;
use simplerest\core\exceptions\InvalidValidationException;

class ElencoImmobili extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		
	];

    static protected $hidden = [

    ];

    static protected $hide_in_response = false;

    function onPostingAfterCheck($id, array &$data)
    {     
               
    }

    function onPost($id, Array &$data)
    {       
        $url = 'https://catasto.openapi.it/richiesta/elenco_immobili/';

        $cfg = config();

        if ($cfg['env'] == 'local' && $cfg['mock_responses']){
            OpenApi::mock(ETC_PATH . 'mocks/'.$this->table_name.'.json'); 
        }

        $res = OpenApi::makeRequest($data, $url, "?r=realstate&sub=elenco_immobili");
        $dec = Strings::isJSON($res) ? json_decode($res, true) : $res; ///

        if ($res === false){
            response()->error("Empty response", 500, "Connection error?");
        }

        $_data     = $dec['data'];
        $status    = strtoupper($_data['status'] ?? $_data['stato'] ?? '');

        if ($dec['error'] !== null){
            response()->error("OpenAPI error", 500, $dec['message'] ?? null);
        } 

        /*
            Actualizo en la DB
        */
        
        DB::table($this->table_name)
        ->find($id)
        ->fill(['status', 'response'])
        ->update([
            'status'   => $status,
            'response' => $res // $_data
        ]);

        // dd(DB::getLog());

        /*
            Lo envio en la respuesta
        */

        $data['status']   = $status;
        $data['response'] = $res;
    }      
} 
