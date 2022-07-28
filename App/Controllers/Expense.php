<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

class Expense extends Authenticated {

    protected function before() {
        parent::before();
        $this->user = Auth::getUser();
    }
    
    public function showAction() {
        View::renderTemplate('Expense/show.twig', [
            'user' => $this->user
        ]);
    }
}