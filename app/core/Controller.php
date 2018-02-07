<?php

namespace app\core;

abstract class Controller
{
    protected $content_view = '';
    protected $parameters = ['localization' => 'ua'];
    protected $localization = 'ua';

    public function __construct()
    {
        //$this->parameters['localization'] = 'ua';

        if ($_SESSION['localization']) {
            $this->localization = $_SESSION['localization'];
            $this->parameters['localization'] = $_SESSION['localization'];
        }
        if ($_SESSION['alert_success']){
            $this->parameters['alert_success'] = $_SESSION['alert_success'];
            unset($_SESSION['alert_success']);
        }
        if ($_SESSION['alert_error']) {
            $this->parameters['alert_error'] = $_SESSION['alert_error'];
            unset($_SESSION['alert_error']);
        }
    }

    public function index()
    {
        $view = new View();
        $view->generate($this->localization, $this->content_view, $this->parameters);
    }
}