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
}