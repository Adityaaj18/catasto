<?php

namespace simplerest\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class IndirizoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'indirizo',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'id_indirizzo', 'dal_civico', 'al_civico', 'result', 'status', 'created_at', 'updated_at', 'deleted_at'],

			'attr_types'	=> [
				'id' => 'INT',
				'id_indirizzo' => 'STR',
				'dal_civico' => 'STR',
				'al_civico' => 'STR',
				'result' => 'STR',
				'status' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR',
				'deleted_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'result', 'status', 'created_at', 'updated_at', 'deleted_at'],

			'required'		=> ['id_indirizzo', 'dal_civico', 'al_civico'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'id_indirizzo' => ['type' => 'str', 'max' => 60, 'required' => true],
				'dal_civico' => ['type' => 'str', 'max' => 30, 'required' => true],
				'al_civico' => ['type' => 'str', 'max' => 30, 'required' => true],
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

