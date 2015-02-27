<?php
if(!isset($datapackexplorergeneratorinclude))
    die('abort into wiki post'."\n");

if(isset($wikivarsapp['apiURL']) && isset($wikivarsapp['username']) && isset($wikivarsapp['password']))
{
    if(!isset($wikivarsapp['generatefullpage']))
        $wikivarsapp['generatefullpage']=false;
    //init
    session_start();
    $_SESSION['login_result']='';
    date_default_timezone_set('UTC');
    $wikivarsapp['lastmod']=date('Y-m-d H:i',getlastmod()).' UTC';
    $wikivarsapp['useragent']='Bot catchchallenger@first-world.info';
    $wikivarsapp['cookiefile']=tempnam('/tmp','CURLCOOKIE');
    $wikivarsapp['curloptions']=array(
            CURLOPT_COOKIEFILE => $wikivarsapp['cookiefile'],
            CURLOPT_COOKIEJAR => $wikivarsapp['cookiefile'],
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_USERAGENT => $wikivarsapp['useragent'],
            CURLOPT_POST => true
        );
    
    //login
    $postdata='action=login&format=php&lgname='.$wikivarsapp['username'].'&lgpassword='.$wikivarsapp['password'];
    $curl_error='';
    $ch=curl_init();
        curl_setopt_array($ch, $wikivarsapp['curloptions']);
        curl_setopt($ch, CURLOPT_URL, $wikivarsapp['apiURL']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        $result=unserialize(curl_exec($ch));
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
            $postdata='action=login&format=php&lgname='.$wikivarsapp['username'].'&lgpassword='.$wikivarsapp['password'].'&lgtoken='.$_SESSION['logintoken'];
            $ch=curl_init();
            curl_setopt_array($ch, $wikivarsapp['curloptions']);
            curl_setopt($ch, CURLOPT_URL, $wikivarsapp['apiURL']);
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
        die('Login error. '.$other_error);
    
    //token
    $postdata='action=tokens&format=php';
    $ch=curl_init();
    curl_setopt_array($ch, $wikivarsapp['curloptions']);
    curl_setopt($ch, CURLOPT_URL, $wikivarsapp['apiURL']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    $content=curl_exec($ch);
    if(!($resulttoken=unserialize($content)))
        die('Not able to unserialize:'.$content);
    if($resulttoken['tokens']['edittoken']=='')
        die('Edit token wrong');
    $finalwikitoken=$resulttoken['tokens']['edittoken'];

    define('MEDIAWIKI',true);
    define('CACHE_NONE',true);
    require '../w2/LocalSettings.php';
    if($wgDBtype!='mysql')
        echo('Only mysql purge supported');
    else
    {
        $wikidblink=mysql_connect($wgDBserver,$wgDBuser,$wgDBpassword,true);
        if(!$wikidblink)
            die('Mysql wiki: unable to connect');
        else if(!mysql_select_db($wgDBname,$wikidblink))
            die('Mysql wiki: unable to select the db');
        else
        {
        }
    }
}

function savewikipage($page,$content,$summary='')
{
    global $wikivarsapp;
    global $finalwikitoken;
    /* edit page */
    if($summary!='')
        $postdata='action=edit&format=php&title='.urlencode($page).'&text='.urlencode($content).'&token='.urlencode($finalwikitoken).'&summary='.urlencode($summary);
    else
        $postdata='action=edit&format=php&title='.urlencode($page).'&text='.urlencode($content).'&token='.urlencode($finalwikitoken);
    $ch=curl_init();
    curl_setopt_array($ch, $wikivarsapp['curloptions']);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:')); //Fixes the HTTP/1.1 417 Expectation Failed Bug
    curl_setopt($ch, CURLOPT_URL, $wikivarsapp['apiURL']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    $content=curl_exec($ch);
    if(!($result=unserialize($content)))
    {
        debug_print_backtrace();
        die('Error to edit: '.$content.', postdata: '.$postdata);
    }
    if(isset($result['error']) || !isset($result['edit']['result']) || $result['edit']['result']!='Success' || 
        (!isset($result['edit']['newtimestamp']) && !isset($result['edit']['nochange']))
        )
        if(isset($result['error']))
        {
            debug_print_backtrace();
            die('Error to edit: '.$result['error']['info'].', postdata: '.$postdata);
        }
        else
        {
            debug_print_backtrace();
            echo 'Error to edit: ';
            print_r($result);
            echo 'postdata: '.$postdata;
            exit;
        }
}