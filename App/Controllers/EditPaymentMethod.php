<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\PaymentMethod;

class EditPaymentMethod extends Authenticated {

    protected function before() {
        parent::before();
        $this->user = Auth::getUser();
    }

    public function showAction() {
        $this->oldPaymentMethod = $_POST['payment_method'];

        View::renderTemplate('EditPaymentMethod/show.twig', [
            'user' => $this->user,
            'payment_method' => $this->oldPaymentMethod
        ]);
    }

    public function editAction() {
        $oldPaymentMethod = $_POST['old_payment_method'];
        $newPaymentMethod = $_POST['payment_method'];

        if (isset($oldPaymentMethod) && isset($newPaymentMethod)) {
            if (PaymentMethod::editPaymentMethod($this->user->id, $oldPaymentMethod, $newPaymentMethod)) {
                $_SESSION['s_payment_method_edit'] = 'Payment method edited sucessfully';
            } else {
                $_SESSION['e_payment_method_edit'] = 'Payment method not edited. Error occured.';
            }
        }
        $this->redirect('/settings/show');

        $this->redirect('/settings/show');
    }
}