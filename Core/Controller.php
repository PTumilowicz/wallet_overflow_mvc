<?php

namespace Core;

use \App\Auth;

abstract class Controller {
    protected $route_params = [];

    public function __construct($route_params) {
        $this->route_params = $route_params;
    }

    public function __call($name, $args) {
        $method = $name . 'Action';

        if (method_exists($this, $method)) {
            if ($this->before() !== false) {
                call_user_func_array([$this, $method], $args);
                $this->after();
            }
        } else {
            //echo "Method $method not found in controller " . get_class($this);
            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
    }

    protected function before() {
    }

    protected function after() {

    }

    public function redirect($url) {
        header('Location: http://' . $_SERVER['HTTP_HOST'] . $url, true, 303);
        exit;
    }

    public function requireLogin() {
        if (!Auth::getUser()) {
            Auth::rememberRequestedPage();
            $this->redirect('/login');
        }
    }

    protected function findLastDayOfMonth($month, $year) {
        switch($month) {
            case '01': case '03': case '05': case '07': case '08': case '10': case '12':
                return '31';
            case '04': case '06': case '09': case '11':
                return '30';
            case '02':
                return ($year % 4 == 0 && $year % 100 != 0) || $year % 400 == 0 ? '29' : '28';
        }
    }

}