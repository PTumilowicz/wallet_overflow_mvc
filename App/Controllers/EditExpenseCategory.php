<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\ExpenseCategory;

class EditExpenseCategory extends Authenticated {

    protected function before() {
        parent::before();
        $this->user = Auth::getUser();
    }

    public function showAction() {
        $this->oldCategory = $_POST['category'];

        View::renderTemplate('EditExpenseCategory/show.twig', [
            'user' => $this->user,
            'expense_category' => $this->oldCategory
        ]);
    }

    public function editAction() {
        $oldCategory = $_POST['old_category'];
        $newCategory = $_POST['category'];
        $cashLimit = $_POST['cash_limit'];

        if (isset($oldCategory) && isset($newCategory)) {
            if (ExpenseCategory::editExpenseCategory($this->user->id, $oldCategory, $newCategory, $cashLimit)) {
                $_SESSION['s_expense_category_edit'] = 'Expense category edited sucessfully';
            } else {
                $_SESSION['e_expense_category_edit'] = 'Expense category not edited. Error occured.';
            }
        }
        $this->redirect('/settings/show');
    }
}