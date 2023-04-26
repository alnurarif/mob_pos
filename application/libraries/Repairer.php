<?php defined('BASEPATH') or exit('No direct script access allowed');

/*
 *  ==============================================================================
 *  Author      : Usman Sher
 *  Email       : uskhan099@Gmail.com
 *  For         : Repairer
 *  Web         : http://otsglobal.org
 *  ==============================================================================
 */

class Repairer
{

    public function __construct()
    {
        
    }

    public function __get($var)
    {
        return get_instance()->controller->$var;
    }

    private function _rglobRead($source, &$array = array())
    {
        if (!$source || trim($source) == "") {
            $source = ".";
        }
        foreach ((array) glob($source . "/*/") as $key => $value) {
            $this->_rglobRead(str_replace("//", "/", $value), $array);
        }
        $hidden_files = glob($source . ".*") and $htaccess = preg_grep('/\.htaccess$/', $hidden_files);
        $files = array_merge(glob($source . "*.*"), $htaccess);
        foreach ($files as $key => $value) {
            $array[] = str_replace("//", "/", $value);
        }
    }

    private function _zip($array, $part, $destination, $output_name = 'sma')
    {
        $zip = new ZipArchive;
        @mkdir($destination, 0777, true);

        if ($zip->open(str_replace("//", "/", "{$destination}/{$output_name}" . ($part ? '_p' . $part : '') . ".zip"), ZipArchive::CREATE)) {
            foreach ((array) $array as $key => $value) {
                $zip->addFile($value, str_replace(array("../", "./"), null, $value));
            }
            $zip->close();
        }
    }

    public function formatMoney($number)
    {
        $decimals = $this->mSettings->decimals;
        $ts       = $this->mSettings->thousands_sep == '0' ? ' ' : $this->mSettings->thousands_sep;
        $ds       = $this->mSettings->decimals_sep;

        return ($this->mSettings->display_symbol == 1 ? $this->mSettings->currency : '') . 
                number_format($number, $decimals, $ds, $ts) .
                ($this->mSettings->display_symbol == 2 ? $this->mSettings->currency : '');
    }

    public function formatQuantity($number, $decimals = '00')
    {
        if (!$decimals) {
            $decimals = $this->mSettings->qty_decimals;
        }

        $ts       = $this->mSettings->thousands_sep == '0' ? ' ' : $this->mSettings->thousands_sep;
        $ds       = $this->mSettings->decimals_sep;

        return number_format($number, $decimals, $ds, $ts);
    }
     public function formatDecimal($number, $decimals = null)
    {
        if (!is_numeric($number)) {
            return null;
        }
        if (!$decimals) {
            $decimals = 2;
        }

        return number_format($number, $decimals, '.', '');
    }


    public function clear_tags($str)
    {
        return htmlentities(
            strip_tags($str,
                '<span><div><a><br><p><b><i><u><img><blockquote><small><ul><ol><li><hr><big><pre><code><strong><em><table><tr><td><th><tbody><thead><tfoot><h3><h4><h5><h6>'
            ),
            ENT_QUOTES | ENT_XHTML | ENT_HTML5,
            'UTF-8'
        );
    }

    public function decode_html($str)
    {
        return html_entity_decode($str, ENT_QUOTES | ENT_XHTML | ENT_HTML5, 'UTF-8');
    }

    public function roundMoney($num, $nearest = 0.05)
    {
        return round($num * (1 / $nearest)) * $nearest;
    }

    public function roundNumber($number, $toref = null)
    {
        switch ($toref) {
            case 1:
                $rn = round($number * 20) / 20;
                break;
            case 2:
                $rn = round($number * 2) / 2;
                break;
            case 3:
                $rn = round($number);
                break;
            case 4:
                $rn = ceil($number);
                break;
            default:
                $rn = $number;
        }
        return $rn;
    }

    public function unset_data($ud)
    {
        if ($this->session->userdata($ud)) {
            $this->session->unset_userdata($ud);
            return true;
        }
        return false;
    }
   
    public function hrsd($sdate)
    {
        if ($sdate) {
            return date($this->dateFormats['php_sdate'], strtotime($sdate));
        } else {
            return '0000-00-00';
        }
    }

    public function hrld($ldate)
    {
        if ($ldate) {
            return date($this->dateFormats['php_ldate'], strtotime($ldate));
        } else {
            return '0000-00-00 00:00:00';
        }
    }

    public function fsd($inv_date)
    {
        if ($inv_date) {
            $jsd = $this->dateFormats['js_sdate'];
            if ($jsd == 'dd-mm-yyyy' || $jsd == 'dd/mm/yyyy' || $jsd == 'dd.mm.yyyy') {
                $date = substr($inv_date, -4) . "-" . substr($inv_date, 3, 2) . "-" . substr($inv_date, 0, 2);
            } elseif ($jsd == 'mm-dd-yyyy' || $jsd == 'mm/dd/yyyy' || $jsd == 'mm.dd.yyyy') {
                $date = substr($inv_date, -4) . "-" . substr($inv_date, 0, 2) . "-" . substr($inv_date, 3, 2);
            } else {
                $date = $inv_date;
            }
            return $date;
        } else {
            return '0000-00-00';
        }
    }

    public function fld($ldate)
    {
        if ($ldate) {
            $date = explode(' ', $ldate);
            $jsd = $this->dateFormats['js_sdate'];
            $inv_date = $date[0];
            $time = $date[1];
            if ($jsd == 'dd-mm-yyyy' || $jsd == 'dd/mm/yyyy' || $jsd == 'dd.mm.yyyy') {
                $date = substr($inv_date, -4) . "-" . substr($inv_date, 3, 2) . "-" . substr($inv_date, 0, 2) . " " . $time;
            } elseif ($jsd == 'mm-dd-yyyy' || $jsd == 'mm/dd/yyyy' || $jsd == 'mm.dd.yyyy') {
                $date = substr($inv_date, -4) . "-" . substr($inv_date, 0, 2) . "-" . substr($inv_date, 3, 2) . " " . $time;
            } else {
                $date = $inv_date;
            }
            return $date;
        } else {
            return '0000-00-00 00:00:00';
        }
    }


    public function md($page = false)
    {
        die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . ($page ? site_url($page) : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'welcome')) . "'; }, 10);</script>");
    }
    public function mdytoymd($date)
    {
        if ($date) {
            $date = explode('-', $date);
            $month = $date[0];
            $day = $date[1];
            $year = $date[2];
            return $year.'-'.$month.'-'.$day;
        } else {
            return '0000-00-00 00:00:00';
        }
    }
    public function mdytoymd2($date)
    {
        if ($date) {
            $datetime = explode(' ', $date);
            $date = $datetime[0];
            $time = $datetime[1];

            $date = explode('-', $date);
            $month = $date[0];
            $day = $date[1];
            $year = $date[2];
            return $year.'-'.$month.'-'.$day .' '.$time;
        } else {
            return '0000-00-00 00:00:00';
        }
    }
   

    public function send_email($to, $subject, $message, $from = null, $from_name = null, $attachment = null, $cc = null, $bcc = null)
    {
        // list($user, $domain) = explode('@', $to);
        $this->load->library('wf_mail');
        return $this->wf_mail->send_mail($to, $subject, $message, $from, $from_name, $attachment, $cc, $bcc);
    }


  
    public function imgto64($file_name){
        $bc = file_get_contents($file_name);
        $bcimage = base64_encode($bc);
        return $bcimage;
    }


    public function barcode($text = null, $bcs = 'code128', $height = 74, $stext = 1, $get_be = false, $re = false)
    {
        $drawText = ($stext != 1) ? false : true;
        $this->load->library('wf_barcode', '', 'bc');
        return $this->bc->generate($text, $bcs, $height, $drawText, $get_be, $re);
    }
    
    public function qrcode($type = 'text', $text = 'http://otsglobal.org', $size = 80, $level = 'H', $sq = null)
    {
        $size = 90;
        $file_name = 'assets/uploads/qrcode/' . $this->session->userdata('user_id') . ($sq ? $sq : '') . '.png' ;
        if ($type == 'link') {
            $text = urldecode($text);
        }
        $this->load->library('wf_qrcode', '', 'qr');
        $config = array('data' => $text, 'size' => $size, 'level' => $level, 'savename' => $file_name);
        $this->qr->generate($config);
        $imagedata = file_get_contents($file_name);
        return "<img src='data:image/png;base64,".base64_encode($imagedata)."' alt='{$text}' class='qrimg' />";
    }
    public function generate_pdf($content, $name = 'download.pdf', $output_type = null, $footer = null, $margin_bottom = null, $header = null, $margin_top = null, $orientation = 'P')
    {

        $this->load->library('wf_mpdf', '', 'pdf');
        return $this->pdf->generate($content, $name, $output_type, $footer, $margin_bottom, $header, $margin_top, $orientation);
    }

    public function checkPermissions($action = null, $js = null, $module = null)
    {
        if (!$this->actionPermissions($action, $module)) {
            $this->session->set_flashdata('error', ("Access Denied! You don't have right to access the requested page. If you think it's by mistake, please contact administrator."));
            if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('panel')) . "'; }, 10);</script>");
            } else {
                redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'panel');
            }
        }
    }

    public function actionPermissions($action = null, $module = null)
    {

        if ($this->Admin) {

            return true;
        } else {
            if (!$module) {
                $module = $this->mCtrler;
            }
            if (!$action) {
                $action = $this->mAction;
            }

            if ($this->Admin || $this->GP[$module . '-' . $action] == 1) {
                return true;
            } else {
                return false;
            }
        }

    }
     public function logged_in()
    {
        return (bool) $this->session->userdata('identity');
    }   
    public function in_group($check_group, $id = false)
    {
        if (!$this->logged_in()) {
            return false;
        }
        $id || $id = $this->session->userdata('user_id');
        $group     = $this->settings_model->getUserGroup($id);
        if ($group->name === $check_group) {
            return true;
        }
        return false;
    }

    public function send_json($data)
    {
        header('Content-Type: application/json');
        die(json_encode($data));
        exit;
    }

    function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    function getUsedStatus() {
        $used_status = array(
            '1' => lang('Ready to Sale'),
            '2' => lang('Needs Repair'),
            '3' => lang('On Hold'),
            '4' => lang('Sold'),
            '5' => lang('Lost/Damaged'),
        ); 
        return $used_status;
    }


    function getUnlockStatus() {
        return array(
            '0' => lang('no'),
            '1' => lang('yes'),
        ); 
    }


     public function getReference($field) {
        
        $q = $this->db->get_where('order_ref', array('ref_id' => '1'), 1);
        if ($q->num_rows() > 0) {
            $ref = $q->row();
            switch ($field) {
                case 'pos':
                    $prefix = isset($this->mSettings->sales_prefix) ? $this->mSettings->sales_prefix . '/POS/' : '';
                    break;
                case 'pay':
                    $prefix = $this->mSettings->payment_prefix;
                    break;
                case 're':
                    $prefix = $this->mSettings->return_prefix;
                    break;
                case 'purchase':
                    $prefix = $this->mSettings->purchase_prefix ?? 'P';
                    break;
                case 'po':
                    $prefix = $this->mSettings->purchase_prefix ?? 'P';
                    break;
                case 'repair':
                    $prefix = $this->mSettings->repair_prefix ?? 'REPAIR';
                    break;
                default:
                    $prefix = '';
            }

            $ref_no = (!empty($prefix)) ? $prefix . '/' : '';
            
            if($field == 'repair' && $this->mSettings->store_wise_reference) {
                // get active store. and get from the store. 
                $number = $this->activeStoreData->repair_ref;
            }else{
                $number = $ref->{$field};
            }

            if ($this->mSettings->reference_format == 1) {
                $ref_no .= date('Y') . '/' . sprintf('%04s', $number);
            } elseif ($this->mSettings->reference_format == 2) {
                $ref_no .= date('Y') . '/' . date('m') . '/' . sprintf('%04s', $number);
            } elseif ($this->mSettings->reference_format == 3) {
                $ref_no .= sprintf('%04s', $number);
            } else {
                $ref_no .= $this->getRandomReference();
            }


            
            return $ref_no;
        }
        return FALSE;
    }


    public function getRandomReference($len = 12)
    {
        $result = '';
        for ($i = 0; $i < $len; $i++) {
            $result .= mt_rand(0, 9);
        }

        if ($this->getSaleByReference($result)) {
            $this->getRandomReference();
        }

        return $result;
    }

    public function getSaleByReference($ref)
    {
        $this->db->like('reference_no', $ref, 'both');
        $q = $this->db->get('sales', 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


    public function updateReference($field) {
        $q = $this->db->get_where('order_ref', array('ref_id' => '1'), 1);
        if ($q->num_rows() > 0) {
            $ref = $q->row();

            if($field == 'repair' && $this->mSettings->store_wise_reference) {
                // get active store. and get from the store. 
                $this->db->update('store', array('repair_ref' => (int)$this->activeStoreData->repair_ref + 1), array('id' => $this->activeStoreData->id));
            }else{
                $this->db->update('order_ref', array($field => (int)$ref->{$field} + 1), array('ref_id' => '1'));
            }

            
            return TRUE;
        }
        return FALSE;
    }


    public function paid_opts($paid_by = null, $empty_opt = '')
    {
        $opts = '';
        if ($empty_opt) {
            $opts .= '<option value="">'.lang('select').'</option>';
        }
        $opts .= '
        <option value="cash"'.($paid_by && $paid_by == 'cash' ? ' selected="selected"' : '').'>'.lang("cash").'</option>
        <option value="CC"'.($paid_by && $paid_by == 'CC' ? ' selected="selected"' : '').'>'.lang("CC").'</option>
        <option value="Cheque"'.($paid_by && $paid_by == 'Cheque' ? ' selected="selected"' : '').'>'.lang("cheque").'</option>
        <option value="other"'.($paid_by && $paid_by == 'other' ? ' selected="selected"' : '').'>'.lang("other").'</option>';
        return $opts;
    }

    
    public function returnOpenRegisterSets() {
          return array(
            'n001' => '0.01',
            'n002' => '0.02',
            'n005' => '0.05',
            'n010' => '0.1',
            'n020' => '0.2',
            'n050' => '0.5',
            'n1' => '1',
            'n2' => '2',
            'n5' => '5',
            'n10' => '10',
            'n20' => '20',
            'n50' => '50',
            'n100' => '100',
            'n200' => '200',
            'n500' => '500',
        );
    }
    
    public function returnShippingMethods() {
        return $dp_p = array(
          '' =>  lang('select_placeholder'),
          'usps' => 'USPS',
          'ups' => 'UPS',
          'fedex' => 'FEDEX',
          'dhl' => 'DHL',
          'other' => 'Other',
      );
    }
    
    function base30_to_jpeg($base30_string, $output_file) {
        require APPPATH.'libraries/jSignature.php';
        $data = str_replace('image/jsignature;base30,', '', $base30_string);
        $converter = new jSignature();
        $raw = $converter->Base64ToNative($data);
        //Calculate dimensions
        $width = 0;
        $height = 0;
        foreach($raw as $line) {
            if (max($line['x']) > $width) $width = max($line['x']);
            if (max($line['y']) > $height) $height = max($line['y']);
        }

        // Create an image
        $im = imagecreatetruecolor($width+20,$height+20);
        // Save transparency for PNG
        imagesavealpha($im, true);
        // Fill background with transparency
        $trans_colour = imagecolorallocatealpha($im, 255, 255, 255, 127);
        imagefill($im, 0, 0, $trans_colour);
        // Set pen thickness
        imagesetthickness($im, 2);
        // Set pen color to black
        $black = imagecolorallocate($im, 0, 0, 0);
        // Loop through array pairs from each signature word
        for ($i = 0; $i < count($raw); $i++)
        {
            // Loop through each pair in a word
            for ($j = 0; $j < count($raw[$i]['x']); $j++)
            {
                // Make sure we are not on the last coordinate in the array
                if ( ! isset($raw[$i]['x'][$j]))
                    break;
                if ( ! isset($raw[$i]['x'][$j+1]))
                // Draw the dot for the coordinate
                    imagesetpixel ( $im, $raw[$i]['x'][$j], $raw[$i]['y'][$j], $black);
                else
                // Draw the line for the coordinate pair
                imageline($im, $raw[$i]['x'][$j], $raw[$i]['y'][$j], $raw[$i]['x'][$j+1], $raw[$i]['y'][$j+1], $black);
            }
        }

        //Create Image
        $ifp = fopen($output_file, "wb");
        imagepng($im, $output_file);
        fclose($ifp);
        imagedestroy($im);
        return true;
    }
    
    
    public function get_cal_lang()
    {
        switch ($this->mSettings->language) {
            case 'french':
                $cal_lang = 'fr';
                break;
            case 'german':
                $cal_lang = 'de';
                break;
            case 'italian':
                $cal_lang = 'it';
                break;
            case 'dutch':
                $cal_lang = 'nl';
                break;
            case 'bulgarian':
                $cal_lang = 'bg';
                break;
            case 'russian':
                $cal_lang = 'ru';
                break;
            default:
                $cal_lang = 'en';
                break;
        }
        return $cal_lang;
    }   

    public function get_parseley_lang()
    {
        switch ($this->mSettings->language) {
            case 'french':
                $cal_lang = 'fr';
                break;
            case 'german':
                $cal_lang = 'de';
                break;
            case 'italian':
                $cal_lang = 'it';
                break;
            case 'dutch':
                $cal_lang = 'nl';
                break;
            case 'bulgarian':
                $cal_lang = 'bg';
                break;
            case 'russian':
                $cal_lang = 'ru';
                break;
            default:
                $cal_lang = 'en';
                break;
        }
        return $cal_lang;
    }   


    public function updateDefectsTable()
    {
        $defects = $this->settings_model->getAllRepairDefects();
        foreach ($defects as $defect) {
            if (!$this->settings_model->checkIfDefectExists($defect->defect)) {
                $this->db->insert('defects', ['name'=>$defect->defect]);
                $insert_id = $this->db->insert_id();
                $this->db->where_in('id', explode(',', $defect->ids))->update('repair', ['defect_id'=>$insert_id]);
            }
        }
    } 

    // public function updateModelsTable()
    // {
    //     $models = $this->settings_model->getAllRepairModels();
    //     foreach ($models as $model) {
    //         if (!$this->settings_model->checkIfDefectExists($defect->defect)) {
    //             $this->db->insert('defects', ['name'=>$defect->defect]);
    //             $insert_id = $this->db->insert_id();
    //             $this->db->where_in('id', explode(',', $defect->ids))->update('repair', ['defect_id'=>$insert_id]);
    //         }
    //     }
    // } 
}
