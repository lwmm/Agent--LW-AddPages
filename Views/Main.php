<?php
/**
 * The main html output will be build.
 * 
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package Agent_AddPages
 */

namespace AgentAddPages\Views;

class Main
{

    protected $config;

    /**
     * @param array $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Returns rendered html.
     * @param array $templates
     * @param array $cobjects
     * @param array $values
     * @param array $errors
     * @return string
     */
    public function render($templates, $cobjects, $values =false, $errors = false)
    {
        $baseUrl = substr(\AgentAddPages\Services\Page::getUrl(), 0, strpos(\AgentAddPages\Services\Page::getUrl(), "index.php")) . "admin.php?obj=addpages";

        $view = new \lw_view(dirname(__FILE__) . '/Templates/Main.phtml');

        $view->bootstrapCSS = $this->config["url"]["media"] . "bootstrap/css/bootstrap.min.css";
        $view->bootstrapJS = $this->config["url"]["media"] . "bootstrap/js/bootstrap.min.js";
        $view->iconPage = $this->config["url"]["media"]."pics/fatcow_icons/16x16_0680/page_white_text.png";
        $view->iconPageAdd = $this->config["url"]["media"]."pics/fatcow_icons/16x16_0680/page_white_put.png";
        $view->iconAdd = $this->config["url"]["media"]."pics/add.png";
        $view->iconPlugin = $this->config["url"]["media"]."pics/fatcow_icons/16x16_0720/plugin.png";
        $view->iconCross = $this->config["url"]["media"]."pics/fatcow_icons/16x16_0300/cross.png";
        
        $view->baseUrl = $baseUrl;
        
        $view->templates = $templates;
        $view->values = $values;
        $view->errors = $errors;
        $view->cobjects = $cobjects;
        $view->containers = $this->config["container"];

        return $view->render();
    }

}