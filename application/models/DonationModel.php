<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class DonationModel extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    public function getDonationUsers() {
        $this->db->select('id, name, email, phone, message, gross_amount as amount, created');
        $this->db->where('transaction_status', 'settlement');
        $this->db->where('fraud_status', 'accept');
        $this->db->order_by('created', 'DESC');
        return $this->db->get('transaction')->result_array();
    }
}
?>