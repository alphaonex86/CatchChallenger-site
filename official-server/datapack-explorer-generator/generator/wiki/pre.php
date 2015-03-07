<?php
if(!isset($datapackexplorergeneratorinclude))
    die('abort into wiki post'."\n");

define('MEDIAWIKI',true);
define('CACHE_NONE',true);

function savewikipage($page,$content,$createonly=false,$summary='')
{
    global $wikivars,$base_datapack_site_http;
    global $finalwikitoken;
    /* edit page */
    $postdata='action=edit&format=php&title='.urlencode($page).'&text='.urlencode($content).'&token='.urlencode($finalwikitoken);
    if($summary!='')
        $postdata.='&summary='.urlencode($summary);
    if($createonly)
        $postdata.='&createonly';
    $ch=curl_init();
    curl_setopt_array($ch, $wikivars['curloptions']);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:')); //Fixes the HTTP/1.1 417 Expectation Failed Bug
    curl_setopt($ch, CURLOPT_URL, $base_datapack_site_http.'/'.$wikivars['wikiFolder'].'/api.php');
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