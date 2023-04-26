<?php defined('BASEPATH') OR exit('No direct script access allowed');

if(! function_exists('super_unique')) {
    function super_unique($array, $key) {
       $temp_array = [];
       foreach ($array as &$v) {
           if (!isset($temp_array[$v[$key]]))
           $temp_array[$v[$key]] =& $v;
       }
       $array = array_values($temp_array);
       return $array;

    }
}
function getDomain()
{
    return preg_replace("/^[\w]{2,6}:\/\/([\w\d\.\-]+).*$/","$1", base_url());
}
function getDueMenu($ending_, $date_opening, $completed) {
  $ending_date = false;
  if ($ending_) {
    if (in_array($ending_, array('15M','30M','45M','1H','2H','3H','4H','5H','6H','7H','8H','9H','10H','11H','12H'))) {
      $ending_date = increment_time($date_opening, $ending_);
    }else{
      $ending_date = increment_date2($date_opening, $ending_);
    }
  }

  if ($ending_date) {
    $today = time();
    $ending_date = strtotime($ending_date);
    if ($ending_date < $today && !$completed) {
         return 'btn-danger';
    } else {
         return 'btn-info';
    }
  }
   return 'btn-info';
}

if(!function_exists('getMenu')) {
    function getMenu($type, $id, $grand_total, $code, $paid) {
    	if ($type == 'lead') {
    		$actions = '<div class="text-center"><div class="btn-group text-left">'
	            . '<button type="button" class="btn btn-info btn-round dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'
	            . lang('actions') . ' <span class="caret"></span></button>
	        <ul class="dropdown-menu " role="menu">';
          $actions .= "<li><a data-dismiss='modal' class='view_lead' href='#view_lead' data-toggle='modal' data-num='".$id."'><i class='fas fa-check'></i> ".lang('view')."</a></li>"; 
	        $actions .= "<li><a  data-dismiss='modal' id='sign_repair' href='#signModal' data-type='lead' data-toggle='modal' data-mode='update_sign' data-num='".$id."'><i class='fas fa-edit'></i> ".lang('sign_repair')."</a></li>"; 
	        $actions .= "<li><a  data-dismiss='modal' id='modify_reparation' href='#repairModal' data-toggle='modal' data-num='".$id."'><i class='fas fa-edit'></i> ".lang('edit_reparation')."</a></li>"; 
	        $actions .= "<li><a id='delete_reparation' data-num='".$id."'><i class='fas fa-trash-alt'></i> ".lang('delete_reparation')."</a></li>"; 
	        $actions .= '</ul></div>';
    	}else{
    		$actions = '<div class="text-center"><div class="btn-group text-left">'
	            . '<button type="button" class="btn btn-info btn-round dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'
	            . lang('actions') . ' <span class="caret"></span></button>
	        <ul class="dropdown-menu" role="menu">';
          $actions .= "<li><a data-dismiss='modal' class='view_repair' href='#view_repair' data-toggle='modal' data-num='".$id."'><i class='fas fa-check'></i> ".lang('view')."</a></li>"; 
	        $actions .= "<li><a  data-dismiss='modal' id='invoice_sign_invoice' href='#signModal' data-toggle='modal' data-type='repair' data-mode='update_sign' data-num='".$id."'><i class='fas fa-edit'></i> ".lang('sign_invoice')."</a></li>"; 
	        $actions .= "<li><a id='invoice_pay_advance' data-num='".$id."___".$grand_total."___".$code."___".$paid."'><i class='fas fa-edit'></i> ".lang('pay_advance')."</a></li>"; 
	        $actions .= "<li><a id='invoice_pay_total' data-num='".$id."___".$grand_total."___".$code."___".$paid."'><i class='fas fa-edit'></i> ".lang('pay_total')."</a></li>"; 
          $actions .= "<li><a target='_blank' href=\"".base_url()."client/reparations/invoice/".$id."/1/\"><i class=\"fas fa-print\"></i> ".lang('invoice')."</a></li>"; 
	        $actions .= '</ul></div>';
    	}
    	return $actions;
    }
}

if(!function_exists('getRecurMenu')) {
  function getRecurMenu($id, $next_date) {
    $actions = '<div class="text-center"><div class="btn-group text-left">'
        . '<button type="button" class="btn-round btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'
        . lang('actions') . ' <span class="caret"></span></button>
    <ul class="dropdown-menu" role="menu">';
    if ($next_date == '0000-00-00') {
      $actions .= "<li><a href='".base_url()."panel/reparation/active_recur/".$id."'><i class='fas fa-redo'></i> ".lang('active_recur')."</a></li>"; 
    }else{
      $actions .= "<li><a href='".base_url()."panel/reparation/stop_recur/".$id."'><i class='fas fa-ban'></i> ".lang('stop_recur')."</a></li>"; 
    }
    $actions .= "<li><a id='edit_recurring_reparation' data-num='".$id."'><i class='fas fa-edit'></i> ".lang('edit_recur')."</a></li>"; 

    $actions .= "<li><a href='".base_url()."panel/reparation/delete_recur/".$id."'><i class='fas fa-trash-alt'></i> ".lang('delete_recur')."</a></li>"; 

    $actions .= '</ul></div>';

    return $actions;
  }
}
if(!function_exists('getWarrantyStatus')) {
  function getWarrantyStatus($date_closing, $warranty, $fontsize = NULL) {
    if ($warranty !== '' && $warranty !== '0' && $warranty !== 'warranty') {
      $new_date = increment_date(date("Y-m-d", strtotime($date_closing)), $warranty);
      $datetime1 = new DateTime();
      $datetime2 = new DateTime($new_date);
      $interval = $datetime1->diff($datetime2);

      $diff = (int)$interval->format("%r%a");
      if ($diff < 0) {
        return '<span class="label"  style="background-color: #ccc;width: 100%;line-height: 1.5;background-color: #ccc;'.($fontsize ? 'font-size:'.$fontsize : '').'">Warranty Expired on '.date('d-m-Y', strtotime($new_date)).'</span>';
      }else{
        $elapsed = $interval->format('%m months %a days %h hours');
        return '<span style="display:inline;'.($fontsize ? 'font-size:'.$fontsize : '').'" class="label label-success">Warranty Expires on '.date('d-m-Y', strtotime($new_date)).'</span>';
      }
    }else{
      return '<span class="label" style="display:inline;background-color: #ccc;">No Warranty</span>';
    }
  }
}


if(!function_exists('getWarrantyStatus2')) {
  function getWarrantyStatus2($date_closing, $warranty, $fontsize = NULL) {
    if ($warranty !== '' && $warranty !== '0' && $warranty !== 'warranty') {
      $new_date = increment_date(date("Y-m-d", strtotime($date_closing)), $warranty);
      $datetime1 = new DateTime();
      $datetime2 = new DateTime($new_date);
      $interval = $datetime1->diff($datetime2);

      $diff = (int)$interval->format("%r%a");
      if ($diff < 0) {
        return '<span class="label"  style="background-color: #ccc;width: 100%;line-height: 1.5;background-color: #ccc;'.($fontsize ? 'font-size:'.$fontsize : '').'">'.date('d-m-Y', strtotime($new_date)).'</span>';
      }else{
        $elapsed = $interval->format('%m months %a days %h hours');
        return '<span style="display:inline;'.($fontsize ? 'font-size:'.$fontsize : '').'" class="label label-success">'.date('d-m-Y', strtotime($new_date)).'</span>';
      }
    }else{
      return '<span class="label" style="display:inline;background-color: #ccc;">No Warranty</span>';
    }
  }
}
if(!function_exists('unserialize_files')) {
  function unserialize_files($files) {
    $str = '';
    $files = explode(',', $files);
    $files = array_filter($files);
    foreach($files as $file){
      $file_info = explode('___', $file);
      $file_info = array_filter($file_info);
      if(array_key_exists(1, $file_info)){
        $str .= '<li><a href="'.base_url().'files/'.$file_info[2].'" target="_blank">'.$file_info[1].'</a></li>';
      }
    }
    return $str;
  }
}

if(!function_exists('getRefLink')) {
  function getRefLink($type, $reference_id) {
    if ($type == 'sale') {
      $detail_link = anchor('panel/pos/modal_view/'.$reference_id, '<i class="fas fa-search"></i> View Sale', 'data-toggle="modal" data-target="#myModal"');
    }else{
      $detail_link = "<a data-dismiss='modal' class='view' onclick='find_reparation(".$reference_id.")' href='#view_reparation' data-toggle='modal' data-num='".$reference_id."'><i class='fas fa-search'></i> View Reparation</a>"; 

    }
    return $detail_link;
  }
}



if(!function_exists('getExpenseMenu')) {
  function getExpenseMenu($action, $type) {
    if ($type == 15) {
      return '';
      # code...
    }
    return $action;

  }
}


function utf8ize($d) {
    if (is_array($d)) {
        foreach ($d as $k => $v) {
            $d[$k] = utf8ize($v);
        }
    } else if (is_string ($d)) {
        return utf8_encode($d);
    }
    return $d;
}

if(!function_exists('escapeStr')) {
  function escapeStr($str) {
    return xss_clean($str);

  }
}


function sort_array_of_array(&$array, $subfield)
{
    $sortarray = array();
    foreach ($array as $key => $row)
    {
        $sortarray[$key] = $row->{$subfield};
    }

    array_multisort($sortarray, SORT_ASC, $array);
}

function getActionMenuSales($id, $repair_id) {
    $detail_link = anchor('panel/sales/modal_view/'.$id, '<i class="fas fa-file"></i> ' . lang('sale_details'), 'data-toggle="modal" data-target="#myModal"');
    $bill_link = '<a href="'.base_url('panel/pos/view/'.$id).'" ><i class="fas fa-file"></i> '.lang('View Sale').'</a>';
    $refund_link = '';
    
    $refund_link = '<a href="'.base_url('panel/sales/refund/'.$id).'" ><i class="fa fa-angle-double-left"></i>'.lang('Refund').'</a>';

    $payments_link = anchor('panel/sales/payments/'.$id, '<i class="fas fa-money-bill-alt"></i> ' . lang('view_payments'), 'data-toggle="modal" data-target="#myModal" class="dropdown-item"');
    $add_payment_link = anchor('panel/sales/add_payment/'.$id, '<i class="fas fa-money-bill-alt"></i> ' . lang('add_payment'), 'data-toggle="modal" data-target="#myModal" class="dropdown-item"');
    $email_invoice = '<a id="email_invoice" data-num="'.$id.'"  data-email="$2"><i class="fas fa-envelope"></i> '.lang('email_invoice').'</a>';

    $view_pdf = anchor('panel/pos/view_pdf/'.$id.'/', '<i class="fas fa-file-pdf"></i> ' . lang('view_pdf'), '');
    $donwload_pdf = anchor('panel/pos/view/'.$id.'/1/0', '<i class="fas fa-file-pdf"></i> ' . lang('donwload_pdf'), '');
    
    $view_repair = '';
    if($repair_id > 0) {
      $view_repair = anchor('panel/repair/view/'.$repair_id, '<i class="fas fa-file"></i> ' . lang('view_repair'), 'data-toggle="modal" data-target="#myModalLG"');
    }

    $action = '<div class="text-center"><div class="btn-group text-left">'
        . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
        . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $detail_link . '</li>
            <li>' . $bill_link . '</li>
            <li>' . $refund_link . '</li>
            <li>' . $payments_link . '</li>
            <li>' . $add_payment_link . '</li>
            <li>' . $email_invoice . '</li>
            <li>' . $view_pdf . '</li>
            <li>' . $view_repair . '</li>
        </ul>
    </div></div>';

    return $action;

  
}