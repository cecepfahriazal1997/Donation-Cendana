<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Donation extends CI_Controller {

    public function __construct() {
		parent::__construct();
        $this->load->model('DonationModel', 'model');
        $this->user = $this->auth->validateUserToken();
	}

    public function listUsers() {
        $response       = array();
        $list           = $this->model->getDonationUsers();
        if (!empty($list)) {
            $tmpResult              = array();
            foreach ($list as $key => $row) {
                $param                  = array();
                $param['id']            = ($key + 1);
                $param['name']          = $row['name'];
                $param['amount']        = 'Rp. ' . number_format($row['amount'], 0, ',', '.');
                $param['date']          = $row['created'];
                $tmpResult[]            = $param;
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