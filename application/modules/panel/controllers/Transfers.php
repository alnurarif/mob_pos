<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Transfers extends Auth_Controller {
		public function __construct() {
			parent::__construct();
			$this->load->model('transfers_model');
		}

		public function index($type = NULL) {
			$this->render("transfers/index");
		}

    public function getAllTransfers($type = NULL)
    {
        header("Access-Control-Allow-Origin: *");

        $this->load->library('datatables');
        $this->datatables
            ->select('id, date, transfer_code, CONCAT(IFNULL(shipping_provider, ""), " - " ,shipping_trackcode) as sp, (SELECT name from store WHERE store.id=transfers.sending_store) as sending_store, (SELECT name from store WHERE store.id=transfers.receiving_store) as receiving_store_name, receiving_store, product_name, quantity, total_cost,IFNULL(shipping_cost, 0.00) as shipping_cost, (total_cost)+(IFNULL(shipping_cost, 0)) as gt, status')
            ->from('transfers');
						
         if ($this->input->get('status')) {
            $status = $this->input->get('status');
            if ($status == 'sent') {
                $this->datatables->where('status', $status);
            }elseif($status == 'received'){
                $this->datatables->where('status', $status);
            }
        }
        if ($this->input->get('start_date')) {
            $start_date = ($this->input->get('start_date')) . " 00:00:00";
        } else {
            $start_date = date('Y-m-d 00:00:00');
        }
        if ($this->input->get('end_date')) {
            $end_date = ($this->input->get('end_date')) . " 23:59:59";
        } else {
            $end_date = date('Y-m-d 23:59:59');
        }
        $this->datatables->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
		$this->datatables->where('(sending_store = '.$this->activeStore.' OR receiving_store = '.$this->activeStore.')', NULL, FALSE);
        $this->datatables->edit_column('status', '$1____$2____$3', 'id, receiving_store, status');
        $this->datatables->unset_column('id');
        $this->datatables->unset_column('receiving_store');
        echo $this->datatables->generate();
    }

    public function suggestions()
    {
        $term = $this->input->get('term', true);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . site_url('welcome') . "'; }, 10);</script>");
        }
        $rows = $this->transfers_model->getAllProductNames($term);
        $rows = array_filter((array)$rows);
        if ($rows) {
            $c = str_replace(".", "", microtime(true));
            $r = 0;
            foreach ($rows as $row) {
                $row->selected_qty = 0;
                $row->max_qty = $row->qty;
                $pr[] = array(
                    'item_id' => $row->id.$row->type,
                    'label' => $row->name . " (" . $row->code . ")",
                    'row' => $row,
                    'serials' => NULL,
                );
                $r++;
            }
            $this->repairer->send_json($pr);
        } else {
            $this->repairer->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

    public function add_transfer()
    {
 		if ($this->input->post('shipping_provider') === 'other') {
            $provider = $this->input->post('provider_input');
        }else{
            $provider = $this->input->post('shipping_provider');
        }
        $items = array();
        $items_to_remove = array();
        $i = sizeof($_POST['product']);
    	for ($r = 0; $r < $i; $r++) {
    		$product_id = $_POST['product_id'][$r];
            $product_type = ($_POST['product_type'][$r]);
            $product_name = ($_POST['product_name'][$r]);
            $quantity = ($_POST['quantity'][$r]);
            $total_cost = ($_POST['subtotal_item'][$r]);
            $stock_to_transfer = ($_POST['stock_to_transfer'][$r]);
            $items = array(
	        	'date' => date('Y-m-d H:i:s'),
	        	'sending_store' => $this->activeStore,
	        	'receiving_store' => $this->input->post('receiving_store'),
	        	'shipping_cost' => $this->input->post('shipping_cost'),
                'shipping_trackcode' => $this->input->post('track_code'),
	        	'transfer_code' => $this->input->post('transfer_code'),
	        	'shipping_provider' => $provider,
	        	'status' => 'sent',
	        	'is_refund' => 0,
	        	'product_id' => $product_id,
	        	'product_name' => $product_name,
	        	'product_type' => $product_type,
	        	'quantity' => $quantity,
                'total_cost' => $total_cost,
                'stock_to_transfer' => $stock_to_transfer,
	        );
            $this->db->insert('transfers', $items);
            $transfer_id = $this->db->insert_id();
            $stock_to_transfer = explode(',', $stock_to_transfer);

            foreach ($stock_to_transfer as $item_id) {
                $this->db->where('id', $item_id);
                $this->db->update('stock',
                            array(
                                'in_state_of_transfer'=>1,
                                'store_id'=> $this->input->post('receiving_store'),
                                'transfer_id'=> $transfer_id
                                )
                        );
            }

    	}
    	$this->session->set_flashdata('message', lang('Transfer Sent'));
    	redirect('panel/transfers');
    }
    public function completed() {
        $stock_id = $this->input->post('id');
        $transfer_id = $this->input->post('transfer_id');

        $this->db
            ->where('id', $stock_id)
            ->update('stock', array('in_state_of_transfer'=>0, 'transfer_id'=>NULL));

       $count = $this->db->where('transfer_id', $transfer_id)->from('stock')->count_all_results();
       if ($count == 0) {
           $this->db->update('transfers', array('status'=>'received'), array('id'=>$transfer_id));
       }
        echo "true";
    }

    public function complete_modal() {
        $row = FALSE;
        if (!$this->input->post('id')) {
            die('false');
        }
        $id = $this->input->post('id');
        $q = $this->db->get_where('transfers', array('id' => $id));
        if ($q->num_rows() > 0) {
            $row = $q->row();
        }
        if (!$row) {
            die('false');
        }
        $stock_to_transfer = explode(',', $row->stock_to_transfer);
        $items = array();

        $items = $this->db->where_in('id', $stock_to_transfer)->get('stock')->result_array();
        if (sizeof($items) < $row->quantity) {
            $sold = $row->quantity - sizeof($items);
            for ($i=0; $i < $sold; $i++) {
                $data = array();
                $data['in_state_of_transfer'] = 0;
                $data['transfer_id'] = NULL;
                $data['id'] = NULL;
                $data['price'] = 'Sold';
                $data['serial_number'] = lang('Not Available');
                $items[] = $data;
            }
        }
        $this->data['transfer'] = $row;
        $this->data['items'] = $items;
        $this->load->view($this->theme.'transfers/complete_modal', $this->data);
    }

    public function multi_add() {
        $row = FALSE;
        if (!$this->input->post('val')) {
            die('false');
        }
        if (!$this->input->post('transfer_id')) {
            die('false');
        }

        $stock_ids = $this->input->post('val');
        $transfer_id = $this->input->post('transfer_id');

        foreach ($stock_ids as $stock_id) {
            $this->db
                ->where('id', $stock_id)
                ->update('stock', array('in_state_of_transfer'=>0, 'transfer_id'=>NULL));
        }

        $count = $this->db->where('transfer_id', $transfer_id)->from('stock')->count_all_results();
        if ($count == 0) {
           $this->db->update('transfers', array('status'=>'received'), array('id'=>$transfer_id));
        }
        echo json_encode($stock_ids);
    }


}
