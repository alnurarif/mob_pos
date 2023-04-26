<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getStockValue()
    {
        $total_stock_cost = 0;
        $this->db->where('store_id', (int)$this->session->userdata('active_store'));
        $q = $this->db
            ->select('SUM(price) as total_stock_cost')
            ->get('stock');
        if ($q->num_rows() > 0) {
            $total_stock_cost = $q->row()->total_stock_cost;
        }

        $inventory_qty = 0;
        $inventory_total = 0;
        $this->db->where('(inventory.universal=1 OR inventory.store_id='.(int)$this->session->userdata('active_store').')' , NULL, FALSE);
        $inv = $this->db
            ->select('price as price, (SELECT COUNT(id) FROM stock WHERE stock.inventory_id = inventory.id AND stock.inventory_type = "repair" AND store_id='.(int)$this->session->userdata('active_store').') as quantity')
            ->get('inventory');
        if ($inv->num_rows() > 0) {
            foreach ($inv->result() as $a) {
                $inventory_qty += $a->quantity;
                $inventory_total += $a->quantity * $a->price;
            }
        }

        $accessories_qty = 0;
        $accessories_total = 0;

        $this->db->where('(accessory.universal=1 OR accessory.store_id='.(int)$this->session->userdata('active_store').')' , NULL, FALSE);
        $q = $this->db
            ->select('price as price, (SELECT COUNT(id) FROM stock WHERE stock.inventory_id = accessory.id AND stock.inventory_type = "accessory" AND store_id='.(int)$this->session->userdata('active_store').') as quantity')
            ->get('accessory');
        
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $a) {
                $accessories_qty += $a->quantity;
                $accessories_total += $a->quantity * $a->price;
            }
        }

        $this->db->where('(other.universal=1 OR other.store_id='.(int)$this->session->userdata('active_store').')' , NULL, FALSE);
        $others_qty = 0;
        $others_total = 0;
        $q = $this->db
            ->select('price as price, (SELECT COUNT(id) FROM stock WHERE stock.inventory_id = other.id AND stock.inventory_type = "other" AND store_id='.(int)$this->session->userdata('active_store').') as quantity')
            ->get('other');
       
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $a) {
                $others_qty += $a->quantity;
                $others_total += $a->quantity * $a->price;
            }
        }

        
        $total_phone_cost = 0;
        $total_phone_price = 0;
        $total_phone_qty = 0;
        $this->db->where('store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('phones.sold', '0');
        $q = $this->db
            ->select('SUM(phone_items.cost) as phone_cost, SUM(phone_items.price) as phone_price, COUNT(phone_items.id) as qty')
            ->join('phones', 'phones.id=phone_items.phone_id')
            ->get('phone_items');
        if ($q->num_rows() > 0) {
            $row = $q->row();
            $total_phone_cost = $row->phone_cost;
            $total_phone_price = $row->phone_price;
            $total_phone_qty = $row->qty;
        }



        // $this->db->where('store_id', (int)$this->session->userdata('active_store'));
        // $nphones_qty = 0;
        // $nphones_total = 0;
        // $q = $this->db
        //     ->select('price as price, (SELECT COUNT(id) FROM stock WHERE stock.inventory_id = other.id AND stock.inventory_type = "phones" AND store_id='.(int)$this->session->userdata('active_store').') as quantity')
        //     ->get('other');
       
        // if ($q->num_rows() > 0) {
        //     foreach ($q->result() as $a) {
        //         $nphones_qty += $a->quantity;
        //         $nphones_total += $a->quantity * $a->price;
        //     }
        // }

        $total_qty = $inventory_qty + $accessories_qty + $others_qty + $total_phone_qty;
        $total_price = ($inventory_total) + ($accessories_total) + ($others_total) + ($total_phone_price);
        $total_cost = $total_stock_cost + $total_phone_cost;

        return array(
            'qty' => $total_qty,
            'cost' => $total_cost,
            'price' => $total_price,
        );
    }
    
    public function getStockTotals()
    {
        $total = 0;
        $this->db->where('(inventory.universal=1 OR inventory.store_id='.(int)$this->session->userdata('active_store').')' , NULL, FALSE);
        $total += $this->db->count_all_results('inventory');

        $this->db->where('(accessory.universal=1 OR accessory.store_id='.(int)$this->session->userdata('active_store').')' , NULL, FALSE);
        $total += $this->db->count_all_results('accessory');

        $this->db->where('(other.universal=1 OR other.store_id='.(int)$this->session->userdata('active_store').')' , NULL, FALSE);
        $total += $this->db->count_all_results('other');

        $this->db->where('phones.store_id', (int)$this->session->userdata('active_store'));
        $total += $this->db->count_all_results('phones');
        return $total;
    }





    public function getTotalSales($start = null, $end = null, $created_by = null)
    {
         if ($created_by) {
            $this->db->where('biller_id', $created_by);
        }
        $this->db->where("sales.date BETWEEN '".$start."' AND '".$end."'", NULL, FALSE);
        $q=$this->db->select('COUNT(sales.id) as count, (date) as date')->group_by('DATE(sales.date)')->get('sales');
        $data = [];
        $count = 0;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $count += $row->count;
                $data[] = [$row->date, $row->count];
            }
        }
        return [$data, $count];
    }

  public function getTotalSalesItems($start = null, $end = null, $created_by = null)
    {
         if ($created_by) {
            $this->db->where('biller_id', $created_by);
        }
        $this->db->where("sales.date BETWEEN '".$start."' AND '".$end." 23:59:59'", NULL, FALSE);
        $q=$this->db
            ->select('SUM(quantity) as count, (sales.date) as date')
            ->group_by('DATE(sales.date)')
            ->join('sale_items', 'sale_items.sale_id=sales.id', 'left')
            ->get('sales');
        $data = [];
        $count = 0;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $count += $row->count;
                $data[] = [$row->date, $row->count];
            }
        }
        return [$data, $count];
    }


    public function getTotalSalesGTotal($start = null, $end = null, $created_by = null)
    {
         if ($created_by) {
            $this->db->where('biller_id', $created_by);
        }
        $this->db->where("sales.date BETWEEN '".$start."' AND '".$end." 23:59:59'", NULL, FALSE);
        $q=$this->db
               ->select("SUM(grand_total) as count,COUNT(sales.id) as avg,(sales.date) as date")
                ->group_by('DATE(sales.date)')
                ->get('sales');
        $data = [];
        $total = 0;
        $count = 0;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $count += $row->avg;
                $total += $row->count;
                $data[] = [$row->date, $row->count];
            }
        }
        return [$data, $total, $count];
    }


    public function getTotalSalesProfit($start = null, $end = null, $created_by = null)
    {
        $this->db->where("sales.date BETWEEN '".$start."' AND '".$end."'", NULL, FALSE);
        
        if ($created_by) {
            $this->db->where('biller_id', $created_by);
        }


         $q=$this->db
               ->select("SUM((SELECT SUM(unit_price * quantity) FROM sale_items WHERE sale_items.sale_id = sales.id)) as price, SUM(if(sales.sale_id IS NOT NULL, (0-(SELECT if(item_type IN ('cp', 'crepairs'), SUM(ABS(unit_price - discount)), if(add_to_stock, SUM(ABS(unit_price - discount)-unit_cost), SUM(ABS(unit_price - discount))) ) FROM sale_items WHERE sale_items.sale_id=sales.id)) + sales.surcharge, (SELECT if(item_type IN ('cp', 'crepairs'), SUM(unit_price - discount), SUM((unit_price - discount)-unit_cost)) FROM sale_items WHERE sale_items.sale_id=sales.id) + (SELECT SUM(activation_spiff) FROM sale_items WHERE sale_items.sale_id=sales.id))) as count,COUNT(sales.id) as avg,(sales.date) as date")
                ->group_by('DATE(sales.date)')
                ->get('sales');

        $data = [];
        $total = 0;
        $count = 0;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $count += $row->avg;
                $total += $row->count;
                $data[] = [$row->date, $row->count];
            }
        }
        return [$data, $total, $count];
    }








    /*
        |--------------------------------------------------------------------------
        | GET EARNINGS BY MONTHS/YEARS
        | @param month, year
        |--------------------------------------------------------------------------
        */
        public function list_earnings($month, $year)
        {
            $data = $this->list_closed_repairs($month, $year);



            $number = array();
            for ($i = 1; $i <= 33; ++$i) {
                $number[$i] = 0;
            }
            for ($d = 0; $d <= count($data); ++$d) {
                $id = @date('j', strtotime($data[$d]['date']));
                $number[$id] = $number[$id] + @$data[$d]['grand_total'] - @$data[$d]['total_tax'];
            }
            $number[32] = (int) $month;
            $number[33] = (int) $year;



            return $number;
        }
        /*
    |--------------------------------------------------------------------------
    | LIST OF CLOSED ORDER/REPARATION
    | @param month, year
    |--------------------------------------------------------------------------
    */
    public function list_closed_repairs($month, $year)
    {
        $data = array();
        $data1 = array();
        $this->db->order_by('id', 'asc');

        if ($this->input->get('assigned_to') > 0) {
            $this->db->where('biller_id', (int)$this->input->get('assigned_to'));
        }

        $this->db->where('store_id', (int)$this->session->userdata('active_store'));
        $query = $this->db
        ->select('sales.*, (SELECT SUM(unit_cost * quantity) FROM sale_items WHERE sale_items.sale_id = sales.id) as cost, (SELECT SUM(unit_price * quantity) FROM sale_items WHERE sale_items.sale_id = sales.id) as price, if(sales.sale_id IS NOT NULL, (0-(SELECT if(item_type IN (\'cp\', \'crepairs\'), SUM(ABS(unit_price - discount)), if(add_to_stock, SUM(ABS(unit_price - discount)-unit_cost), SUM(ABS(unit_price - discount))) ) FROM sale_items WHERE sale_items.sale_id=sales.id)) + sales.surcharge, (SELECT if(item_type IN (\'cp\', \'crepairs\'), SUM(unit_price - discount), SUM((unit_price - discount)-unit_cost)) FROM sale_items WHERE sale_items.sale_id=sales.id) + (SELECT SUM(activation_spiff) FROM sale_items WHERE sale_items.sale_id=sales.id)) as profit')
        ->get('sales');


        if ($query->num_rows() > 0) {
            $data = $query->result_array();
        }
        foreach ($data as $d) {
            if ($d['total_items'] != 0) {
                if ((date('m', strtotime($d['date'])) == $month) && (date('Y', strtotime($d['date'])) == $year)) {
                    $data1[] = $d;
                }
            }
        }
        return $data1;
    }

    public function getAllSuppliers() {
        $this->db->where('(universal=1 OR store_id='.(int)$this->session->userdata('active_store').')' , NULL, FALSE);
        $q = $this->db->get('suppliers');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[$row->id] = $row->name;
            }
            return $data;
        }
        return FALSE;
    }

     public function getAllCustomers() {
        $this->db->where('(universal=1 OR store_id='.(int)$this->session->userdata('active_store').')' , NULL, FALSE);
        $q = $this->db->get('clients');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[$row->id] = $row->first_name . ' ' . $row->last_name .' ('.preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $row->telephone).')';
            }
            return $data;
        }
        return FALSE;
    }
   
   // G/L Report
    public function inventoryRecieved($start_date, $end_date)
    {
        $this->db->where('(purchases.store_id='.(int)$this->session->userdata('active_store').' AND date_verified is NOT NULL AND date_verified BETWEEN "' . $start_date . '" and "' . $end_date . '")', NULL, FALSE);
        $this->db->or_where('(purchases.store_id='.(int)$this->session->userdata('active_store').' AND purchases.status="returned" AND purchases.date BETWEEN "' . $start_date . '" and "' . $end_date . '")');
        $this->db->join('purchases', 'purchases.id=purchase_items.purchase_id');
        return $this->db->select('SUM(unit_cost*quantity) as total')->get('purchase_items')->row()->total;
    }

    public function purchasesRecieved($start_date, $end_date)
    {
        $this->db->where('purchases.store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('purchases.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        return $this->db
                ->select('0-SUM(ABS(unit_cost*quantity)) as total')
                ->join('purchases', 'purchases.id=purchase_items.purchase_id')
                ->where('purchases.status', 'returned')
                ->get('purchase_items')
                ->row()
                ->total;
    }

    public function vendorOrdersPlaced($start_date, $end_date)
    {
        $this->db->where('purchases.store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        return $this->db->select('SUM(grand_total-shipping) as total')->get('purchases')->row()->total;
        
    }
    public function shippingTotal($start_date, $end_date)
    {
        $this->db->where('purchases.store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        return $this->db->select('SUM(shipping) as total')->get('purchases')->row()->total;
    }

    public function customerPurchases($start_date, $end_date)
    {
        $this->db->where('sale_items.store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        return $this->db->select('SUM(subtotal) as total')->where(array('item_type' => 'cp', 'refund_item'=>0))->get('sale_items')->row()->total;
    }

    public function repairDeposited($start_date, $end_date)
    {
        $this->db->where('sale_items.store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        return $this->db->select('SUM(unit_price) as total')->where(array('item_type' => 'crepairs', 'refund_item'=>0))->get('sale_items')->row()->total;
    }
    public function repairClosed($start_date, $end_date)
    {
        $this->db->where('sale_items.store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        return $this->db->select('SUM(unit_price) as total')->where(array('item_type' => 'drepairs', 'refund_item'=>0))->get('sale_items')->row()->total;
    }
    public function usedPhoneSales($start_date, $end_date)
    {
        $this->db->where('sale_items.store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        return $this->db->select('SUM(unit_price) as total')->where(array('item_type' => 'used_phone', 'refund_item'=>0))->get('sale_items')->row()->total;
    }
    public function newPhoneSales($start_date, $end_date)
    {
        $this->db->where('sale_items.store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        return $this->db->select('SUM(unit_price) as total')->where(array('item_type' => 'new_phone', 'refund_item'=>0))->get('sale_items')->row()->total;
    }
    public function accessorySales($start_date, $end_date)
    {
        $this->db->where('sale_items.store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        return $this->db->select('SUM(unit_price) as total')->where(array('item_type' => 'accessory', 'refund_item'=>0))->get('sale_items')->row()->total;
    }
    public function otherSales($start_date, $end_date)
    {
        $this->db->where('sale_items.store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        return $this->db->select('SUM(unit_price) as total')->where(array('item_type' => 'other', 'refund_item'=>0))->get('sale_items')->row()->total;
    }
    public function planSales($start_date, $end_date)
    {
        $this->db->where('sale_items.store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        return $this->db->select('SUM(unit_price) as total')->where(array('item_type' => 'plans', 'refund_item'=>0))->get('sale_items')->row()->total;
    }

    public function totalTaxes($start_date, $end_date)
    {
        $this->db->where('sale_items.store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        return $this->db->select('SUM(tax) as total')->get('sale_items')->row()->total;
    }

    public function refundsIssued($start_date, $end_date)
    {
        $this->db->where('sales.store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        return $this->db->select('0-SUM(ABS(grand_total)+total_tax) as total')->where(array('sale_status'=>'returned'))->get('sales')->row()->total;
    }

    public function refundsSurcharges($start_date, $end_date)
    {
        $this->db->where('sales.store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        return $this->db->select('SUM(surcharge) as total')->get('sales')->row()->total;
    }

    public function inventoryOut($start_date, $end_date)
    {
        $this->db->where('sale_items.store_id', (int)$this->session->userdata('active_store'));
        return @$this->db->select('(SELECT SUM(ABS(unit_cost)) FROM sale_items WHERE sale_items.store_id='.(int)$this->session->userdata('active_store').' AND refund_item = 1 AND add_to_stock=1 AND date BETWEEN "' . $start_date . '" and "' . $end_date . '" ) - (SELECT SUM(unit_cost) FROM sale_items WHERE sale_items.store_id='.(int)$this->session->userdata('active_store').' AND refund_item = 0 AND date BETWEEN "' . $start_date . '" and "' . $end_date . '" ) as total, unit_cost')->get('sale_items')->row()->total;
    }
    public function getTotalProfit($start_date, $end_date)
    {
        $this->db->where('sales.store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        $ttt = $this->db->select("SUM(if(sales.sale_id IS NOT NULL, (0-(SELECT if(item_type IN ('cp', 'crepairs'), SUM(ABS(unit_price)), if(add_to_stock, SUM(ABS(unit_price)-unit_cost), SUM(ABS(unit_price))) )  FROM sale_items WHERE sale_items.sale_id=sales.id)) + sales.surcharge, (SELECT if(item_type IN ('cp', 'crepairs'), SUM(unit_price), SUM((unit_price)-unit_cost)) FROM sale_items WHERE sale_items.sale_id=sales.id AND store_id=".(int)$this->session->userdata('active_store')."))) + (SELECT SUM(activation_spiff) FROM sale_items WHERE sale_items.sale_id=sales.id) as total")->get('sales')->row()->total;
        $this->db->where('is_refund', 1);
        $this->db->where('transfers.receiving_store', (int)$this->session->userdata('active_store'));
        $this->db->where('status', 'received');
        $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        $tt = $this->db->select('SUM(total_cost) as total')->get('transfers')->row()->total;
        if ($tt > 0) {
            return $tt - $ttt;
        }
        return $ttt;
    }

    public function rt($start_date, $end_date)
    {
        $this->db->where('transfers.sending_store', (int)$this->session->userdata('active_store'));
        $this->db->where('transfers.is_refund', 0);
        $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        return $this->db->select('0-SUM(total_cost) as total')->get('transfers')->row()->total;
    }

    public function ttf($start_date, $end_date)
    {
        $this->db->where('transfers.sending_store', (int)$this->session->userdata('active_store'));
        $this->db->where('transfers.is_refund', 1);
        $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        return $this->db->select('0-SUM(total_cost) as total')->get('transfers')->row()->total;
    }

    public function ttr($start_date, $end_date)
    {
        $this->db->where('transfers.receiving_store', (int)$this->session->userdata('active_store'));
        $this->db->where('status', 'received');
        $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        return $this->db->select('SUM(total_cost) as total')->get('transfers')->row()->total;
    }

    public function tpt($start_date, $end_date)
    {
        $this->db->where('status', 'sent');
        $this->db->where('is_refund', 0);
        $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        $this->db->where('transfers.sending_store', (int)$this->session->userdata('active_store'));
        $sending_store = $this->db->select('SUM(total_cost) as total')->get('transfers')->row()->total;

        $this->db->where('status', 'sent');
        $this->db->where('is_refund', 0);
        $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        $this->db->where('transfers.receiving_store', (int)$this->session->userdata('active_store'));
        $receiving_store = $this->db->select('SUM(total_cost) as total')->get('transfers')->row()->total;

        return $receiving_store-$sending_store;
    }

    public function expenses($start_date, $end_date)
    {
        $this->db->where('store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('type', 'expense');
        $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        return $this->db->select('SUM(amount) as total')->get('account_entries')->row()->total;
    }

    public function deposits($start_date, $end_date)
    {
        $this->db->where('store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('type', 'deposit');
        $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        return $this->db->select('SUM(amount) as total')->get('account_entries')->row()->total;
    }

}
