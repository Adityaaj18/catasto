<?php

namespace simplerest\models;

use simplerest\core\Model;
use simplerest\core\libs\DB;
use simplerest\core\libs\Logger;
use simplerest\core\libs\Strings;

class MyModel extends Model 
{
    // protected $createdAt = 'gen_dtimFechaActualizacion';
    // protected $createdBy = 'usu_intIdCreador';
	// protected $updatedBy = 'usu_intIdActualizador';	
    
    function __construct(bool $connect = false, $schema = null, bool $load_config = true){
        parent::__construct($connect, $schema, $load_config);

        $this->unfill([
            'status',
            'response',
            'result'           
        ]);

        $this->hide([
            'created_at',
            'updated_at',
            'deleted_at',
            'req_uid'      // lo oculto
        ]);

        $this->field_names = array_merge($this->field_names, [
            'id'         => 'ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
            'cf_piva'    => 'CF/P.IVA',
            'result'     => 'Resultato',
        ]);

        /*
            Indicacion para el FronEnd

            Los campos que aparecen primero, deben mostrarse primero. Afecta a get_model_defs() y get_defs()
        */
        $this->field_order = [
            'response',
            'status',
            'result'
        ];
        
        $this->formatters['response'] = 'json';
        $this->formatters['result']   = 'json';
    }

    function onCreating(array &$data)
	{

	}

	function onUpdating(array &$data)
	{
        // Logger::dd($data, "DATA en " . __FUNCTION__ . " para tabla ". $this->table_name);

        if (isset( $data['response'])){
            $response = $data['response'];
            $response = json_decode($response, true);
            $req_uid  = $response['data']['id'] ?? null; 

            if (is_null($req_uid)){
                Logger::dd("req_uid vacio en " . __FUNCTION__ . " para tabla ". $this->table_name, '');
            } 

            $data['req_uid'] = $req_uid;
        }
	}

    function wp(){
		return $this->prefix('wp_');
	}

    protected function boot(){          
        if (empty($this->prefix) && (in_array(DB::getCurrentConnectionId(), ['woo3', null]))){
			$this->wp();
		}        
    }

    protected function init(){		
	}

    function __destruct()
    {

    }
}