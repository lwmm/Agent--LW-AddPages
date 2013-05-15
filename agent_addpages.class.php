<?php

class agent_addpages extends lw_agent
{

    protected $config;
    protected $request;
    protected $response;

    public function __construct()
    {
        parent::__construct();
        $this->config = $this->conf;
        $this->className = "agent_addpages";
        $this->adminSurfacePath = $this->config['path']['agents'] . "adminSurface/templates/";

        $usage = new lw_usage($this->className, "0");
        $this->secondaryUser = $usage->executeUsage();

        include_once(dirname(__FILE__) . '/Services/Autoloader.php');
        $autoloader = new \AgentAddPages\Services\Autoloader();
    }

    protected function showEdit()
    {
        $response = new \AgentAddPages\Services\Response();
        $response->setDbObject($this->db);
        $response->setDataByKey("userID", $this->auth->getUserdata("id"));
        $controller = new \AgentAddPages\Controller\AddPagesController($this->config, $response, $this->request);
        $controller->execute();
        return $response->getOutputByKey("AgentAddPages");
    }

    protected function buildNav()
    {
        $view = new \AgentAddPages\Views\Navigation();
        return $view->render();
    }

    protected function deleteAllowed()
    {
        return true;
    }

}