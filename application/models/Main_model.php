<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Main_model extends CI_Model
{
    /*------------------------------------------------------------------------
    | GENERATE THE TOKEN ANTI CSRF
    -------------------------------------------------------------------------*/
    public function gen_token()
    {
        if( !isset( $_SESSION['token'] ) ) //Se non è stato settato nessun Token
        {
            $token = md5( rand() );
            $token = str_split( $token, 10 );
            $_SESSION['token'] = $token[0]; //Settiamo il token
        }
    }
    

    
}
