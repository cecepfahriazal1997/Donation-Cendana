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
                $param['date']          = $row['transaction_time'];
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

    public function report() {
        $response       = array();
		$list		    = $this->model->getDonationByDate();
        if (!empty($list)) {
            $tmpResult              = array();
            $total                  = 0;
            foreach ($list as $key => $row) {
                $param                  = array();
                $param['id']            = ($key + 1);
                $param['amount']        = '<span style="float: right">Rp. ' . number_format($row['amount'], 0, ',', '.') . '</span>';
                $param['date']          = date('d F Y', strtotime($row['transaction_time']));
                $tmpResult[]            = $param;
                $total                  += $row['amount'];
            }
            $response['status']     = true;
            $response['data']       = $tmpResult;
            $response['total']      = number_format($total, 0, ',', '.');
        } else {
            $response['status']     = false;
            $response['message']    = "Belum ada yang memberikan donasi saat ini.";
        }

		$this->general->response($response, 200);
    }
}
?>