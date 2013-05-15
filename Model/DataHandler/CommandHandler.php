<?php

namespace AgentAddPages\Model\DataHandler;

class CommandHandler
{
    protected $db;
    protected $userID;


    public function __construct($db, $userID)
    {
        $this->db = $db;
        $this->userID = $userID;
    }
    
    public function addPage($array)
    {
        $this->db->setStatement("INSERT INTO t:lw_pages (name, page_template, relation, seq, path, lw_first_user, lw_last_user) VALUES (:name, :page_template, :relation, :seq, :path, :user, :user) ");
        $this->db->bindParameter("name", "s", $array["name"]);
        $this->db->bindParameter("page_template", "i", $array["page_template"]);
        $this->db->bindParameter("relation", "i", $array["relation"]);
        $this->db->bindParameter("seq", "i", $array["seq"]);
        $this->db->bindParameter("path", "s", $array["path"]);
        $this->db->bindParameter("user", "i", $this->userID);
        
        return $this->db->pdbinsert($this->db->gt("lw_pages"));
    }
}