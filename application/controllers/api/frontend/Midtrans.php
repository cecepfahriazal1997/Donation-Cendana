<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Midtrans controller.
 * 
 * @author Cecep Rokani
 */

class Midtrans extends CI_Controller {
	function __construct() {
        // Construct the parent class
		parent::__construct();
		$this->load->model('MidtransModel', 'model');
	}

	public function notification() {
		$response		= array();
		$synchronizeData = $this->model->notification();
		if ($synchronizeData) {
			$response['status']		= true;
			$response['message']	= 'Billing data synchronized successfully !';
		} else {
			$response['status']		= false;
			$response['message']	= 'Billing data failed to synchronize !';
		}

		exit(json_encode($response));
	}

	public function finish() {
		$get				    = $this->input->get(null, true);
		$orderId			    = $get['order_id'];
		$dataTrx			    = $this->model->getBillByOrderId($orderId);
		
		$data['transaction']	= $dataTrx;
        $data['image']	        = "success_payment.png";
        $data['title']	        = "Donasi Berhasil Diproses!!";
        $data['body']	        = "Silahkan untuk melakukan pembayaran menggunakan metode yang telah ditentukan.";
        $data['status']	        = true;
        $this->twig->display('frontend/notification_midtrans', $data);
	}

	public function unfinish() {
		$get				= $this->input->get(null, true);
		$orderId			= $get['order_id'];
		$dataTrx			= $this->model->getBillByOrderId($orderId);
		
		$data['transaction']	= $dataTrx;
        $data['image']	        = "failed_payment.png";
        $data['title']	        = "Donasi Dibatalkan!!";
        $data['body']	        = "Proses donasi anda telah dibatalkan. Jika berubah pikiran, silahkan kembali ke Dashboard.";
        $data['status']	        = false;
        $this->twig->display('frontend/notification_midtrans', $data);
	}

	public function error() {
		$get				    = $this->input->get(null, true);
		$orderId			    = $get['order_id'];
		$dataTrx			    = $this->model->getBillByOrderId($orderId);
		
		$data['transaction']	= $dataTrx;
        $data['image']	        = "failed_payment.png";
        $data['title']	        = "Donasi Gagal Diproses!!";
        $data['body']	        = "Proses donasi gagal diproses, silahkan coba beberapa saat lagi.";
        $data['status']	        = false;
        $this->twig->display('frontend/notification_midtrans', $data);
	}
}
?>