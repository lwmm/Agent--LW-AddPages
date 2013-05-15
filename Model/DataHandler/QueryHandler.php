<?php

namespace AgentAddPages\Model\DataHandler;

class QueryHandler
{
    protected $db;
    
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    public function loadPageTemplateList()
    {
        $this->db->setStatement("SELECT id,name FROM t:lw_templates WHERE category = 1 ORDER BY name ASC ");
        return $this->db->pselect();
    }
    
    public function getPath($id) 
	{
        $this->db->setStatement("SELECT path FROM t:lw_pages WHERE id = :id ");
        $this->db->bindParameter("id", "i", $id);
        $result = $this->db->pselect1();
        return $result["path"];
    }
}