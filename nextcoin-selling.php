<?php
require_once 'config.php';

function send_mail($title,$text,$to,$type,$from)
{
    $headers = 'From: '.$from."\r\n";
    $headers .= 'MIME-Version: 1.0'."\r\n";
    $headers .= 'Content-type: '.$type.'; charset=UTF-8'."\r\n";
    $return=@mail($to,'=?UTF-8?B?'.base64_encode($title).'?=',$text,$headers);
    return $return;
}

function filewrite($file,$content)
{
    if($filecurs=fopen($file, 'w'))
    {
        if(fwrite($filecurs,$content) === false)
            die('Unable to write the file: '.$file);
        fclose($filecurs);
    }
    else
        die('Unable to write or create the file: '.$file);
}

if($nxt_secretPhrase=='')
    die('Wrong $nxt_secretPhrase');
if($nxt_secretPhrase=='')
    die('Wrong $nxt_product_id');
if(!preg_match('#^NXT-#isU',$nxt_seller))
    die('Wrong $nxt_seller');

if(file_exists('key-nextcoin.txt') && $key_nextcoin=file_get_contents('key-nextcoin.txt'))
{}
else
    die('key-nextcoin.txt not found');

if(file_exists('key-nextcoin-sold.txt'))
    $key_nextcoin_sold=file_get_contents('key-nextcoin-sold.txt');
else
    die('key-nextcoin-sold.txt not found');

$key_nextcoin_list=explode("\n",$key_nextcoin);
$key_nextcoin_sold_list=explode("\n",$key_nextcoin_sold);

if($key_nextcoin_list[0]=='')
    unset($key_nextcoin_list[0]);

$org_count=count($key_nextcoin_list);

$jsonurl = 'http://localhost:7876/nxt?requestType=getDGSPendingPurchases&seller='.$nxt_seller;
$json = file_get_contents($jsonurl,0,null,null);
$json_output = json_decode($json,true);
foreach($json_output['purchases'] as $entryPending)
{
    if(isset($entryPending['pending']) && isset($entryPending['goods']))
    {
        if($entryPending['pending'] && $entryPending['goods']==$nxt_product_id)
        {
            if(count($key_nextcoin_list)>=$entryPending['quantity'])
            {
                $key_list=array();
                $index=0;
                while($index<$entryPending['quantity'])
                {
                    foreach($key_nextcoin_list as $id=>$key)
                    {
                        $key_list[]=$key;
                        $key_nextcoin_sold_list[]=$key.';'.$entryPending['buyerRS'].';'.$entryPending['purchase'].';'.$entryPending['timestamp'];
                        unset($key_nextcoin_list[$id]);
                        break;
                    }
                    $index++;
                }
                $jsonurl_message = 'http://127.0.0.1:7876/nxt?requestType=encryptTo&recipient='.$entryPending['buyerRS'].'&messageToEncrypt='.urlencode(implode(',',$key_list)).'&messageToEncryptIsText=true&secretPhrase='.urlencode($nxt_secretPhrase);
                $json_message = file_get_contents($jsonurl_message,0,null,null);
                $json_output_message = json_decode($json_message,true);
                if(isset($json_output_message['data']))
                {
                    print_r($json_output_message);
                    print_r($entryPending);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL,'http://127.0.0.1:7876/nxt?requestType=dgsDelivery');
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS,'recipient='.$entryPending['buyerRS'].'&purchase='.$entryPending['purchase'].'&goodsToEncrypt=true&goodsIsText=true&secretPhrase='.urlencode($nxt_secretPhrase).'&goodsData='.$json_output_message['data'].'&goodsNonce='.$json_output_message['nonce'].'&deadline=1440&feeNQT=100000000');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $server_output = curl_exec ($ch);
                    curl_close ($ch);
                    print_r(json_decode($server_output,true));
                }
                else
                {
                    send_mail('Data not encrypted','Data not encrypted for nextcoin goods for '.$_SERVER['SERVER_NAME'],$admin_email,'text/plain',$admin_email);
                    die('Data not encrypted');
                }
            }
            else
            {
                send_mail('No more key','No more key for nextcoin goods for '.$_SERVER['SERVER_NAME'],$admin_email,'text/plain',$admin_email);
                die('No more key');
            }
        }
        else
            echo 'ignoring, pending: '.$entryPending['pending'].' || goods!='.$nxt_product_id;
    }
    else
    {
        send_mail('Missing value','Missing value for nextcoin goods for '.$_SERVER['SERVER_NAME'],$admin_email,'text/plain',$admin_email);
        echo 'Missing value';
    }
}

if($org_count!=count($key_nextcoin_list))
{
    filewrite('key-nextcoin.txt',implode("\n",$key_nextcoin_list));
    filewrite('key-nextcoin-sold.txt',implode("\n",$key_nextcoin_sold_list));
}

