<?php
require FCPATH.'vendor/autoload.php';
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
define("AUTHORIZENET_LOG_FILE", "phplog");
class Authorize
{
    public function __construct()
    {
        $this->_ci =& get_instance();
        $this->_ci->load->model('settings_model');
        $imp = $this->get();

        $this->MERCHANT_LOGIN_ID = $imp->authorize_login_id;
        $this->MERCHANT_TRANSACTION_KEY = $imp->authorize_transaction_id;
    }

    public function get()
    {
        $settings = $this->_ci->settings_model->getSettings();
        return $settings;
    }

    function createAnAcceptPaymentTransaction($amount, $nounce)
    {
        /* Create a merchantAuthenticationType object with authentication details
           retrieved from the constants file */
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($this->MERCHANT_LOGIN_ID);
        $merchantAuthentication->setTransactionKey($this->MERCHANT_TRANSACTION_KEY);
        
        // Set the transaction's refId
        $refId = 'ref' . time();
        // Create the payment object for a payment nonce
        $opaqueData = new AnetAPI\OpaqueDataType();
        $opaqueData->setDataDescriptor($nounce['desc']);
        $opaqueData->setDataValue($nounce['value']);
        // Add the payment data to a paymentType object
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setOpaqueData($opaqueData);
        // Create order information
        $order = new AnetAPI\OrderType();
        $order->setInvoiceNumber("10101");


        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction"); 
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);

        // Assemble the complete transaction request
        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);
        // Create the controller and get the response
        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
        
        if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode()) {
                // Since the API request was successful, look for a transaction response
                // and parse it to display the results of authorizing the card
                $tresponse = $response->getTransactionResponse();
            
                if ($tresponse != null && $tresponse->getMessages() != null) {
	                $trans_id = $tresponse->getTransId();
	                $rcode = $tresponse->getResponseCode();
	               	 
	               	switch( $rcode ) {
						case '1': // Approved
							return TRUE;
						break;
					
						case '2': // Declined
						case '3': // Error
						case '4': // Held for Review
							$error = $tresponse->getMessages()[0]->getDescription();
							return False;
						return FALSE;
						break;
			
						default: // ??
						break;
					}
                } else {
	                return FALSE;
/*
                    echo "Transaction Failed \n";
                    if ($tresponse->getErrors() != null) {
                        echo " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                        echo " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
                    }
*/

                }
                // Or, print errors if the API request wasn't successful
            } else {
	            return FALSE;
/*
                echo "Transaction Failed \n";
                $tresponse = $response->getTransactionResponse();
            
                if ($tresponse != null && $tresponse->getErrors() != null) {
                    echo " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                    echo " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
                } else {
                    echo " Error Code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n";
                    echo " Error Message : " . $response->getMessages()->getMessage()[0]->getText() . "\n";
                }
*/
            }      
        } else {
            $error =  "No response returned \n";
            return FALSE;

        }
        return $response;
    }
    
    
	function refundTransaction($amount, $cc)
	{
	    /* Create a merchantAuthenticationType object with authentication details
	       retrieved from the constants file */
	    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
	    $merchantAuthentication->setName($this->MERCHANT_LOGIN_ID);
	    $merchantAuthentication->setTransactionKey($this->MERCHANT_TRANSACTION_KEY);
	    
	    // Set the transaction's refId
	    $refId = 'ref' . time();
	
	    // Create the payment data for a credit card
	    $creditCard = new AnetAPI\CreditCardType();
	    $creditCard->setCardNumber($cc['number']);
	    $creditCard->setExpirationDate($cc['exp']);
	    $paymentOne = new AnetAPI\PaymentType();
	    $paymentOne->setCreditCard($creditCard);
	    //create a transaction
	    $transactionRequest = new AnetAPI\TransactionRequestType();
	    $transactionRequest->setTransactionType("refundTransaction"); 
	    $transactionRequest->setAmount($amount);
	    $transactionRequest->setPayment($paymentOne);
	 
	
	    $request = new AnetAPI\CreateTransactionRequest();
	    $request->setMerchantAuthentication($merchantAuthentication);
	    $request->setRefId($refId);
	    $request->setTransactionRequest( $transactionRequest);
	    $controller = new AnetController\CreateTransactionController($request);
        // $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::PRODUCTION);
        $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
	
	    if ($response != null) {
	    	if($response->getMessages()->getResultCode()) {
		        $tresponse = $response->getTransactionResponse();
			    if ($tresponse != null && $tresponse->getMessages() != null) {
		          if($tresponse->getResponseCode() == 1){
			          return TRUE;
		          }
		          return FALSE;
		        } else {
		          return FALSE;
		        }
	      	} else {
		        return FALSE;
	      	}      
	    } else {
	      	return FALSE;
	    }
	
	    return $response;
	}
	
}



    


    