<?php
if(!isset($datapackexplorergeneratorinclude))
    die('abort into wiki init'."\n");

if(!isset($wikivars['generatefullpage']))
    $wikivars['generatefullpage']=false;
//init

$other_error='';
$_SESSION['login_result']='';
date_default_timezone_set('UTC');
$wikivars['lastmod']=date('Y-m-d H:i',getlastmod()).' UTC';
$wikivars['useragent']='Bot catchchallenger@first-world.info';
$wikivars['cookiefile']=tempnam('/tmp','CURLCOOKIE');
$wikivars['curloptions']=array(
        CURLOPT_COOKIEFILE => $wikivars['cookiefile'],
        CURLOPT_COOKIEJAR => $wikivars['cookiefile'],
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_USERAGENT => $wikivars['useragent'],
        CURLOPT_POST => true
    );

//login
$postdata='action=login&format=php&lgname='.$wikivars['username'].'&lgpassword='.$wikivars['password'];
$curl_error='';
$ch=curl_init();
    curl_setopt_array($ch, $wikivars['curloptions']);
    curl_setopt($ch, CURLOPT_URL, $base_datapack_site_http.'/'.$wikivars['wikiFolder'].'/api.php');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    $content=curl_exec($ch);
    if(!($result=unserialize($content)))
    {
        $other_error='Error to decode the reply: '.$content;
        echo 'Error to decode the reply: '.$content.' for '.$base_datapack_site_http.'/'.$wikivars['wikiFolder'].'/api.php?'.$postdata."\n";
    }
    if(curl_errno($ch)){
        $curl_error='Error 003: '.curl_error($ch);
    }
curl_close($ch);

// Basic error check + Confirm token
if($curl_error)
    $domain_error=$curl_error;
else if($result['login']['result']=='NeedToken')
{
    if(!empty($result['login']['token']))
    {
        $_SESSION['logintoken']=$result['login']['token'];
        $postdata='action=login&format=php&lgname='.$wikivars['username'].'&lgpassword='.$wikivars['password'].'&lgtoken='.$_SESSION['logintoken'];
        $ch=curl_init();
        curl_setopt_array($ch, $wikivars['curloptions']);
        curl_setopt($ch, CURLOPT_URL, $base_datapack_site_http.'/'.$wikivars['wikiFolder'].'/api.php');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        $result=unserialize(curl_exec($ch));
        if(curl_errno($ch))
            $curl_error= 'Error 004: '.curl_error($ch);
        curl_close($ch);
    }
    else
        $other_error='Error 006: Token error.';
}


// Check for all documented errors
if($curl_error)
    $domain_error=$curl_error;
else if($result['login']['result']=='Success')
{
    $_SESSION['login_result']=$result['login']['result'];
    $_SESSION['login_lguserid']=$result['login']['lguserid'];
    $_SESSION['login_lgusername']=$result['login']['lgusername'];
}
else
    switch($result['login']['result'])
    {
        case 'NeedToken':
            $other_error='Error 005: Token error.';
        break;
        case 'NoName':
            $other_error='The username can not be blank';
        break;
        case 'Illegal':
            $other_error='You provided an illegal username';
        break;
        case 'NotExists':
            $other_error='The username you provided doesn\'t exist';
        break;
        case 'EmptyPass':
            $other_error='The password can not be blank';
        break;
        case 'WrongPluginPass':
        case 'WrongPass':
            $other_error='The password you provided is incorrect';
        break;
        case 'CreateBlocked':
            $other_error='Autocreation was blocked from this IP address';
        break;
        case 'Throttled':
            $other_error='You\'ve logged in too many times in a short time. Try again later.';
        break;
        case 'mustbeposted':
            $other_error='Error 004: Logindata was not send correctly';
        break;
        case 'Blocked':
            $other_error='This account is blocked.';
        break;
        default:
            if(isset($result['login']['result']) && $result['login']['result']!='')
                $other_error='Error 001: An unknown event occurred.';
            else
                $other_error='Error 002: An unknown event occurred.';
        break;
    }
if($_SESSION['login_result']!=='Success')
{
    $wiki_error=true;
    echo 'Login error. '.$other_error;
}
else
{
    $wiki_error=false;
    //token
    $postdata='action=tokens&format=php';
    $ch=curl_init();
    curl_setopt_array($ch, $wikivars['curloptions']);
    curl_setopt($ch, CURLOPT_URL, $base_datapack_site_http.'/'.$wikivars['wikiFolder'].'/api.php');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    $content=curl_exec($ch);
    if(!($resulttoken=unserialize($content)))
        die('Not able to unserialize:'.$content);
    if($resulttoken['tokens']['edittoken']=='')
        die('Edit token wrong');
    $finalwikitoken=$resulttoken['tokens']['edittoken'];

    require '../'.$wikivars['wikiFolder'].'/LocalSettings.php';
    $wgDBprefix_final=$wgDBprefix;
    if($wgDBtype!='mysql')
        echo('Only mysql purge supported');
    else
    {
        $wikidblink=mysqli_connect($wgDBserver,$wgDBuser,$wgDBpassword,$wgDBname);
        if(!$wikidblink)
            die('Mysql wiki: unable to connect');
        else
        {
        }
    }
}
