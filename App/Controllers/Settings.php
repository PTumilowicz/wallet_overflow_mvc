<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\IncomeCategory;
use \App\Models\ExpenseCategory;
use \App\Models\PaymentMethod;

class Settings extends Authenticated {

    protected function before() {
        parent::before();
        $this->user = Auth::getUser();
        $this->income_categories = IncomeCategory::getUserIncomeCategories($this->user->id);
        $this->expense_categories = ExpenseCategory::getUserExpenseCategories($this->user->id);
        $this->payment_methods = PaymentMethod::getUserPaymentMethods($this->user->id);
    }

    protected function after() {
        unset($_SESSION['e_new_income_category']);
        unset($_SESSION['s_new_income_category']);
        unset($_SESSION['e_new_expense_category']);
        unset($_SESSION['s_new_expense_category']);
        unset($_SESSION['e_new_payment_method']);
        unset($_SESSION['s_new_payment_method']);
        unset($_SESSION['e_income_category_remove']);
        unset($_SESSION['s_income_category_remove']);
        unset($_SESSION['e_income_category_edit']);
        unset($_SESSION['s_income_category_edit']);
        unset($_SESSION['e_expense_category_remove']);
        unset($_SESSION['s_expense_category_remove']);
        unset($_SESSION['e_expense_category_edit']);
        unset($_SESSION['s_expense_category_edit']);
        unset($_SESSION['e_payment_method_remove']);
        unset($_SESSION['s_payment_method_remove']);
        unset($_SESSION['e_payment_method_edit']);
        unset($_SESSION['s_payment_method_edit']);
    }

    public function showAction() {
        View::renderTemplate('Settings/show.twig', [
            'user' => $this->user,
            'income_categories' => $this-> income_categories,
            'expense_categories' => $this-> expense_categories,
            'payment_methods' => $this-> payment_methods
        ]);
    }

    public function addIncomeCategoryAction() {
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
        $this->redirect('/settings/show');
    }

    public function addExpenseCategoryAction() {
        $expenseCategory = $_POST['new_expense_category'];
        $user_id = $this->user->id;

        $categoryToUpper = strtoupper($expenseCategory);

        if (ExpenseCategory::checkIfExpenseCategoryExists($user_id, $categoryToUpper)) {
            if (ExpenseCategory::addExpenseCategory($user_id, $expenseCategory)) {
                $_SESSION['s_new_expense_category'] = 'New expense category added.';
            }
        } else {
            $_SESSION['e_new_expense_category'] = 'Category already exists.';
        }
        $this->redirect('/settings/show');
    }

    public function addPaymentMethodAction() {
        $paymentMethod = $_POST['new_payment_method'];
        $user_id = $this->user->id;

        $paymentMethodToUpper = strtoupper($paymentMethod);

        if (ExpenseCategory::checkIfPaymentMethodExists($user_id, $paymentMethodToUpper)) {
            if (ExpenseCategory::addPaymentMethod($user_id, $paymentMethod)) {
                $_SESSION['s_new_payment_method'] = 'New payment method added.';
            }
        } else {
            $_SESSION['e_new_payment_method'] = 'Payment method already exists.';
        }
        $this->redirect('/settings/show');
    }

    public function removeIncomeCategoryAction() {
        $incomeCategory = $_POST['category'];
        $user_id = $this->user->id;

        if (isset($incomeCategory)) {
            if (IncomeCategory::removeIncomeCategory($user_id, $incomeCategory) == 1) {
                $_SESSION['s_income_category_remove'] = 'Category removed sucessfully';
            } else {
                $_SESSION['e_income_category_remove'] = 'Category not removed. Error occured.';
            }
        }
        $this->redirect('/settings/show');
    }

    public function removeExpenseCategoryAction() {
        $expenseCategory = $_POST['category'];
        $user_id = $this->user->id;

        if (isset($expenseCategory)) {
            if (ExpenseCategory::removeExpenseCategory($user_id, $expenseCategory) == 1) {
                $_SESSION['s_expense_category_remove'] = 'Expense category removed sucessfully';
            } else {
                $_SESSION['e_expense_category_remove'] = 'Expense category not removed. Error occured.';
            }
        }
        $this->redirect('/settings/show');
    }

    public function removePaymentMethodAction() {
        $expensePaymentMethod = $_POST['payment_method'];
        $user_id = $this->user->id;

        if (isset($expensePaymentMethod)) {
            if (PaymentMethod::removePaymentMethod($user_id, $expensePaymentMethod) == 1) {
                $_SESSION['s_payment_method_remove'] = 'Payment method removed sucessfully';
            } else {
                $_SESSION['e_payment_method_remove'] = 'Payment method not removed. Error occured.';
            }
        }
        $this->redirect('/settings/show');
    }
}