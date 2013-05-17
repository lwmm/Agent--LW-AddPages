<?php

namespace AgentAddPages\Model\Service;

class CreateHierachicPageStructure
{

    protected $queryHandler;
    protected $commandHandler;

    public function __construct($db, $userID)
    {
        $this->queryHandler = new \AgentAddPages\Model\DataHandler\QueryHandler($db);
        $this->commandHandler = new \AgentAddPages\Model\DataHandler\CommandHandler($db, $userID);
    }

    public function addMultipleHierarchicData($pid = false, $layout = false, $array = false, $check = false)
    {
        if ($check) {
            echo "<pre>";
            print_r($array);
            echo "</pre>";
        }
        $pidArray = array();
        $j = 1;
        $phash["x"] = $this->queryHandler->getPath($pid) . $pid . ":";
        foreach ($array as $page) {
            $path = false;
            $parts = explode(";", $page);
            $temp_id = $parts[0];
            $pathparts = explode(":", $parts[1]);
            foreach ($pathparts as $pathpart) {
                if (strlen(trim($pathpart)) > 0)
                    $path.= $phash[$pathpart] . ":";
                if (strstr($phash[$pathpart], ":")) {
                    $relation = $pid;
                }
                else {
                    $relation = $phash[$pathpart];
                }
            }
            $name = $parts[2];
            $seq = $j++;
            if (strlen(trim($name)) > 0) {
                try {
                    $data['name'] = $name;
                    $data['page_template'] = $layout;
                    $data['relation'] = $relation;
                    $data['seq'] = $seq;
                    $data['path'] = str_replace("::", ":", $path);
                } catch (Exception $e) {
                    $error = $e->getMessage();
                }
                if (!$error) {
                    if ($check) {
                        $phash[$temp_id] = $seq + 12;
                        echo "<pre>";
                        print_r($data);
                        echo "</pre>";
                    }
                    else {
                        $pidArray[] = $phash[$temp_id] = $this->commandHandler->addPage($data);
                    }
                }
                else {
                    die("error");
                }
            }
        }
        return $pidArray;
    }

}