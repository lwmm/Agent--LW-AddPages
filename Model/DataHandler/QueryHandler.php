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

    public function getContentObjects()
    {
        $this->db->setStatement("SELECT id,name FROM t:lw_cobject ");
        return $this->db->pselect();
    }

    public function loadContainerEntry($id)
    {
        $this->db->setStatement("SELECT * FROM t:lw_container WHERE id = :id ");
        $this->db->bindParameter("id", "i", $id);
        return $this->db->pselect1();
    }
    
    public function getCObjectIdByName($name)
    {
        $this->db->setStatement("SELECT id FROM t:lw_cobject WHERE name = :name ");
        $this->db->bindParameter("name", "s", $name);
        return $this->db->pselect1();
    }

}