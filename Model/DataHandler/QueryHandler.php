<?php
/**
 * Collects informations from the database.
 * 
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package Agent_AddPages
 */

namespace AgentAddPages\Model\DataHandler;

class QueryHandler
{

    protected $db;

    /**
     * @param object $db
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Returns a list of saved page templates.
     * @return array
     */
    public function loadPageTemplateList()
    {
        $this->db->setStatement("SELECT id,name FROM t:lw_templates WHERE category = 1 ORDER BY name ASC ");
        return $this->db->pselect();
    }

    /**
     * Returns the path of a certain page.
     * @param int $id
     * @return string
     */
    public function getPath($id)
    {
        $this->db->setStatement("SELECT path FROM t:lw_pages WHERE id = :id ");
        $this->db->bindParameter("id", "i", $id);
        $result = $this->db->pselect1();
        return $result["path"];
    }

    /**
     * Returns a list of existing content objects.
     * @return array
     */
    public function getContentObjects()
    {
        $this->db->setStatement("SELECT id,name FROM t:lw_cobject ");
        return $this->db->pselect();
    }

    /**
     * Returns a certain container entry.
     * @param int $id
     * @return array
     */
    public function loadContainerEntry($id)
    {
        $this->db->setStatement("SELECT * FROM t:lw_container WHERE id = :id ");
        $this->db->bindParameter("id", "i", $id);
        return $this->db->pselect1();
    }
    
    /**
     * Returns the id of a specific content object.
     * @param string $name
     * @return array
     */
    public function getCObjectIdByName($name)
    {
        $this->db->setStatement("SELECT id FROM t:lw_cobject WHERE name = :name ");
        $this->db->bindParameter("name", "s", $name);
        return $this->db->pselect1();
    }

}