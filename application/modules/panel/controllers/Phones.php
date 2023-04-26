<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Phones extends Auth_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->library('repairer');
	}
	public function update_qs() {
		$id = $this->input->post('row_id');
		$val = $this->input->post('val1');
		$this->db->where('id', $id);
		$this->db->update('phones', array('quick_sale' => $val));
		echo "true";
	}
	function toggle()
	{
        $toggle = $this->input->post('toggle');
        if ($toggle == 'enable') {
            $data = array(
                'disable' => 0,
            );
            $a = lang('enabled');
        }else{
            $data = array(
                'disable' => 1,
            );
            $a = lang('disabled');
        }
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('phones', $data);
        echo json_encode(array('ret' => 'true', 'toggle' => $a));
	}
	function addByAjax()
    {
        if ($this->input->get('token') && $this->input->get('token') == $this->session->userdata('user_csrf') && $this->input->is_ajax_request()) {
            $product = $this->input->get('product');
            $product['date_added'] = date('Y-m-d');
            $product['store_id'] = $this->activeStore;
            
            if ($row = $this->addAjaxProduct($product)) {
                $row->type = 'new_phone';
                $row->cost = 0;
                $row->unit_cost = 0;
                $row->discount = 0;
                $row->is_serialized = 1;
                $row->qty = 1;
                $pr = array(
                    'item_id' => uniqid(), 
                    'id'	=> (int)$row->id,
                    'label' => $row->phone_name . " (" . $row->code . ")", 
                    'row' => $row,
                    'pr_tax' => null,
                );
                $this->repairer->send_json(array('msg' => 'success', 'result' => $pr));
            } else {
                exit(json_encode(array('msg' => lang('failed_to_add_product'))));
            }
        } else {
            json_encode(array('msg' => lang('Invalid token')));
        }

    }
    function addUsedByAjax() {
        if ($this->input->get('token') && $this->input->get('token') == $this->session->userdata('user_csrf') && $this->input->is_ajax_request()) {
            $product = $this->input->get('product');
            $product['date_acquired'] = date('Y-m-d');
            $product['date_added'] = date('Y-m-d');
            $product['store_id'] = $this->activeStore;
           
            if ($row = $this->addAjaxProduct($product)) {
                $row->type = 'used_phone';
                $row->cost = 0;
                $row->unit_cost = 0;
                $row->discount = 0;
                $row->qty = 1;
                $row->is_serialized = 1;
                $pr = array(
                    'item_id' => uniqid(), 
                    'id'	=> (int)$row->id,
                    'label' => $row->phone_name . " (" . $row->code . ")", 
                    'row' => $row,
                    'pr_tax' => null,
                );
                $this->repairer->send_json(array('msg' => 'success', 'result' => $pr));
            } else {
                exit(json_encode(array('msg' => lang('failed_to_add_product'))));
            }
        } else {
            json_encode(array('msg' => lang('Invalid token')));
        }
    }

    public function addAjaxProduct($data) {
        if ($this->db->insert('phones', $data)) {
            $product_id = $this->db->insert_id();
            return $this->db->select('*, phone_name as name, model_name as code, tax_id as tax_rates')->where('id', $product_id)->get('phones')->row();
        }
        return false;
    }

	public function addmore() {
		$this->load->view("phones/add_row");
	}

	private function isPhoneDeletable($id) {
		$q = $this->db->where('item_type', 'phones')->where('product_id', $id)->get('sale_items');
		if ($q->num_rows() > 0) {
            return FALSE;
		}
		return TRUE;
	}

	public function delete() {
		$id = $this->input->post('id');
		if ($this->isPhoneDeletable($id)) {
			$this->db->where('inventory_type', 'phones')->where('inventory_id', $id)->delete('stock');
			$this->db->where('id', $id)->delete('phones');
		}else{
			$this->db->where('id', $id)->update('phones', array('disable' => 1));
		}
		echo 'true';
	}

	public function index($type = 'new') {
		redirect('panel/phones/view/'.$type);
	}
	public function view($type = 'new', $type2 = NULL) {
		$this->mPageTitle = ucfirst($type)." Phones";
		$this->data['type'] = $type;
		$this->data['type2'] = $type2;
        $this->data['cat_filter'] = $this->settings_model->getCategoriesTree();
        $this->data['saps'] = $this->settings_model->getAllSAPs();
		$this->render("phones/index");
	}

	// GENERATE THE AJAX TABLE CONTENT //
    public function getAllPhones($type, $used_type = NULL)
    {
        if ($type == 'new') {
        	$this->load->library('datatables');
	        $this->datatables
	            ->select('phones.id as id, type, phone_name, manufacturers.name, model_name, price,(SELECT COUNT(id) FROM stock WHERE stock.inventory_id = phones.id AND stock.inventory_type = "phones") as count, quick_sale, phones.disable as disable') 
	            ->join('manufacturers', 'phones.manufacturer_id=manufacturers.id', 'left')
	            ->from('phones')
				->where('phones.store_id', $this->activeStore)
	            ->where('type', $type)
	            ->where('phones.sold', 0);
        		// $this->datatables->edit_column('max_discount', '$1__$2', 'max_discount, discount_type');
        		$this->datatables->unset_column('discount_type');
	            $this->datatables->where('phones.disable', 0);
        		// 
			   	// if ($used_type == 'enabled') {
	      //       	// $this->datatables->where('phones.disable', 0);
			    // }elseif($used_type == 'disabled') {
	      //       	$this->datatables->where('phones.disable', 1);
			    // }
	        
        }else{
        	
        	$this->load->library('datatables');
	        $this->datatables
	            ->select('phones.id as id, type, phone_name, (SELECT GROUP_CONCAT( phone_items.imei SEPARATOR \'<br>\') FROM phone_items WHERE phone_items.phone_id = phones.id GROUP BY phones.id) as imei, (SELECT GROUP_CONCAT( phone_items.cost SEPARATOR \'<br>\') FROM phone_items WHERE phone_items.phone_id = phones.id GROUP BY phones.id) as cost, (SELECT GROUP_CONCAT( phone_items.price SEPARATOR \'<br>\') FROM phone_items WHERE phone_items.phone_id = phones.id GROUP BY phones.id) as price, manufacturers.name, model_name, used_status, cosmetic_condition, operational_condition, unlocked,quick_sale, sold, phones.disable as disable') 
	            ->join('manufacturers', 'phones.manufacturer_id=manufacturers.id', 'left')
	            ->from('phones')
	            // ->where('phones.sold', 0)
				->where('phones.store_id', $this->activeStore)
	            ->where('type', $type);
	            
	            $this->datatables->where('phones.disable', 0);
			    if ($used_type == 'ready') {
	            	$this->datatables->where('phones.used_status', 1);
			    }elseif ($used_type == 'repairs') {
	            	$this->datatables->where('phones.used_status', 2);
			    }elseif ($used_type == 'hold') {
	            	$this->datatables->where('phones.used_status', 3);
			    }

        }
        if ($this->input->get('cat_id')) {
            $this->datatables->where('category', $this->input->get('cat_id'));
        }
        if ($this->input->get('sub_id')) {
            $this->datatables->where('sub_category', $this->input->get('sub_id'));
        }
        $this->datatables->add_column('quick_sale', '$1__$2', 'id, quick_sale');
        $this->datatables->unset_column('quick_sale');
        $this->datatables->add_column('actions', "$1___$2___$3", 'id, type, disable');

        $this->datatables->unset_column('type');
        $this->datatables->unset_column('disable');

        echo $this->datatables->generate();
    }
	public function add($type = NULL) {
		if (!$type) {
			redirect('panel/phones/add/new');
		}
		if ($type == 'new') {
    		$this->repairer->checkPermissions('add_new');
		}
		if ($type == 'used') {
    		$this->repairer->checkPermissions('add_used');
		}
		$this->data['type'] = $type;
		$this->mPageTitle = lang('add')." ".lang($type)." ".lang('Phone');

		$this->load->model('repair_model');
		$this->form_validation->set_rules('phone_name', lang('phone_name'), 'trim|required|trim');
		if ($this->form_validation->run() == FALSE) {
			$this->data['manufacturers'] = $this->settings_model->getManufacturers();
			$this->data['carriers'] = $this->settings_model->getCarriers();
            $this->data['tax_rates'] = $this->settings_model->getTaxRates();
            $this->data['categories'] = $this->settings_model->getAllCategories();
			$this->data['subcategories'] = $this->settings_model->getAllCategories(FALSE);
        	$this->data['warranty_plans'] = $this->settings_model->getAllWarranties();
        	$this->data['saps'] = $this->settings_model->getAllSAPs();

			if ($type=='new') {
            	$this->data['frm_priv'] = $this->settings_model->getMandatory('new_phones');
			}else{
            	$this->data['frm_priv'] = $this->settings_model->getMandatory('used_phones');
			}

			$this->render('phones/add');
		} else {
			$phone_data = array();

			$phone_data['phone_name'] = $this->input->post('phone_name');
			$phone_data['model_name'] = $this->input->post('model');
			$phone_data['manufacturer_id'] = $this->input->post('manufacturer');
			$phone_data['carrier_id'] = $this->input->post('carrier') ?? null;
			$phone_data['description'] = $this->input->post('description');
			$phone_data['max_discount'] = $this->input->post('max_discount');
			$phone_data['discount_type'] = $this->input->post('discount_type');
			$phone_data['max_discount2'] = $this->input->post('max_discount2');
			$phone_data['discount_type2'] = $this->input->post('discount_type2');
			$phone_data['taxable'] = $this->input->post('taxable');
			$phone_data['store_id'] = $this->activeStore;
			$phone_data['category'] = $this->input->post('category_id');
			$phone_data['sub_category'] = $this->input->post('sub_category');
			$phone_data['date_added'] = date('Y-m-d H:i:s');
			$phone_data['warranty_id'] = $this->input->post('warranty_id');
			$phone_data['activation_price'] = $this->input->post('activation_price');
			$phone_data['s_activation_plan'] = $this->input->post('activation_plan') ? $this->input->post('activation_plan') : NULL;
			$phone_data['quick_sale'] = 1;

		

			if ($type == 'new') {
				$phone_data['type'] = 'new';
				$phone_data['price'] = $this->input->post('price');
				$phone_data['alert_quantity'] = $this->input->post('alert_quantity');

			}else{
				$phone_data['type'] = 'used';
				$phone_data['cosmetic_condition'] = $this->input->post('cosmetic_condition');
				$phone_data['operational_condition'] = $this->input->post('operational_condition');
				$phone_data['date_acquired'] = date('Y-m-d');
				$phone_data['used_status'] = $this->input->post('phone_status');
				$phone_data['unlocked'] = $this->input->post('unlock_status');
			}

			if ($this->db->insert('phones', $phone_data)) {
				$id = $this->db->insert_id();
				if ($type == 'used') {
					$phones = array();
					if ($this->input->post('imei')) {
			            $i = sizeof($this->input->post('imei'));
			            for ($r = 0; $r < $i; $r++) {
			                $imei = $this->security->xss_clean($this->input->post('imei')[$r]);
			                $cost = $this->security->xss_clean($this->input->post('purchase_price')[$r]);
			                $price = $this->security->xss_clean($this->input->post('list_price')[$r]);
			                $phones[] = array(
			                	'phone_id' => $id,
			                    'imei' => $this->security->xss_clean($imei),
			                    'cost' => $this->security->xss_clean($cost),
			                    'price' =>  $this->security->xss_clean($price),
			                );
			            }
			        }
	            	$this->db->insert_batch('phone_items', $phones);
			    }

			    $this->session->set_flashdata('message', lang('Phone added successfully'));
            	redirect('panel/phones/view/'.$type);
			}else{
				$this->session->set_flashdata('message', lang('error adding Phone'));
            	redirect('panel/phones/view/'.$type);
			}


            
		}
	}

	public function edit($type = NULL, $id = NULL) {
		if (!$type) {
			redirect('panel/phones/add/new');
		}
		if ($type == 'new') {
    		$this->repairer->checkPermissions('edit_new');
		}
		if ($type == 'used') {
    		$this->repairer->checkPermissions('edit_used');
		}
		$this->data['type'] = $type;
		$this->mPageTitle = lang('Edit')." ".lang($type)." ".lang('Phone');

		$this->load->model('repair_model');
		$this->form_validation->set_rules('phone_name', 'name', 'trim|required|trim');
		
		
		if ($this->form_validation->run() == FALSE) {
			$this->data['phone'] = $this->db->where('id', $id)->get('phones')->row();
			$this->data['phone_items'] = $this->db->where(array('phone_id'=> $id, 'sold'=> 0))->get('phone_items')->result();
			if ($type == 'used' && !$this->data['phone_items']) {
				$this->session->set_flashdata('error', lang('This phone cannot be edited'));
				redirect('panel/phones/view/'.$type);
			}
			$this->data['manufacturers'] = $this->settings_model->getManufacturers();
			$this->data['carriers'] = $this->settings_model->getCarriers();
            $this->data['tax_rates'] = $this->settings_model->getTaxRates();
            $this->data['categories'] = $this->settings_model->getAllCategories();
			$this->data['subcategories'] = $this->settings_model->getAllCategories(FALSE);
        	$this->data['warranty_plans'] = $this->settings_model->getAllWarranties();
        	$this->data['saps'] = $this->settings_model->getAllSAPs();

			if ($type=='new') {
            	$this->data['frm_priv'] = $this->settings_model->getMandatory('new_phones');
			}else{
            	$this->data['frm_priv'] = $this->settings_model->getMandatory('used_phones');
			}
			$this->render('phones/edit');
		} else {
			$phone_data = array();

			$phone_data['phone_name'] = $this->input->post('phone_name');
			$phone_data['model_name'] = $this->input->post('model');
			$phone_data['manufacturer_id'] = $this->input->post('manufacturer');
			$phone_data['carrier_id'] = $this->input->post('carrier');
			$phone_data['description'] = $this->input->post('description');
			$phone_data['max_discount'] = $this->input->post('max_discount');
			$phone_data['discount_type'] = $this->input->post('discount_type');
			$phone_data['max_discount2'] = $this->input->post('max_discount2');
			$phone_data['discount_type2'] = $this->input->post('discount_type2');
			$phone_data['taxable'] = $this->input->post('taxable');
			$phone_data['category'] = $this->input->post('category_id');
			$phone_data['sub_category'] = $this->input->post('sub_category');
			$phone_data['warranty_id'] = $this->input->post('warranty_id');
			$phone_data['activation_price'] = $this->input->post('activation_price');
			$phone_data['date_added'] = date('Y-m-d H:i:s');
			$phone_data['s_activation_plan'] = $this->input->post('activation_plan') ? $this->input->post('activation_plan') : NULL;

			if ($type == 'new') {
				$phone_data['type'] = 'new';
				$phone_data['price'] = $this->input->post('price');
				$phone_data['alert_quantity'] = $this->input->post('alert_quantity');
			}else{
				$phone_data['type'] = 'used';
				$phone_data['cosmetic_condition'] = $this->input->post('cosmetic_condition');
				$phone_data['operational_condition'] = $this->input->post('operational_condition');
				$phone_data['used_status'] = $this->input->post('phone_status');
				$phone_data['unlocked'] = $this->input->post('unlock_status');
			}

			$this->db->where('id', $id);
			$this->db->update('phones', $phone_data); 
			
			if ($type == 'used') {
				$phones = array();
				if ($this->input->post('imei')) {
		            $i = sizeof($this->input->post('imei'));
		            for ($r = 0; $r < $i; $r++) {
		                $imei = $this->security->xss_clean($this->input->post('imei')[$r]);
		                $cost = $this->security->xss_clean($this->input->post('purchase_price')[$r]);
		                $price = $this->security->xss_clean($this->input->post('list_price')[$r]);
		                $phones[] = array(
		                	'phone_id' => $id,
		                    'imei' => $this->security->xss_clean($imei),
		                    'cost' => $this->security->xss_clean($cost),
		                    'price' =>  $this->security->xss_clean($price),
		                );
		            }
		        }
		        $this->db->where(array('phone_id' => $id, 'sold' => 0));
	            $this->db->delete('phone_items');
	            $this->db->insert_batch('phone_items', $phones);
	        }

            $this->session->set_flashdata('message', lang('Phone edited successfully'));
            redirect('panel/phones/edit/'.$type.'/'.$id);
		}
	}

	function misc_actions($type = 'new')
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
                	if ($type == 'used') {
						$products = $this->db
						            ->select('phones.id as id, type, phone_name, (SELECT GROUP_CONCAT( phone_items.imei SEPARATOR \'<br>\') FROM phone_items WHERE phone_items.phone_id = phones.id GROUP BY phones.id) as imei, (SELECT GROUP_CONCAT( phone_items.cost SEPARATOR \'<br>\') FROM phone_items WHERE phone_items.phone_id = phones.id GROUP BY phones.id) as cost, (SELECT GROUP_CONCAT( phone_items.price SEPARATOR \'<br>\') FROM phone_items WHERE phone_items.phone_id = phones.id GROUP BY phones.id) as price, manufacturers.name as mname, model_name, used_status, cosmetic_condition, operational_condition, unlocked,quick_sale, phones.disable as disable') 
						            ->join('manufacturers', 'phones.manufacturer_id=manufacturers.id', 'left')
						            ->from('phones')
									->where('phones.store_id', $this->activeStore)
						            ->where('type', $type)
						            ->where_in('phones.id', $_POST['val'])
						            ->get();
		                if ($products->num_rows() > 0) {
		                	$products = $products->result();


		                	$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                    		$sheet = $spreadsheet->getActiveSheet();

		                    $sheet->setTitle(lang('Used Phones'));
		                    $sheet->SetCellValue('A1', lang('Phone Name'));
		                    $sheet->SetCellValue('B1', lang('IMEI'));
		                    $sheet->SetCellValue('C1', lang('cost'));
		                    $sheet->SetCellValue('D1', lang('price'));
		                    $sheet->SetCellValue('E1', lang('Manufacturer'));
		                    $sheet->SetCellValue('F1', lang('Model'));
		                    $sheet->SetCellValue('G1', lang('Status'));
		                    $sheet->SetCellValue('H1', lang('Cosmetic Condition'));
		                    $sheet->SetCellValue('I1', lang('Operational Condition'));
		                    $sheet->SetCellValue('J1', lang('Unlocked'));

		                    $row = 2;

		                    foreach ($products as $product) {
		                        $sheet->SetCellValue('A' . $row, $product->phone_name);
		                        $sheet->SetCellValue('B' . $row, $product->imei);
		                        $sheet->SetCellValue('C' . $row, $product->cost);
		                        $sheet->SetCellValue('D' . $row, $product->price);
		                        $sheet->SetCellValue('E' . $row, $product->mname);
		                        $sheet->SetCellValue('F' . $row, $product->model_name);
		                        $used_status = $this->repairer->getUsedStatus();
		                        $sheet->SetCellValue('G' . $row, $used_status[$product->used_status]);


		                        $sheet->SetCellValue('H' . $row, $product->cosmetic_condition.'/5');
		                        $sheet->SetCellValue('I' . $row, $product->operational_condition.'/5');
	                        	$unlock_status = $this->repairer->getUnlockStatus();
		                        $sheet->SetCellValue('J' . $row, $unlock_status[$product->unlocked]);
		                        
		                        $row++;
		                    }
		                    $sheet->getColumnDimension('A')->setWidth(20);
		                    $sheet->getColumnDimension('B')->setWidth(20);
		                    $sheet->getColumnDimension('C')->setWidth(10);
		                    $sheet->getColumnDimension('D')->setWidth(10);
		                    $sheet->getColumnDimension('E')->setWidth(15);
		                    $sheet->getColumnDimension('F')->setWidth(20);
		                    $sheet->getColumnDimension('G')->setWidth(20);
		                    $sheet->getColumnDimension('H')->setWidth(20);
		                    $sheet->getColumnDimension('I')->setWidth(20);
		                    $sheet->getColumnDimension('J')->setWidth(20);
		                    


	                    	$sheet->getParent()->getDefaultStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

		                    $filename = 'used_phones_' . date('Y_m_d_H_i_s');
		                    

		                    
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
		                        $sheet->getStyle('A0:J'.($row-1))->applyFromArray($styleArray);
		                        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		                        header('Content-Type: application/pdf');
		                        header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
		                        header('Cache-Control: max-age=0');
		                        $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Mpdf');
		                        $writer->save('php://output');
		                    }


		                    redirect($_SERVER["HTTP_REFERER"]);
		                }else{
		                	 $this->session->set_flashdata('error', $this->lang->line("no_product_selected"));
	                		redirect($_SERVER["HTTP_REFERER"]);
		                }
                	}else{
						$products = $this->db
	            				->select('phones.id as id, type, phone_name, (SELECT GROUP_CONCAT( phone_items.imei SEPARATOR \'<br>\') FROM phone_items WHERE phone_items.phone_id = phones.id GROUP BY phones.id) as imei, (SELECT GROUP_CONCAT( phone_items.cost SEPARATOR \'<br>\') FROM phone_items WHERE phone_items.phone_id = phones.id GROUP BY phones.id) as cost, (SELECT GROUP_CONCAT( phone_items.price SEPARATOR \'<br>\') FROM phone_items WHERE phone_items.phone_id = phones.id GROUP BY phones.id) as price, manufacturers.name as mname, model_name, max_discount,quick_sale, phones.disable as disable') 
						            ->join('manufacturers', 'phones.manufacturer_id=manufacturers.id', 'left')
						            ->from('phones')
									->where('phones.store_id', $this->activeStore)
						            ->where('type', $type)
						            ->where_in('phones.id', $this->security->xss_clean($_POST['val']))
						            ->get();
		                if ($products->num_rows() > 0) {
		                	$products = $products->result();

		                    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		                    $sheet = $spreadsheet->getActiveSheet();

		                    $sheet->setTitle(lang('New Phones'));
		                    $sheet->SetCellValue('A1', lang('Phone Name'));
		                    $sheet->SetCellValue('B1', lang('IMEI'));
		                    $sheet->SetCellValue('C1', lang('cost'));
		                    $sheet->SetCellValue('D1', lang('price'));
		                    $sheet->SetCellValue('E1', lang('Manufacturer'));
		                    $sheet->SetCellValue('F1', lang('Model'));

		                    $row = 2;

		                    foreach ($products as $product) {
		                        $sheet->SetCellValue('A' . $row, $product->phone_name);
		                        $sheet->SetCellValue('B' . $row, $product->imei);
		                        $sheet->SetCellValue('C' . $row, $product->cost);
		                        $sheet->SetCellValue('D' . $row, $product->price);
		                        $sheet->SetCellValue('E' . $row, $product->mname);
		                        $sheet->SetCellValue('F' . $row, $product->model_name);
		                      
		                        $row++;
		                    }
		                    $sheet->getColumnDimension('A')->setWidth(20);
		                    $sheet->getColumnDimension('B')->setWidth(20);
		                    $sheet->getColumnDimension('C')->setWidth(10);
		                    $sheet->getColumnDimension('D')->setWidth(10);
		                    $sheet->getColumnDimension('E')->setWidth(15);
		                    $sheet->getColumnDimension('F')->setWidth(20);
		                   
                    		$sheet->getParent()->getDefaultStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
		                    $filename = 'new_phones_' . date('Y_m_d_H_i_s');

		                    
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
		                        $sheet->getStyle('A0:F'.($row-1))->applyFromArray($styleArray);
		                        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		                        header('Content-Type: application/pdf');
		                        header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
		                        header('Cache-Control: max-age=0');
		                        $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Mpdf');
		                        $writer->save('php://output');
		                    }

		                    redirect($_SERVER["HTTP_REFERER"]);
		                }else{
		                	 $this->session->set_flashdata('error', $this->lang->line("no_product_selected"));
	                		redirect($_SERVER["HTTP_REFERER"]);
		                }
                	}
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

    public function assign_sap()
    {
    	$id = $this->input->post('id');
    	if ($id && is_numeric($id)) {
    		$q = $this->db->select('s_activation_plan')->get_where('phones', array('id'=> $id));
    		if ($q->num_rows() > 0 ) {
    			$this->repairer->send_json(array('success'=>true, 'data' => $q->row()));
    			return TRUE;
    		}
    	}
    	$this->repairer->send_json(array('success'=>false));
		return FALSE;
    }

    public function assign_sap_save() {
    	$id = $this->input->post('id');
    	$sap = $this->input->post('sap');
    	if ($id && is_numeric($id) && $sap && is_numeric($sap)) {
    		$q = $this->db
    			->update('phones', array('s_activation_plan'=>$sap), array('id'=>$id));
    		$this->repairer->send_json(array('success'=>true));
    	}
    	$this->repairer->send_json(array('success'=>false, 'message' => "Error Occured."));
    }


}