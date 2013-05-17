<?php

namespace AgentAddPages\Model\DataHandler;

class CommandHandler
{

    protected $db;
    protected $userID;
    protected $queryHandler;

    public function __construct($db, $userID)
    {
        $this->db = $db;
        $this->userID = $userID;
        $this->queryHandler = new \AgentAddPages\Model\DataHandler\QueryHandler($db);
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

    public function insertEntry($oid, $pos = false, $cid = false, $index = false, $amount = false)
    {
        //echo "oid: ".$oid." pos: ".$pos." cid: ".$cid." index: ".$index." amount: ".$amount." "; exit();
        if ($amount > 10) {
            return false;
        }
        $this->db->beginTransaction();
        if (!$pos || $pos < 1) {
            $posEntry['page_id'] = $index;
            $posEntry['container_id'] = $cid;
            $posEntry['seq'] = 0;
            $ok = $this->changeAllSeqsAbove("add", $posEntry);

            $data['page_id'] = $index;
            $data['container_id'] = $cid;
            $data['object_id'] = $oid;
            $data['seq'] = 1;
            $data['lw_first_date'] = date("Ymdhis");
            $data['lw_last_date'] = date("Ymdhis");
        }
        else {
            $posEntry = $this->queryHandler->loadContainerEntry($pos);

            $ok = $this->changeAllSeqsAbove("add", $posEntry);

            $data['page_id'] = $posEntry['page_id'];
            $data['container_id'] = $posEntry['container_id'];
            $data['object_id'] = $oid;
            $data['seq'] = $posEntry['seq'] + 1;
            $data['lw_first_date'] = date("Ymdhis");
            $data['lw_last_date'] = date("Ymdhis");
        }
        
        $this->db->setStatement("INSERT INTO t:lw_container (page_id, container_id, object_id, seq, lw_first_date, lw_last_date) VALUES (:pid, :cid, :oid, :seq, :first_date, :last_date) ");
        $this->db->bindParameter("pid", "i",  $data['page_id']);
        $this->db->bindParameter("cid", "i",  $data['container_id']);
        $this->db->bindParameter("oid", "i",  $data['object_id']);
        $this->db->bindParameter("seq", "i",  $data['seq']);
        $this->db->bindParameter("first_date", "i",  $data['lw_first_date']);
        $this->db->bindParameter("last_date", "i",  $data['lw_last_date']);

        $newID = $this->db->pdbinsert($this->db->gt("lw_container"));

        $ok2 = $this->updateLastChange($data['page_id']);

        if ($newID && $ok && $ok2) {
            $this->db->commit();
            $this->db->endTransaction();
            if ($amount > 1) {
                $amount = $amount - 1;
                $newID = $this->insertEntry($oid, $pos, $cid, $index, $amount);
            }
            return $newID;
        }
        else {
            $this->db->rollback();
            $this->db->endTransaction();
            return false;
        }
    }

    private function changeAllSeqsAbove($todo, $posEntry)
    {
        if ($todo == "add") {
            $this->db->setStatement("UPDATE t:lw_container SET seq = seq + 1 WHERE page_id = :pid AND container_id = :cid AND seq > :seq ");
        }
        elseif ($todo == "sub") {
            $this->db->setStatement("UPDATE t:lw_container SET seq = seq - 1 WHERE page_id = :pid AND container_id = :cid AND seq > :seq ");
        }
        $this->db->bindParameter("pid", "i", $posEntry['page_id']);
        $this->db->bindParameter("cid", "i", $posEntry['container_id']);
        $this->db->bindParameter("seq", "i", $posEntry['seq']);
        return $this->db->pdbquery();
    }

    private function updateLastChange($pid)
    {
        $this->db->setStatement("UPDATE t:lw_pages SET changed = :date WHERE id = :id ");
        $this->db->bindParameter("date", "i", date("YmdHis"));
        $this->db->bindParameter("id", "i", $pid);
        return $this->db->pdbquery();
    }

}