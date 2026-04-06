<?php

namespace simplerest\controllers;

use simplerest\core\libs\DB;
use simplerest\libs\OpenApi;
use simplerest\core\libs\Url;
use simplerest\core\libs\Logger;
use simplerest\core\libs\Strings;
use simplerest\core\libs\Files;
use simplerest\libs\FlowLu;
use simplerest\controllers\MyController;
use simplerest\core\exceptions\InvalidValidationException;
use Throwable;

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
        try {

            // Read 
            $req = file_get_contents("php://input");

            /*
                LOG --untouched-
            */

            Logger::log($req . "\n", 'reqs.txt'); // callback response

            if ($req === null){
                return;
            }

            // Admito JSON para poder probar en POSTMAN con respuestas ya decodificadas
            $dec = Strings::isJSON($req) ? json_decode($req, true) : OpenApi::decode($req);

            if (empty($dec)){
                return;
            }

            //dd($dec, 'DEC'); //

            // Handle new JSON format where data is wrapped in a "data" key
            if (isset($dec['data']) && is_array($dec['data']) && isset($dec['data']['endpoint'])){
                $dec = $dec['data'];
            }

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

            // Determine result based on callback format
            if (Strings::isJSON($req)){
                // New JSON format: use the decoded result directly
                $data['result'] = is_array($result) ? json_encode($result) : $result;
            } else {
                // Old URL-encoded format: strip "data=" prefix and decode
                $data['result'] = substr($req, 5);
                $data['result'] = urldecode($data['result']);
            }

            /*
                Casting
            */
            
            try {
                if (isset($data['result'])){
                    $data['result']     = (empty($data['result']) ? null : $data['result']); 
                }    

                if (isset($data['foglio'])){
                    $data['foglio']     = Strings::fromInt($data['foglio']);   // podria en otro caso ser fromIntOrFail()
                }    

                if (isset($data['particella'])){
                    $data['particella'] = Strings::fromInt($data['particella']);
                }

                if (isset($data['subalterno'])){
                    $data['subalterno'] = Strings::fromInt($data['subalterno']);
                }               
            
            } catch (\Exception $e){
                error(trans('Data validation error'), 400, $e->getMessage());
            }     

            // mismo "uid" presente en la response del request
            $req_uid = $dec['id'] ?? null;

            unset($data['id']);
            $data['req_uid'] = $req_uid;

            try {

                $id = DB::table($endpoint)
                ->where(['req_uid' => $req_uid])
                ->fill([
                    'status',
                    'result'
                ])
                ->update([
                    'status' => $data['status'],
                    'result' => $data['result']
                ]);

            } catch (\Exception $e) {
                response()->error("Error updating data", 400, $e->getMessage());
            }         

            $data['id'] = $id;

            // If this callback belongs to a FlowLu lead, update it now
            $leadId = isset($cb_params['lead_id']) ? (int) $cb_params['lead_id'] : 0;

            if ($leadId && $endpoint === 'prospetto_catastale' && $status === 'EVASA') {
                Logger::log("Callback: updating FlowLu lead $leadId from prospetto_catastale result\n", 'flowlu_webhooks.txt');
                $this->updateFlowLuFromResult($leadId, $result, $parametri);
            }

            return $data;

        } catch (\Throwable $e){
            Logger::logError("Error in Callback! ". $e->getMessage());
        }


        exit;
    
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




    }

    private function updateFlowLuFromResult(int $leadId, $risultato, array $parametri = []): void
    {
        if (is_string($risultato)) {
            $risultato = json_decode($risultato, true);
        }

        if (empty($risultato)) {
            Logger::log("FlowLu callback: empty risultato for lead $leadId\n", 'flowlu_webhooks.txt');
            // Still move stage to Unassigned
            $unassignedStage = (int) env('FLOWLU_UNASSIGNED_STAGE_ID', 49);
            FlowLu::updateLead($leadId, ['pipeline_stage_id' => $unassignedStage]);
            return;
        }

        $foglio     = (string) ($parametri['foglio']     ?? '');
        $particella = (string) ($parametri['particella'] ?? '');
        $comune     = (string) ($parametri['comune']     ?? '');

        // Collect owners keyed by taxCode|foglio|particella
        $allOwners = [];

        foreach ($risultato['immobili'] ?? [] as $immobile) {
            foreach ($immobile['intestatari'] ?? [] as $owner) {
                $taxCode = $owner['cf'] ?? null;
                if (!$taxCode) continue;

                $key = $taxCode . '|' . $foglio . '|' . $particella;
                if (isset($allOwners[$key])) continue;

                $allOwners[$key] = [
                    'denominazione' => $owner['denominazione'] ?? '',
                    'cf'            => $taxCode,
                    'proprieta'     => $owner['proprieta']    ?? '',
                    'quota'         => $owner['quota']        ?? '',
                    'rendita'       => $immobile['rendita']   ?? '',
                    'categoria'     => $immobile['categoria'] ?? $immobile['qualita'] ?? '',
                    'indirizzo'     => $immobile['indirizzo'] ?? '',
                    'foglio'        => $foglio,
                    'particella'    => $particella,
                    'comune'        => $comune,
                ];
            }
        }

        Logger::log("FlowLu callback: owners found for lead $leadId: " . json_encode($allOwners) . "\n", 'flowlu_webhooks.txt');

        $listId = (int) env('FLOWLU_LAND_OWNERS_LIST_ID', 0);

        foreach ($allOwners as $owner) {
            $taxCode = $owner['cf'];

            // Find or create contact
            $contact = FlowLu::searchContactByTaxCode($taxCode);
            if ($contact === null) {
                $name      = $this->extractName($owner['denominazione']);
                $contactId = FlowLu::createContact($name, $taxCode);
                Logger::log("FlowLu callback: created contact $contactId for $taxCode\n", 'flowlu_webhooks.txt');
            } else {
                $contactId = isset($contact['id']) ? (int) $contact['id'] : null;
                Logger::log("FlowLu callback: found contact $contactId for $taxCode\n", 'flowlu_webhooks.txt');
            }

            if ($contactId) {
                FlowLu::linkContactToLead($contactId, $leadId);
            }

            // Create Land Owners record list entry
            if ($listId) {
                $res = FlowLu::createLandOwnerRecord($listId, $leadId, $owner, $owner['foglio'], $owner['particella'], $owner['comune']);
                Logger::log("FlowLu callback: created Land Owner record: " . json_encode($res) . "\n", 'flowlu_webhooks.txt');
            }
        }

        // Move opportunity to Unassigned stage
        $unassignedStage = (int) env('FLOWLU_UNASSIGNED_STAGE_ID', 49);
        $res = FlowLu::updateLead($leadId, ['pipeline_stage_id' => $unassignedStage]);
        Logger::log("FlowLu callback: updateLead $leadId response: " . json_encode($res) . "\n", 'flowlu_webhooks.txt');
    }

    private function extractName(string $denominazione): string
    {
        $name = preg_replace('/\s+(nato|nata)\s+.*/i', '', $denominazione);
        $name = preg_replace('/\s+con\s+sede\s+.*/i', '', $name);
        return trim($name);
    }
}

