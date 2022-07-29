<?php

namespace App\Models;

use PDO;

class Expenses extends \Core\Model {

    public $errors = [];

    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function addExpense($user_id) {
        $this->validate();

        if (empty($this->errors)) {
            $category_id = static::findExpenseCategoryId($user_id, $this->category);
            $payment_method_id = static::findPaymentMethodId($user_id, $this->payment_method);

            $sql = 'INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment)
			        VALUES (:user_id, :expense_category_assigned_to_user_id, :payment_method_assigned_to_user_id, :amount, :date_of_expense, :expense_comment)';
        
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':expense_category_assigned_to_user_id', $category_id, PDO::PARAM_STR);
            $stmt->bindParam(':payment_method_assigned_to_user_id', $payment_method_id, PDO::PARAM_STR);
            $stmt->bindParam(':amount', $this->amount, PDO::PARAM_STR);
            $stmt->bindParam(':date_of_expense', $this->date, PDO::PARAM_STR);
            $stmt->bindParam(':expense_comment', $this->comment, PDO::PARAM_STR);
            return $stmt->execute();
        }
        return false;
    }

    public function validate() {
        //Expense amount
        if ($this->amount == '') {
            $this->errors[] = 'Expense amount is required';
        }

        //Expense date
        if ($this->date == '') {
            $this->errors[] = 'Expense date is required';
        }

        if ($this->date < '2000-01-01') {
            $this->errors[] = 'Expense date out of range';
        }

        //Expense comment
        $pattern = '/[^\wżźćńółęąśŻŹĆĄŚĘŁÓŃ ]/i';
        $result = preg_match($pattern, $this->comment);
        if ($result == 1) {
            $this->errors[] = 'Comment can include only alphanumerical values';
            $_SESSION['e_expense_comment'] = 'Only alphanumeric values and spaces allowed.';
        }

        $_SESSION['e_expense'] = 'Expense was not added';
    }

    public static function findExpenseCategoryId($user_id, $category) {
        $sql = 'SELECT id FROM expenses_category_assigned_to_users
                WHERE user_id = :user_id AND name = :name LIMIT 1';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $category, PDO::PARAM_STR);
        $stmt->execute();

        $fetchArray = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fetchArray['id'];
    }

    public static function findPaymentMethodId($user_id, $category) {
        $sql = 'SELECT id FROM payment_methods_assigned_to_users
                WHERE user_id = :user_id AND name = :name LIMIT 1';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $category, PDO::PARAM_STR);
        $stmt->execute();

        $fetchArray = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fetchArray['id'];
    }
}