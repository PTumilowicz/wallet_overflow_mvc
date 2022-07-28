<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

class Balance extends Authenticated {

    protected function before() {
        parent::before();
        $this->user = Auth::getUser();
    }
    
    public function showAction() {
        View::renderTemplate('Balance/show.twig', [
            'user' => $this->user
        ]);
    }
}