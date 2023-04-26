<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
 *  ============================================================================== 
 *  Author	: Usman Sher
 *  Email   : uskhan099@gmail.com
 *  For		: PHP QR Code
 *  Web		: http://phpqrcode.sourceforge.net
 *  License	: open source (LGPL)
 *  ============================================================================== 
 */
include(APPPATH . "/third_party/phpqrcode/qrlib.php");

class Phpqrcode
{

    public function generate($params = array())
    {
        $params['data'] = (isset($params['data'])) ? $params['data'] : 'http://otsglobal.org';
        if (isset($params['svg']) && !empty($params['svg'])) {

            QRcode::svg($params['data'], $params['savename'], 'H', 2, 0); 
            return $params['savename'];

        } else {

            QRcode::png($params['data'], $params['savename'], 'H', 2, 0);
            return $params['savename'];

        }
    }

}
