<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Welcome_model extends CI_Model {
	public function __construct()

    {

        parent::__construct();

    }

    public function getRepairCount() {

        $pending_Statuses = $this->settings_model->getRepairStatusesPending();

        $this->db->from('repair');
        $this->db->where_in('status', $pending_Statuses);
        $this->db->where('disable', 0);
        // $this->db->where('status !=', 5);
        // $this->db->where('status !=', 0);
        // $this->db->where('status !=', 2);		
        $this->db->where('store_id', (int)$this->session->userdata('active_store'));
        return $this->db->count_all_results();

    }
	
	public function getCompletedRepairCount7()

    {
        $completed = $this->settings_model->getRepairStatusesCompleted();


        $this->db->from('repair');
        $this->db->where_in('status', $completed);
        // $this->db->where('status', 0);
        $this->db->where('disable', 0);
		$this->db->where('date_closing BETWEEN DATE_SUB(NOW(), INTERVAL 7 DAY) AND NOW()');
        $this->db->where('store_id', (int)$this->session->userdata('active_store'));

		

        return $this->db->count_all_results();

    }
	
	public function getCompletedRepairCount30()

    {
        $completed = $this->settings_model->getRepairStatusesCompleted();


        $this->db->from('repair');
        // $this->db->where('status', 0);
        $this->db->where_in('status', $completed);
        
        $this->db->where('disable', 0);
		$this->db->where('date_closing BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW()');
        $this->db->where('store_id', (int)$this->session->userdata('active_store'));

        return $this->db->count_all_results();

    }

    public function getClientCount()

    {
        $this->db->where('(universal=1 OR store_id='.(int)$this->session->userdata('active_store').')' , NULL, FALSE);
        $this->db->from('clients');

        return $this->db->count_all_results();

    }

    public function getStockCount()

    {
        $this->db->where('(universal=1 OR store_id='.(int)$this->session->userdata('active_store').')' , NULL, FALSE);
        $this->db->from('inventory');
        return $this->db->count_all_results();

    }

public function getRecentSales($perms) {
    if ($perms && !$perms['recent_sales-viewall']) {
        if ($perms['recent_sales-ownstore']) {
            $activeStore = (int)$this->session->userdata('active_store');
            $this->db->where('sales.store_id', $activeStore);
        }else{
            $this->db->where_in('store_id', explode(',', $perms['recent_sales-stores']));
        }
    }
    $q = $this->db->select("LPAD(sales.id, 4, '0') as sale_id, customer, (SELECT CASE WHEN item_type = 'crepairs' THEN GROUP_CONCAT(CONCAT(product_name,' (Deposit)')) WHEN item_type = 'drepairs' THEN GROUP_CONCAT(CONCAT(product_name,' (Repair Pickup)')) WHEN item_type IN ('new_phone', 'used_phone') THEN GROUP_CONCAT(CONCAT(product_name,' (Phone Sold)')) ELSE GROUP_CONCAT(product_name) END FROM sale_items WHERE sale_items.sale_id = sales.id) as name, TRUNCATE(grand_total, 2) as grand_total")
    ->order_by('id', 'DESC')
    ->limit(10)->get('sales');
    if ($q->num_rows() > 0) {
        return $q->result();
    }else{
        return array();
    }
}

    public function getAllCommissions($interval) {
        $this->db->where('sales.biller_id', $this->ion_auth->row()->id);
        $this->db->where('sale_items.item_type !=', 'crepairs');
        $this->db->where('sale_items.item_type !=', 'cp');
        if ($interval == 'day') {
            $this->db->where('DATE(sales.date) = CURDATE()');
        }elseif ($interval == 'week') {
            $this->db->where('sales.date BETWEEN DATE_SUB(NOW(), INTERVAL 7 DAY) AND NOW()');
        }elseif ($interval == 'month') {
            $this->db->where('sales.date BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW()');
        }
        $q = $this->db
                ->select('SUM(sale_items.commission) as commission')
                ->join('sales', 'sales.id=sale_items.sale_id')
                ->join('repair_items', 'repair_items.repair_id=sale_items.product_id', 'left')
                ->join('commission', 'commission.id=sale_items.plan_id', 'left')
                ->join('store', 'store.id=sales.store_id', 'left')
                ->join('groups', 'groups.id=sales.biller_id')
                ->from('sale_items')->get();

         if ($q->num_rows() > 0) {
            if ($q->row()->commission) {
                return $q->row()->commission;
            }else{
                return number_format(0,2);
            }
         }else{
            return number_format(0,2);
         }
    }
   
    public function getBoardMessages() {
        $q = $this->db->select("id, timestamp, user_id, message,")
            ->order_by('timestamp')
            ->get('message_board');
        if ($q->num_rows() > 0) {
            return $q->result();
        }else{
            return array();
        }
    }

}

