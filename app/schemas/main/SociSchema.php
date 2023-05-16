<?php

namespace simplerest\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class SociSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'soci',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'piva_cf_or_id', 'result', 'status', 'response', 'created_at', 'updated_at', 'deleted_at'],

			'attr_types'	=> [
				'id' => 'INT',
				'piva_cf_or_id' => 'STR',
				'result' => 'STR',
				'status' => 'STR',
				'response' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR',
				'deleted_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'result', 'status', 'response', 'created_at', 'updated_at', 'deleted_at'],

			'required'		=> ['piva_cf_or_id'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'piva_cf_or_id' => ['type' => 'str', 'max' => 60, 'required' => true],
				'result' => ['type' => 'str'],
				'status' => ['type' => 'str', 'max' => 20],
				'response' => ['type' => 'str'],
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

