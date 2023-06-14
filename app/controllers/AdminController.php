<?php

namespace simplerest\controllers;

use simplerest\core\Acl;
use simplerest\core\traits\PagesTrait;
use simplerest\controllers\MyController;

class AdminController extends MyController
{    
	use PagesTrait;

	// 'tabulator', 'main',...
	public $default_page  = 'tabulator';

	public $tpl           = 'templates/adminlte_tpl.php'; //
	public $tpl_params    = [
		'brand_name' => 'Catasto',
		'logo'       => 'img/gmap.png',
		'logo_alt'   => 'Catasto',
		'footer'     => '	<!-- To the right -->
							<div class="float-right d-none d-sm-inline">
							</div>
							
							<!-- Default to the left        -->
							<!-- some info data of creators -->'
	];	

	function __construct()
	{
		parent::__construct();

		/*
			Quiero poder usar algunos .js en cualquier escenario dentro de Admin
		*/
		
		js_file('vendors/sweetalert2/sweetalert2@11.js');
		js_file('js/my_datatables/custom.js');
	}
	
	// ..
}
