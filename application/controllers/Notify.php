<?php 
class Notify extends MY_Controller 
{
    public function __construct() 
    {
        parent::__construct(); 
    } 

    public function show_404() 
    { 
        $this->output->set_status_header('404'); 
        $this->load->view($this->theme.'404_error');//loading in my template 
    } 
}
?>