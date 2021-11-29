<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model {

    public function insertIgnore($table, $setData)
    {
        $insertString = $this->db->insert_string($table, $setData);
        $this->db->query(str_replace('INSERT INTO', 'INSERT IGNORE INTO', $insertString));
        return $this->db->insert_id();
    }
}
