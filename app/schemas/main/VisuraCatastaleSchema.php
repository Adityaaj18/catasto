<?php

namespace simplerest\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class VisuraCatastaleSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'visura_catastale',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'entita', 'id_immobile', 'tipo_catasto', 'provincia', 'comune', 'foglio', 'particella', 'subalterno', 'tipo_visura', 'richiedente', 'result', 'status', 'response', 'created_at', 'updated_at', 'deleted_at', 'req_uid'],

			'attr_types'		=> [
				'id' => 'INT',
				'entita' => 'STR',
				'id_immobile' => 'STR',
				'tipo_catasto' => 'STR',
				'provincia' => 'STR',
				'comune' => 'STR',
				'foglio' => 'STR',
				'particella' => 'STR',
				'subalterno' => 'INT',
				'tipo_visura' => 'STR',
				'richiedente' => 'STR',
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

			'nullable'			=> ['id', 'id_immobile', 'provincia', 'comune', 'foglio', 'particella', 'subalterno', 'result', 'status', 'response', 'created_at', 'updated_at', 'deleted_at', 'req_uid'],

			'required'			=> ['entita', 'tipo_catasto', 'tipo_visura', 'richiedente'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'entita' => ['type' => 'str', 'required' => true],
				'id_immobile' => ['type' => 'str', 'max' => 240],
				'tipo_catasto' => ['type' => 'str', 'max' => 2, 'required' => true],
				'provincia' => ['type' => 'str', 'max' => 60],
				'comune' => ['type' => 'str', 'max' => 60],
				'foglio' => ['type' => 'str', 'max' => 60],
				'particella' => ['type' => 'str', 'max' => 60],
				'subalterno' => ['type' => 'int'],
				'tipo_visura' => ['type' => 'str', 'required' => true],
				'richiedente' => ['type' => 'str', 'max' => 60, 'required' => true],
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

