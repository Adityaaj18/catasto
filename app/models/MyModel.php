<?php

namespace simplerest\models;

use simplerest\core\Model;
use simplerest\core\libs\DB;
use simplerest\core\libs\Logger;

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
        ]);

        $this->field_names = array_merge($this->field_names, [
            'id'         => 'ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
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
        
        $this->formatters['response'] = 'textarea';
        $this->formatters['result']   = 'textarea';
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