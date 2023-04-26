<?php defined('BASEPATH') OR exit('No direct script access allowed');
class pos_inventory extends Auth_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('pos_inventory_model');

	}
	
	public function index($type = NULL,$id = NULL)
	{
		$this->mPageTitle 	= lang('manage_stock');
		if ((!$type) or (!$id)) {
			redirect('panel/');
		}
		if ($type == 'repair') {
        	$this->repairer->checkPermissions('manage_stock', FALSE, 'inventory');
		}

		if ($type == 'accessory') {
        	$this->repairer->checkPermissions('manage_stock', FALSE, 'accessory');
		}

		if ($type == 'other') {
        	$this->repairer->checkPermissions('manage_stock', FALSE, 'other');
		}

		$this->data['id']	= $id;
		
		$this->data['type']	= $type;

		if($type=='accessory' or $type=='other' or $type=='repair' or $type=='phones'){ 
			$type_back = $type;
			$ptype = $type;
			if ($type == 'repair') {
				$ptype = 'inventory';
			}
			$record = $this->pos_inventory_model->get_inventory($id,$ptype);
			$this->data['records'] = $this->pos_inventory_model->get_products($type,$id);
			$this->data['record'] = $record;
			$this->mPageTitle 	= sprintf(lang('manage_inventory_for'), $record['name']);
			$this->data['is_serialized'] = $record['is_serialized'];

			$this->form_validation->set_rules('price_cost', 'Price Cost', 'required|trim');
			$this->form_validation->set_rules('quantity', 'Quantity', 'required|trim');

			if ($this->form_validation->run() == FALSE)  {
				$this->render("pos_inventory/add_inventory");
			} else {

				$stock_data = array(
					'inventory_id'		=> $id,
					'inventory_type'	=> $type,
					'price'				=> $this->input->post('price_cost'),
					'serial_number'		=> $this->input->post('serial_number'),
					'store_id' 			=> $this->activeStore,
					'in_state_of_transfer' => 0,
				);

				
				if ($type == 'repair') {
					$type = 'inventory';
				}
				$this->pos_inventory_model->update_inventory(
					$id,
					$type,
					$stock_data,
					$this->input->post('quantity')
				);
				
				/* Redirect on listing page as per type */
				redirect('panel/pos_inventory/index/'.$type_back.'/'.$id);
			}
		}
	}
	public function getStockData($type = NULL, $id = NULL)
	{
		$ptype = $type;
		if ($type == 'repair') {
			$ptype = 'inventory';
		}
		$record = $this->pos_inventory_model->get_inventory($id,$ptype);
		$is_serialized = $record['is_serialized'];
		$this->load->library('datatables');
		if ($is_serialized) {
			$this->datatables
            ->select('id, serial_number, price, modified_date') 
            ->from('stock')
            ->where(
            	array(
					"inventory_type" => $type,
					"inventory_id"=>$id,
					'store_id'=> $this->activeStore,
					'in_state_of_transfer' => 0,
				)
            );
		}else{
			$this->datatables
            ->select('id, price, modified_date') 
            ->from('stock')
            ->where(
            	array(
					"inventory_type" => $type,
					"inventory_id"=>$id,
					'store_id'=> $this->activeStore,
					'in_state_of_transfer' => 0,
				)
            );
		}
        $this->datatables->add_column('actions', "<a data-dismiss='modal' id='modify' href='#othermodal' data-toggle='modal' data-num='$1'><button class='btn btn-primary btn-xs'><i class='fas fa-edit'></i> ".lang('edit')."</button></a><a id='delete' data-num='$1'><button class='btn btn-danger btn-xs'><i class='fas fa-trash'></i> ".lang('delete')."</button></a>", 'id');
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
	}
	function delete()
	{
		// get ID
		$id = $this->input->post('id');
		$reason = $this->input->post('reason');
		$user_id = $this->input->post('user_id');
		$user = $this->settings_model->getUserByID($user_id);
		// fetch data

		$stock_data = $this->db->get_where('stock', array('id'=> $id));
		if ($stock_data->num_rows() > 0) {
			$stock_data=$stock_data->row();

			$type = $stock_data->inventory_type;
			if($type=='accessory' or $type=='other' or $type=='repair' or $type=='phones'){ 
				$type_back = $type;
				$ptype = $type;
				if ($type == 'repair') {
					$ptype = 'inventory';
				}
				$record = $this->pos_inventory_model->get_inventory($stock_data->inventory_id,$ptype);
				$stock_data->product_name = $record['name'];
			}
			// delete stock data
			$this->db->delete('stock', array('id'=> $id));
			if ($this->db->affected_rows()) {
    			$this->settings_model->addLog('delete-stock-item', 'inventory', $id, json_encode([
    				'reason' => $reason,
    				'user_id' => $user_id,
    				'user_name' => $user->first_name . ' ' . $user->last_name,
    				'data' => $stock_data,
    			]));
				echo "true";
			}else{
				echo "false";
			}
		}else{
			echo "false";
		}

	}
	public function getProductByID()
	{
		$id = $this->input->post('id');
		echo json_encode($this->db->get_where("stock", array(			
				"id"=>$id
			)
		)->row());		
	}
	public function edit()
	{
		$this->pos_inventory_model->update_stock($this->input->post('id'),array('price'=>$this->input->post('it_price'), 'modified_date'=> date('Y-m-d H:i:s')));
		echo "true";
	}
}