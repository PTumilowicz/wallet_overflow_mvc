<?php

namespace App\Models;

use PDO;

class Incomes extends \Core\Model {

    public $errors = [];

    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function addIncome($user_id) {
        $this->validate();

        if (empty($this->errors)) {
            $category_id = static::findInomeCategoryId($user_id, $this->category);

            $sql = 'INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment)
			        VALUES (:user_id, :income_category_assigned_to_user_id, :amount, :date_of_income, :income_comment)';
        
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':income_category_assigned_to_user_id', $category_id, PDO::PARAM_STR);
            $stmt->bindParam(':amount', $this->amount, PDO::PARAM_STR);
            $stmt->bindParam(':date_of_income', $this->date, PDO::PARAM_STR);
            $stmt->bindParam(':income_comment', $this->comment, PDO::PARAM_STR);
            return $stmt->execute();
        }
        return false;
    }

    public function validate() {
        //Income amount
        if ($this->amount == '') {
            $this->errors[] = 'Income amount is required';
        }

        //Income date
        if ($this->date == '') {
            $this->errors[] = 'Income date is required';
        }

        if ($this->date < '2000-01-01') {
            $this->errors[] = 'Income date out of range';
        }

        //Income comment
        $pattern = '/[^\wżźćńółęąśŻŹĆĄŚĘŁÓŃ ]/i';
        $result = preg_match($pattern, $this->comment);
        if ($result == 1) {
            $this->errors[] = 'Comment can include only alphanumerical values';
            $_SESSION['e_income_comment'] = 'Only alphanumeric values and spaces allowed.';
        }

        $_SESSION['e_income'] = 'Income was not added';
    }

    public static function findInomeCategoryId($user_id, $category) {
        $sql = 'SELECT id FROM incomes_category_assigned_to_users
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