<?php

namespace simplerest\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ElencoImmobiliSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'elenco_immobili',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'tipo_catasto', 'provincia', 'comune', 'sezione', 'sezione_urbana', 'foglio', 'particella', 'result', 'status', 'created_at', 'updated_at', 'deleted_at', 'response', 'req_uid'],

			'attr_types'		=> [
				'id' => 'INT',
				'tipo_catasto' => 'STR',
				'provincia' => 'STR',
				'comune' => 'STR',
				'sezione' => 'STR',
				'sezione_urbana' => 'STR',
				'foglio' => 'STR',
				'particella' => 'STR',
				'result' => 'STR',
				'status' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR',
				'deleted_at' => 'STR',
				'response' => 'STR',
				'req_uid' => 'STR'
			],

			'attr_type_detail'	=> [
				'result' => 'JSON',
				'response' => 'JSON'
			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'sezione', 'sezione_urbana', 'result', 'status', 'created_at', 'updated_at', 'deleted_at', 'response', 'req_uid'],

			'required'			=> ['tipo_catasto', 'provincia', 'comune', 'foglio', 'particella'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'tipo_catasto' => ['type' => 'str', 'required' => true],
				'provincia' => ['type' => 'str', 'max' => 60, 'required' => true],
				'comune' => ['type' => 'str', 'max' => 60, 'required' => true],
				'sezione' => ['type' => 'str', 'max' => 60],
				'sezione_urbana' => ['type' => 'str', 'max' => 60],
				'foglio' => ['type' => 'str', 'max' => 60, 'required' => true],
				'particella' => ['type' => 'str', 'max' => 60, 'required' => true],
				'result' => ['type' => 'str'],
				'status' => ['type' => 'str', 'max' => 20],
				'created_at' => ['type' => 'datetime'],
				'updated_at' => ['type' => 'datetime'],
				'deleted_at' => ['type' => 'datetime'],
				'response' => ['type' => 'str'],
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

