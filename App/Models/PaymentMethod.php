<?php

namespace App\Models;

use PDO;

class PaymentMethod extends \Core\Model {

    public $errors = [];

    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public static function getUserPaymentMethods($user_id) {
        $sql = 'SELECT name FROM payment_methods_assigned_to_users
                WHERE user_id = :userId';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userId', $user_id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function removePaymentMethod($user_id, $paymentMethod) {
        $paymentMethodId = static::getPaymentMethodId($user_id, $paymentMethod);

        $sql = 'DELETE FROM `payment_methods_assigned_to_users`
                WHERE `user_id` = :user_id AND `name` = :name
                LIMIT 1';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $paymentMethod, PDO::PARAM_STR);

        if ($stmt->execute()) {
            static::removePaymentMethodExpenses($user_id, $paymentMethodId);
            return true;
        }
        return false;
    }

    public static function getPaymentMethodId($user_id, $paymentMethod) {
        $sql = 'SELECT id FROM `payment_methods_assigned_to_users`
                WHERE `user_id` = :user_id AND `name` = :name
                LIMIT 1';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $paymentMethod, PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetch();
        return $result['id'];
    }

    public static function removePaymentMethodExpenses($user_id, $paymentMethodId) {
        $sql = 'DELETE FROM `expenses`
                WHERE `user_id` = :user_id AND `payment_method_assigned_to_user_id` = :paymentMethodId';
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':paymentMethodId', $paymentMethodId, PDO::PARAM_INT);

        $stmt->execute();
    }

    public static function editPaymentMethod($user_id, $oldPaymentMethod, $newPaymentMethod) {
        if (static::validateCategory($newPaymentMethod)) {
            $sql = 'UPDATE `payment_methods_assigned_to_users`
                    SET `name`= :new_payment_method WHERE `user_id` = :user_id AND `name` = :old_payment_method
                    LIMIT 1';
    
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':old_payment_method', $oldPaymentMethod, PDO::PARAM_STR);
            $stmt->bindParam(':new_payment_method', $newPaymentMethod, PDO::PARAM_STR);
    
            return $stmt->execute();
        }
        return false;
    }

    public static function validateCategory($paymentMethod) {
        $pattern = '/[^\wżźćńółęąśŻŹĆĄŚĘŁÓŃ0-9 ]/i';
        $result = preg_match($pattern, $paymentMethod);

        if ($result == 1) {
            return false;
        }
        return true;
    }
}