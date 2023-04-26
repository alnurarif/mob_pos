<?php


/*
 * extends MY_Controller
 */

class Auth_Controller extends MY_Controller {
	public function __construct() {
	    parent::__construct();
		$this->verify_login();
	}
	protected function render($view_file, $layout = 'default')
	{
		parent::render($view_file);
	}
}

