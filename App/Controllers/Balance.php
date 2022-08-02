<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
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
        $user_id = $this->user->id;
        
        $currentYear = date('Y');
        $currentMonth = date('m');

        $period = $_POST['period'];

        $dateRange = static::validateDate($period, $currentYear, $currentMonth);

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
            'income_balance' => $income_balance,
            'expense_balance' => $expense_balance
        ]);
        exit;
    }

    public function validateDate($period, $currentYear, $currentMonth) {
        $dateRange = [];

        if ($period == 'current_month') {
            $endDay = static::findLastDayOfMonth($currentMonth, $currentYear);

            $startDate = $currentYear .'-'. $currentMonth . '-01';
            $endDate = $currentYear .'-'. $currentMonth . '-' . $endDay;
        }

        if ($period == 'last_month') {
            $lastMonth = '';
            $year = '';

            if ($currentMonth != '01') {
                $lastMonth = (int) $currentMonth - 1;
                
                if (strlen($lastMonth) == 1) {
                    $lastMonth = '0' . $lastMonth;
                }

                $year = $currentYear;
            } else {
                $lastMonth = '12';
                $year = (int) $currentYear - 1;
            }

            $endDay = static::findLastDayOfMonth($lastMonth, $year);
            
            $startDate = $year .'-'. $lastMonth . '-01';
            $endDate = $year .'-'. $lastMonth . '-' . $endDay;
        }

        if ($_POST['period'] == 'current_year') {
            $startDate = $currentYear .'-01-01';
            $endDate = $currentYear .'-12-31';
        }

        if ($_POST['period'] == 'custom_period') {
            $startDate = $_POST['start_date'];
            $endDate = $_POST['end_date'];

            if ($startDate > $endDate) $_SESSION['e_period'] = 'Start date is later than end date.';
            return $dateRange;
        }

        $dateRange = [
            "start_date" => $startDate,
            "end_date" => $endDate
        ];

        $_SESSION['start_date'] = $startDate;
        $_SESSION['end_date'] = $endDate;

        return $dateRange;
    }
}