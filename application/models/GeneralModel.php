<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class GeneralModel extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    public function fetchListData($table, $where=null, $select='*') {
        $this->db->select($select);
        if (isset($where)) {
            foreach ($where as $key => $value) {
                $this->db->where($key, $value);
            }
        }
        return $this->db->get($table)->result_array();
    }

    public function fetchSingleData($where, $table) {
        $this->db->where($where[0], $where[1]);
        return $this->db->get($table)->last_row('array');
    }

    public function insertData($table, $data) {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }

    public function updateData($id, $table, $data) {
        if (isset($id)) {
            $this->db->where('id', $id);
            return $this->db->update($table, $data);
        } else {
            return false;
        }
    }

    public function deleteData($id, $table) {
        if (isset($id)) {
            $this->db->where('id', $id);
            return $this->db->delete($table);
        } else {
            return false;
        }
    }
}
?>