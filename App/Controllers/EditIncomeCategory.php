<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\IncomeCategory;

class EditIncomeCategory extends Authenticated {

    protected function before() {
        parent::before();
        $this->user = Auth::getUser();
    }

    public function showAction() {
        $this->oldCategory = $_POST['category'];

        View::renderTemplate('EditIncomeCategory/show.twig', [
            'user' => $this->user,
            'income_category' => $this->oldCategory
        ]);
    }

    public function editAction() {
        $oldCategory = $_POST['old_category'];
        $newCategory = $_POST['category'];

        IncomeCategory::editIncomeCategory($this->user->id, $oldCategory, $newCategory);

        if (isset($oldCategory) && isset($newCategory)) {
            if (IncomeCategory::editIncomeCategory($this->user->id, $oldCategory, $newCategory)) {
                $_SESSION['s_income_category_edit'] = 'Category edited sucessfully';
            } else {
                $_SESSION['e_income_category_edit'] = 'Category not edited. Error occured.';
            }
        }
        $this->redirect('/settings/show');

        $this->redirect('/settings/show');
    }

    // public function updateAction() {
    //     if ($this->user->updateProfile($_POST)) {
    //         Flash::addMessage('Changes saved');
    //         $this->redirect('/profile/show');
    //     } else {
    //         View::renderTemplate('Profile/edit.twig', [
    //             'user' => $this->user
    //         ]);
    //     }
    // }
}