<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\IncomeCategory;
use \App\Models\Incomes;

class Income extends Authenticated {

    protected function before() {
        parent::before();
        $this->user = Auth::getUser();
        $this->income_categories = IncomeCategory::getUserIncomeCategories($this->user->id);
    }

    protected function after() {
        unset($_SESSION['e_income_comment']);
        unset($_SESSION['s_income']);
        unset($_SESSION['e_income']);
        unset($_SESSION['e_new_income_category']);
        unset($_SESSION['s_new_income_category']);
    }
    
    public function showAction() {
        View::renderTemplate('Income/show.twig', [
            'user' => $this->user,
            'income_categories' => $this-> income_categories
        ]);
    }

    public function addAction() {
        $income = new Incomes($_POST);
        $user_id = $this->user->id;

        if ($income->addIncome($user_id)) {
            $_SESSION['s_income'] = 'Income added succesfully.';
        }
        $this->redirect('/income/show');
    }

    public function addCategoryAction() {
        $incomeCategory = $_POST['new_income_category'];
        $user_id = $this->user->id;

        $categoryToUpper = strtoupper($incomeCategory);

        if (IncomeCategory::checkIfIncomeCategoryExists($user_id, $categoryToUpper)) {
            if (IncomeCategory::addIncomeCategory($user_id, $incomeCategory)) {
                $_SESSION['s_new_income_category'] = 'New income category added.';
            }
        } else {
            $_SESSION['e_new_income_category'] = 'Category already exists.';
        }
        $this->redirect('/income/show');
    }
}