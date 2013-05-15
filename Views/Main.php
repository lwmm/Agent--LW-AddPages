<?php

namespace AgentAddPages\Views;

class Main
{

    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function render($templates, $values =false, $errors = false)
    {
        $baseUrl = substr(\AgentAddPages\Services\Page::getUrl(), 0, strpos(\AgentAddPages\Services\Page::getUrl(), "index.php")) . "admin.php?obj=addpages";

        $view = new \lw_view(dirname(__FILE__) . '/Templates/Main.phtml');

        $view->bootstrapCSS = $this->config["url"]["media"] . "bootstrap/css/bootstrap.min.css";
        $view->bootstrapJS = $this->config["url"]["media"] . "bootstrap/js/bootstrap.min.js";
        $view->iconPage = $this->config["url"]["media"]."pics/fatcow_icons/16x16_0680/page_white_text.png";
        $view->iconPageAdd = $this->config["url"]["media"]."pics/fatcow_icons/16x16_0680/page_white_put.png";
        $view->iconAdd = $this->config["url"]["media"]."pics/add.png";
        
        $view->baseUrl = $baseUrl;
        
        $view->templates = $templates;
        $view->values = $values;
        $view->errors = $errors;

        return $view->render();
    }

}