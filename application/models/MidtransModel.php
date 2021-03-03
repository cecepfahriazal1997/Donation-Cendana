<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Midtrans model.
 * 
 * @author Cecep Rokani
 */
class MidtransModel extends CI_Model {
    var $serverKey, $merchantId, $clientKey;
	function __construct() {
        parent::__construct();
		$this->serverKey   = 'SB-Mid-server-TRKxngmv-rRzO4HLr9l_My88';
		$this->merchantId  = 'G005849780';
		$this->clientKey   = 'SB-Mid-client-2i-Bu6MzJ_zQ6365';
	}
	
	public function generateToken($name, $email, $phone, $amount, $message) {
		$order			= array();
		
		$this->db->where('email', $email);
		$this->db->where('transaction_status != ', 'settlement');
		$checkCurrentTrx	= $this->db->get('transaction')->last_row();

		$order['id']			= 'order-'.bin2hex(random_bytes(16));
		$order['firstName']		= $name;
		$order['lastName']		= '';
		$order['email']			= $email;
		$order['phone']			= $phone;
		$order['price']         = $amount;

		$generateToken			= $this->createToken($order);
		if (isset($generateToken['token']) && !empty($generateToken['token'])) {
			if (empty($checkCurrentTrx)) {
				$insert['name']		    = $name;
				$insert['email']		= $email;
				$insert['phone']		= $phone;
				$insert['message']		= $message;
				$insert['order_id']		= $order['id'];
				$insert['gross_amount']	= $order['price'];
				$insert['token']		= $generateToken['token'];
				$insert['url']			= $generateToken['redirect_url'];
				$insert['created']		= date('Y-m-d H:i:s');

				if ($this->db->insert('transaction', $insert)) {
					$response['status']		= true;
					$response['message']	= 'Token successfully created !';
					$response['token']		= $generateToken['token'];
					$response['url']		= $generateToken['redirect_url'];
				} else {
					$response['status']		= false;
					$response['message']	= 'Transaction failed to process !';
				}
			} else {
				// $update['order_id']		= $order['id'];
				$update['token']		= $generateToken['token'];
				$update['url']			= $generateToken['redirect_url'];
				$this->db->where('id', $checkCurrentTrx->id);
				$this->db->update('transaction', $update);
				
				$response['status']		= true;
				$response['message']	= 'You have current loan waiting to payment here !';
				$response['url']		= $generateToken['redirect_url'];
			}
		} else {
			$response['status']		= false;
			$response['message']	= 'Token failed to generate !';
		}

		return $response;
	}

	public function notification() {
        Veritrans_Config::$isProduction = false;
        Veritrans_Config::$serverKey = $this->serverKey;
		$notification 	= new Veritrans_Notification();

        $this->db->where('order_id', $notification->order_id);
        $this->db->from('transaction');
        $total = $this->db->count_all_results();

        if ($total > 0) {
            $this->db->where('order_id', $notification->order_id);
            $this->db->from('transaction');
            $data_transaction = $this->db->get()->row();
            
            $update_transaction['transaction_id']       = $notification->transaction_id;
            $update_transaction['order_id']             = $notification->order_id;
            $update_transaction['payment_type']         = $notification->payment_type;
            $update_transaction['gross_amount']         = $notification->gross_amount;
            $update_transaction['transaction_status']   = $notification->transaction_status;
            $update_transaction['transaction_time']     = $notification->transaction_time;
            $update_transaction['fraud_status']         = $notification->fraud_status;

            if ($notification->payment_type == 'bank_transfer')
            {
                if ($notification->permata_va_number != '') {
                    $update_transaction['bank']             = 'permata';
                    $update_transaction['va_number']        = $notification->permata_va_number;
                } else {
                    $update_transaction['bank']             = $notification->va_numbers[0]->bank;
                    $update_transaction['va_number']        = $notification->va_numbers[0]->va_number;
                }
            }

            $this->db->where('id', $data_transaction->id);
            $this->db->update('transaction', $update_transaction);

			// ketika sudah dibayar
            if ($notification->transaction_status == 'settlement') {
			} if ($notification->transaction_status == 'expire') {
			}
			return true;
        } else {
			return false;
		}
	}

	public function getBillByOrderId($orderId) {
		$this->db->where('order_id', $orderId);
		return $this->db->get('transaction')->last_row();
	}

	private function createToken($order) {
		try {
			\Veritrans_Config::$serverKey = $this->serverKey;
			// Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
			\Veritrans_Config::$isProduction = false;
			// Set sanitization on (default)
			\Veritrans_Config::$isSanitized = true;
			// Set 3DS transaction for credit card to true
			\Veritrans_Config::$is3ds = true;
			
			$complete_request = [
				"transaction_details" => [
					"order_id" => $order['id'],
					"gross_amount" => $order['price']
				],
				"customer_details" => [
					'first_name'    => $order['firstName'],
					'last_name'     => $order['lastName'],
					"email"			=> $order['email'],
					"phone" 		=> $order['phone']
				],
				// 'enabled_payments' => [
				// 	"gopay",
				// 	"permata_va",
				// 	"bni_va",
				// 	'bca_va',
				// ],
				"gopay" => [
					"enable_callback" => true,
					"callback_url" => base_url().'midtrans/successPayment'
				]
			];

			// Get Snap Payment Page URL
			$paymentUrl = \Veritrans_Snap::createTransaction($complete_request);
			
			$return = (array) $paymentUrl;
		} catch (Exception $e) {
			$return['message'] = $e->getMessage();
		}

		return $return;
	}
}
?>