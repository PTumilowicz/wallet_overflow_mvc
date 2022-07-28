<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\IncomeCategory;

class Income extends Authenticated {

    protected function before() {
        parent::before();
        $this->user = Auth::getUser();
        $this->income_categories = IncomeCategory::getUserIncomeCategories($this->user->id);
    }
    
    public function showAction() {
        View::renderTemplate('Income/show.twig', [
            'user' => $this->user,
            'income_categories' => $this-> income_categories
        ]);
    }
}