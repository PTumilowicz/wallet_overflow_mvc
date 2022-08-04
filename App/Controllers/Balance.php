<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\DateValidator;
use \App\Models\Incomes;
use \App\Models\Expenses;

class Balance extends Authenticated {

    protected function before() {
        parent::before();
        $this->user = Auth::getUser();
    }

    protected function after() {
        unset($_SESSION['e_period']);
    }
    
    public function showAction() {
        View::renderTemplate('Balance/show.twig', [
            'user' => $this->user
        ]);
    }

    public function calculateAction() {
        if (!isset($_POST['period'])) {
            $this->redirect('/balance/show');
            exit;
        }

        $user_id = $this->user->id;
        
        $currentYear = date('Y');
        $currentMonth = date('m');

        $period = $_POST['period'];

        $dateRange = DateValidator::validateDate($period, $currentYear, $currentMonth);

        if (empty($dateRange)) {
            $this->redirect('/balance/show');
            exit;
        }

        $start_date = $dateRange['start_date'];
        $end_date = $dateRange['end_date'];

        $income_balance = Incomes::getIncomeBalance($user_id, $start_date, $end_date);
        $expense_balance = Expenses::getExpenseBalance($user_id, $start_date, $end_date);

        View::renderTemplate('Balance/show.twig', [
            'user' => $this->user,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'income_balance' => $income_balance,
            'expense_balance' => $expense_balance
        ]);
    }

}