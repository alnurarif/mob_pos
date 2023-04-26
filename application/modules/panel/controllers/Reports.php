<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends Auth_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('repairer');
        $this->load->model('reports_model');
    }

    function stock()
    {
        $this->repairer->checkPermissions();
        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['stock'] = $this->reports_model->getStockValue();
        $this->data['totals'] = $this->reports_model->getStockTotals();
        $this->data['cat_filter'] = $this->settings_model->getCategoriesTree();

        $selected = explode(',', (string)$this->input->get('types'));
        if ($this->input->get('types')) {
            $repair = in_array('repair_parts', $selected) ? $this->getRepairPartAlerts(false) : array();
            $phones = in_array('new_phones', $selected) ? $this->getNewPhones(false) : array();
            $acc    = in_array('accessories', $selected) ? $this->getAccessoryAlerts(false) : array();
            $other  = in_array('others', $selected) ? $this->getOtherAlerts(false) : array();
            $used  = in_array('used_phones', $selected) ? $this->getUsedPhones(false) : array();
        }else{
            $repair = $this->getRepairPartAlerts(false);
            $phones = $this->getNewPhones(false);
            $acc = $this->getAccessoryAlerts(false);
            $used  = $this->getUsedPhones(false);
            $other  = $this->getOtherAlerts(false);
        }
        
        $this->data['selected'] = $selected;
        $this->data['records'] = array_merge($acc, $other, $phones, $repair, $used);


        $this->render('reports/stock_chart');

    }

    public function finance()
    {
        $start = date("Y-m-d",strtotime("-1 month"));
        if ($this->input->get('start')) {
            $start = $this->repairer->fsd($this->input->get('start'));
        }

        
        $end = date('Y-m-d 23:59:59');
        if ($this->input->get('end')) {
            $end = $this->repairer->fsd($this->input->get('end'));
        }

        $created_by = null;
        if ($this->input->get('created_by')) {
            $created_by = $this->input->get('created_by');
        }

        $sales_count = $this->reports_model->getTotalSales($start,$end, $created_by);
        $sale_items_count = $this->reports_model->getTotalSalesItems($start,$end, $created_by);
        $sale_gross_totals = $this->reports_model->getTotalSalesGTotal($start,$end, $created_by);
        $sale_gross_profit = $this->reports_model->getTotalSalesProfit($start,$end, $created_by);
        $this->data['reports_data'] = [
            'order_item_counts' => $sale_items_count[0],
            'order_counts' => $sales_count[0],
            'gross_order_amounts' => $sale_gross_totals[0],
            'gross_order_amounts_total' => $sale_gross_totals[1],
            'order_item_counts_total' => $sale_items_count[1],
            'order_counts_total' => $sales_count[1],
            'profit_amounts' => $sale_gross_profit[0],
            'profit_amounts_total' => $sale_gross_profit[1],

        ];
        $this->data['users'] = $this->db->where('active', 1)->where('hidden', 0)->get('users')->result();
        
        $this->render('reports/finance_new');
    }


    public function finance_old($month = NULL, $year = NULL)
    {
        $this->repairer->checkPermissions();

        $this->data['currency'] = $this->mSettings->currency;
        $this->data['settings'] = $this->mSettings;
        
        if (isset($month) && isset($year)) {
            $this->data['list'] = $this->reports_model->list_earnings($month, $year);
        } else {
            $month = date('m');
            $this->data['list'] = $this->reports_model->list_earnings(date('m'), date('Y'));
        }
        $this->data['month'] = $month;
        $this->render('reports/finance');
    }

    function sales()
    {
        $this->repairer->checkPermissions();
        $this->data['users'] = $this->db->where('active', 1)->where('hidden', 0)->get('users')->result();

        $this->render('reports/sales');
    }
    
   
    function getAllSales($pdf = NULL, $xls = NULL)
    {
        if ($this->input->get('start_date')) {
            $start_date = ($this->input->get('start_date')) . " 00:00:00";
        } else {
            $start_date = null;
        }
        if ($this->input->get('end_date')) {
            $end_date = ($this->input->get('end_date')) . " 23:59:59";
        } else {
            $end_date = null;
        }

        if ($this->input->get('created_by')) {
            $created_by = ($this->input->get('created_by'));
        }else{
            $created_by = null;
        }

        if ($this->input->get('payment_type')) {
            $payment_type = ($this->input->get('payment_type'));
        }else{
            $payment_type = null;
        }


        if ($pdf || $xls) {
            $this->db->select("sales.id as id,LPAD(sales.id, 4, '0') as sale_id, date, customer, (SELECT 
                    CASE
                        WHEN item_type = 'crepairs' THEN GROUP_CONCAT(CONCAT(product_name, '(','item_details', ')',' (".lang('Deposit').")'))
                        WHEN item_type = 'drepairs' THEN GROUP_CONCAT(CONCAT(product_name, '(','item_details', ')',' (".lang('Repair Pickup').")'))
                        WHEN item_type IN ('new_phone', 'used_phone') THEN GROUP_CONCAT(CONCAT(product_name, '(','item_details', ')',' (".lang('Phone Sold').")'))
                        ELSE GROUP_CONCAT(product_name, '(','item_details', ')')
                    END
                FROM sale_items WHERE sale_items.sale_id = sales.id LIMIT 1 ) as name, TRUNCATE(grand_total-total_tax, 2) as total, TRUNCATE(total_tax, 2) as total_tax, TRUNCATE(grand_total, 2) as grand_total")
                ->from('sales')
                ->where('sale_status', 'completed')
                ->group_by('sales.id');
            $this->db->where('sales.store_id', $this->activeStore);
                
            if ($start_date) {
                $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }


            if ($created_by) {
                $this->db->where('biller_id', $created_by);
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $row->date = $this->repairer->hrld($row->date);
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $sheet->SetCellValue('A1', sprintf(lang('sales_report_from_to'), date('m-d-Y H:i:s', strtotime($start_date)),  date('m-d-Y H:i:s', strtotime($end_date))));
                $sheet->mergeCells('A1:G1');
                $sheet->setTitle(lang('Sales Report'));
                $sheet->SetCellValue('A2', lang('Sale ID'));
                $sheet->SetCellValue('B2', lang('date'));
                $sheet->SetCellValue('C2', lang('Customer'));
                $sheet->SetCellValue('D2', lang('Product Name'));
                $sheet->SetCellValue('E2', lang('Total'));
                $sheet->SetCellValue('F2', lang('Tax'));
                $sheet->SetCellValue('G2', lang('grand_total'));

                $row = 3;
                $ttotal = 0;
                $ttotal_tax = 0;
                $tgrand_total = 0;
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
                    $total = number_format($data_row->total, 2);
                    $total_tax = number_format($data_row->total_tax, 2);
                    $grand_total = number_format($data_row->grand_total, 2);
                    $ttotal += $total;
                    $ttotal_tax += $total_tax;
                    $tgrand_total += $grand_total;
                    $sheet->SetCellValue('A' . $row, ($data_row->sale_id));
                    $sheet->SetCellValue('B' . $row, $data_row->date);
                    $sheet->SetCellValue('C' . $row, $data_row->customer);
                    $sheet->SetCellValue('D' . $row, $data_row->name);
                    $sheet->SetCellValue('E' . $row, $total);
                    $sheet->SetCellValue('F' . $row, $total_tax);
                    $sheet->SetCellValue('G' . $row, $grand_total);
                    $row++;
                }
                 $style_header = array(      
                    'fill' => array(
                        'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => array('rgb'=>'fdbf2d'),
                    ),
                );
                $sheet->getStyle("A$row:G$row")->applyFromArray( $style_header );
                $sheet->SetCellValue('E' . $row, $ttotal);
                $sheet->SetCellValue('F' . $row, $ttotal_tax);
                $sheet->SetCellValue('G' . $row, $tgrand_total);

                $sheet->getColumnDimension('A')->setWidth(10);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(45);
                $sheet->getColumnDimension('E')->setWidth(15);
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(15);
               
                
                $filename = 'sales_report';
                $sheet->getParent()->getDefaultStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('E2:E' . ($row))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getStyle('F2:F' . ($row))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getStyle('G2:G' . ($row))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);


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


                $header = 'A'.$row.':G'.$row;
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

            if ($created_by) {
                $this->datatables->where('biller_id', $created_by);
            }
            if ($start_date ) {
                $this->datatables->where('sales.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            if ($payment_type ) {
                $this->datatables->where('paid_by', $payment_type);
            }


            $this->datatables
                ->select("sales.id as id,LPAD(sales.id, 4, '0') as sale_id,  sales.date, CONCAT(customer, IF(clients.telephone, CONCAT( '(', clients.telephone, ')' ),'' )) as customer, (SELECT 
                    CASE
                        WHEN item_type = 'crepairs' THEN GROUP_CONCAT(CONCAT(product_name, ' ',item_details, '' ,' (".lang('Deposit').")'))
                        WHEN item_type = 'drepairs' THEN GROUP_CONCAT(CONCAT(product_name, ' ',item_details, '' ,' (".lang('Repair Pickup').")'))
                        WHEN item_type IN ('new_phone', 'used_phone') THEN GROUP_CONCAT(CONCAT(product_name, ' ',item_details, '' ,' (".lang('Phone Sold').")'))
                        ELSE GROUP_CONCAT(product_name, ' ',item_details, '' )
						END
                FROM sale_items WHERE sale_items.sale_id = sales.id ) as name, (SELECT CONCAT(first_name,' ',last_name) FROM users WHERE sales.biller_id = users.id) as biller_id, (grand_total-total_tax) as total, total_tax, (grand_total), paid, payment_status,GROUP_CONCAT(payments.paid_by) as paid_by, CONCAT(warranties, '____', sales.date, '____', sales.id), (SELECT sale_items.product_id from sale_items where sales.id=sale_items.sale_id and sale_items.item_type = 'drepairs' LIMIT 1 ) as repair_id")
                ->join('payments', 'sales.id=payments.sale_id', 'left')
                ->join('clients', 'clients.id=sales.customer_id', 'left')
                ->from('sales')
                ->where('sale_status', 'completed')
                ->group_by('sales.id');

            $this->datatables->where('sales.store_id', $this->activeStore);


            
            $this->datatables->add_column('actions', '$1', 'getActionMenuSales(id, repair_id)');
            $this->datatables->unset_column('id');
            $this->datatables->unset_column('repair_id');
            echo $this->datatables->generate();
        }



    }

    
    function profit($start = null, $end = null)
    {
        $this->repairer->checkPermissions();
        
        $this->data['start'] = $start;
        $this->data['end'] = $end;
        $this->data['users'] = $this->db->where('active', 1)->where('hidden', 0)->get('users')->result();

        $this->render('reports/profit');
    }
    
    function getProfitReport($pdf = NULL, $xls = NULL)
    {
        if ($this->input->get('start_date')) {
            $start_date = ($this->input->get('start_date')) . " 00:00:00";
        } else {
            $start_date = null;
        }
        if ($this->input->get('end_date')) {
            $end_date = ($this->input->get('end_date')) . " 23:59:59";
        } else {
            $end_date = null;
        }

 if ($this->input->get('created_by')) {
            $created_by = ($this->input->get('created_by'));
        }else{
            $created_by = null;
        }

        if ($pdf || $xls) {
            if ($start_date ) {
                $this->db->where('sales.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

             if ($created_by) {
                $this->db->where('biller_id', $created_by);
            }


            $this->db->where('sales.store_id', $this->activeStore);
            $this->db
                ->select("LPAD(sales.id, 4, '0') as sale_id, date, customer, (SELECT 
                    CASE
                        WHEN item_type = 'crepairs' THEN GROUP_CONCAT(CONCAT(product_name, '(','item_details', ')',' (".lang('Deposit').")'))
                        WHEN item_type = 'drepairs' THEN GROUP_CONCAT(CONCAT(product_name, '(','item_details', ')',' (".lang('Reparation').")'))
                        WHEN item_type IN ('new_phone', 'used_phone') THEN GROUP_CONCAT(CONCAT(product_name, '(','item_details', ')',' (".lang('Phone Sold').")'))
                        ELSE GROUP_CONCAT(product_name, '(','item_details', ')')
                    END
                     FROM sale_items WHERE sale_items.sale_id = sales.id) as name, (grand_total-total_tax) as total, total_tax, grand_total, SUM(if(sales.sale_id IS NOT NULL, (0-(SELECT if(item_type IN ('cp', 'crepairs'), SUM(ABS(unit_price)), if(add_to_stock, SUM(ABS(unit_price)-unit_cost), SUM(ABS(unit_price))) )  FROM sale_items WHERE sale_items.sale_id=sales.id)) + sales.surcharge, (SELECT if(item_type IN ('cp', 'crepairs'), SUM(unit_price), SUM((unit_price)-unit_cost)) FROM sale_items WHERE sale_items.sale_id=sales.id AND store_id=".(int)$this->session->userdata('active_store').")) + (SELECT SUM(activation_spiff) FROM sale_items WHERE sale_items.sale_id=sales.id) ) as profit, sales.id as id")
                ->from('sales')
                ->group_by('sales.id');

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $row->date = $this->repairer->hrld($row->date);

                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $sheet->setTitle(lang('Profit Report'));
                $sheet->SetCellValue('A2', lang("Sale ID"));
                $sheet->SetCellValue('B2', lang("Date"));
                $sheet->SetCellValue('C2', lang('Customer'));
                $sheet->SetCellValue('D2', lang('Product Name'));
                $sheet->SetCellValue('E2', lang('Subtotal'));
                $sheet->SetCellValue('F2', lang('Tax'));
                $sheet->SetCellValue('G2', lang('Total'));
                $sheet->SetCellValue('H2', lang('Profit'));


                $sheet->SetCellValue('A1', sprintf(lang('profit_report_from_to'), date('m-d-Y H:i:s', strtotime($start_date)), date('m-d-Y H:i:s', strtotime($end_date))));
                $sheet->mergeCells('A1:H1');
                $row = 3;
                $total = 0;
                $total_tax = 0;
                $grand_total = 0;
                $profit = 0;
                foreach ($data as $data_row) {
                     $ir = $row + 1;
                    if ($ir % 2 == 0) {
                        $style_header = array(                  
                            'fill' => array(
                                'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => array('rgb'=>'CCCCCC'),
                            ),
                        );
                        $sheet->getStyle("A$row:H$row")->applyFromArray( $style_header );
                    }
                    $total += $data_row->total;
                    $total_tax += $data_row->total_tax;
                    $grand_total += $data_row->grand_total;
                    $profit += $data_row->profit;
                    $sheet->SetCellValue('A' . $row, ($data_row->sale_id));
                    $sheet->SetCellValue('B' . $row, $data_row->date);
                    $sheet->SetCellValue('C' . $row, $data_row->customer);
                    $sheet->SetCellValue('D' . $row, $data_row->name);
                    $sheet->SetCellValue('E' . $row, $data_row->total);
                    $sheet->SetCellValue('F' . $row, $data_row->total_tax);
                    $sheet->SetCellValue('G' . $row, $data_row->grand_total);
                    $sheet->SetCellValue('H' . $row, $data_row->profit);
                    $row++;
                }


                $style_header = array(      
                    'fill' => array(
                        'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => array('rgb'=>'fdbf2d'),
                    ),
                );
                $sheet->getStyle("A$row:H$row")->applyFromArray( $style_header );
                $sheet->SetCellValue('E' . $row, $total);
                $sheet->SetCellValue('F' . $row, $total_tax);
                $sheet->SetCellValue('G' . $row, $grand_total);
                $sheet->SetCellValue('H' . $row, $profit);

                $sheet->getColumnDimension('A')->setWidth(10);
                $sheet->getColumnDimension('B')->setWidth(25);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(45);
                $sheet->getColumnDimension('E')->setWidth(15);
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(15);
              
                $filename = 'profit_report';

                $sheet->getParent()->getDefaultStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);


                $sheet->getStyle('E2:E' . ($row))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getStyle('F2:F' . ($row))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getStyle('G2:G' . ($row))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getStyle('H2:H' . ($row))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);



                $header = 'A1:H1';
                $sheet->getStyle($header)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('94ce58');
                $style = array(
                    'font' => array('bold' => true,),
                    'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,),
                );
                $sheet->getStyle($header)->applyFromArray($style);


                $header = 'A2:H2';
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
                    $sheet->getStyle('A0:H'.($row))->applyFromArray($styleArray);
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

            if ($start_date ) {
                $this->datatables->where('sales.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }
            $this->datatables->where('sales.store_id', $this->activeStore);

             if ($created_by) {
                $this->datatables->where('biller_id', $created_by);
            }

            $this->datatables
               ->select("LPAD(sales.id, 4, '0') as sale_id, date, customer, (SELECT 
                    CASE
                        WHEN item_type = 'crepairs' THEN GROUP_CONCAT(CONCAT(product_name, ' ',item_details, '' ,' (".lang('Deposit').")'))
                        WHEN item_type = 'drepairs' THEN GROUP_CONCAT(CONCAT(product_name, ' ',item_details, '' ,' (".lang('Reparation').")'))
                        WHEN item_type IN ('new_phone', 'used_phone') THEN GROUP_CONCAT(CONCAT(product_name, ' ',item_details, '' ,' (".lang('Phone Sold').")'))
                        ELSE GROUP_CONCAT(product_name, ' ',item_details, '' )
                    END
                     FROM sale_items WHERE sale_items.sale_id = sales.id) as name, (grand_total-total_tax) as total, total_tax, grand_total, (SELECT SUM(unit_cost * quantity) FROM sale_items WHERE sale_items.sale_id = sales.id) as cost, (SELECT SUM(unit_price * quantity) FROM sale_items WHERE sale_items.sale_id = sales.id) as price, if(sales.sale_id IS NOT NULL, (0-(SELECT if(item_type IN ('cp', 'crepairs'), SUM(ABS(unit_price - discount)), if(add_to_stock, SUM(ABS(unit_price - discount)-unit_cost), SUM(ABS(unit_price - discount))) )
                                         FROM sale_items WHERE sale_items.sale_id=sales.id)) + sales.surcharge, (SELECT if(item_type IN ('cp', 'crepairs'), SUM(unit_price - discount), SUM((unit_price - discount)-unit_cost)) FROM sale_items WHERE sale_items.sale_id=sales.id) + (SELECT SUM(activation_spiff) FROM sale_items WHERE sale_items.sale_id=sales.id)) as profit, (SELECT CONCAT(first_name,' ',last_name) FROM users WHERE sales.biller_id = users.id) as biller_id, sales.id as id")
                ->from('sales')
                ->group_by('sales.id');

            $detail_link = anchor('panel/sales/modal_view/$1', '<i class="fas fa-file-text-o"></i> ' . "Sale Details", 'data-toggle="modal" data-target="#myModal"');
            $bill_link = '<a href="'.base_url('panel/pos/view/$1').'" >'.lang('View Sale').'</a>';
            $refund_link = '<a href="'.base_url('panel/sales/refund/$1').'" >'.lang('Refund').'</a>';
            
             $action = '<div class="text-center"><div class="btn-group text-left">'
                . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
                . lang('actions') . ' <span class="caret"></span></button>
                <ul class="dropdown-menu pull-right" role="menu">
                    <li>' . $detail_link . '</li>
                    <li>' . $bill_link . '</li>
                    <li>' . $refund_link . '</li>
                </ul>
            </div></div>';
            $this->datatables->add_column('actions', $action, 'id');
            $this->datatables->add_column('id', "$1", 'id');
            $this->datatables->unset_column('id');
            echo $this->datatables->generate();
        }
    }
    function tax()
    {
        $this->repairer->checkPermissions();
        $this->data['tax_followed_items'] = NULL;
        $this->form_validation->set_rules('date_range_o', lang('Date Range'), 'required');
        $this->form_validation->set_rules('taxes[]', lang('Taxes'), 'required');
        if ($this->form_validation->run() == true){
            extract((array)json_decode($this->input->post('date_range')));
            $taxes = ($this->input->post('taxes'));

            $start = (isset($start) ? $start : date('Y-m-d')) . " 00:00:00" ;
            $end = (isset($end) ? $end : date('Y-m-d')) . " 23:59:59";
            
            $this->db->where('sale_items.date BETWEEN "' . $start . '" and "' . $end . '"');
            $this->db->where('sale_items.refund_item = 0');
            $this->db->where('sale_items.item_type !=', 'crepairs');
            $this->db->where('sale_items.taxable = 1');
            $this->db->where('sale_items.store_id', $this->activeStore);

            $q = $this->db->select('LPAD(sale_id, 4, "0") as sale_id, date, product_id, product_code, product_name, unit_price tax_rate_id, item_type,tax_rate, discount')->get('sale_items');

            // print_r($q->result());die();
            $items = array();
            if ($q->num_rows() > 0) {
                foreach ($q->result() as $item) {
                    if ($item->item_type == 'drepairs') {
                        $q = $this->db->select('product_code,product_name,unit_price,tax_rate')->get_where('repair_items', array('repair_id' => $item->product_id));
                        if ($q->num_rows() > 0 ) {
                            foreach ($q->result() as $ritem) {
                                $ritem->sale_id = $item->sale_id;
                                $ritem->product_name .= ' (Repair Item Used for '.$item->product_name.') ';
                                $ritem->date = $item->date;
                                $ritem->tax_rate = json_decode($ritem->tax_rate);
                                $tax_ids = array();
                                foreach ($ritem->tax_rate as $tax) {
                                    $tax_ids[] = $tax->id;
                                }
                                $ritem->discount = 0;
                                $ritem->item_type = 'repair_items';
                                $ritem->tax_rate_id = array_intersect($taxes, $tax_ids);
                                if (!empty(array_intersect($taxes, $tax_ids))) {
                                    $items[] = $ritem;
                                }
                            }
                        }
                    }else{
                        $item->tax_rate = json_decode($item->tax_rate);
                        $tax_ids = array();
                        foreach ($item->tax_rate as $tax) {
                            $tax_ids[] = $tax->id;
                        }

                        $item->tax_rate_id = array_intersect($taxes, $tax_ids);
                        if (!empty(array_intersect($taxes, $tax_ids))) {
                            $items[] = $item;
                        }
                    }
                }
            }
            $items_filtered = array();
            foreach ($taxes as $tax) {
                foreach ($items as $item) {
                    $item = (array)$item;
                    if (in_array($tax, $item['tax_rate_id'])) {
                        $t_rate = NULL;
                        foreach ($item['tax_rate'] as $trate) {
                            if ($trate->id == $tax) {
                                $t_rate = $trate;
                                $keee = $trate->name;

                                break;
                            }
                        }
                        if ($trate->type == 1) {
                            $pr_tax_val = (abs($item['unit_price']) - $item['discount']) * ($trate->rate) / 100;
                        } else {
                            $pr_tax_val = ($trate->rate);
                        }
                        $item['tax'] = $pr_tax_val;
                        unset($item['tax_rate']);
                        unset($item['tax_rate_id']);
                        $items_filtered[$keee][] = $item;
                    }
                }
            }
            $this->data['tax_followed_items'] = $items_filtered;
        }
            
        $this->render('reports/tax');
    }
    /* ------------------------------------------------------------------------- */
    

    public function Vendor_purchases()
    {
        $this->repairer->checkPermissions();

        $this->data['suppliers_'] = $this->reports_model->getAllSuppliers();
        $this->render('reports/vendor_purchases');
    }

    public function getPurchases($pdf = NULL, $xls = NULL)
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
             if ($this->input->get('suppliers')) {
                $this->db->where_in('supplier_id', explode(',', $this->input->get('suppliers')));
            }
          
            if ($start_date ) {
                $this->db->where('purchases.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }
            $this->db
                ->select("date, reference_no, supplier, status, grand_total")
                ->from('purchases');
            $this->db->where('purchases.store_id', $this->activeStore);

             $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $row->date = $this->repairer->hrld($row->date);
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }
            if (!empty($data)) {

                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $sheet->setTitle(lang('Vendor Purchases Report'));
                $sheet->SetCellValue('A2', lang("date"));
                $sheet->SetCellValue('B2', lang('reference_no'));
                $sheet->SetCellValue('C2', lang('Supplier'));
                $sheet->SetCellValue('D2', lang('status'));
                $sheet->SetCellValue('E2', lang('grand_total'));

                $sheet->SetCellValue('A1', sprintf(lang('vendor_report_from_to'), date('m-d-Y H:i:s', strtotime($start_date)), date('m-d-Y H:i:s', strtotime($end_date))));

                $sheet->mergeCells('A1:E1');

                $row = 3;
                $total = 0;
                foreach ($data as $data_row) {
                    $ir = $row + 1;
                    if ($ir % 2 == 0) {
                        $style_header = array(                  
                            'fill' => array(
                                'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => array('rgb'=>'CCCCCC'),
                            ),
                        );
                        $sheet->getStyle("A$row:E$row")->applyFromArray( $style_header );
                    }

                    $sheet->SetCellValue('A' . $row, ($data_row->date));
                    $sheet->SetCellValue('B' . $row, $data_row->reference_no);
                    $sheet->SetCellValue('C' . $row, $data_row->supplier);
                    $sheet->SetCellValue('D' . $row, $data_row->status);
                    $sheet->SetCellValue('E' . $row, $data_row->grand_total);
                    $total += $data_row->grand_total;
                    $row++;
                }
                $style_header = array(      
                    'fill' => array(
                        'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => array('rgb'=>'fdbf2d'),
                    ),
                );
                $sheet->getStyle("A$row:E$row")->applyFromArray( $style_header );
                $sheet->SetCellValue('E' . $row, $total);

                $sheet->getColumnDimension('A')->setWidth(30);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(25);
                $sheet->getColumnDimension('E')->setWidth(15);
              
                $filename = 'vendor_purchases_report';
                $sheet->getParent()->getDefaultStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('E2:E' . ($row))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


                 $header = 'A1:E1';
                $sheet->getStyle($header)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('94ce58');
                $style = array(
                    'font' => array('bold' => true,),
                    'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,),
                );
                $sheet->getStyle($header)->applyFromArray($style);
                

                $header = 'A2:E2';
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
                    $sheet->getStyle('A0:E'.($row))->applyFromArray($styleArray);
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

                    // $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
                    // $writer->save("05featuredemo.xlsx");


                    $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
                    $writer->save('php://output');
                    exit();
                }
            }
        }else{
            $this->load->library('datatables');
            $this->datatables->where('purchases.store_id', $this->activeStore);

            if ($this->input->get('suppliers')) {
                $this->db->where_in('supplier_id', explode(',', $this->input->get('suppliers')));
            }
          
            if ($start_date ) {
                $this->datatables->where('purchases.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }
            $this->datatables
                ->select("date, reference_no, supplier, status, grand_total, attachment, id")
                ->from('purchases');
            echo $this->datatables->generate();
        }
    }

    /* ------------------------------------------------------------------------- */

    public function Customer_purchases()
    {
        $this->repairer->checkPermissions();
        $this->mPageTitle = lang('Customer Purchases');
        $this->data['customers'] = $this->reports_model->getAllCustomers();
        $this->render('reports/customer_purchases');
    }

    public function getCustomerPurchases($pdf = NULL, $xls = NULL)
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
            if ($this->input->get('customers')) {
                $this->db->where_in('customer_id', explode(',', $this->input->get('customers')));
            }
          
            if ($start_date ) {
                $this->db->where('customer_purchases.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }
            $this->db->where('customer_purchases.store_id', $this->activeStore);

            $this->db
            ->select("date, customer, if(status=1, 'Ready to Purchase' , 'Purchased') as status, grand_total, id")
            ->from('customer_purchases');
             $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $row->date = $this->repairer->hrld($row->date);

                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }
            if (!empty($data)) {

               

                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $sheet->setTitle(lang('Customer Purchases Report'));
                $sheet->SetCellValue('A2', lang("date"));
                $sheet->SetCellValue('B2', lang('Customer'));
                $sheet->SetCellValue('C2', lang('status'));
                $sheet->SetCellValue('D2', lang('grand_total'));

                $sheet->SetCellValue('A1', sprintf(lang('customer_report_from_to'), date('m-d-Y H:i:s', strtotime($start_date)), date('m-d-Y H:i:s', strtotime($end_date))));

                $sheet->mergeCells('A1:D1');


                $row = 3;
                $total = 0;
                foreach ($data as $data_row) {
                    $ir = $row + 1;
                    if ($ir % 2 == 0) {
                        $style_header = array(                  
                            'fill' => array(
                                'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => array('rgb'=>'CCCCCC'),
                            ),
                        );
                        $sheet->getStyle("A$row:D$row")->applyFromArray( $style_header );
                    }
                    $sheet->SetCellValue('A' . $row, $data_row->date);
                    $sheet->SetCellValue('B' . $row, $data_row->customer);
                    $sheet->SetCellValue('C' . $row, $data_row->status);
                    $sheet->SetCellValue('D' . $row, $data_row->grand_total);
                    $total += $data_row->grand_total;
                    $row++;
                }
                 
                $style_header = array(      
                    'fill' => array(
                        'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => array('rgb'=>'fdbf2d'),
                    ),
                );
                $sheet->getStyle("A$row:D$row")->applyFromArray( $style_header );
                $sheet->SetCellValue('D' . $row, $total);


                $sheet->getColumnDimension('A')->setWidth(30);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(25);
              
                $filename = 'customer_purchases_report';


                $sheet->getParent()->getDefaultStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $sheet->getStyle('D2:D' . ($row))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                $header = 'A1:D1';
                $sheet->getStyle($header)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('94ce58');
                $style = array(
                    'font' => array('bold' => true,),
                    'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,),
                );
                $sheet->getStyle($header)->applyFromArray($style);
                

                $header = 'A2:D2';
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
                    $sheet->getStyle('A0:D'.($row))->applyFromArray($styleArray);
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
        }else{
            $this->db->where('customer_purchases.store_id', $this->activeStore);
            $this->load->library('datatables');
            if ($this->input->get('customers')) {
                $this->db->where_in('customer_id', explode(',', $this->input->get('customers')));
            }
            if ($start_date ) {
                $this->datatables->where('customer_purchases.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }
            $this->datatables
            ->select(" date, customer, status, grand_total, id")
            ->from('customer_purchases');
            echo $this->datatables->generate();
        }
       
    }

    /* ------------------------------------------------------------------------- */

    public function drawer()
    {
        $this->repairer->checkPermissions();
        $this->mPageTitle = lang('Drawer Report');
        $this->render('reports/drawer');
    }

    function getDrawerReport($pdf = NULL, $xls = NULL)
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
             $this->db
                ->select("date, closed_at, (SELECT CONCAT(users.first_name, ' ', users.last_name) FROM users where users.id=pos_register.user_id) as opened_by,(SELECT CONCAT(users.first_name, ' ', users.last_name) FROM users where users.id=pos_register.closed_by) as closed_by, cash_in_hand, total_cc, total_cheques, total_cash, total_cc_submitted, total_cheques_submitted,total_cash_submitted, count_note", FALSE)
                ->from("pos_register")
                ->order_by('date desc');
            $this->db->where('pos_register.store_id', $this->activeStore);

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


                $sheet->setTitle(lang('drawer_report'));
                $sheet->SetCellValue('A2', lang('open_time'));
                $sheet->SetCellValue('B2', lang('close_time'));
                $sheet->SetCellValue('C2', lang('opened_by'));
                $sheet->SetCellValue('D2', lang('closed_by'));
                $sheet->SetCellValue('E2', lang('cash_in_hand'));
                $sheet->SetCellValue('F2', lang('cc_slips'));
                $sheet->SetCellValue('G2', lang('cheques'));
                $sheet->SetCellValue('H2', lang('total_cash'));
                $sheet->SetCellValue('I2', lang('cc_slips_submitted'));
                $sheet->SetCellValue('J2', lang('cheques_submitted'));
                $sheet->SetCellValue('K2', lang('total_cash_submitted'));
                $sheet->SetCellValue('L2', lang('count_note'));
               
                $sheet->SetCellValue('A1', sprintf(lang('drawer_report_from_to'), date('m-d-Y H:i:s', strtotime($start_date)), date('m-d-Y H:i:s', strtotime($end_date))));

                $sheet->mergeCells('A1:L1');

                $row = 3;
                foreach ($data as $data_row) {

                    $ir = $row + 1;
                    if ($ir % 2 == 0) {
                         $style_header = array(                  
                            'fill' => array(
                                'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => array('rgb'=>'CCCCCC'),
                            ),
                        );
                        $sheet->getStyle("A$row:L$row")->applyFromArray( $style_header );
                    }

                    $sheet->SetCellValue('A' . $row, $this->repairer->hrld($data_row->date));
                    $sheet->SetCellValue('B' . $row, $this->repairer->hrld($data_row->closed_at));
                    $sheet->SetCellValue('C' . $row, $data_row->opened_by);
                    $sheet->SetCellValue('D' . $row, $data_row->closed_by);
                    $sheet->SetCellValue('E' . $row, $data_row->cash_in_hand);
                    $sheet->SetCellValue('F' . $row, $data_row->total_cc);
                    $sheet->SetCellValue('G' . $row, $data_row->total_cheques);
                    $sheet->SetCellValue('H' . $row, $data_row->total_cash);
                    $sheet->SetCellValue('I' . $row, $data_row->total_cc_submitted);
                    $sheet->SetCellValue('J' . $row, $data_row->total_cheques_submitted);
                    $sheet->SetCellValue('K' . $row, $data_row->total_cash_submitted);
                    $sheet->SetCellValue('L' . $row, $data_row->count_note);
                    if($data_row->total_cash_submitted < $data_row->total_cash || $data_row->total_cheques_submitted < $data_row->total_cheques || $data_row->total_cc_submitted < $data_row->total_cc) {
                            $sheet->getStyle("A$row:L$row")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F2DEDE');
                    }
                    $row++;
                }

                $sheet->getColumnDimension('A')->setWidth(25);
                $sheet->getColumnDimension('B')->setWidth(25);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(25);
                $sheet->getColumnDimension('E')->setWidth(15);
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('H')->setWidth(15);
                $sheet->getColumnDimension('I')->setWidth(15);
                $sheet->getColumnDimension('J')->setWidth(15);
                $sheet->getColumnDimension('K')->setWidth(15);
                $sheet->getColumnDimension('L')->setWidth(35);
                $filename = 'register_report';

                
                $sheet->getParent()->getDefaultStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('E2:K' . ($row))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                

                $header = 'A1:L1';
                $sheet->getStyle($header)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('94ce58');
                $style = array(
                    'font' => array('bold' => true,),
                    'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,),
                );
                $sheet->getStyle($header)->applyFromArray($style);
                

                $header = 'A2:L2';
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
                    $sheet->getStyle('A0:L'.($row))->applyFromArray($styleArray);
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
            }else{
                $this->session->set_flashdata('warning', 'No Data');
                redirect('panel/reports/drawer');
            }
        } else {
            $this->load->library('datatables');
            $this->datatables->where('pos_register.store_id', $this->activeStore);

            $this->datatables

                ->select("(SELECT CONCAT(users.first_name, ' ', users.last_name) FROM users where users.id=pos_register.user_id) as opener,date as op_date, cash_in_hand, (SELECT CONCAT(users.first_name, ' ', users.last_name) FROM users where users.id=pos_register.closed_by) as closer, closed_at as cl_date,total_cash, count_note, pos_register.id as id, pos_register.status as status")
                ->from('pos_register');
            if ($start_date) {
                $this->datatables->where('pos_register.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }
            echo $this->datatables->generate();
        }



    }


    public function drawer_modal_view($id, $export = NULL)
    {

        $register = $this->db->get_where('pos_register', array('id'=>$id, 'pos_register.store_id'=> $this->activeStore))->row();
        $this->data['register'] = $register;
        $ruser = $this->ion_auth->user($register->user_id)->row();
        $this->data['ruser'] = $ruser;

        if ($export == 'pdf' || $export == 'xls') {

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();


            $sheet->setTitle(lang('drawer_report'));


            $sheet->SetCellValue('A1', sprintf(lang('drawer_report_for_from_to'),$ruser->first_name.' '.$ruser->last_name, $this->repairer->hrld($register->date), $this->repairer->hrld($register->closed_at)));


            $sheet->mergeCells('A1:D1');
            $sheet->SetCellValue('A2', '#');
            $sheet->SetCellValue('B2', lang('System Totals'));
            $sheet->SetCellValue('C2', lang('Your Totals'));
            $sheet->SetCellValue('D2', lang('Difference'));

            $sheet->SetCellValue('A3', lang('Credit Card'));
            $sheet->SetCellValue('B3', $register->total_cc);
            $sheet->SetCellValue('C3', $register->total_cc_submitted);
            $sheet->SetCellValue('D3', $register->total_cc-$register->total_cc_submitted);

            $sheet->SetCellValue('A4', lang('Cheques'));
            $sheet->SetCellValue('B4', $register->total_cheques);
            $sheet->SetCellValue('C4', $register->total_cheques_submitted);
            $sheet->SetCellValue('D4', $register->total_cheques-$register->total_cheques_submitted);

            $sheet->SetCellValue('A5', lang('Cash'));
            $sheet->SetCellValue('B5', $register->total_cash);
            $sheet->SetCellValue('C5', $register->total_cash_submitted);
            $sheet->SetCellValue('D5', $register->total_cash-$register->total_cash_submitted);

            $sheet->SetCellValue('A6', lang('PayPal'));
            $sheet->SetCellValue('B6', $register->total_ppp);
            $sheet->SetCellValue('C6', $register->total_ppp_submitted);
            $sheet->SetCellValue('D6', $register->total_ppp-$register->total_ppp_submitted);

            $sheet->SetCellValue('A7', lang('Other'));
            $sheet->SetCellValue('B7', $register->total_others);
            $sheet->SetCellValue('C7', $register->total_others_submitted);
            $sheet->SetCellValue('D7', $register->total_others-$register->total_others_submitted);

            $systemtotals = $register->total_cc + $register->total_cheques + $register->total_cash + $register->total_ppp + $register->total_others;
            $usertotals = $register->total_cc_submitted + $register->total_cheques_submitted + $register->total_cash_submitted + $register->total_ppp_submitted + $register->total_others_submitted;
            $sheet->SetCellValue('A8', lang('Total'));
            $sheet->SetCellValue('B8', $systemtotals);
            $sheet->SetCellValue('C8', $usertotals);
            $sheet->SetCellValue('D8', $systemtotals - $usertotals);
            
            $sheet->SetCellValue('A8', lang('Total'));
            $sheet->SetCellValue('B8', $systemtotals);
            $sheet->SetCellValue('C8', $usertotals);
            $sheet->SetCellValue('D8', $systemtotals - $usertotals);
            
            $sheet->SetCellValue('A9', lang('Deposits To Safe From Register'));
            $sheet->SetCellValue('D9', $register->tosafetranfers);
            $sheet->SetCellValue('A10', lang('Deposits to Register From Safe'));
            $sheet->SetCellValue('D10', $register->todrawertranfers);
            $sheet->mergeCells('A9:C9');
            $sheet->mergeCells('A10:C10');

            $sheet->mergeCells('A11:D14');
            $sheet->SetCellValue('A11', $register->count_note);

            $sheet->getColumnDimension('A')->setWidth(50);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(20);
            
        

            $header = 'A1:D1';
            $sheet->getStyle($header)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('94ce58');
            $style = array(
                'font' => array('bold' => true,),
                'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,),
            );
            $sheet->getStyle($header)->applyFromArray($style);
            

            $header = 'A2:D2';
            $sheet->getStyle($header)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('fdbf2d');
            $style = array(
                'font' => array('bold' => true,),
                'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_GENERAL,),
            );
            $sheet->getStyle($header)->applyFromArray($style);


            $filename = 'register_report';


            if ($export == 'pdf') {
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                            'color' => ['argb' => 'FFFF0000'],
                        ],
                    ],
                ];
                $sheet->getStyle('A0:D14')->applyFromArray($styleArray);
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
                header('Cache-Control: max-age=0');
                $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Mpdf');
                $writer->save('php://output');
            }
            if ($export == 'xls') {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
                header('Cache-Control: max-age=0');



                $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save('php://output');
                exit();
            }
        
        }else{
            $this->load->view($this->theme.'reports/drawer_modal', $this->data);
        }
    }

    /* ------------------------------------------------------------------------- */

    

    public function gl($export = NULL)
    {
        $this->repairer->checkPermissions();

        $this->mPageTitle = lang('General Ledger Report');

        // Post Vars for Output
        if ($this->input->post('date_range')) {
            $date_range = json_decode($this->input->post('date_range'));
            $start_date = ($date_range->start) . " 00:00:00";
            $end_date = ($date_range->end) . " 23:59:59";
        } else {
            $start_date = date('Y-m-d 00:00:00');
            $end_date = date('Y-m-d 23:59:59');
        }

        // Get Vars For Export
        if ($export == 'pdf' || $export == 'xls') {
            if ($this->input->get('start_date')) {
                $start_date = $this->input->get('start_date');
            }else{
                $start_date = date('Y-m-d 00:00:00');
            }

            if ($this->input->get('end_date')) {
                $end_date = $this->input->get('end_date');
            }else{
                $end_date = date('Y-m-d 00:00:00');
            }
        }

        $this->data['vpt'] = $this->reports_model->inventoryRecieved($start_date, $end_date);
        $this->data['pr'] = $this->reports_model->purchasesRecieved($start_date, $end_date);
        $this->data['vop'] = $this->reports_model->vendorOrdersPlaced($start_date, $end_date);
        $this->data['vs'] = $this->reports_model->shippingTotal($start_date, $end_date);
        $this->data['cs'] = $this->reports_model->customerPurchases($start_date, $end_date);
        $this->data['rd'] = $this->reports_model->repairDeposited($start_date, $end_date);
        $this->data['rc'] = $this->reports_model->repairClosed($start_date, $end_date);
        $this->data['ups'] = $this->reports_model->usedPhoneSales($start_date, $end_date);
        $this->data['nps'] = $this->reports_model->newPhoneSales($start_date, $end_date);
        $this->data['as'] = $this->reports_model->accessorySales($start_date, $end_date);
        $this->data['os'] = $this->reports_model->otherSales($start_date, $end_date);
        $this->data['pt'] = $this->reports_model->planSales($start_date, $end_date);
        $this->data['tc'] = $this->reports_model->totalTaxes($start_date, $end_date);
        $this->data['ri'] = $this->reports_model->refundsIssued($start_date, $end_date);
        $this->data['rs'] = $this->reports_model->refundsSurcharges($start_date, $end_date);
        $this->data['io'] = $this->reports_model->inventoryOut($start_date, $end_date);
        $this->data['rt'] = $this->reports_model->rt($start_date, $end_date);
        $this->data['ttf'] = $this->reports_model->ttf($start_date, $end_date);
        $this->data['ttr'] = $this->reports_model->ttr($start_date, $end_date);
        $this->data['tpt'] = $this->reports_model->tpt($start_date, $end_date);
        $this->data['expenses'] = $this->reports_model->expenses($start_date, $end_date);
        $this->data['deposits'] = $this->reports_model->deposits($start_date, $end_date);
        $this->data['profit'] = $this->reports_model->getTotalProfit($start_date, $end_date);

         if ($export == 'pdf' || $export == 'xls') {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();


          
            $sheet->setTitle(lang('General Ledger Report'));

            
            $sheet->SetCellValue('A1', sprintf(lang('gl_report_from_to'), $this->repairer->hrld($start_date),  $this->repairer->hrld($end_date)));

            $sheet->mergeCells('A1:B1');
            

            $sheet->SetCellValue('A2', lang('Inventory Received From Vendors'));
            $sheet->SetCellValue('B2', number_format($this->data['vpt'], 2));

            $sheet->SetCellValue('A3', lang('Purchases Returned'));
            $sheet->SetCellValue('B3', number_format($this->data['pr'], 2, NULL,''));

            $sheet->SetCellValue('A4', lang('Vendor Orders Placed'));
            $sheet->SetCellValue('B4', number_format($this->data['vop'], 2, NULL,''));

            $sheet->SetCellValue('A5', lang('Shipping Cost'));
            $sheet->SetCellValue('B5', number_format($this->data['vs'], 2, NULL,''));

            $sheet->SetCellValue('A6', lang('Purchases Made From Customers'));
            $sheet->SetCellValue('B6', number_format($this->data['cs'], 2, NULL,''));

            $sheet->SetCellValue('A7', lang('Repair Deposits Received'));
            $sheet->SetCellValue('B7', number_format($this->data['rd'], 2, NULL,''));

            $sheet->SetCellValue('A8', lang('Repairs Closed Out'));
            $sheet->SetCellValue('B8', number_format($this->data['rc'], 2, NULL,''));

            $sheet->SetCellValue('A9', lang('Used Phones Sales'));
            $sheet->SetCellValue('B9', number_format($this->data['ups'], 2, NULL,''));

            $sheet->SetCellValue('A10', lang('New Phones Sales'));
            $sheet->SetCellValue('B10', number_format($this->data['nps'], 2, NULL,''));

            $sheet->SetCellValue('A11', lang('Accessories Sales'));
            $sheet->SetCellValue('B11', number_format($this->data['as'], 2, NULL,''));

            $sheet->SetCellValue('A12', lang('Other Sales '));
            $sheet->SetCellValue('B12', number_format($this->data['os'], 2, NULL,''));

            $sheet->SetCellValue('A13', lang('Cellular Plan Sales'));
            $sheet->SetCellValue('B13', number_format($this->data['pt'], 2, NULL,''));

            $sheet->SetCellValue('A14', lang('Total Tax Collected'));
            $sheet->SetCellValue('B14', number_format($this->data['tc'], 2, NULL,''));

            $sheet->SetCellValue('A15', lang('Refunds Issued'));
            $sheet->SetCellValue('B15', number_format($this->data['ri'], 2, NULL,''));

            $sheet->SetCellValue('A16', lang('Refund Surcharges (Restocking Fees) '));
            $sheet->SetCellValue('B16', number_format($this->data['rs'], 2, NULL,''));

            $sheet->SetCellValue('A17', lang('Inventory Out'));
            $sheet->SetCellValue('B17', number_format($this->data['io'], 2, NULL,''));

             
            $sheet->SetCellValue('A18', lang('Regular transfers'));
            $sheet->SetCellValue('B18', number_format($this->data['rt'], 2, NULL,''));

            $sheet->SetCellValue('A19', lang('transfers through refunds'));
            $sheet->SetCellValue('B19', number_format($this->data['ttf'], 2, NULL,''));

            $sheet->SetCellValue('A20', lang('Transfers Received'));
            $sheet->SetCellValue('B20', number_format($this->data['ttr'], 2, NULL,''));

            $sheet->SetCellValue('A21', lang('Pending Transfers'));
            $sheet->SetCellValue('B21', number_format($this->data['tpt'], 2, NULL,''));


            $sheet->SetCellValue('A22', lang('Expenses'));
            $sheet->SetCellValue('B22', number_format($this->data['expenses'], 2, NULL,''));

            $sheet->SetCellValue('A23', lang('Deposits'));
            $sheet->SetCellValue('B23', number_format($this->data['deposits'], 2, NULL,''));

            $this->data['profit'] += $this->data['deposits'] ? number_format($this->data['deposits'], 2) : number_format(0, 2);
            $this->data['profit'] += $this->data['expenses'] ? number_format($this->data['expenses'], 2) : number_format(0, 2);
            $sheet->SetCellValue('A24', lang('Gross Profit'));
            $sheet->SetCellValue('B24', number_format($this->data['profit'], 2, NULL,''));




            $sheet->getColumnDimension('A')->setWidth(80);
            $sheet->getColumnDimension('B')->setWidth(20);
            $filename = 'general_ledger_report';
            
            $header = 'A1:B1';
            
            $sheet->getStyle($header)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('94ce58');
             $style = array(
                    'font' => array('bold' => true,),
                    'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,),
                );
            $sheet->getStyle($header)->applyFromArray($style);


            if ($export == 'pdf') {
                $styleArray = [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                'color' => ['argb' => 'FFFF0000'],
                            ],
                        ],
                    ];
                    $sheet->getStyle('A0:B'. 24)->applyFromArray($styleArray);
                    $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                    header('Content-Type: application/pdf');
                    header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
                    header('Cache-Control: max-age=0');
                    $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Mpdf');
                    $writer->save('php://output');
            }
            if ($export == 'xls') {
               header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
                    header('Cache-Control: max-age=0');

                    $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
                    $writer->save('php://output');
                    exit();
            }
        }else{
            $this->render('reports/gl');
        }
    }


    function activities() {
        $this->repairer->checkPermissions();

        $this->render('reports/activities');
    }
    
    public function getAllActivities($which = NULL) {

        $this->load->library('datatables');

        if ($this->input->get('type')) {
            $type = $this->input->get('type');
            if ($type == 'past') {
                $this->datatables->where('CURDATE() > DATE_FORMAT(client_activity.remind_date, "%Y-%m-%d")', NULL, FALSE);
            }elseif($type == 'future'){
                $this->datatables->where('CURDATE() < DATE_FORMAT(client_activity.remind_date, "%Y-%m-%d")', NULL, FALSE);
            }elseif($type == 'today'){
                $this->datatables->where('CURDATE() = DATE_FORMAT(client_activity.remind_date, "%Y-%m-%d")', NULL, FALSE);
            }else{
                return;
            }
        }
       
        $this->datatables
            ->select("CONCAT(clients.first_name, ' ', clients.last_name, '___', clients.id) as name, a1.name as activity, a2.name as sub_activity, store.name as sname, client_activity.remind_date as remind_date, CASE WHEN CURDATE() > DATE_FORMAT(client_activity.remind_date, '%Y-%m-%d')THEN \"past\" WHEN CURDATE() < DATE_FORMAT(client_activity.remind_date, '%Y-%m-%d') THEN \"future\" ELSE 'today' END as remind_dat, client_activity.priority as priority, client_activity.status as status, client_activity.id as id")
            ->join('clients', 'clients.id=client_activity.client_id')
            ->join('activities as a1', 'client_activity.activity_id=a1.id')
            ->join('activities as a2', 'client_activity.subactivity_id=a2.id')
            ->join('store', 'client_activity.locations=store.id')
            ->where('status', "open")
            //commented line below because I think employee should be able to see all of his activity no matter what store he is logged into
         //   ->where('client_activity.locations', $this->activeStore)
            ->from('client_activity');
        $this->datatables->edit_column("status", '$1___$2', "id, status");
        echo $this->datatables->generate();
    }

   

    public function getAccessoryAlerts($check_alert_quantity = true) {
        $this->db
            ->select('upc_code as code, price, name as name,(SELECT COUNT(id) FROM stock WHERE stock.inventory_id = accessory.id AND stock.inventory_type = "accessory") as quantity, alert_quantity, accessory.id as id')
            ->from('accessory');

        if ($check_alert_quantity) {
            $this->db->where('alert_quantity >= (SELECT COUNT(id) FROM stock WHERE stock.inventory_id = accessory.id AND stock.inventory_type = "accessory")', NULL)->where('alert_quantity >=', 0);
        }


        $q = $this->db->get();
        $data = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $row->type = "accessory";
                $row->total_price = $this->getAvgPriceProductByTypeAndID('accessory', $row->id, true);

                $row->cost = $this->getAvgPriceProductByTypeAndID('accessory', $row->id);
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getOtherAlerts($check_alert_quantity = true) {
        $this->db
            ->select('upc_code as code,price, name as name,(SELECT COUNT(id) FROM stock WHERE stock.inventory_id = other.id AND stock.inventory_type = "other") as quantity, alert_quantity, other.id as id')
            ->from('other');

        if ($check_alert_quantity) {
            $this->db->where('alert_quantity >= (SELECT COUNT(id) FROM stock WHERE stock.inventory_id = other.id AND stock.inventory_type = "other")', NULL)->where('alert_quantity >=', 0);
        }

        if ($this->input->get('cat_id')) {
            $this->db->where('category', $this->input->get('cat_id'));
        }
        if ($this->input->get('sub_id')) {
            $this->db->where('sub_category', $this->input->get('sub_id'));
        }
        
        $q = $this->db->get();
        $data = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $row->type = "other";
                $row->total_price = $this->getAvgPriceProductByTypeAndID('other', $row->id, true);

                $row->cost = $this->getAvgPriceProductByTypeAndID('other', $row->id);
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getNewPhones($check_alert_quantity = true) {
        $this->db
            ->select('model_name as code,price, phone_name as name,(SELECT COUNT(id) FROM stock WHERE stock.inventory_id = phones.id AND stock.inventory_type = "phones") as quantity, alert_quantity, phones.id as id')
            ->where('type', 'new')
            ->from('phones');
            

        if ($this->input->get('cat_id')) {
            $this->db->where('category', $this->input->get('cat_id'));
        }
        if ($this->input->get('sub_id')) {
            $this->db->where('sub_category', $this->input->get('sub_id'));
        }

        if ($check_alert_quantity) {
            $this->db->where('alert_quantity >= (SELECT COUNT(id) FROM stock WHERE stock.inventory_id = phones.id AND stock.inventory_type = "phones")', NULL)->where('alert_quantity >=', 0);
        }
        $q = $this->db->get();
        $data = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $row->type = "new_phone";
                $row->total_price = $this->getAvgPriceProductByTypeAndID('phones', $row->id, true);
                $row->cost = $this->getAvgPriceProductByTypeAndID('phones', $row->id);
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getUsedPhones($check_alert_quantity = true) {
        $this->db
            ->select('model_name as code, IF(phones.sold, 0, phone_items.price) as price,phone_items.cost as cost, phone_name as name, IF(phones.sold, "0", "1") as quantity, alert_quantity, phones.id as id, phones.sold as sold')
            ->join('phone_items', 'phone_items.phone_id=phones.id')
            // ->where('phones.sold', false)
            ->where('type', 'used')
            ->from('phones');

        if ($this->input->get('cat_id')) {
            $this->db->where('category', $this->input->get('cat_id'));
        }
        if ($this->input->get('sub_id')) {
            $this->db->where('sub_category', $this->input->get('sub_id'));
        }
        if ($check_alert_quantity) {
            $this->db->where('alert_quantity >= "1"', NULL)->where('alert_quantity >=', 0);
        }
        $q = $this->db->get();
        $data = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $row->type = "used_phone";

                $row->total_price = $row->cost;
                if ((int)$row->sold == 1) {
                    $row->total_price = 0;
                }
                
                $row->cost = $row->price;
                if ((int)$row->sold == 1) {
                    $row->cost = 0;
                }
                $data[] = $row;
            }
        }
        return $data;
    }
    public function getRepairPartAlerts($check_alert_quantity = true) {
        $this->db
            ->select('code as code,price, name, (SELECT COUNT(id) FROM stock WHERE stock.inventory_id = inventory.id AND stock.inventory_type = "repair") as quantity, alert_quantity, inventory.id as id')
            ->from('inventory');
        
        if ($check_alert_quantity) {
            $this->db->where('alert_quantity >= (SELECT COUNT(id) FROM stock WHERE stock.inventory_id = inventory.id AND stock.inventory_type = "repair")', NULL);
            $this->db->where('alert_quantity >=', 0);
        }

        if ($this->input->get('cat_id')) {
            $this->db->where('category', $this->input->get('cat_id'));
        }
        if ($this->input->get('sub_id')) {
            $this->db->where('sub_category', $this->input->get('sub_id'));
        }

        $q = $this->db->get();
        $data = array();


        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $row->type = "repair_part";
                $row->cost = $this->getAvgPriceProductByTypeAndID('repair', $row->id);
                $row->total_price = $this->getAvgPriceProductByTypeAndID('repair', $row->id, true);
                $data[] = $row;
            }
        }
        return $data;
    }

    private function getAvgPriceProductByTypeAndID($type, $product_id, $price_sum = false){
        $this->db
            ->select('price')
            ->from('stock')
            ->where('inventory_type', $type)
            ->where('inventory_id', $product_id);
        $q = $this->db->get();


        $sum_price = 0;
        $sum_items = 0;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $sum_price += $row->price;
                $sum_items++;
            }
        }

        if ($price_sum) {
            return $sum_price;
        }

        if ((float)$sum_price > 0 && (float)$sum_items > 0) {
            return $sum_price / $sum_items;
        }
        return 0;
    }

    public function quantity_alerts() {
        $selected = explode(',', (string)$this->input->get('types'));
        if ($this->input->get('types')) {
            $repair = in_array('repair_parts', $selected) ? $this->getRepairPartAlerts() : array();
            $phones = in_array('new_phones', $selected) ? $this->getNewPhones() : array();
            $acc    = in_array('accessories', $selected) ? $this->getAccessoryAlerts() : array();
            $other  = in_array('others', $selected) ? $this->getOtherAlerts() : array();
        }else{
            $repair = $this->getRepairPartAlerts();
            $phones = $this->getNewPhones();
            $acc = $this->getAccessoryAlerts();
            $other  = $this->getOtherAlerts();
        }
        $this->data['selected'] = $selected;
        $this->data['records'] = array_merge($acc, $other, $phones, $repair);
        $this->render('reports/quantity_alerts');
    }
}
