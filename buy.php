<?php
@session_start();
$_SESSION['time']     = time();
if(!isset($_POST['product']) || !isset($_POST['lang']) || !isset($_POST['email']) || !isset($_POST['pay']) || !isset($_POST['name']) || !isset($_POST['currency']))
    die('bug');
require 'FGETNw3g7uSX3J9c.php';

if(!isset($eurchange))
    $eurchange=9;
if(!isset($usdchange))
    $usdchange=8;

$pay='';
if(isset($_POST['pay']))
{
    if(!is_string($_POST['pay']))
        die('p string');
    else
        $pay=$_POST['pay'];
}
if($pay=='')
    die('p undef');

if($pay!='khipu' && $pay!='cybersource' && $pay!='paypal')
    die('PM');
else if($pay=='khipu')
    $currency='BOB';
else if($pay=='paypal')
{
    if($_POST['currency']=='EUR' || $_POST['currency']=='USD')
        $currency=$_POST['currency'];
    else
        die('CUR'.$_POST['currency']);
}
else if($pay=='cybersource')
{
    if($_POST['currency']=='BOB' || $_POST['currency']=='USD')
        $currency=$_POST['currency'];
    else
        die('CUR'.$_POST['currency']);
}
else
    die('PM2');

switch($_POST['product'])
{
    case 'catchchallenger':
    $name='CatchChallenger Ultimate key';
    $internaltotal=14.99;
    break;
    default:
    exit;
}
switch($_POST['lang'])
{
    case 'en':
    case 'es':
    case 'fr':
    break;
    default:
    exit;
}

$opts["payer_email"]=$_POST['email'];

if($pay=='paypal')
{
    require $_SERVER['DOCUMENT_ROOT'].'/paiment/paypal/PayPal-PHP-SDK/autoload.php';
    $apiContext = new \PayPal\Rest\ApiContext(
        new \PayPal\Auth\OAuthTokenCredential($paypal_ClientID,$paypal_ClientSecret)
    );
    if(isset($paypal_mode) && $paypal_mode=='live')
    $apiContext->setConfig(
        array(
           /*         'log.LogEnabled' => true,
            'log.FileName' => 'PayPal.log',
            'log.LogLevel' => 'DEBUG', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
            */
            'mode' => 'live',
        )
    );
    $payer = new \PayPal\Api\Payer();
    $payer->setPaymentMethod('paypal');
    $transaction = new \PayPal\Api\Transaction();
    $redirectUrls = new \PayPal\Api\RedirectUrls();
    $redirectUrls->setReturnUrl("https://ultracopier.herman-brule.com/paiment/paypal/notify.php?success=true&lang=".$_POST['lang']."&product=".$_POST['product']."&email=".$_POST['email'])
        ->setCancelUrl("https://ultracopier.herman-brule.co/paiment/paypal/notify.php?success=false&lang=".$_POST['lang']."&product=".$_POST['product']."&email=".$_POST['email']);
        
    $amount = new \PayPal\Api\Amount();
    if($currency=='EUR')
        $amountfinalcurrency = (int)ceil($internaltotal*$usdchange/$eurchange);
    else if($currency=='USD')
        $amountfinalcurrency = (int)ceil($internaltotal);//*$usdchange/$usdchange
    /*else if($currency=='BOB')
        $amountfinalcurrency = (int)ceil($internaltotal*$usdchange);*/
    else
        die('Internal bug');
    if($amountfinalcurrency<1)
        $amountfinalcurrency=1;
    $details = new \PayPal\Api\Details();
    $details->setSubtotal($amountfinalcurrency);
    $amount->setTotal($amountfinalcurrency)
    ->setCurrency($currency)
    ->setDetails($details);
    $item1 = new \PayPal\Api\Item();
    $item1->setName($name)
        ->setCurrency($currency)
        ->setQuantity(1)
        ->setSku(time()) // Similar to `item_number` in Classic API
        ->setPrice($amountfinalcurrency);
    $itemList = new \PayPal\Api\ItemList();
    $itemList->setItems(array($item1));
    $transaction->setAmount($amount)
    ->setItemList($itemList)
    ->setDescription($name)
    ->setInvoiceNumber(time());
    
    try {
        $payment = new \PayPal\Api\Payment();
        $payment->setIntent('sale')
            ->setPayer($payer)
            ->setTransactions(array($transaction))
            ->setRedirectUrls($redirectUrls);
        $payment->create($apiContext);
        $paymentUrl = $payment->getApprovalLink();
    }
    catch (Exception $e) {
        echo 'Error, send this to admin:';
    echo '<pre>';
        echo $e->getMessage();
        print_r($e);
        exit;
    }
    //echo 'toto15';
    file_put_contents('95xcwCtkELBuUa8fqqtztnSx/paypal/'.$payment->getToken().'.json',json_encode($_POST));
    header('Location: '.$paymentUrl);
    //echo 'redirecting to: '.$paymentUrl;
    exit;
}
else if($pay=='cybersource')
{
include 'security.php'; ?>

<html>
<body>
Redirecting...
<form id="payment_confirmation" action="https://secureacceptance.cybersource.com/pay" method="post"/>
<?php
switch($_POST['lang'])
{
    case 'es':
    case 'fr':
    //$locale=$_POST['lang'];
    $locale='es-en';
    break;
    default:
    $locale='es-en';
    break;
}
$transaction_uuid=uniqid();
/*if($currency=='EUR')
    $amountfinalcurrency = (int)ceil($internaltotal*$usdchange/$eurchange);
else */if($currency=='USD')
    $amountfinalcurrency = (int)ceil($internaltotal);//*$usdchange/$usdchange
else if($currency=='BOB')
    $amountfinalcurrency = (int)ceil($internaltotal*$usdchange);
else
    die('Internal bug');
if($amountfinalcurrency<1)
    $amountfinalcurrency=1;
$customername=$_POST['name'];
$datacustomer=array('email'=>$_POST['email']);
$inscription=time();
$nit='';
$merchant=Array('merchant_defined_data1'=>'SI','merchant_defined_data2'=>date('d-m-Y',$inscription),'merchant_defined_data4'=>date('dmY'),'merchant_defined_data6'=>'NO','merchant_defined_data7'=>'Confiared S.R.L.','merchant_defined_data9'=>'Pagina Web','merchant_defined_data10'=>'N','merchant_defined_data11'=>$nit,
    'merchant_defined_data14'=>'Servicios','merchant_defined_data19'=>$nit,'merchant_defined_data24'=>1,
    'merchant_defined_data87'=>'0575847','merchant_defined_data88'=>'Confiared S.R.L.','merchant_defined_data90'=>'confiared product','merchant_defined_data91'=>$amountfinalcurrency,
    'state'=>'BOS','bill_to_address_country'=>'BO','bill_to_address_line1'=>'28 C/Genesis UV77 MZ36','bill_to_address_state'=>'S','bill_to_email'=>$datacustomer['email'],'bill_to_forename'=>$customername,'bill_to_surname'=>$customername,'bill_to_address_city'=>'Santa cruz de la sierra','bill_to_address_postal_code'=>'00000');
$params=array_merge(Array('access_key'=>$cybspub,
'profile_id'=>$cybsid,
'transaction_uuid'=>$transaction_uuid,
'signed_field_names'=>'access_key,profile_id,transaction_uuid,signed_field_names,unsigned_field_names,signed_date_time,locale,transaction_type,reference_number,amount,currency,'.implode(',',array_keys($merchant)),
'unsigned_field_names'=>'',
'signed_date_time'=>gmdate("Y-m-d\TH:i:s\Z"),
'locale'=>$locale,
'transaction_type'=>'sale',
'reference_number'=>time(),
'amount'=>$amountfinalcurrency,
'currency'=>$currency,
'submit'=>'Submit'),$merchant);
file_put_contents('95xcwCtkELBuUa8fqqtztnSx/cybersource/'.$transaction_uuid.'.json',json_encode($_POST));
foreach($params as $name => $value)
    if($name!='submit')
        echo "<input type=\"hidden\" id=\"" . $name . "\" name=\"" . $name . "\" value=\"" . $value . "\"/>\n";
echo "<input type=\"hidden\" id=\"signature\" name=\"signature\" value=\"" . sign($params) . "\"/>\n";
?>
</form>
<script>
window.onload = function(){
  document.getElementById('payment_confirmation').submit();
}
</script>
</body>
</html>
<?php
exit;
}
header('Location: /?return=Isupm');
exit;
