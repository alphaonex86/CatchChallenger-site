<?php
$ip='';
if(isset($_SERVER['REMOTE_ADDR']))
{
    if(strpos($_SERVER['REMOTE_ADDR'],'2803:1920:')===0 || strpos($_SERVER['REMOTE_ADDR'],'172.20.')===0 || strpos($_SERVER['REMOTE_ADDR'],'172.16.')===0)
    {
        if(isset($_SERVER['X-Real-IP']))
            $ip=$_SERVER['X-Real-IP'];
        else if(isset($_SERVER['X-Forwarded-For']))
            $ip=$_SERVER['X-Forwarded-For'];
        else if(isset($_SERVER['HTTP_CLIENT_IP']))
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        else
            $ip=$_SERVER['REMOTE_ADDR'];
    }
    else
        $ip=$_SERVER['REMOTE_ADDR'];
}

if(!isset($_SERVER['REMOTE_ADDR']) || $ip=='')
    die('Buscar su IPv6, usted no esta en IPv6, bug');
if(strpos($ip,':')===FALSE)
    die('Buscar su IPv6, usted no esta en IPv6: '.$ip);
echo base64_encode($ip);

