<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
 *  ==============================================================================
 *  Author  : Usman Sher
 *  Email   : uskhan099@gmail.com
 *  For     : PHP QR Code
 *  Web     : http://phpqrcode.sourceforge.net
 *  License : open source (LGPL)
 *  ==============================================================================
 */

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Response\QrCodeResponse;

class Wf_qrcode
{

    public function generate($params = array()) {
		$params['data'] = (isset($params['data'])) ? $params['data'] : 'http://otsglobal.org';
       
    	$qrCode = new QrCode($params['data']);
		$qrCode->setSize((int)$params['size']);

		// Set advanced options
		$qrCode->setWriterByName('png');
		$qrCode->setMargin(0);
		$qrCode->setEncoding('UTF-8');
		$qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel(ErrorCorrectionLevel::HIGH));
		$qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
		$qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);

		// Save it to a file
		$qrCode->writeFile(FCPATH.$params['savename']);

		
    }

}
