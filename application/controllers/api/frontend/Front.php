<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Front extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('MidtransModel', 'midtrans');
    }

    public function generateToken() {
        $post           = $this->input->post(null, true);

        $name           = $post['name'];
        $email          = $post['email'];
        $phone          = $post['phone'];
        $amount         = $post['amount'];
        $message        = $post['message'];

        $token          = $this->midtrans->generateToken($name, $email, $phone, $amount, $message);
        exit(json_encode($token));
    }
}
?>