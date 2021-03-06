<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Front extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('MidtransModel', 'midtrans');
        $this->load->model('DonationModel', 'donation');
    }

    public function listDonation() {
        $response       = array();
        $list           = $this->donation->getDonationUsers();
        if (!empty($list)) {
            $tmpResult              = array();
            foreach ($list as $row) {
                $row['amount']          = number_format($row['amount'], 0, ',', '.');
                $row['elapsed_time']    = $this->general->timeElapsedString($row['created']);
                $tmpResult[]            = $row;
            }
            $response['status']     = true;
            $response['data']       = $tmpResult;
        } else {
            $response['status']     = false;
            $response['message']    = "Belum ada yang memberikan donasi saat ini.";
        }

		$this->general->response($response, 200);
    }
}
?>