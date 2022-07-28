<?php

namespace App\Controllers;

use \Core\View;

class Home extends Authenticated {

    public function indexAction() {
        View::renderTemplate('Home/index.twig');
    }
}