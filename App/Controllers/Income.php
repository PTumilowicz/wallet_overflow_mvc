<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

class Income extends Authenticated {

    protected function before() {
        parent::before();
        $this->user = Auth::getUser();
    }
    
    public function showAction() {
        View::renderTemplate('Income/show.twig', [
            'user' => $this->user
        ]);
    }
}