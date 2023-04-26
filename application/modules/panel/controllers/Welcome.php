<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends Auth_Controller {
	public function __construct()
    {
        parent::__construct();
        $this->load->model('welcome_model');
        $this->load->model('reports_model');
    }
	
	public function index()
    {
     
        $this->showPageTitle = false;
        $this->data['repair_count'] = $this->welcome_model->getRepairCount();
        $this->data['completed_repair_count7'] = $this->welcome_model->getCompletedRepairCount7();
        $this->data['completed_repair_count30'] = $this->welcome_model->getCompletedRepairCount30();
        $this->data['clients_count'] = $this->welcome_model->getClientCount();
        $this->data['stock_count'] = $this->welcome_model->getStockCount();
        $this->data['currency'] = $this->mSettings->currency;
        $this->data['stock'] = $this->reports_model->getStockValue();
        $this->data['list'] = $this->reports_model->list_earnings(date('m'), date('Y'));
        $this->data['rsales'] = $this->welcome_model->getRecentSales(!$this->Admin ? $this->GP: NULL);
        $this->data['commission_day'] = $this->welcome_model->getAllCommissions('day');
        $this->data['commission_week'] = $this->welcome_model->getAllCommissions('week');
        $this->data['commission_month'] = $this->welcome_model->getAllCommissions('month');
        $this->data['messages'] = $this->welcome_model->getBoardMessages();

        $this->render('dashboard');
    }

    public function loadMessages()
    {
        $data['messages'] = $this->welcome_model->getBoardMessages();
        $this->load->view($this->theme.'board', $data);
    }

	public function lookup_sale() {
		$sale_id = $this->input->post('sale_id');
        $q = $this->db->get_where('sales', array('id'=> $sale_id));
        if ($q->num_rows() > 0 ) {
        	echo json_encode(array('success'=>true));
        }else{
    		echo json_encode(array('success'=>false));
        }
	}
	public function lookup_repair() {
		$repair_code = $this->input->post('repair_code');

        $this->db->order_by('id', 'desc')->where('code', $repair_code)->or_where('telephone', $repair_code);
        $q = $this->db->get('repair');
        if ($q->num_rows() > 0 ) {
        	echo json_encode(array('success'=>true, 'id'=>$q->row()->id));
        }else{
    		echo json_encode(array('success'=>false));
        }
	}

	public function lookup_client() {
        $client_id = $this->input->post('client_id');
        $q = $this->db->get_where('clients', array('id'=> $client_id));
        if ($q->num_rows() > 0 ) {
            echo json_encode(array('success'=>true, 'id'=>$q->row()->id));
        }else{
            echo json_encode(array('success'=>false));
        }
    }

    public function add_message() {
        $message = $this->input->post('message');
        $user = $this->ion_auth->user()->row();
        $name = $user->first_name . ' ' . $user->last_name;
        $data = array(
            'message'   => filter_var($message, FILTER_SANITIZE_STRING),
            'user_id'   => $user->id,
            'timestamp' => date('Y-m-d H:i:s'),
        );
        $this->db->insert('message_board', $data);
        echo json_encode(array('success'=>true));
        
    }

    
    public function nav_toggle() {
        $this->output->set_header('Content-Type: application/json; charset=utf-8');
        $state = (string) $this->input->post('state');
        if ($state == '') {
            $state = null;
            $this->session->unset_userdata('main_sidebar_state');
        } else {
            $this->session->set_userdata('main_sidebar_state', $state);
        }
        $this->output->set_output(json_encode(array('state' => $state)));
    }



}
