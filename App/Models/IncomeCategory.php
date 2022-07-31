<?php

namespace App\Models;

use PDO;

class IncomeCategory extends \Core\Model {

    public $errors = [];

    public static function getUserIncomeCategories($user_id) {
        $sql = 'SELECT name FROM incomes_category_assigned_to_users
                WHERE user_id = :userId';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userId', $user_id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function checkIfIncomeCategoryExists($user_id, $category) {
        $sql = 'SELECT name FROM (SELECT UPPER(name) AS name FROM incomes_category_assigned_to_users
                WHERE user_id = :userId) a WHERE name = :category';

        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userId', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':category', $category, PDO::PARAM_STR);

        $stmt->execute();
        
        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            return true;
        }
        return false;
    }

    public static function addIncomeCategory($user_id, $incomeCategory) {

        if (static::validateCategory($incomeCategory)) {
            $sql = 'INSERT INTO incomes_category_assigned_to_users (user_id, name)
            VALUES (:user_id, :name)';
    
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':name', $incomeCategory, PDO::PARAM_STR);
    
            return $stmt->execute();
        }
        return false;
    }

    public static function validateCategory($incomeCategory) {
        $pattern = '/[^\wżźćńółęąśŻŹĆĄŚĘŁÓŃ0-9 ]/i';
        $result = preg_match($pattern, $incomeCategory);

        if ($result == 1) {
            $_SESSION['e_new_income_category'] = 'Only alphanumeric values and spaces allowed.';
            return false;
        }
        return true;
    }
}