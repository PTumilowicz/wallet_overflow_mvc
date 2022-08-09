<?php

namespace App\Models;

use PDO;

class ExpenseCategory extends \Core\Model {

    public $errors = [];

    public static function getUserExpenseCategories($user_id) {
        $sql = 'SELECT name FROM expenses_category_assigned_to_users
                WHERE user_id = :userId';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userId', $user_id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function checkIfExpenseCategoryExists($user_id, $category) {
        $sql = 'SELECT name FROM (SELECT UPPER(name) AS name FROM expenses_category_assigned_to_users
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

    public static function addExpenseCategory($user_id, $expenseCategory) {

        if (static::validateCategory($expenseCategory)) {
            $sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name)
            VALUES (:user_id, :name)';
    
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':name', $expenseCategory, PDO::PARAM_STR);
    
            return $stmt->execute();
        }
        $_SESSION['e_new_expense_category'] = 'Only alphanumeric values and spaces allowed.';
        return false;
    }

    public static function validateCategory($expenseCategory) {
        $pattern = '/[^\wżźćńółęąśŻŹĆĄŚĘŁÓŃ0-9 ]/i';
        $result = preg_match($pattern, $expenseCategory);

        if ($result == 1) {
            return false;
        }
        return true;
    }

    public static function checkIfPaymentMethodExists($user_id, $payment_method) {
        $sql = 'SELECT name FROM (SELECT UPPER(name) AS name FROM payment_methods_assigned_to_users
                WHERE user_id = :user_id) a WHERE name = :payment_method';

        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':payment_method', $payment_method, PDO::PARAM_STR);

        $stmt->execute();
        
        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            return true;
        }
        return false;
    }

    public static function addPaymentMethod($user_id, $payment_method) {

        if (static::validatePaymentMethod($payment_method)) {
            $sql = 'INSERT INTO payment_methods_assigned_to_users (user_id, name)
                    VALUES (:user_id, :name)';
    
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':name', $payment_method, PDO::PARAM_STR);
    
            return $stmt->execute();
        }
        return false;
    }

    public static function validatePaymentMethod($payment_method) {
        $pattern = '/[^\wżźćńółęąśŻŹĆĄŚĘŁÓŃ0-9 ]/i';
        $result = preg_match($pattern, $payment_method);

        if ($result == 1) {
            $_SESSION['e_new_payment_method'] = 'Only alphanumeric values and spaces allowed.';
            return false;
        }
        return true;
    }
    
    public static function removeExpenseCategory($user_id, $expenseCategory) {
        $categoryId = static::getCategoryId($user_id, $expenseCategory);

        $sql = 'DELETE FROM `expenses_category_assigned_to_users`
                WHERE `user_id` = :user_id AND `name` = :name
                LIMIT 1';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $expenseCategory, PDO::PARAM_STR);

        if ($stmt->execute()) {
            static::removeCategoryExpenses($user_id, $categoryId);
            return true;
        }

        return false;
    }

    public static function getCategoryId($user_id, $expenseCategory) {
        $sql = 'SELECT id FROM `expenses_category_assigned_to_users`
                WHERE `user_id` = :user_id AND `name` = :name
                LIMIT 1';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $expenseCategory, PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetch();
        return $result['id'];
    }

    public static function removeCategoryExpenses($user_id, $expenseCategoryId) {
        $sql = 'DELETE FROM `expenses`
                WHERE `user_id` = :user_id AND `expense_category_assigned_to_user_id` = :expenseCategoryId';
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':expenseCategoryId', $expenseCategoryId, PDO::PARAM_INT);

        $stmt->execute();
    }

    public static function editExpenseCategory($user_id, $oldCategory, $newCategory) {
        if (static::validateCategory($newCategory)) {
            $sql = 'UPDATE `expenses_category_assigned_to_users`
                    SET `name`= :new_category WHERE `user_id` = :user_id AND `name` = :old_category
                    LIMIT 1';
    
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':old_category', $oldCategory, PDO::PARAM_STR);
            $stmt->bindParam(':new_category', $newCategory, PDO::PARAM_STR);
    
            return $stmt->execute();
        }
        return false;
    }
}