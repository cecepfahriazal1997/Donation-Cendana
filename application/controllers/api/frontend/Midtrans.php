<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use chriskacerguis\RestServer\RestController;
/**
 * Midtrans controller.
 * 
 * @author Cecep Rokani
 */

class Midtrans extends RestController {
	function __construct() {
        // Construct the parent class
		parent::__construct();
		$this->load->model('MidtransModel', 'model');
	}

    public function generateToken_post() {
        $post           = $this->input->post(null, true);

        $name           = $this->post('name');
        $email          = $this->post('email');
        $phone          = $this->post('phone');
        $amount         = $this->post('amount');
        $message        = $this->post('message');

        $response       = $this->model->generateToken($name, $email, $phone, $amount, $message);
		$this->response($response, 200);
    }

	public function notification_get() {
		$response			= array();
		$synchronizeData 	= $this->model->notification();
		if ($synchronizeData) {
			$response['status']		= true;
			$response['message']	= 'Billing data synchronized successfully !';
		} else {
			$response['status']		= false;
			$response['message']	= 'Billing data failed to synchronize !';
		}

		$this->response($response, 200);
	}

	public function finish_get() {
		$orderId			    = $this->get('order_id');
		$dataTrx			    = $this->model->getBillByOrderId($orderId);
		
		$data['transaction']	= $dataTrx;
        $data['image']	        = "success_payment.png";
        $data['title']	        = "Donasi Berhasil Diproses!!";
        $data['body']	        = "Silahkan untuk melakukan pembayaran menggunakan metode yang telah ditentukan.";
        $data['status']	        = true;
        $this->twig->display('frontend/notification_midtrans', $data);
	}

	public function unfinish_get() {
		$orderId			= $this->get('order_id');
		$dataTrx			= $this->model->getBillByOrderId($orderId);
		
		$data['transaction']	= $dataTrx;
        $data['image']	        = "failed_payment.png";
        $data['title']	        = "Donasi Dibatalkan!!";
        $data['body']	        = "Proses donasi anda telah dibatalkan. Jika berubah pikiran, silahkan kembali ke Dashboard.";
        $data['status']	        = false;
        $this->twig->display('frontend/notification_midtrans', $data);
	}

	public function error_get() {
		$orderId				= $this->get('order_id');
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