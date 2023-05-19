<?php

namespace simplerest\controllers;

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
	
	// ..
}
