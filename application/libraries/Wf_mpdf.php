<?php defined('BASEPATH') OR exit('No direct script access allowed');
/*
 *  ==============================================================================
 *  Author  : Usman Sher
 *  Email   : usman@otsglobal.org
 *  For     : mPDF
 *  Web     : https://github.com/mpdf/mpdf
 *  License : GNU General Public License v2.0
 *          : https://github.com/mpdf/mpdf/blob/development/LICENSE.txt
 *  ==============================================================================
 */

use Mpdf\Mpdf;

class Wf_mpdf
{
    public function __construct() {
    }

    public function __get($var)
    {
        return get_instance()->controller->$var;
    }
  
    public function generate($content, $name = 'download.pdf', $output_type = null, $footer = null, $margin_bottom = null, $header = null, $margin_top = null, $orientation = 'P') {

        if (!$output_type) {
            $output_type = 'D';
        }
        if (!$margin_top) {
            $margin_top = 20;
        }

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];




        $mpdf = new Mpdf([
            'setAutoBottomMargin' => 'pad', 
            'fontDir' => array_merge($fontDirs, [
                FCPATH . '/assets/fonts',
            ]),
            'fontdata' => $fontData + [
                'opensans' => [
                    'R' => 'OpenSans-Regular.ttf',
                    'I' => 'OpenSans-Italic.ttf',
                ]
            ],
            'default_font' => 'opensans'
        ]);



        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->SetTopMargin($margin_top);
        // $mpdf->setAutoBottomMargin($margin_bottom);
        // setAutoBottomMargin
        $mpdf->SetTitle($this->mSettings->title);
        $mpdf->SetAuthor($this->mSettings->title);
        $mpdf->SetCreator($this->mSettings->title);
        $mpdf->SetDisplayMode('fullpage');

        if (is_array($content)) {
            $mpdf->SetHeader($this->mSettings->title.'||{PAGENO}/{nbpg}', '', TRUE); // For simple text header
            $as = sizeof($content);
            $r = 1;
            foreach ($content as $page) {
                $mpdf->WriteHTML($page['content']);
                if (!empty($page['footer'])) {
                    $mpdf->SetHTMLFooter('<p class="text-center">' . $page['footer'] . '</p>', '', true);
                }
                if ($as != $r) {
                    $mpdf->AddPage();
                }
                $r++;
            }

        } else {

            $mpdf->WriteHTML($content);
            if ($header != '') {
                $mpdf->SetHTMLHeader('<p class="text-center">' . $header . '</p>', '', true);
            }
            if ($footer != '') {
                $mpdf->SetHTMLFooter('<p class="text-center">' . $footer . '</p>', '', true);
            }

            // $mpdf->SetHTMLFooter($this->mSettings->title.'||{PAGENO}/{nbpg}', '', TRUE); // For simple text header
            

        }

        if ($output_type == 'S') {
            $file_content = $mpdf->Output('', 'S');
            write_file('assets/uploads/' . $name, $file_content);
            return 'assets/uploads/' . $name;
        } else {
            $mpdf->Output($name, $output_type);
        }
    }

}
