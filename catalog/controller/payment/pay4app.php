<?php
class ControllerPaymentPay4App extends Controller {
	protected function index() {
		$this->language->load('payment/pay4app');
		
		$this->data['button_confirm'] = $this->language->get('button_confirm');
		
		$this->data['action'] = 'https://pay4app.com/checkout.php';
		
		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		if ($order_info) {
			$this->data['merchantid'] = $this->config->get('pay4app_merchantid');
			$this->data['apisecretkey'] = $this->config->get('pay4app_apisecretkey');
			$this->data['item_name'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');				
						
			$this->data['total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
			
			$hash = $this->data['merchantid'].$this->session->data['order_id'].$this->data['total'].$this->data['apisecretkey'];
			$this->data['signature'] = hash('sha256', $hash);

			$this->data['redirect'] 		= $this->url->link('checkout/success');
			$this->data['transferpending'] 	= $this->url->link('checkout/success');
			$this->data['ascallback'] 		= $this->url->link('payment/pay4app/callback', '', 'SSL');		
			
			$this->data['orderid'] = $this->session->data['order_id'];
		
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/pay4app.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/payment/pay4app.tpl';
			} else {
				$this->template = 'default/template/payment/pay4app.tpl';
			}
	
			$this->render();
		}
	}
	
	public function callback() {

				
		if ( isset($this->request->get['merchant']) AND isset($this->request->get['checkout']) AND isset($this->request->get['order']) AND isset($this->request->get['amount']) AND isset($this->request->get['email']) AND isset($this->request->get['phone']) AND isset($this->request->get['timestamp']) AND isset($this->request->get['digest']) ) {
	 	
			$hash = $this->config->get('pay4app_merchantid').$this->request->get['checkout'].$this->request->get['order'].$this->request->get['amount'].$this->request->get['email'].$this->request->get['phone'].$this->request->get['timestamp'].$this->config->get('pay4app_apisecretkey');
			$digest = hash('sha256', $hash);

			if (!($digest === $this->request->get['digest']) ){
				die ("{status: 0}");
			}

			$order_id = $this->request->get['order'];
			
		
		} else {
			$order_id = 0;
			die ("invalid request");
		}		
		


		$this->load->model('checkout/order');
				
		$order_info = $this->model_checkout_order->getOrder($order_id);
		
		if ($order_info) {
			
			
			if ($this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) == $this->request->get['amount']) {
				
				$message = "Pay4App Checkout ID: ".$this->request->get['checkout'];
				$this->model_checkout_order->confirm($order_id, $this->config->get('pay4app_order_status_id'), $message);				
				echo json_encode( array('status'=>1, 'message'=>'moo moo') );				
				return;

			} else {
				$message = "The payment amount is not the same as the expected payment on the order. The customer made a payment of $".$this->request->get['amount'].". Pay4App Checkout Reference ID: ".$this->request->get['checkout'];
				$this->model_checkout_order->confirm($order_id, $this->config->get('config_order_status_id'), $message);
				echo json_encode( array('status'=>1, 'message'=>'amount mismatch') );
				return;
				
			}

		
		}
		else{
			// TODO: ok cannot fetch orser info poitwa sei
			echo json_encode( array('status'=>0, 'message'=>'order not found') );
			return;
		}	
	}
}
?>