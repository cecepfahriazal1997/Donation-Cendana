<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class EventsModel extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    public function list() {
        $this->db->order_by('create_at', 'DESC');
        return $this->db->get('events')->result_array();
    }
}
?>