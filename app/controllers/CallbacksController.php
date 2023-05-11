<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;

class CallbacksController extends MyController
{
    /*
        https://ticiwe.com/callbacks?r=realstate&sub=elenco_immobili
        https://ticiwe.com/callbacks?r=realstate&sub=prospetto_catastale
        https://ticiwe.com/callbacks?r=realstate&sub=ricerca_persona
        https://ticiwe.com/callbacks?r=realstate&sub=ricerca_nazionale
        https://ticiwe.com/callbacks?r=realstate&sub=indirizzo

        https://ticiwe.com/callbacks?r=company_info&sub=soci

        https://ticiwe.com/callbacks?r=rintracio&sub=telefoni
    */
    function index()
    {
        dd($_GET);             
    }
}

