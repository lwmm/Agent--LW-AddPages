<?php

namespace AgentAddPages\Views;

class Navigation
{

    public function __construct()
    {
        
    }

    public function render()
    {
        $view = new \lw_view(dirname(__FILE__) . '/Templates/Navigation.phtml');
        return $view->render();
    }

}