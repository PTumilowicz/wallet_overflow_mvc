<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\ExpenseCategory;
use \App\Models\PaymentMethod;

class Expense extends Authenticated {

    protected function before() {
        parent::before();
        $this->user = Auth::getUser();
        $this->expense_categories = ExpenseCategory::getUserExpenseCategories($this->user->id);
        $this->payment_methods = PaymentMethod::getUserPaymentMethods($this->user->id);
    }
    
    public function showAction() {
        View::renderTemplate('Expense/show.twig', [
            'user' => $this->user,
            'expense_categories' => $this->expense_categories,
            'payment_methods' => $this->payment_methods
        ]);
    }
}