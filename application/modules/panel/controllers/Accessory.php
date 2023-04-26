<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Accessory extends Auth_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('pos_inventory_model');

	}
	function toggle()
	{
        $toggle = $this->input->post('toggle');
        if ($toggle == 'enable') {
            $data = array(
                'deleted' => 0,
            );
            $a = lang('enabled');
        }else{
            $data = array(
                'deleted' => 1,
            );
            $a = lang('disabled');
        }
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('accessory', $data);
        echo json_encode(array('ret' => 'true', 'toggle' => $a));
	}
	function addByAjax()
    {
        $upc = $this->settings_model->UPCCodeExists($this->input->post('a_upc_code'));
        if ($upc) {
            $this->repairer->send_json(array('msg'=>'error', 'message'=>lang('code_already_exists', $upc->name, humanize($upc->type))));
        }
        
    	$data = array(
			'name'					=> $this->input->post('a_name'),
			'upc_code'				=> $this->input->post('a_upc_code'),
			'price'					=> $this->input->post('a_price'),
			'max_discount'			=> $this->input->post('a_max_discount'),
            'discount_type'         => $this->input->post('a_discount_type'),
			'alert_quantity'	    => $this->input->post('alert_quantity'),
			'taxable'				=> 1,
			'note'					=> $this->input->post('note'),
			'category'				=> $this->input->post('category_id'),
			'sub_category'			=> $this->input->post('sub_category'),		
			'is_serialized'			=> $this->input->post('is_serialized'),
            'universal' 			=> $this->input->post('universal') ? $this->input->post('universal') : $this->mSettings->universal_accessories,
			'store_id'				=> $this->activeStore,
            'warranty_id'           => $this->input->post('warranty_id'),
            'quick_sale'              => 1,

		);

        if ($row = $this->addAjaxProduct($data)) {
            $row->type = 'accessory';
            $row->cost = 1;
            $row->unit_cost = 1;
            $row->discount = 0;
            $row->qty = 1;
            $pr = array(
                'item_id' => uniqid(), 
                'id'	=> (int)$row->id,
                'label' => $row->name . " (" . $row->code . ")", 
                'row' => $row,
                'pr_tax' => (isset($o_taxes)) ? $o_taxes : null,
            );
            $this->repairer->send_json(array('msg' => 'success', 'result' => $pr));
        } else {
            exit(json_encode(array('msg' => lang('failed_to_add_product'))));
        }
       
    }
    public function addAjaxProduct($data)
    {

        if ($this->db->insert('accessory', $data)) {
            $product_id = $this->db->insert_id();
            return $this->db->select('*, name as name, upc_code as code, tax_id as tax_rates, taxable, is_serialized')->where('id', $product_id)->get('accessory')->row();
        }
        return false;
    }

	public function update_qs() {
		$id = $this->input->post('row_id');
		$val = $this->input->post('val1');
		$this->db->where('id', $id);
		$this->db->update('accessory', array('quick_sale' => $val));
		echo "true";
	}


    private function isDeletable($id) {
        $q = $this->db->where('item_type', 'accessory')->where('product_id', $id)->get('sale_items');
        if ($q->num_rows() > 0) {
            return FALSE;
        }
        return TRUE;

    }

    public function delete() {
        $id = $this->input->post('id');
        if ($this->isDeletable($id)) {
            $this->db->where('inventory_type', 'accessory')->where('inventory_id', $id)->delete('stock');

            $this->db->where('id', $id)->delete('accessory');
        }else{
            $this->db->where('id', $id)->update('accessory', array('deleted' => 1));
        }
        echo 'true';
    }

	public function index($type = NULL) {

		$this->mPageTitle = "Accessories";
		if ($type === 'disabled' || $type === 'enabled') {
        	$this->data['toggle_type'] = $type;
		}else{
        	$this->data['toggle_type'] = NULL;
		}
        $this->data['tax_rates'] = $this->settings_model->getTaxRates();
		$this->data['categories'] = $this->settings_model->getAllCategories();
		$this->data['subcategories'] = $this->settings_model->getAllCategories(FALSE);
        $this->data['frm_priv'] = $this->settings_model->getMandatory('accessory');
        $this->data['cat_filter'] = $this->settings_model->getCategoriesTree();
        $this->data['warranty_plans'] = $this->settings_model->getAllWarranties();
		$this->render("accessory/index");
	}

	// GENERATE THE AJAX TABLE CONTENT //
    public function getAllAccessories($type = NULL)
    {

    	$this->load->library('datatables');
        $this->datatables
            ->select('id, name, upc_code, price as price, max_discount, discount_type, quick_sale, deleted') 
            ->from('accessory');
  //       if ($type === 'disabled') {
  //       	$this->datatables->where('deleted', 1);
		// } elseif($type === 'enabled') {
        	$this->datatables->where('deleted', 0);
		// }
        if ($this->input->get('cat_id')) {
            $this->datatables->where('category', $this->input->get('cat_id'));
        }
        if ($this->input->get('sub_id')) {
            $this->datatables->where('sub_category', $this->input->get('sub_id'));
        }
		$this->datatables->where('(universal=1 OR store_id='.$this->activeStore.')',NULL, FALSE);
        $this->datatables->add_column('max_discount', '$1__$2', 'max_discount, discount_type');
        $this->datatables->add_column('quick_sale', '$1__$2', 'id, quick_sale');
        $this->datatables->add_column('actions', "$1___$2", 'id, deleted');
        // $this->datatables->unset_column('id');
        $this->datatables->unset_column('quick_sale');
        $this->datatables->unset_column('max_discount');
        $this->datatables->unset_column('discount_type');
        $this->datatables->unset_column('deleted');
        echo $this->datatables->generate();
    }
	
	public function getAccessoryByID()
	{
		$id = $this->input->post('id');
		$this->db->where('id', $id);
		$row = $this->db->get('accessory')->row();
		echo json_encode($row);
	}

	public function add()
	{
        $upc = $this->settings_model->UPCCodeExists($this->input->post('code'));
        if ($upc) {
            $this->repairer->send_json(array('success'=>false, 'message'=>lang('code_already_exists', $upc->name, humanize($upc->type))));
        }

		$this->repairer->checkPermissions();
		$data = array(
			'name'					=> $this->input->post('a_name'),
			'upc_code'				=> $this->input->post('a_upc_code'),
			'price'					=> $this->input->post('a_price'),
			'max_discount'			=> $this->input->post('a_max_discount'),
			'discount_type'			=> $this->input->post('a_discount_type'),
			'taxable'				=> $this->input->post('taxable'),
			'note'					=> $this->input->post('note'),
			'category'				=> $this->input->post('category_id'),
			'sub_category'			=> $this->input->post('sub_category'),	
			'is_serialized'			=> $this->input->post('is_serialized'),
            'alert_quantity'        => $this->input->post('alert_quantity'),
            'universal' => $this->input->post('universal') ? $this->input->post('universal') : $this->mSettings->universal_accessories,
            'store_id'              => $this->activeStore,
            'warranty_id'           => $this->input->post('warranty_id'),
		);

	

		$insert = $this->db->insert('accessory', $data);
		if ($this->db->insert_id()) {
            $this->repairer->send_json(array('success'=>true, 'message' => lang('Accessory added successfully')));
		}else{
            $this->repairer->send_json(array('success'=>false, 'message'=>lang('Unable to add entry')));
		}
	}
	public function edit()
	{
		$this->repairer->checkPermissions();
		$id = $this->input->post('id');
        $product = $this->settings_model->getByTypeAndID('accessory', $id);
        $upc = $this->settings_model->UPCCodeExists($this->input->post('a_upc_code'));
        if ($this->input->post('a_upc_code') !== $product->upc_code) {
            if ($upc) {
                echo json_encode(array('success'=>false, 'message'=>lang('code_already_exists', $upc->name, humanize($upc->type))));
                die();
            }
        }
        
		$data = array(
			'name'					=> $this->input->post('a_name'),
			'upc_code'				=> $this->input->post('a_upc_code'),
			'price'					=> $this->input->post('a_price'),
			'max_discount'			=> $this->input->post('a_max_discount'),
			'discount_type'			=> $this->input->post('a_discount_type'),
			'taxable'				=> $this->input->post('taxable'),
			'note'					=> $this->input->post('note'),
			'category'				=> $this->input->post('category_id'),
			'sub_category'			=> $this->input->post('sub_category'),		
			'is_serialized'			=> $this->input->post('is_serialized'),
            'alert_quantity'        => $this->input->post('alert_quantity'),
            'warranty_id'           => $this->input->post('warranty_id'),
            'universal' => $this->input->post('universal') ? $this->input->post('universal') : $this->mSettings->universal_accessories,
		);

		$this->db->trans_start();
		$this->db->where('id', $id);
		$this->db->update('accessory',$data);
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
            $this->repairer->send_json(array('success'=>false, 'message'=>lang('Unable to edit entry')));
		}else{
            $this->repairer->send_json(array('success'=>true, 'message' => lang('Accessory edited successfully')));
		}
	}

	function actions()
    {
        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->inventory_model->deleteProduct($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("products_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {


                    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();

                    $sheet->setTitle(lang('accessories'));
                    $sheet->SetCellValue('A1', lang('name'));
                    $sheet->SetCellValue('B1', lang('upc_code'));
                    $sheet->SetCellValue('C1', lang('price'));
                    $sheet->SetCellValue('D1', lang('max_discount'));


                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $product = $this->db->get_where('accessory', array('id'=>$id))->row();
                        $sheet->SetCellValue('A' . $row, $product->name);
                        $sheet->SetCellValue('B' . $row, $product->upc_code);
                        $sheet->SetCellValue('C' . $row, $product->price);
                        if ($product->discount_type == 1) {
                            $product->max_discount = $product->max_discount.'%';
                        }else{
                            $product->max_discount = $this->repairer->formatMoney($product->max_discount);
                        }
                        $sheet->SetCellValue('D' . $row, $product->max_discount);
                        $row++;
                    }

                    $sheet->getColumnDimension('A')->setWidth(30);
                    $sheet->getColumnDimension('B')->setWidth(20);
                    $sheet->getColumnDimension('C')->setWidth(15);
                    $sheet->getColumnDimension('D')->setWidth(10);


                    $sheet->getParent()->getDefaultStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    $sheet->getStyle('C2:C' . ($row - 1))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


                    $filename = 'accessoriers_' . date('Y_m_d_H_i_s');
                  
                   
                    if ($this->input->post('form_action') == 'export_excel') {
                        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
                        header('Cache-Control: max-age=0');

                        $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
                        $writer->save('php://output');
                        exit();
                    }

                    if ($this->input->post('form_action') == 'export_pdf') {
                        $styleArray = [
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                    'color' => ['argb' => 'FFFF0000'],
                                ],
                            ],
                        ];
                        $sheet->getStyle('A0:D'.($row-1))->applyFromArray($styleArray);
                        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                        header('Content-Type: application/pdf');
                        header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
                        header('Cache-Control: max-age=0');
                        $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Mpdf');
                        $writer->save('php://output');
                    }



                    redirect($_SERVER["HTTP_REFERER"]);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_product_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

}