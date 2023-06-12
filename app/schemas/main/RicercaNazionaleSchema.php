<?php

namespace simplerest\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class RicercaNazionaleSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'ricerca_nazionale',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'cf_piva', 'tipo_catasto', 'result', 'status', 'response', 'created_at', 'updated_at', 'deleted_at', 'req_uid'],

			'attr_types'		=> [
				'id' => 'INT',
				'cf_piva' => 'STR',
				'tipo_catasto' => 'STR',
				'result' => 'STR',
				'status' => 'STR',
				'response' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR',
				'deleted_at' => 'STR',
				'req_uid' => 'STR'
			],

			'attr_type_detail'	=> [
				'result' => 'JSON',
				'response' => 'JSON'
			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'result', 'status', 'response', 'created_at', 'updated_at', 'deleted_at', 'req_uid'],

			'required'			=> ['cf_piva', 'tipo_catasto'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'cf_piva' => ['type' => 'str', 'max' => 30, 'required' => true],
				'tipo_catasto' => ['type' => 'str', 'max' => 60, 'required' => true],
				'result' => ['type' => 'str'],
				'status' => ['type' => 'str', 'max' => 20],
				'response' => ['type' => 'str'],
				'created_at' => ['type' => 'datetime'],
				'updated_at' => ['type' => 'datetime'],
				'deleted_at' => ['type' => 'datetime'],
				'req_uid' => ['type' => 'str', 'max' => 240]
			],

			'fks' 				=> [],

			'relationships' => [
				
			],

			'expanded_relationships' => array (
),

			'relationships_from' => [
				
			],

			'expanded_relationships_from' => array (
)
		];
	}	
}

