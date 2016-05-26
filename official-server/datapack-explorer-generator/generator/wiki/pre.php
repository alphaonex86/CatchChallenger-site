<?php
if(!isset($datapackexplorergeneratorinclude))
    die('abort into wiki post'."\n");

define('MEDIAWIKI',true);
define('CACHE_NONE',true);

if(!isset($pagetodointowiki))
    $pagetodointowiki=array();
if(!isset($pageintowikiduplicate))
    $pageintowikiduplicate=array();


function savewikipage($page,$content,$createonly=false,$summary='')
{
    global $pagetodointowiki,$pageintowikiduplicate;
    if(in_array($page,$pageintowikiduplicate))
    {
        debug_print_backtrace();
        echo 'Page duplicate: '.$page."\n";
        exit;
    }
    $pageintowikiduplicate[]=$page;

    if(/*FAKE:*/false)
    {
        $pagetodointowiki=array();
        return;
    }

    $pagetodointowiki[]=array($page,$content,$createonly,$summary);
    if(count($pagetodointowiki)>50)
        savewikipagereal();
}

function savewikipagereal()
{
    global $pagetodointowiki;
    global $wikivars,$base_datapack_site_http,$curlmaster;
    global $finalwikitoken;

    if(/*FAKE:*/false)
    {
        $pagetodointowiki=array();
        return;
    }

    $curl_arr=array();
    $curlmaster = curl_multi_init();
    foreach($pagetodointowiki as $contententry)
    {
        $page=$contententry[0];
        $content=$contententry[1];
        $createonly=$contententry[2];
        $summary=$contententry[3];
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
        curl_multi_add_handle($curlmaster, $ch);
        $curl_arr[]=$ch;        
    }
    do {
        curl_multi_exec($curlmaster,$running);
        usleep(10*1000);
    } while($running > 0);
    foreach($curl_arr as $curl_arr_desc)
    {
        $content=curl_multi_getcontent($curl_arr_desc);
        if(!($result=unserialize($content)))
        {
            debug_print_backtrace();
            die('Error to edit: '.$content.', postdata: '.$postdata);
        }
        if(isset($result['error']) || !isset($result['edit']['result']) || $result['edit']['result']!='Success' || 
            (!isset($result['edit']['newtimestamp']) && !isset($result['edit']['nochange']))
            )
        {
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
    }
    curl_multi_close($curlmaster);
    $pagetodointowiki=array();
    //echo 'Extract: '.$page.' for '.$base_datapack_site_http."\n";
}