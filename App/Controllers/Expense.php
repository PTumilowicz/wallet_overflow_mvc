<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\ExpenseCategory;
use \App\Models\PaymentMethod;
use \App\Models\Expenses;

class Expense extends Authenticated {

    protected function before() {
        parent::before();
        $this->user = Auth::getUser();
        $this->expense_categories = ExpenseCategory::getUserExpenseCategories($this->user->id);
        $this->payment_methods = PaymentMethod::getUserPaymentMethods($this->user->id);
    }

    protected function after() {
        unset($_SESSION['s_expense']);
        unset($_SESSION['e_expense']);
        unset($_SESSION['e_expense_comment']);
        unset($_SESSION['e_new_expense_category']);
        unset($_SESSION['s_new_expense_category']);
        unset($_SESSION['e_new_payment_method']);
        unset($_SESSION['s_new_payment_method']);
    }
    
    public function showAction() {
        View::renderTemplate('Expense/show.twig', [
            'user' => $this->user,
            'expense_categories' => $this->expense_categories,
            'payment_methods' => $this->payment_methods
        ]);
    }

    public function addAction() {
        $expense = new Expenses($_POST);
        $user_id = $this->user->id;

        if ($expense->addExpense($user_id)) {
            $_SESSION['s_expense'] = 'Expense added succesfully.';
        }
        $this->redirect('/expense/show');
    }
}