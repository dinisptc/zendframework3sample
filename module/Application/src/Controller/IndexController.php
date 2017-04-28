<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Barcode\Barcode;
use Zend\Mvc\MvcEvent;
use User\Entity\User;
use User\Entity\Empresas;
use Zend\Session\Container; // We need this when using sessions
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;


include_once("public/paypal/functions.php");
/**
 * This is the main controller class of the User Demo application. It contains
 * site-wide actions such as Home or About.
 */
class IndexController extends AbstractActionController 
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
    
    private $translator;
    
    private $mailtransport;
    
                    /**
     * Authentication service.
     * @var \Zend\Authentication\AuthenticationService
     */
    private $authService;
    
        /**
     * User manager.
     * @var User\Service\UserManager 
     */
    private $userManager;
    
    private $userImageManager;
    
    /**
     * Constructor. Its purpose is to inject dependencies into the controller.
     */
    public function __construct($entityManager,$translator,$mailtransport, $userManager, $authService, $userImageManager) 
    {
       $this->entityManager = $entityManager;
       $this->translator = $translator;
       $this->mailtransport = $mailtransport;
       $this->userManager = $userManager;
       $this->authService = $authService;
       $this->userImageManager = $userImageManager;
       
       
    }
    
    private function traduz()
    {
        
        $user_session = new Container('language');
        $lang = $user_session->lang;
        
        $translator = $this->translator;//$this->getServiceLocator()->get('translator');
        
        if (($lang=='') || ($lang==null))
        {
          $translator->setLocale(\Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']));
            
        }else
        {
            
         $translator->setLocale($user_session->lang);
        }
        
    }
    
    /**
     * This is the default "index" action of the controller. It displays the 
     * Home page.
     */
    public function indexAction() 
    {
        
        $this->traduz();
        
        return new ViewModel([
         
            'entitymanager'=>$this->entityManager,
        ]);
      
    }


    
    /**
     * The "settings" action displays the info about currently logged in user.
     */
    public function settingsAction()
    {
        $this->traduz();
        
        $user = $this->entityManager->getRepository(User::class)
                ->findOneByEmail($this->identity());
        
        if ($user==null) {
            throw new \Exception('Not found user with such email');
        }
        $this->flashMessenger()->addMessage(
                            _('Change process.'));
        return new ViewModel([
            'user' => $user,
            'flash'=>$this->flashMessenger()->getMessages(),
            'userImageManager'=>$this->userImageManager,
            'entitymanager'=>$this->entityManager,
        ]);
    }
    
    
    public function setenAction()
    {         
        
        
        $user_session = new Container('language');
        $user_session->lang = 'en_US';
        
        $translator = $this->translator; // $this->getServiceLocator()->get('translator');
        
        $translator->setLocale('en_US');
        
        return $this->redirect()->toRoute('home');
      
        //return new ViewModel();
    }
    
    public function setptAction()
    {     
        
        $user_session = new Container('language');
        $user_session->lang = 'pt_PT';
                
        $translator = $this->translator; //$this->getServiceLocator()->get('translator');
        
        
        $translator->setLocale('pt_PT');
      
        return $this->redirect()->toRoute('home');
        //return new ViewModel();
                        
    }
    
    
    public function contactosAction()
    {             

        
        
      $this->traduz();
        
        

    
     
     $mensagemdeerro=-1;

      
      
    $form = new \Application\Form\MsgForm();

    $form->get('submit')->setAttribute('label', 'Reply');
    
    
    $request = $this->getRequest();
    if ($request->isPost()) 
    {
    
            // Fill in the form with POST data
            $ad = $this->params()->fromPost(); 
       
        $form->setData($ad);
        
   if ($form->isValid()) 
       {

         $mess=$form->getData();
         
       
      
         //envia email 
         if($this->sendNotificationReplyEmail("dinisnet@hotmail.com",$mess['email'],$mess['name'],$mess['mensagem']))
         {
             $mensagemdeerro=1;
         }else
         {
             $mensagemdeerro=0;
             
         }
      
 
    }
        
        
      
    }
 
    return array(
        'mensagemerro'=> $mensagemdeerro,
        'form' => $form,
    );

    
        
   
    }
    
    
    
    
    
    public function sendNotificationReplyEmail($usr_email,$email,$name,$mensagem)
{
		$transport = $this->mailtransport; //$this->getServiceLocator()->get('mail.transport');
		$message = new Message();
		$this->getRequest()->getServer();  //Server vars
                $htmlMarkup='Email de quem enviou :'.$email.'<br><br>'.$mensagem;
                $html = new MimePart($htmlMarkup);
                $html->type = "text/html";
                $body = new MimeMessage();
                $body->setParts(array($html));
		$message->addTo($usr_email)
				->addFrom($email)
				->setSubject('O/A : '.$name.' enviou uma mensagem atraves do site www.thejoboard.com')
				->setBody($body);
                
                //Send it!
                $sent = true;
                try {
                    $transport->send($message);
                } catch (Exception $e){
                    $sent = false;
                }

                //Do stuff (display error message, log it, redirect user, etc)
                if($sent){
                    return true;
                } else {
                    //Mail failed to send.
                    return false;
                }
                
		
}
    
    
        /**
     * This is the default "index" action of the controller. It displays the 
     * Home page.
     */
    public function localizacaoAction() 
    {
        
        $this->traduz();
       
        return new ViewModel();
    }
    
    
    
     /**
     * This is the default "index" action of the controller. It displays the 
     * Home page.
     */
    public function precosAction() 
    {
        
        $this->traduz();
       
        return new ViewModel();
    }
    
    
    
    
     public function paypaloneAction()
{
    
    $this->traduz();
    
    //ubuntu live
    include_once("public/paypal/config.php");
    $mensagemdeerro=null;
    
    
    //get user
    $user = $this->entityManager->getRepository(User::class)->findOneByEmail($this->authService->getIdentity());
    
    if(($user->getPerfil()==User::PERFIL_ADMIN)||(($user->getPerfil()==User::PERFIL_PRO)))
    {
        
         return $this->redirect()->toRoute('application');
        
    }else{
        $userID=$user->getId();
    }

    $post='checkout';
    
    $request = $this->getRequest();
    $token=$request->getQuery()->token;
    $PayerID=$request->getQuery()->PayerID;
    
    if($token!='' && $PayerID!='')
        $post='do';
    
    $paypal= new MyPayPal();
    
    if($token!='' && $PayerID!=''){
		
		//------------------DoExpressCheckoutPayment-------------------		
		
		//Paypal redirects back to this page using ReturnURL, We should receive TOKEN and Payer ID
		//we will be using these two variables to execute the "DoExpressCheckoutPayment"
		//Note: we haven't received any payment yet.
		
		$mensagemdeerro=$paypal->DoExpressCheckoutPayment();
                
                        
                if($mensagemdeerro['sucesso']!=0 && $mensagemdeerro['transacaoid']!=0)
                {
                    if($mensagemdeerro['complete']==1 || $mensagemdeerro['pending']==1)
                    {
                
                $expiredate=$this->add_date(date("Y-m-d H:i:s"),12);
                
                //adicionar a data de expiracao ao user 
                 // Add user.
                $this->userManager->setProexpireDate($expiredate,$userID);
                

                 }
                }
                

	}
        
        


    
    
            
 
    
    
        $form = new \Application\Form\PaypaloneForm();
    $form->get('submit')->setAttribute('label', 'Pay');
    
    $request = $this->getRequest();
    if ($request->isPost()) {
        
        $itemname="Become PRO one year thejoboard";
        $itemnumber=$userID;
        $itemdesc=$user->getEmail();
        
        //sandbox
       // $price=1.00;
       $price=25.00;
      
        $itemprice=$price;
        $itemQty=1;
        
    

        
	
        

	
	//Post Data received from product list page.
	//if(_GET('paypal')=='checkout'){
        if($post=='checkout'){
		
		//-------------------- prepare products -------------------------
		
		//Mainly we need 4 variables from product page Item Name, Item Price, Item Number and Item Quantity.
		
		//Please Note : People can manipulate hidden field amounts in form,
		//In practical world you must fetch actual price from database using item id. Eg: 
		//$products[0]['ItemPrice'] = $mysqli->query("SELECT item_price FROM products WHERE id = Product_Number");
		
		$products = [];
                
                $products[0]['ItemName'] = $itemname; //Item Name
		$products[0]['ItemPrice'] = $itemprice; //Item Price
		$products[0]['ItemNumber'] = $itemnumber; //Item Number
		$products[0]['ItemDesc'] = $itemdesc; //Item Number
		$products[0]['ItemQty']	= $itemQty; // Item Quantity
		
		
		
	
		
		//-------------------- prepare charges -------------------------
		
		$charges = [];
		
		//Other important variables like tax, shipping cost
		$charges['TotalTaxAmount'] = 0;  //Sum of tax for all items in this order. 
		$charges['HandalingCost'] = 0;  //Handling cost for this order.
		$charges['InsuranceCost'] = 0;  //shipping insurance cost for this order.
		$charges['ShippinDiscount'] = 0; //Shipping discount for this order. Specify this as negative number.
		$charges['ShippinCost'] = 0; //Although you may change the value later, try to pass in a shipping amount that is reasonably accurate.
		
		//------------------SetExpressCheckOut-------------------
		
		//We need to execute the "SetExpressCheckOut" method to obtain paypal token

		$paypal->SetExpressCheckOut($products, $charges);		
	}

        

    }
                                               
    
    
                                               
 
    return array(
      'mensagemerro'=> $mensagemdeerro,
      'form' => $form
    );


}


 function add_date($orgDate,$mth)
 {
  $cd = strtotime($orgDate);
  $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mth,date('d',$cd),date('Y',$cd)));
  return $retDAY;
}  


}

///////////////////////////////////////Mypaypal Class
class MyPayPal {
    
    	
            
 
		
		function GetItemTotalPrice($item){
		
			//(Item Price x Quantity = Total) Get total amount of product;
			return $item['ItemPrice'] * $item['ItemQty']; 
		}
		
		function GetProductsTotalAmount($products){
		
			$ProductsTotalAmount=0;

			foreach($products as $p => $item){
				
				$ProductsTotalAmount = $ProductsTotalAmount + $this -> GetItemTotalPrice($item);	
			}
			
			return $ProductsTotalAmount;
		}
		
		function GetGrandTotal($products, $charges){
			
			//Grand total including all tax, insurance, shipping cost and discount
			
			$GrandTotal = $this -> GetProductsTotalAmount($products);
			
			foreach($charges as $charge){
				
				$GrandTotal = $GrandTotal + $charge;
			}
			
			return $GrandTotal;
		}
		
		function SetExpressCheckout($products, $charges, $noshipping='1'){
			
			//Parameters for SetExpressCheckout, which will be sent to PayPal
			
			$padata  = 	'&METHOD=SetExpressCheckout';
			
			$padata .= 	'&RETURNURL='.urlencode(PPL_RETURN_URL);
			$padata .=	'&CANCELURL='.urlencode(PPL_CANCEL_URL);
			$padata .=	'&PAYMENTREQUEST_0_PAYMENTACTION='.urlencode("SALE");
			
			foreach($products as $p => $item){
				
				$padata .=	'&L_PAYMENTREQUEST_0_NAME'.$p.'='.urlencode($item['ItemName']);
				$padata .=	'&L_PAYMENTREQUEST_0_NUMBER'.$p.'='.urlencode($item['ItemNumber']);
				$padata .=	'&L_PAYMENTREQUEST_0_DESC'.$p.'='.urlencode($item['ItemDesc']);
				$padata .=	'&L_PAYMENTREQUEST_0_AMT'.$p.'='.urlencode($item['ItemPrice']);
				$padata .=	'&L_PAYMENTREQUEST_0_QTY'.$p.'='. urlencode($item['ItemQty']);
			}		

			/* 
			
			//Override the buyer's shipping address stored on PayPal, The buyer cannot edit the overridden address.
			
			$padata .=	'&ADDROVERRIDE=1';
			$padata .=	'&PAYMENTREQUEST_0_SHIPTONAME=J Smith';
			$padata .=	'&PAYMENTREQUEST_0_SHIPTOSTREET=1 Main St';
			$padata .=	'&PAYMENTREQUEST_0_SHIPTOCITY=San Jose';
			$padata .=	'&PAYMENTREQUEST_0_SHIPTOSTATE=CA';
			$padata .=	'&PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE=US';
			$padata .=	'&PAYMENTREQUEST_0_SHIPTOZIP=95131';
			$padata .=	'&PAYMENTREQUEST_0_SHIPTOPHONENUM=408-967-4444';
			
			*/
						
			$padata .=	'&NOSHIPPING='.$noshipping; //set 1 to hide buyer's shipping address, in-case products that does not require shipping
						
			$padata .=	'&PAYMENTREQUEST_0_ITEMAMT='.urlencode($this -> GetProductsTotalAmount($products));
			
			$padata .=	'&PAYMENTREQUEST_0_TAXAMT='.urlencode($charges['TotalTaxAmount']);
			$padata .=	'&PAYMENTREQUEST_0_SHIPPINGAMT='.urlencode($charges['ShippinCost']);
			$padata .=	'&PAYMENTREQUEST_0_HANDLINGAMT='.urlencode($charges['HandalingCost']);
			$padata .=	'&PAYMENTREQUEST_0_SHIPDISCAMT='.urlencode($charges['ShippinDiscount']);
			$padata .=	'&PAYMENTREQUEST_0_INSURANCEAMT='.urlencode($charges['InsuranceCost']);
			$padata .=	'&PAYMENTREQUEST_0_AMT='.urlencode($this->GetGrandTotal($products, $charges));
			$padata .=	'&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode(PPL_CURRENCY_CODE);
			
			//paypal custom template
			
			$padata .=	'&LOCALECODE='.PPL_LANG; //PayPal pages to match the language on your website;
			$padata .=	'&LOGOIMG='.PPL_LOGO_IMG; //site logo
			$padata .=	'&CARTBORDERCOLOR=FFFFFF'; //border color of cart
			$padata .=	'&ALLOWNOTE=1';
						
			############# set session variable we need later for "DoExpressCheckoutPayment" #######
			
			$_SESSION['ppl_products'] =  $products;
			$_SESSION['ppl_charges'] 	=  $charges;
			
			$httpParsedResponseAr = $this->PPHttpPost('SetExpressCheckout', $padata);
			
			//Respond according to message we receive from Paypal
			if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])){

				$paypalmode = (PPL_MODE=='sandbox') ? '.sandbox' : '';
			
				//Redirect user to PayPal store with Token received.
				
				$paypalurl ='https://www'.$paypalmode.'.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token='.$httpParsedResponseAr["TOKEN"].'';
				
				header('Location: '.$paypalurl);
                                
                                exit;
			}
			else{
				
				//Show error message
				
				echo '<div style="color:red"><b>Error : </b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
				
				echo '<pre>';
					
					print_r($httpParsedResponseAr);
				
				echo '</pre>';
			}	
		}		
		
			
		function DoExpressCheckoutPayment(){
			
                    $mensagem=array();
                    $mensagem = array('sucesso' => 0, 'transacaoid' => 0,'complete'=>0,'pending'=>'','erro'=>0);


                    
			if(!empty(_SESSION('ppl_products'))&&!empty(_SESSION('ppl_charges'))){
				
				$products=_SESSION('ppl_products');
				
				$charges=_SESSION('ppl_charges');
				
				$padata  = 	'&TOKEN='.urlencode(_GET('token'));
				$padata .= 	'&PAYERID='.urlencode(_GET('PayerID'));
				$padata .= 	'&PAYMENTREQUEST_0_PAYMENTACTION='.urlencode("SALE");
				
				//set item info here, otherwise we won't see product details later	
				
				foreach($products as $p => $item){
					
					$padata .=	'&L_PAYMENTREQUEST_0_NAME'.$p.'='.urlencode($item['ItemName']);
					$padata .=	'&L_PAYMENTREQUEST_0_NUMBER'.$p.'='.urlencode($item['ItemNumber']);
					$padata .=	'&L_PAYMENTREQUEST_0_DESC'.$p.'='.urlencode($item['ItemDesc']);
					$padata .=	'&L_PAYMENTREQUEST_0_AMT'.$p.'='.urlencode($item['ItemPrice']);
					$padata .=	'&L_PAYMENTREQUEST_0_QTY'.$p.'='. urlencode($item['ItemQty']);
				}
				
				$padata .= 	'&PAYMENTREQUEST_0_ITEMAMT='.urlencode($this -> GetProductsTotalAmount($products));
				$padata .= 	'&PAYMENTREQUEST_0_TAXAMT='.urlencode($charges['TotalTaxAmount']);
				$padata .= 	'&PAYMENTREQUEST_0_SHIPPINGAMT='.urlencode($charges['ShippinCost']);
				$padata .= 	'&PAYMENTREQUEST_0_HANDLINGAMT='.urlencode($charges['HandalingCost']);
				$padata .= 	'&PAYMENTREQUEST_0_SHIPDISCAMT='.urlencode($charges['ShippinDiscount']);
				$padata .= 	'&PAYMENTREQUEST_0_INSURANCEAMT='.urlencode($charges['InsuranceCost']);
				$padata .= 	'&PAYMENTREQUEST_0_AMT='.urlencode($this->GetGrandTotal($products, $charges));
				$padata .= 	'&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode(PPL_CURRENCY_CODE);
				
				//We need to execute the "DoExpressCheckoutPayment" at this point to Receive payment from user.
				
				$httpParsedResponseAr = $this->PPHttpPost('DoExpressCheckoutPayment', $padata);
					
				//vdump($httpParsedResponseAr);

				//Check if everything went ok..
				if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])){

					//echo '<h2>Success</h2>';
					//echo 'Your Transaction ID : '.urldecode($httpParsedResponseAr["PAYMENTINFO_0_TRANSACTIONID"]);
                                        $mensagem['sucesso']=1;
                                        $mensagem['transacaoid']=urldecode($httpParsedResponseAr["PAYMENTINFO_0_TRANSACTIONID"]);      
                                                
//                                                =array(
//                                            'sucesso'=>'<h2>Success</h2>',                     
//                                            'transecao'=>'Your Transaction ID : '.urldecode($httpParsedResponseAr["PAYMENTINFO_0_TRANSACTIONID"])
//                                            );
//                                        
//                                        // Correct
//                                            print $arr['fruit'];  // apple
//                                            print $arr['veggie']; // carrot

				
					/*
					//Sometimes Payment are kept pending even when transaction is complete. 
					//hence we need to notify user about it and ask him manually approve the transiction
					*/
					
					if('Completed' == $httpParsedResponseAr["PAYMENTINFO_0_PAYMENTSTATUS"]){
						
						
                                            
            
                                                    
                                                 //   echo '<div style="color:green">Payment Received! You are now a Premium User!</div>';
                                                   // $mensagem='<div style="color:green">Payment Received! You are now a Premium User!</div>';
                                            
                                                 //  $mensagem['complete']='<div style="color:green">Payment Received! You are now a Premium User! - Logout - and Login again</div>';
                                                   $mensagem['complete']=1;
                                                //}
                                                
					}
					elseif('Pending' == $httpParsedResponseAr["PAYMENTINFO_0_PAYMENTSTATUS"]){
                                            
                                            $mensagem['pending']=1;
						
						//echo '<div style="color:red">Transaction Complete, but payment may still be pending! '.
						//'If that\'s the case, You can manually authorize this payment in your <a target="_new" href="http://www.paypal.com">Paypal Account</a></div>';
					}
					
					$this->GetTransactionDetails();
				}
				else{
						
					//echo '<div style="color:red"><b>Error : </b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
					//$mensagem['erro']='<div style="color:red"><b>Error : </b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
					$mensagem['erro']=urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]);
//					echo '<pre>';
//					
//						print_r($httpParsedResponseAr);
//						
//					echo '</pre>';
				}
			}
			else{
				
				// Request Transaction Details
				
				$this->GetTransactionDetails();
			}
                        
                           return $mensagem;
		}
				
		function GetTransactionDetails(){
		
			// we can retrive transection details using either GetTransactionDetails or GetExpressCheckoutDetails
			// GetTransactionDetails requires a Transaction ID, and GetExpressCheckoutDetails requires Token returned by SetExpressCheckOut
			
			$padata = 	'&TOKEN='.urlencode(_GET('token'));
			
			$httpParsedResponseAr = $this->PPHttpPost('GetExpressCheckoutDetails', $padata, PPL_API_USER, PPL_API_PASSWORD, PPL_API_SIGNATURE, PPL_MODE);

			if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])){
				
				//echo '<br /><b>Stuff to store in database :</b><br /><pre>';
                                
                                
                                
				/*
				#### SAVE BUYER INFORMATION IN DATABASE ###
				//see (http://www.sanwebe.com/2013/03/basic-php-mysqli-usage) for mysqli usage
				
				$buyerName = $httpParsedResponseAr["FIRSTNAME"].' '.$httpParsedResponseAr["LASTNAME"];
				$buyerEmail = $httpParsedResponseAr["EMAIL"];
				
				//Open a new connection to the MySQL server
				$mysqli = new mysqli('host','username','password','database_name');
				
				//Output any connection error
				if ($mysqli->connect_error) {
					die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
				}		
				
				$insert_row = $mysqli->query("INSERT INTO BuyerTable 
				(BuyerName,BuyerEmail,TransactionID,ItemName,ItemNumber, ItemAmount,ItemQTY)
				VALUES ('$buyerName','$buyerEmail','$transactionID','$products[0]['ItemName']',$products[0]['ItemNumber'], $products[0]['ItemTotalPrice'],$ItemQTY)");
				
				if($insert_row){
					print 'Success! ID of last inserted record is : ' .$mysqli->insert_id .'<br />'; 
				}else{
					die('Error : ('. $mysqli->errno .') '. $mysqli->error);
				}
				
				*/
				
				//echo '<pre>';
				
				//	print_r($httpParsedResponseAr);
					
				//echo '</pre>';
			} 
			else  {
				
//				echo '<div style="color:red"><b>GetTransactionDetails failed:</b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
				
//				echo '<pre>';
//				
//					print_r($httpParsedResponseAr);
//					
//				echo '</pre>';

			}
		}
		
		function PPHttpPost($methodName_, $nvpStr_) {
				
				// Set up your API credentials, PayPal end point, and API version.
				$API_UserName = urlencode(PPL_API_USER);
				$API_Password = urlencode(PPL_API_PASSWORD);
				$API_Signature = urlencode(PPL_API_SIGNATURE);
				
				$paypalmode = (PPL_MODE=='sandbox') ? '.sandbox' : '';
		
				$API_Endpoint = "https://api-3t".$paypalmode.".paypal.com/nvp";
				$version = urlencode('109.0');
			
				// Set the curl parameters.
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
				curl_setopt($ch, CURLOPT_VERBOSE, 1);
				//curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1');
				
				// Turn off the server and peer verification (TrustManager Concept).
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
			
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POST, 1);
			
				// Set the API operation, version, and API signature in the request.
				$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";
			
				// Set the request as a POST FIELD for curl.
				curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
			
				// Get response from the server.
				$httpResponse = curl_exec($ch);
			
				if(!$httpResponse) {
					exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
				}
			
				// Extract the response details.
				$httpResponseAr = explode("&", $httpResponse);
			
				$httpParsedResponseAr = array();
				foreach ($httpResponseAr as $i => $value) {
					
					$tmpAr = explode("=", $value);
					
					if(sizeof($tmpAr) > 1) {
						
						$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
					}
				}
			
				if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
					
					exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
				}
			
			return $httpParsedResponseAr;
		}
                

	}