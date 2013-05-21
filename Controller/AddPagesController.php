<?php
/**
 * Main controller of the add pages agent.
 * 
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package Agent_AddPages
 */

namespace AgentAddPages\Controller;

class AddPagesController
{

    protected $config;
    protected $response;
    protected $request;

    /**
     * @param array $config
     * @param object $response
     * @param object $request
     */
    public function __construct($config, $response, $request)
    {
        $this->config = $config;
        $this->response = $response;
        $this->request = $request;
    }

    /**
     * Collects and sets informations for the requested task. 
     */
    public function execute()
    {
        $view = new \AgentAddPages\Views\Main($this->config);
        $queryHandler = new \AgentAddPages\Model\DataHandler\QueryHandler($this->response->getDbObject());
        $commandHandler = new \AgentAddPages\Model\DataHandler\CommandHandler($this->response->getDbObject(), $this->response->getDataByKey("userID"));
        $cobjects = $queryHandler->getContentObjects();
        
        if ($this->request->getInt("sent") && $this->request->getAlnum("cmd") == "save") {
            
            if($this->request->getInt("sent") == 2 && $this->request->getRaw("pageCode") != "" ){
                $str = $this->request->getRaw("pageCode");
                $filename = "CPS_".date("YmdHis").".cps";
                header('Content-Type: application/octet-stream;');
                header("Content-Disposition: attachment; filename=\"".$filename."\";" ); 
                die($str);
             }
            
            $debug = $this->request->getInt("debug");
            $fileDataArray = $this->request->getFileData("uploadfield");

            if(!empty($fileDataArray["name"])){ 
                $pageCode = file_get_contents($fileDataArray["tmp_name"]);
            }else{
                $pageCode = $this->request->getRaw("pageCode");
            }
            
            $values = array(
                "pageCode" => $pageCode,
                "pageID" => $this->request->getInt("pageID"),
                "templateID" => $this->request->getInt("templateID"),
                "uploadfield" => $fileDataArray
            );

            $validation = new \AgentAddPages\Model\Service\isValid();
            $validation->setValues($values);
            if ($validation->validate()) {

                $values["pageCode"] = explode(PHP_EOL, $values["pageCode"]);
                unset($values["pageCode"][count($values["pageCode"]) - 1]);

                $addPageStructure = new \AgentAddPages\Model\Service\CreateHierachicPageStructure($this->response->getDbObject(), $this->response->getDataByKey("userID"));
                $pidArray = $addPageStructure->addMultipleHierarchicData($values["pageID"], $values["templateID"], $values["pageCode"], $debug);
                $pageNames = $this->preparePageNames($values["pageCode"]);
                
                $array = $this->prepareContainerCObjectPairs($values["pageCode"]);
                $i = 0;
                foreach ($array as $pairs) {
                    $pairs = array_reverse($pairs);
                    $k = 1;
                    foreach ($pairs as $p) {
                        $p = str_replace("\r", "", str_replace(PHP_EOL, "", $p));
                        $temp = explode(":", $p);
                        $cid = $temp[0];
                        $oid = $temp[1];
                        
                        if(!ctype_digit($oid)){
                            $result = $queryHandler->getCObjectIdByName($oid);
                            $oid = $result["id"];
                        }

                        if (!$debug) {
                            $commandHandler->insertEntry($oid, 0, $cid, $pidArray[$i], 1);
                        }
                        else {
                            echo"<br>debug : in page " . $pageNames[$i] . " inserted cbox " . $oid . " on pos ".$k++." in container 1<br>";
                        }
                    }
                    $i++;
                }

                if ($debug) {
                    die();
                }

                $baseUrl = substr(\AgentAddPages\Services\Page::getUrl(), 0, strpos(\AgentAddPages\Services\Page::getUrl(), "index.php")) . "admin.php?obj=content&index=";
                \AgentAddPages\Services\Page::reload($baseUrl . $values["pageID"]);
            }
            else {
                $this->response->setOutputByKey("AgentAddPages", $view->render($queryHandler->loadPageTemplateList(), $cobjects, $values, $validation->getErrors()));
            }
        }
        else {
            $this->response->setOutputByKey("AgentAddPages", $view->render($queryHandler->loadPageTemplateList(), $cobjects));
        }
    }

    /**
     * Prepares an array which contains the combination of container and
     * content object.
     * 
     * @param array $array
     * @return array
     */
    private function prepareContainerCObjectPairs($array)
    {
        foreach ($array as $page) {
            $tempArray[] = explode(";", $page);
        }
        for ($i = 0; $i <= count($tempArray) - 1; $i++) {
            $containerCObjectPair[$tempArray[$i][0]] = explode(",", $tempArray[$i][3]);
        }

        return $containerCObjectPair;
    }
    
    /**
     * The pagenames are filtered and collected in an
     * array from the post pageCode.
     * 
     * @param array $array
     * @return array
     */
    private function preparePageNames($array)
    {
        foreach ($array as $page) {
            $tempArray = explode(";", $page);
            $pageNames[] = $tempArray[2];
        }
        return $pageNames;
    }

}