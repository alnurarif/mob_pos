<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	
	private function getIfProductExist($repair_id, $product_id){
		$this->db->where('repair_id', $repair_id)->where('product_id', $product_id)->get('sale_items');
		if ($q->num_rows() > 0) {
			return true;
		}
		return false;
	}


	

	public function updateRI()
	{
		$items = $this->db->get('repair_items')->result();
		$new_items = [];
		foreach ($items as $item) {
			if($this->getIfProductExist($item->repair_id, $item->product_id)){
				continue;
			}

			$new_items[] = array(
              'store_id'      => $item->store_id,
              'product_id'    => $item->product_id,
              'repair_id'     => $item->repair_id,
              'unit_cost'     => $item->unit_cost,
              'product_name'  => $item->product_name,
              'product_code'  => $item->product_code,
              'quantity'      => 1,
              'unit_price'    => $item->unit_price,
              'taxable'       => ($item->tax > 0) ? 1 : 0,
              'tax'           => $item->tax,
              'tax_rate'      => $item->tax_rate,
              'subtotal'      => ($item->unit_price) - $item->discount,
              'option_id'     => null,
              'discount'      => $item->discount,
              'item_type'     => 'repair',
              'serial_number' => null,
              'date'          => $item->date_added,
              'refund_item'   => 0,
              'add_to_stock'  => false,
              'sale_item_id'  => NULL,
              'real_store_id' => $item->store_id,
              'store_id'      => $item->store_id,
              'phone_number'  => 'null',
              'set_reminder'  => false,
              'warranty'      => 'false',
              'activation_spiff' => 0,
              'item_details' => '',
          	);
		}
		$this->db->insert_batch('sale_items', $new_items);
	}



	
	
	public function index()
	{
		// $this->db->query("CREATE TABLE `defects` (
		// 	`id` int(11) NOT NULL,
		// 	`name` VARCHAR(255) NOT NULL,
		// 	`description` VARCHAR(255) NOT NULL
		// ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
		// $this->db->query("ALTER TABLE `defects` ADD PRIMARY KEY (`id`);");
		// $this->db->query("ALTER TABLE `defects` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
		// $this->db->query("ALTER TABLE `manufacturers` ADD `parent_id` INT NULL AFTER `disable`;");
		// $this->db->query("ALTER TABLE `settings` ADD `use_defects_input_dropdown` BOOLEAN NOT NULL AFTER `repair_prefix`;");
		// $this->db->query("ALTER TABLE `settings` ADD `use_models_input_dropdown` BOOLEAN NOT NULL AFTER `repair_prefix`;");
		// $this->db->query("ALTER TABLE `repair` ADD `defect_id` INT NULL AFTER `sms`;");
		// $this->db->query("ALTER TABLE `repair` ADD `model_id` INT NULL AFTER `manufacturer_id`;");
		// $this->db->query("ALTER TABLE `repair` ADD `public_note` TEXT NULL AFTER `pos_sold`;");
		// $this->db->query("ALTER TABLE `repair` ADD `date_ready` DATE NULL AFTER `public_note`;");
		// $this->db->query("ALTER TABLE `repair` ADD `aesthetic_conditions`VARCHAR(255) NULL;");
		// $this->db->query("ALTER TABLE `order_ref` CHANGE `repair` `repair` INT NOT NULL; ");
		// $this->db->query("ALTER TABLE `repair` CHANGE `tax_id` `tax_id` INT NULL;");


		
		
		
		// get from repair_items
		$data = array();
		$this->load->model('settings_model');
		$data['settings'] = $this->settings_model->getSettings();
		$this->load->view($this->theme.'home', $data);
		$this->load->language('main_lang');
	}
	public function status()
    {
      header("Access-Control-Allow-Origin: *");

        $code = $this->input->post('code', true);
        $data = array();
        

        $this->db
          ->where('code', $code)
          ->select('*, status.label as status, fg_color, bg_color')
          ->join('status', 'status.id=repair.status');
        $query = $this->db->get('repair');
        if ($query->num_rows() > 0 && strlen($code) > 2) {
            $data = $query->row_array();
            echo json_encode($data);
        } else {
            echo 'false';
        }
    }


  
}
