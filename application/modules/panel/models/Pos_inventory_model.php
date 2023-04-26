<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Pos_inventory_model extends CI_Model 
{
	public function get_products($type,$id)
	{
		return $this->db->order_by('modified_date', 'ASC')->get_where("stock", array(
				"inventory_type" => $type,
				"inventory_id"=>$id,
				'store_id' => $this->session->userdata('active_store'),
				'in_state_of_transfer' => 0,
			)
		)->result_array();		
	}

	public function get_inventory($id,$table)
	{
		if ($table == 'phones') {
			$this->db->select('phone_name as name, 1 as is_serialized');
		}

		return $this->db->get_where($table, array(			
			"id"=>$id
			)
		)->row_array();		
	}
	public function isSerialized($id,$table)
	{
		$bool = $this->db->get_where($table, array(			
			"id"=>$id
			)
		)->row()->is_serialized;		

	}

	public function update_stock($id,$data)
	{
		$this->db->where('id',$id);
		$this->db->update("stock",$data);
	}

	public function update_inventory($id,$table,$stock_data,$count)
	{
		if($count>0)
		{
			for($i=1;$i<=$count;$i++) 
			{			
				$this->db->insert('stock', $stock_data);
			}
		}

	}

	public function add_inventory($stock_data,$count)
	{
		if($count>0)
		{
			for($i=1;$i<=$count;$i++) 
			{			
				$this->db->insert('stock', $stock_data);
			}
		}

	}

}