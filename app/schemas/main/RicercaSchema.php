<?php

namespace simplerest\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class RicercaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'ricerca',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'tipo_catasto', 'provincia', 'comune', 'sezione', 'foglio', 'particella', 'partita', 'indirizzo', 'cf_ditta', 'cf_socio', 'telefono', 'last_req', 'status', 'created_at', 'updated_at', 'deleted_at'],

			'attr_types'	=> [
				'id' => 'INT',
				'tipo_catasto' => 'STR',
				'provincia' => 'STR',
				'comune' => 'STR',
				'sezione' => 'STR',
				'foglio' => 'STR',
				'particella' => 'STR',
				'partita' => 'STR',
				'indirizzo' => 'STR',
				'cf_ditta' => 'STR',
				'cf_socio' => 'STR',
				'telefono' => 'STR',
				'last_req' => 'STR',
				'status' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR',
				'deleted_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'tipo_catasto', 'provincia', 'comune', 'sezione', 'foglio', 'particella', 'partita', 'indirizzo', 'cf_ditta', 'cf_socio', 'telefono', 'last_req', 'status', 'created_at', 'updated_at', 'deleted_at'],

			'required'		=> [],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'tipo_catasto' => ['type' => 'str', 'max' => 20],
				'provincia' => ['type' => 'str', 'max' => 2],
				'comune' => ['type' => 'str', 'max' => 60],
				'sezione' => ['type' => 'str', 'max' => 60],
				'foglio' => ['type' => 'str', 'max' => 60],
				'particella' => ['type' => 'str', 'max' => 60],
				'partita' => ['type' => 'str', 'max' => 60],
				'indirizzo' => ['type' => 'str', 'max' => 200],
				'cf_ditta' => ['type' => 'str', 'max' => 30],
				'cf_socio' => ['type' => 'str', 'max' => 30],
				'telefono' => ['type' => 'str', 'max' => 25],
				'last_req' => ['type' => 'str', 'max' => 20],
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

