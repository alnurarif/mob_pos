<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Sales extends Auth_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('pos_model');
        $this->load->library('repairer');
    }
    public function return_sales()
    {
        $this->repairer->checkPermissions();
        $this->render('sales/return_sales');
    }
    function getReturns($pdf = NULL, $xls = NULL)
    {
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
        
        if ($pdf || $xls) {
            $this->db->where('(sales.store_id = '.(int)$this->session->userdata('active_store').' OR sales.real_store_id = '.(int)$this->session->userdata('active_store').')', NULL, FALSE);

            $this->db
                ->select("date, return_sale_ref, reference_no, biller, customer, surcharge, grand_total")
                ->from('sales');
            $this->db->where('sale_status', 'returned');
            if ($start_date) {
                $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();


                $sheet->setTitle(lang('Sales Report'));
                $sheet->SetCellValue('A2', lang("date"));
                $sheet->SetCellValue('B2', lang("Return Sale Reference"));
                $sheet->SetCellValue('C2', lang('reference_no'));
                $sheet->SetCellValue('D2', lang('Biller'));
                $sheet->SetCellValue('E2', lang('Customer'));
                $sheet->SetCellValue('F2', lang('Surchage'));
                $sheet->SetCellValue('G2', lang('grand_total'));
                $sheet->SetCellValue('A1', sprintf(lang('refund_report_from_to'), date('m-d-Y H:i:s', strtotime($start_date)), date('m-d-Y H:i:s', strtotime($end_date))));
                $sheet->mergeCells('A1:G1');
                $row = 3;
                $surcharge = 0; $total=0;
                foreach ($data as $data_row) {
                    $ir = $row + 1;
                    if ($ir % 2 == 0) {
                        $style_header = array(                  
                            'fill' => array(
                                'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => array('rgb'=>'CCCCCC'),
                            ),
                        );
                        $sheet->getStyle("A$row:G$row")->applyFromArray( $style_header );
                    }

                    $sheet->SetCellValue('A' . $row, $this->repairer->hrld($data_row->date));
                    $sheet->SetCellValue('B' . $row, $data_row->return_sale_ref);
                    $sheet->SetCellValue('C' . $row, $data_row->reference_no);
                    $sheet->SetCellValue('D' . $row, $data_row->biller);
                    $sheet->SetCellValue('E' . $row, $data_row->customer);
                    $sheet->SetCellValue('F' . $row, $data_row->surcharge);
                    $sheet->SetCellValue('G' . $row, $data_row->grand_total);
                    $surcharge += $data_row->surcharge;
                    $total += abs($data_row->grand_total);
                    $row++;
                }
                 $style_header = array(      
                    'fill' => array(
                        'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => array('rgb'=>'fdbf2d'),
                    ),
                );
                $sheet->getStyle("A$row:G$row")->applyFromArray( $style_header );
                $sheet->SetCellValue('F' . $row, $surcharge);
                $sheet->SetCellValue('G' . $row, 0-$total);


                $sheet->getColumnDimension('A')->setWidth(25);
                $sheet->getColumnDimension('B')->setWidth(25);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(25);
                $sheet->getColumnDimension('E')->setWidth(30);
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(15);
              
                $filename = 'returned_sales';


                $sheet->getParent()->getDefaultStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('F2:F' . ($row))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getStyle('G2:G' . ($row))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                
                $header = 'A1:G1';
                $sheet->getStyle($header)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('94ce58');
                $style = array(
                    'font' => array('bold' => true,),
                    'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,),
                );
                $sheet->getStyle($header)->applyFromArray($style);
                

                $header = 'A2:G2';
                $sheet->getStyle($header)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('fdbf2d');
                $style = array(
                    'font' => array('bold' => true,),
                    'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_GENERAL,),
                );
                $sheet->getStyle($header)->applyFromArray($style);


                if ($pdf) {
                    $styleArray = [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                'color' => ['argb' => 'FFFF0000'],
                            ],
                        ],
                    ];
                    $sheet->getStyle('A0:G'.($row))->applyFromArray($styleArray);
                    $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                    header('Content-Type: application/pdf');
                    header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
                    header('Cache-Control: max-age=0');
                    $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Mpdf');
                    $writer->save('php://output');
                }
                if ($xls) {
                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
                    header('Cache-Control: max-age=0');



                    $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
                    $writer->save('php://output');
                    exit();
                }
            }
        } else {
            $this->load->library('datatables');
            $detail_link = anchor('panel/sales/modal_view/$1', '<i class="fas fa-file-text-o"></i> ' . "Sale Details", 'data-toggle="modal" data-target="#myModal"');
            $action = '<div class="text-center"><div class="btn-group text-left">'
                . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
                . lang('actions') . ' <span class="caret"></span></button>
                <ul class="dropdown-menu pull-right" role="menu">
                    <li>' . $detail_link . '</li>
                </ul>
            </div></div>';
            $this->datatables->where('(sales.store_id = '.(int)$this->session->userdata('active_store').' OR sales.real_store_id = '.(int)$this->session->userdata('active_store').')', NULL, FALSE);

            if ($start_date) {
                $this->datatables->where('sales.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $this->datatables
                ->select("date, return_sale_ref, reference_no, biller, customer, surcharge, grand_total, id")
                ->from('sales');
            $this->datatables->where('sale_status', 'returned');
            $this->datatables->add_column("Actions", $action, "id");
            echo $this->datatables->generate();
        }

    }
    public function modal_view($id = null)
    {

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->pos_model->getInvoiceByID($id);
        
        $this->data['settings'] = ($this->mSettings);
        $this->data['customer'] = ($inv->customer);
        $this->data['biller'] = ($inv->biller);
        $this->data['created_by'] = ($inv->created_by);
        $this->data['inv'] = $inv;
        $this->data['rows'] = $this->pos_model->getAllInvoiceItems($id);
        $this->data['return_sale'] = $inv->return_id ? $this->pos_model->getInvoiceByID($inv->return_id) : NULL;
        $this->data['return_rows'] = $inv->return_id ? $this->pos_model->getAllInvoiceItems($inv->return_id) : NULL;

        $this->load->view($this->theme.'sales/modal_view', $this->data);
    }



    public function refund($id = NULL) {
        $this->repairer->checkPermissions();

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $return_items = NULL;
        $refund_items = array();
        $sale = $this->pos_model->getInvoiceByID($id);
        $inv_items = $this->pos_model->getAllInvoiceItems($id);
        if ($sale->return_id) {
            $returned_sales = $this->db->get_where('sales', array('sale_id' => $sale->id, 'sale_status' => 'returned', 'store_id'=>$this->activeStore));
            if ($returned_sales->num_rows() > 0) {
                $returned_sales = $returned_sales->result();
                foreach ($returned_sales as $sale) {
                    $return_items = $this->db->get_where('sale_items', array('sale_id'=>$sale->id));
                    if ($return_items->num_rows() > 0) {
                        foreach ($return_items->result() as $item) {
                            $refund_items[] = $item;
                        }
                    }
                }
            }
        }

        if ($refund_items && $inv_items) {
            foreach ($inv_items as $key => $value) {
                foreach ($refund_items as $rkey => $rvalue) {
                    if (($value->id == $rvalue->sale_item_id) && $rvalue->refund_item) {
                        unset($inv_items[$key]);
                    }
                }
            }
        }

        if ($sale->return_id) {
            $this->session->set_flashdata('error', lang('Some Items of the Sale Already Returned. Which are not displayed here'));
        }
        
        $inv_items = array_filter($inv_items);
        if (empty($inv_items)) {
            $this->session->set_flashdata('error', lang('The Sale Items are already returned'));
            redirect('panel/sales/return_sales');
        }
        
        //////////////////////////////////////////////////////////////////////////////////////////

        $this->data['inv'] = $sale;
        $r = rand(100000, 9999999);

        foreach ($inv_items as $row) {
            if ($row->taxable) {
                $o_taxes = json_decode($row->tax_rate);
            }else{
                $o_taxes = NULL;
            }
            if ($row->item_type == 'crepairs' or $row->item_type == 'drepairs') {
                $item_id = $row->item_type.$row->id;
                $row_id = $row->item_type.$row->id;
            }else{
                $item_id = $row->item_type.($r);
                $row_id = $row->item_type.time();
            }
            $row->sale_item_id = $row->id;

            $pr['refund_'.$r.time().$row_id] = array(
                'row_id' => 'refund_'.$r.time().$row_id,
                'item_id' => $item_id,
                'label' => $row->product_name . " (" . $row->product_code . ")", 
                'code' => $row->product_code, 
                'name' => $row->product_name, 
                'price' => $row->unit_price, 
                'qty' => $row->quantity, 
                'type' => $row->item_type, 
                'cost'=>$row->unit_cost, 
                'product_id'=>$row->product_id,
                'taxable'=>$row->taxable,
                'pr_tax' => $o_taxes,
                'row' => $row,
                'discount' => 0,
                'serial_number' => $row->serial_number,
                'variants' => FALSE,
                'option_selected' => FALSE,
                'options' => FALSE,
                'option' => NULL,
                'is_serialized' => FALSE,
                'serialed' => FALSE,
                'refund_item' => TRUE,
                'add_to_stock' => 0,
                'items_restock' => 0,
            );
            $r++;
        }

        $this->data['inv_items'] = json_encode($pr);
        $this->data['id'] = $id;
        $this->data['payment_ref'] = '';
        $this->data['reference'] = '';
        $this->data['tax_rates'] = $this->settings_model->getTaxRates();
        $this->data['refund_id'] = $id;
        $this->render('sales/refund');
    }

    // Sale Payments
    public function payments($id = null) {
        $this->data['payments'] = $this->pos_model->getInvoicePayments($id);
        $this->data['inv'] = $this->pos_model->getInvoiceByID($id);
        $this->load->view($this->theme.'/sales/payments', $this->data);
    }

    public function delete_payment($id = null) {
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->pos_model->deletePayment($id)) {
            $this->session->set_flashdata('message', lang("payment_deleted"));
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    public function add_payment($id = NULL) {
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $sale = $this->pos_model->getInvoiceByID($id);
        if ($sale->payment_status == 'paid' && $sale->grand_total == $sale->paid) {
            $this->session->set_flashdata('error', lang("sale_already_paid"));
            $this->repairer->md();
        }

        $this->form_validation->set_rules('amount-paid', lang("amount"), 'required');
        $this->form_validation->set_rules('paid_by', lang("paid_by"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');
        if ($this->form_validation->run() == TRUE) {
            $date = date('Y-m-d H:i:s');
            $payment = array(
                'date'         => $date,
                'sale_id'      => $this->input->post('sale_id'),
                'reference_no' => $this->repairer->getReference('pay'),
                'amount'       => $this->input->post('amount-paid'),
                'paid_by'      => $this->input->post('paid_by'),
                'cheque_no'    => $this->input->post('cheque_no'),
                'cc_no'        => $this->input->post('paid_by') == 'gift_card' ? $this->input->post('gift_card_no') : $this->input->post('pcc_no'),
                'cc_holder'    => $this->input->post('pcc_holder'),
                'cc_month'     => $this->input->post('pcc_month'),
                'cc_year'      => $this->input->post('pcc_year'),
                'cc_type'      => $this->input->post('pcc_type'),
                'cc_cvv2'      => $this->input->post('pcc_ccv'),
                'note'         => $this->input->post('note'),
                'created_by'   => $this->session->userdata('user_id'),
                'type'         => 'received',
            );

        } elseif ($this->input->post('add_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->form_validation->run() == TRUE && $msg = $this->pos_model->addPayment($payment)) {
            if ($msg) {
                $this->session->set_flashdata('message', lang("payment_added"));
            } else {
                $this->session->set_flashdata('error', lang("payment_failed"));
            }
            redirect("panel/reports/sales");
        } else {
            if ($sale->sale_status == 'returned' && $sale->paid == $sale->grand_total) {
                $this->session->set_flashdata('warning', lang('payment_was_returned'));
                $this->repairer->md();
            }

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $sale = $this->pos_model->getInvoiceByID($id);
            $this->data['inv'] = $sale;
            $this->data['payment_ref'] = $this->repairer->getReference('pay');
            $this->load->view($this->theme.'/sales/add_payment', $this->data);
        }
    }


    public function edit_payment($id = null, $sale_id = null)
    {
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $payment = $this->pos_model->getPaymentByID($id);

        
      
        $this->form_validation->set_rules('amount-paid', lang("amount"), 'required');
        $this->form_validation->set_rules('paid_by', lang("paid_by"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');

        if ($this->form_validation->run() == true) {
            $date = date('Y-m-d H:i:s');

            $payment = array(
                'date' => $date,
                'sale_id' => $this->input->post('sale_id'),
                'reference_no' => $this->repairer->getReference('pay'),
                'amount' => $this->input->post('amount-paid'),
                'paid_by' => $this->input->post('paid_by'),
                'cheque_no' => $this->input->post('cheque_no'),
                'cc_no'        => $this->input->post('paid_by') == 'gift_card' ? $this->input->post('gift_card_no') : $this->input->post('pcc_no'),
                'cc_holder' => $this->input->post('pcc_holder'),
                'cc_month' => $this->input->post('pcc_month'),
                'cc_year' => $this->input->post('pcc_year'),
                'cc_type' => $this->input->post('pcc_type'),
                'note'         => $this->input->post('note'),
                'created_by' => $this->session->userdata('user_id'),
            );


        } elseif ($this->input->post('edit_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->form_validation->run() == true && $this->pos_model->updatePayment($id, $payment)) {
            $this->session->set_flashdata('message', lang("payment_updated"));
            redirect("panel/reports/sales");
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['payment'] = $payment;
            $this->load->view($this->theme . '/sales/edit_payment', $this->data);
        }
    }


    public function payment_note($id = null)
    {
        $payment = $this->pos_model->getPaymentByID($id);
        $inv = $this->pos_model->getInvoiceByID($payment->sale_id);
        $this->data['customer'] = $this->pos_model->getCustomerByID($inv->customer_id);
        $this->data['inv'] = $inv;
        $this->data['payment'] = $payment;
        $this->data['settings'] = $this->mSettings;

        $this->data['page_title'] = lang("payment_note");
        $this->load->view($this->theme . '/sales/payment_note', $this->data);
    }


    public function email_payment($id = null)
    {
        $payment = $this->pos_model->getPaymentByID($id);
        $inv = $this->pos_model->getInvoiceByID($payment->sale_id);
        $customer = $this->pos_model->getCustomerByID($inv->customer_id);
        if (!$customer) {
            $this->repairer->send_json(array('msg' => lang("customer_not_found")));die();
        }
        if (!$customer->email) {
            $this->repairer->send_json(array('msg' => lang("update_customer_email")));
        }
        $this->data['inv'] = $inv;
        $this->data['payment'] = $payment;
        $this->data['customer'] =$customer;
        $this->data['page_title'] = lang("payment_note");
        $this->data['settings'] = $this->mSettings;
        $html = $this->load->view($this->theme . '/sales/payment_note', $this->data, TRUE);

        $html = str_replace(array('<i class="fa fa-2x">&times;</i>', 'modal-', '<p>&nbsp;</p>', '<p style="border-bottom: 1px solid #666;">&nbsp;</p>', '<p>'.lang("stamp_sign").'</p>'), '', $html);
        $html = preg_replace("/<img[^>]+\>/i", '', $html);

        $this->load->library('parser');
        $parse_data = array(
            'stylesheet' => '<link rel="stylesheet" href="'.$this->assets.'assets/vendor/bootstrap/css/bootstrap.css" />',
            'name' => $customer->company && $customer->company != '-' ? $customer->company :  $customer->name,
            'email' => $customer->email,
            'heading' => lang('payment_note').'<hr>',
            'msg' => $html,
            'site_link' => base_url(),
            'site_name' => $this->mSettings->title,
            'logo' => '<img src="' . base_url('assets/uploads/logos/' . $this->mSettings->logo) . '" alt="' . $this->mSettings->title . '"/>'
        );
        $msg = file_get_contents(FCPATH. 'themes/' .$this->theme.'email_templates/email_con.html');
        $message = $this->parser->parse_string($msg, $parse_data);
        $subject = lang('payment_note') . ' - ' . $this->mSettings->title;


        if ($this->repairer->send_email($customer->email, $subject, $message)) {
            $this->repairer->send_json(array('msg' => lang("email_sent")));
        } else {
            $this->repairer->send_json(array('msg' => lang("email_failed")));
        }
    }
}