<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Tools extends Auth_Controller {
	public function __construct()
	{
		parent::__construct();
	}
	public function meid_convert(){
		$this->render('tools/meid');
	}
	public function email(){
		
        $this->data['clients'] = $this->getClients();
		$this->render('tools/email');
	}
	public function getClients(){
		$this->db->where('(universal=1 OR store_id='.$this->activeStore.')', NULL, FALSE);
        $this->db->where('email IS NOT NULL', NULL, FALSE);
        $this->db->select('id, CONCAT(first_name, " ", last_name) as name, email');
        $q = $this->db->get('clients');
        $clients = array();
        foreach($q->result() as $client){
        	if (filter_var($client->email, FILTER_VALIDATE_EMAIL)){
        		$clients[] = $client;
        	}
        } 
        return $clients;
	}
	public function meid_convert_ajax(){
		$meid = $this->input->post('meid');//
		require_once(APPPATH.'third_party/meid/MEID.php');
		require_once(APPPATH.'third_party/meid/MetroSPC.php');
		try {
			$converter = new MetroPcsSpcCalculator($meid);
			$result = $converter->calculate();
			$result['success'] = 1;
			echo json_encode($result);
		} catch (Exception $e) {
			echo json_encode(array('success'=>0));
		}
	}
	public function send_mail(){
		$emails = ($this->input->post('emailto')  != '') ? $this->input->post('emailto') : FALSE;
		$emails = filter_var_array((array)$emails, FILTER_VALIDATE_EMAIL);
		$emails = array_filter($emails);
		$send_to_all = $this->input->post('send_to_all');
		if ($send_to_all) {
			$clients = array();
			foreach ($this->getClients() as $client) {
				$clients[] = $client->email;
			}
			$emails = array_merge((array)$clients, (array)$emails);
		}
		$subject = ($this->input->post('subject') != '') ? $this->input->post('subject') : FALSE;

		$body = ($this->input->post('body') != '') ? $this->input->post('body') : FALSE;

		if ($emails==FALSE OR $subject==FALSE OR $body==FALSE) {
			echo 2;
			die();
		}
		$this->load->library('repairer');
		$result = $this->repairer->send_email($emails, $subject, $body, null, null, null, null,$emails );
		if ($result) {
			echo 1;
		}else{
			echo 0;
		}
	}
}
?>