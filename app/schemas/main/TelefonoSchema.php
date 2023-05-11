<?php

namespace simplerest\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TelefonoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'telefono',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'cf_piva', 'result', 'status', 'created_at', 'updated_at', 'deleted_at'],

			'attr_types'	=> [
				'id' => 'INT',
				'cf_piva' => 'STR',
				'result' => 'STR',
				'status' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR',
				'deleted_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'cf_piva', 'result', 'status', 'created_at', 'updated_at', 'deleted_at'],

			'required'		=> [],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'cf_piva' => ['type' => 'str', 'max' => 60],
				'result' => ['type' => 'str'],
				'status' => ['type' => 'str', 'max' => 20],
				'created_at' => ['type' => 'datetime'],
				'updated_at' => ['type' => 'datetime'],
				'deleted_at' => ['type' => 'datetime']
			],

			'fks' 			=> [],

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

