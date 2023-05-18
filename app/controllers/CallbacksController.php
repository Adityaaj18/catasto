<?php

namespace simplerest\controllers;

use simplerest\core\libs\DB;
use simplerest\libs\OpenApi;
use simplerest\core\libs\Url;
use simplerest\core\libs\Logger;
use simplerest\core\libs\Strings;
use simplerest\controllers\MyController;
use simplerest\core\exceptions\InvalidValidationException;

class CallbacksController extends MyController
{
    /*
        https://ticiwe.com/callbacks?r=realstate&sub=elenco_immobili
        https://ticiwe.com/callbacks?r=realstate&sub=prospetto_catastale
        https://ticiwe.com/callbacks?r=realstate&sub=ricerca_persona
        https://ticiwe.com/callbacks?r=realstate&sub=ricerca_nazionale
        https://ticiwe.com/callbacks?r=realstate&sub=indirizzo

        https://ticiwe.com/callbacks?r=company_info&sub=soci

        https://ticiwe.com/callbacks?r=rintracio&sub=telefoni

        Se hace uso de OpenApi::getParams($table) para saber que parametri buscar en la response
        y poder asi "poblar" la tabla
    */
    function index()
    {
        $req = file_get_contents("php://input");

        // LOG --untouched-
        file_put_contents(LOGS_PATH . 'reqs.txt', $req . "\n", FILE_APPEND);

        if ($req === null){
            return;
        }

        // Admito JSON para poder probar en POSTMAN con respuestas ya decodificadas
        $dec = Strings::isJSON($req) ? json_decode($req, true) : OpenApi::decode($req);

        if (empty($dec)){
            return;
        }

        //dd($dec, 'DEC'); //

        $status    = strtoupper($dec['status']   ?? $dec['stato'] ?? '');
        $result    = $dec['soggetto'] ?? $dec['risultato'] ?? '';
        $callback  = $dec['callback']['url'] ?? null;
        
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

        $parametri = [];

        if (isset($dec['parametri'])){
            $parametri   = $dec['parametri'];
        } else {
            $param_names = OpenApi::getParams($endpoint);

            foreach($param_names as $pn){
                $parametri[$pn] = $dec[$pn] ?? null;
            }
        }

        // dd($endpoint, 'ENDPOINT');
        // dd($parametri,'PARAMS');
        // dd($callback, 'CALLBACK');

        // dd($status, 'STATUS');
        // dd($result, 'RESULT');


        /*
            Armo el registro
        */

        $data = $parametri;

        // dd($data, $endpoint);
        // exit;

        $data['status'] = $status;
        $data['result'] = $req; // $result

        $data['result'] = substr($data['result'], 5);
        $data['result'] = urldecode($data['result']);

        /*
            Casting
        */
        
        try {
            $data['result']     = (empty($data['result']) ? null : $data['result']); 
            $data['foglio']     = Strings::fromInt($data['foglio']);   // deberia ser fromIntOrFail()
            $data['particella'] = Strings::fromInt($data['particella']);
        } catch (\Exception $e){
            throw new InvalidValidationException($e->getMessage());
        }     

        //dd($data, $endpoint);

        try {

            $id = DB::table($endpoint)
            ->fill([
                'status',
                'result'
            ])
            ->create($data);

        } catch (\Exception $e) {
            response()->error("Error inserting data", 400, $e->getMessage());
        }         

        $data['id'] = $id;

        /*
            Envio la respuesta
        */

        return $data;
        
        /*
            - Generar un log con cada respuesta para que no se pierdan

            - Actualizar la TABLA correspondiente !!!

                . status
                . response

            Notas: 
            
                . El status puede ser "status" o "stato" 

                    "evasa" significa "COMPLETED"

                . Si hay callback apareceria tambien el la respuesta:
                
                    [callback] => Array
                    (
                        [url] => https://catasto.000webhostapp.com/callback.php
                        [field] => data
                        [method] => POST
                        [data] => Array
                            (
                            )

                    )

                . Muchas veces los parametros estan sueltos

                    [cf_piva] => CCCMRN48T59E625G

                pero otras veces son parte de "parametri"

                    [parametri] => Array
                    (
                        [tipo_catasto] => F
                        [provincia] => MATERA Territorio-MT
                        [comune] => F052#MATERA#0#0
                        [sezione] =>
                        [sezione_urbana] =>
                        [foglio] => 52
                        [particella] => 597
                    )

                . El resultado a veces es "soggetto" y otras veces "risultato"

                    [soggetto] => Array
                    (
                        [code] => CCCMRN48T59E625G
                        [utenze] => Array
                            (
                                [0] => 3273271075
                                [1] => 3405355951
                            )

                    )

                o ...

                    [risultato] => Array
                    (
                        [immobili] => Array
                            (
                                [0] => Array
                                    (
                                        [sezione_urbana] =>
                                        [foglio] => 52
                                        [particella] => 597
                                        [subalterno] => 1
                                        /...
                                        [partita] => Soppressa
                                        [id_immobile] => MzIzMyMzMjMzI0YjNTIjNTk3I0YwNTIjU29wcHJlc3NhIzEjICNNQVRFUkE=
                                    )

                                [1] => Array
                                    (
                                        [sezione_urbana] =>
                                        [foglio] => 52
                                        [particella] => 597
                                        [subalterno] => 2
                                        /...
                                        [partita] =>
                                        [id_immobile] => MzIzNCMzMjM0I0YjNTIjNTk3I0YwNTIjIzIjICNNQVRFUkE=
                                    )
                            // ...
                    )

        */



        exit;
    }
}

