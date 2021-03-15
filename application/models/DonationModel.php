<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class DonationModel extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    public function getDonationUsers() {
        $this->db->select('id, name, email, phone, message, gross_amount as amount, transaction_time');
        $this->db->where('transaction_status', 'settlement');
        $this->db->where('fraud_status', 'accept');
        $this->db->order_by('transaction_time', 'DESC');
        return $this->db->get('transaction')->result_array();
    }

    public function countDonatur() {
        return $this->db
                    ->where('transaction_status', 'settlement')
                    ->where('fraud_status', 'accept')
                    ->group_by('phone')
                    ->count_all_results('transaction');
    }

    public function countDonation() {
        return $this->db->select('SUM(gross_amount) as total')
                    ->where('transaction_status', 'settlement')
                    ->where('fraud_status', 'accept')
                    ->get('transaction')->row()->total;
    }

    public function getPeriode() {
        $this->db->select('min(created) as min_date, max(created) as max_date');
        $this->db->where('transaction_status', 'settlement');
        $this->db->where('fraud_status', 'accept');
        return $this->db->get('transaction')->last_row();
    }

    public function getListDonation() {
        $this->db->select('gross_amount, count(id) as total');
        $this->db->where('transaction_status', 'settlement');
        $this->db->where('fraud_status', 'accept');
        $this->db->group_by('gross_amount');
        $this->db->order_by('created', 'ASC');
        return $this->db->get('transaction')->result_array();
    }

    public function getListDonationByMonth() {
        $this->db->select('created, count(id) as total');
        $this->db->where('transaction_status', 'settlement');
        $this->db->where('fraud_status', 'accept');
        $this->db->group_by('month(created)');
        $this->db->order_by('created', 'ASC');
        return $this->db->get('transaction')->result_array();
    }

    public function getDonationByDate() {
        $this->db->select('id, name, email, phone, message, SUM(gross_amount) as amount, transaction_time');
        $this->db->where('transaction_status', 'settlement');
        $this->db->where('fraud_status', 'accept');
        $this->db->group_by('transaction_time');
        $this->db->order_by('transaction_time', 'DESC');
        return $this->db->get('transaction')->result_array();
    }

}
?>