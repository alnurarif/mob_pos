<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Repairer
 *
 * @author      Repairer
 * @copyright   Copyright (c) otsglobal.org
 * @link        https://otsglobal.org
 */

/**
 * Class Cron
 */
class Cron extends MY_Controller
{

    public function index(){

        // Update the next recur date for the recurring invoice
        $this->entries_recurring();
        $this->payroll_recurring();
        
    }

    private function payroll_recurring()
    {
        //check for recurring payroll
        $payrolls = $this->db->where('recurring', 1)->get('payroll');
        if ($payrolls->num_rows() > 0) {
            $payrolls = $payrolls->result();
            foreach ($payrolls as $payroll) {
                if (empty($payroll->recur_end_date) || $payroll->recur_end_date == null) {
                    if ($payroll->recur_next_date == date("Y-m-d")) {

                        $date = explode('-', date("Y-m-d"));

                        $datetime1 = new DateTime($payroll->from_date);
                        $datetime2 = new DateTime($payroll->to_date);
                        $interval = $datetime1->diff($datetime2);
                        $interval->format('%R%a days');

                        $from_date = date('Y-m-d');
                        $to_date = date('Y-m-d', strtotime($from_date . $interval->format('%R%a days')));

                        $data = array(
                            'payroll_template_id' => $payroll->payroll_template_id,
                            'user_id' => $payroll->user_id,
                            'employee_name' => $payroll->employee_name,
                            'business_name' => $payroll->business_name,
                            'payment_method' => $payroll->payment_method,
                            'bank_name' => $payroll->bank_name,
                            'account_number' => $payroll->account_number,
                            'description' => $payroll->description,
                            'comments' => $payroll->comments,
                            'paid_amount' => $payroll->paid_amount,
                            'from_date' => $from_date,
                            'to_date' => $to_date,
                        );
                        $this->db->insert('payroll', $data);
                        $payroll_id = $this->db->insert_id();


                        $recur_next_date = date_format(date_add(date_create(date("Y-m-d")), date_interval_create_from_date_string($payroll->recur_frequency . ' ' . $payroll->recur_type . 's')), 'Y-m-d');
                        $this->db->where('id', $payroll->id)->update('payroll',array('recur_next_date' => $recur_next_date));

                        $payroll_metas = $this->db->where('payroll_id', $payroll->id)->get('payroll_meta');
                        if ($payroll_metas->num_rows() > 0) {
                            $payroll_metas = $payroll_metas->result();
                            foreach ($payroll_metas as $payroll_meta) {
                                $data = array(
                                    'value' => $payroll_meta->value,
                                    'payroll_id' => $payroll_id,
                                    'payroll_template_meta_id' => $payroll_meta->payroll_template_meta_id,
                                    'position' => $payroll_meta->position,
                                    'name' => $payroll_meta->name,
                                );
                                $this->db->insert('payroll_meta', $data);
                            }
                        }
                      
                    } else {
                        if (date("Y-m-d") <= $payroll->recur_end_date) {

                            if ($payroll->recur_next_date == date("Y-m-d")) {

                                $date = explode('-', date("Y-m-d"));

                                $data = array(
                                    'payroll_template_id' => $payroll->payroll_template_id,
                                    'user_id' => $payroll->user_id,
                                    'employee_name' => $payroll->employee_name,
                                    'business_name' => $payroll->business_name,
                                    'payment_method' => $payroll->payment_method,
                                    'bank_name' => $payroll->bank_name,
                                    'account_number' => $payroll->account_number,
                                    'description' => $payroll->description,
                                    'comments' => $payroll->comments,
                                    'paid_amount' => $payroll->paid_amount,
                                    'date' => date("Y-m-d"),

                                );
                                //save payroll meta
                                //
                                $this->db->insert('payroll', $data);
                                $payroll_id = $this->db->insert_id();
                                

                                $payroll_metas = $this->db->where('payroll_id', $payroll->id)->get('payroll_meta');
                                if ($payroll_metas->num_rows() > 0) {
                                    $payroll_metas = $payroll_metas->result();
                                    foreach ($payroll_metas as $payroll) {
                                        $data = array(
                                            'value' => $payroll_meta->value,
                                            'payroll_id' => $payroll_id,
                                            'payroll_template_meta_id' => $payroll_meta->payroll_template_meta_id,
                                            'position' => $payroll_meta->position,
                                            'name' => $payroll_meta->name,
                                        );
                                        $this->db->insert('payroll_meta', $data);
                                    }
                                }

                                $recur_next_date = date_format(date_add(date_create(date("Y-m-d")), date_interval_create_from_date_string($payroll->recur_frequency . ' ' . $payroll->recur_type . 's')),  'Y-m-d');
                                $this->db->where('id', $payroll->id)->update('payroll',array('recur_next_date' => $recur_next_date));
                            }
                        }
                    }
                }
            }
        }
        
    }

    private function entries_recurring()
    {
        $entries = $this->db->where('recurring', 1)->get('entries');
        if ($entries->num_rows() > 0) {
            $entries = $entries->result();
            foreach ($entries as $entry) {
                if (empty($entry->recur_end_date)) {
                    if ($entry->recur_next_date == date("Y-m-d")) {
                        $date = explode('-', date("Y-m-d"));
                        $data = array(
                            'type' => $entry->type,
                            'type_id' => $entry->type_id,
                            'amount' => $entry->amount,
                            'notes' => $entry->notes,
                            'date' => $entry->date,
                            'files' => serialize(array()),
                        );
                        $this->db->insert('entries', $data);
                        $recur_next_date = date_format(date_add(date_create(date("Y-m-d")), date_interval_create_from_date_string($entry->recur_frequency . ' ' . $entry->recur_type . 's')), 'Y-m-d');
                        $this->db->where('id', $entry->id)->update('entries',array('recur_next_date' => $recur_next_date));
                    }
                } else {
                    if (date("Y-m-d") <= $entry->recur_end_date) {
                        if ($entry->recur_next_date == date("Y-m-d")) {
                            $date = explode('-', date("Y-m-d"));
                            $data = array(
                                'type' => $entry->type,
                                'type_id' => $entry->type_id,
                                'amount' => $entry->amount,
                                'notes' => $entry->notes,
                                'files' => serialize(array()),
                                'date' => $entry->date,
                            );
                            $this->db->insert('entries', $data);
                            
                            $recur_next_date = date_format(date_add(date_create(date("Y-m-d")), date_interval_create_from_date_string($entry->recur_frequency . ' ' . $entry->recur_type . 's')),  'Y-m-d');
                            $this->db->where('id', $entry->id)->update('entries',array('recur_next_date' => $recur_next_date));
                        }
                    }
                }
            }
        }
    }

}
