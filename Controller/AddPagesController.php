<?php

namespace AgentAddPages\Controller;

class AddPagesController
{

    protected $config;
    protected $response;
    protected $request;

    public function __construct($config, $response, $request)
    {
        $this->config = $config;
        $this->response = $response;
        $this->request = $request;
    }

    public function execute()
    {
        $view = new \AgentAddPages\Views\Main($this->config);
        $queryHandler = new \AgentAddPages\Model\DataHandler\QueryHandler($this->response->getDbObject());            
        
        if($this->request->getInt("sent") && $this->request->getAlnum("cmd") == "save"){
            $debug = $this->request->getInt("debug");
            
            $values = array(
                "pageCode" => $this->request->getRaw("pageCode"),
                "pageID" => $this->request->getInt("pageID"),
                "templateID" => $this->request->getInt("templateID")
            );
            
            $validation = new \AgentAddPages\Model\Service\isValid();
            $validation->setValues($values);
            if($validation->validate()){
                
                $values["pageCode"] = explode(PHP_EOL, $values["pageCode"]);
                unset($values["pageCode"][count($values["pageCode"]) - 1]);
                
                $addPageStructure = new \AgentAddPages\Model\Service\CreateHierachicPageStructure($this->response->getDbObject(), $this->response->getDataByKey("userID"));
                $addPageStructure->addMultipleHierarchicData($values["pageID"], $values["templateID"], $values["pageCode"], $debug);
            }
            else{
                $this->response->setOutputByKey("AgentAddPages", $view->render($queryHandler->loadPageTemplateList(),$values, $validation->getErrors()));
            }
        }
        else{
            $this->response->setOutputByKey("AgentAddPages", $view->render($queryHandler->loadPageTemplateList()));
        }
    }

}