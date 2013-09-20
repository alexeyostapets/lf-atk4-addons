<?php
class billing_Model_PaymentMethod_Qpay extends billing_Model_PaymentMethod_generic {
    protected $requestFingerprintOrder = "";  // initalize FingerprintOrder
    protected $requestFingerprintSeed  = "";  // initalize FingerprintSeed
    
    protected $qpayURL = "https://www.qenta.com/qpay/init.php";
    
    protected $customerId = "SET UP YOUR CUSTOMERID";
    protected $secret = "SET UP YOUR SECRET";
    
    protected $amount = "SET UP THE AMOUNT";
    protected $currency = "EUR";
    protected $language = "en";
    protected $orderDescription = "Deposit funds on Linked Finance";
    protected $displayText = "Thank you";
    protected $successURL = "/qpayproxy";
    protected $cancelURL  = "/qpayproxy";
    protected $failureURL = "/qpayproxy";
    protected $serviceURL = "/qpayproxy";
    protected $imageURL = '';
    protected $order_id = '';
    
    function init(){
        $this->customerId=$this->api->getConfig('billing/qpay/customer_id');
        $this->secret=$this->api->getConfig('billing/qpay/secret');
        $this->successURL='http://'.$_SERVER['HTTP_HOST'].'/qpayproxy';
        $this->cancelURL='http://'.$_SERVER['HTTP_HOST'].'/qpayproxy';
        $this->failureURL='http://'.$_SERVER['HTTP_HOST'].'/qpayproxy';
        $this->serviceURL='http://'.$_SERVER['HTTP_HOST'].'/qpayproxy';
        $this->imageURL='http://'.$_SERVER['HTTP_HOST'].'/templates/jui/images/logo.png';
    }
    
    function getRequestFingerprint(){
        $this->requestFingerprintOrder = '';
        $this->requestFingerprintSeed  = '';
        $this->requestFingerprintOrder .= "customerId,";
        $this->requestFingerprintSeed  .= $this->customerId;
        $this->requestFingerprintOrder .= "secret,";
        $this->requestFingerprintSeed  .= $this->secret;
        $this->requestFingerprintOrder .= "amount,";
        $this->requestFingerprintSeed  .= $this->amount;
        $this->requestFingerprintOrder .= "currency,";
        $this->requestFingerprintSeed  .= $this->currency;
        $this->requestFingerprintOrder .= "language,";
        $this->requestFingerprintSeed  .= $this->language;
        $this->requestFingerprintOrder .= "orderDescription,";
        $this->requestFingerprintSeed  .= $this->orderDescription;
        $this->requestFingerprintOrder .= "displayText,";
        $this->requestFingerprintSeed  .= $this->displayText;
        $this->requestFingerprintOrder .= "successURL,";
        $this->requestFingerprintSeed  .= $this->successURL;
        $this->requestFingerprintOrder .= "customField1,";
        $this->requestFingerprintSeed  .= $this->order_id;
        $this->requestFingerprintOrder .= "requestFingerprintOrder";   // add requestFingerprintOrder to itself
        $this->requestFingerprintSeed  .= $this->requestFingerprintOrder;    // add requestFingerprintOrder to fingerprint
        
        return md5($this->requestFingerprintSeed);
    }
    function setAmount($amount){
        $this->amount=$amount;
    }
    function setOrderId($order_id){
        $this->order_id=$order_id;
    }
    function setCurrency($currency){
        $this->currency=$currency;
    }
    function setOrderDescription($order_id){
        $this->orderDescription="Deposit funds on Linked Finance Nr ".$order_id;
    }
    
    function charge($order_id,$amount,$currency='EUR',$extras=array()){
        $this->setOrderDescription($order_id);
        $this->setAmount($amount);
        
		// returns URL to redirect to
		return $this->api->getDestinationURL('/qpayproxy',array_merge(
					$extras,
					//is_array($descr)?$descr:array('descr'=>$descr),
					array(
						'amount'=>$this->amount,
						'order_id'=>$order_id,
						'comment'=>$comment,
						'currency'=>$currency)));
	}
	function ppproxy($id=null){
		// returns true if paymentwas successful

		// return false if used as thank you page
		
/*
		if($_POST){
			if($_POST['RESULT']==='00'){
                //success case
				return $_POST['ORDER_ID'];

			}
			echo '<a href="'. $this->api->getConfig('billing/realex/error_url', 'https://linkedfinance.com/') . '">Problem with payment</a>';
		}
		*/
		if($_GET['amount']){
		    $this->setAmount($_GET['amount']);
		    if ($_GET['order_id']) $this->setOrderId($_GET['order_id']);
		    if ($_GET['currency']) $this->setCurrency($_GET['currency']);
		    
			$requestFingerprint=$this->getRequestFingerprint();
$r= '
<html><head></head><body onload="document.forms[0].submit()">
<form action="'.$this->qpayURL.'" method="post" name="form" style="visibility: hidden">
<input type="hidden" name="customerId" value="'.$this->customerId.'" />
<input type="hidden" name="successURL" value="'.$this->successURL.'" />
<input type="hidden" name="failureURL" value="'.$this->failureURL.'" />
<input type="hidden" name="cancelURL" value="'.$this->cancelURL.'" />
<input type="hidden" name="serviceURL" value="'.$this->serviceURL.'" />
<input type="hidden" name="imageURL" value="'.$this->imageURL.'" />
<input type="text" name="amount" value="'.addslashes($this->amount).'">
<input type="hidden" name="currency" value="'.$this->currency.'" />
<input type="hidden" name="language" value="'.$this->language.'" />
<input type="hidden" name="orderDescription" value="'.$this->orderDescription.'" />
<input type="hidden" name="displayText" value="'.$this->displayText.'" />
<input type="hidden" name="paymenttype" value="CCARD" />
<input type="hidden" name="customField1" value="'.$this->order_id.'" />
<input type="hidden" name="requestFingerprintOrder" value="'.$this->requestFingerprintOrder.'" />
<input type="hidden" name="requestfingerprint" value="'.$requestFingerprint.'" />
        
<input type="submit"/>
</form>
</body></html>
';
if($_GET['debug'])echo '<pre>'.htmlspecialchars($r);else echo $r;
exit;

		}else{
		    return $_POST;
		}
	}
}
