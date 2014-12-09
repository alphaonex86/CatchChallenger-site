<?php
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

if(file_exists('config.php'))
    require 'config.php';
else
    require '../config.php';
if(!isset($_GET['commit']))
    die('Var commit not found');
if(!isset($_GET['key']))
    die('Var key not found');
if(!isset($_GET['platform']))
    die('Var platform not found');
if(!isset($_GET['details']))
    die('Var details not found');
if(!preg_match('#^[a-z0-9A-Z\- ]+$#i',$_GET['platform']))
    die('Var platform wrong');
if(!preg_match('#^[a-z0-9A-Z]+$#i',$_GET['commit']))
    die('Var commit wrong');
if($benchmark_key!=$_GET['key'])
    die('Var key wrong');

//get the commit order
$asso=array();
$asso_reverse=array();
{
    $pwd=getcwd();
    
    if(chdir($git_source_program)===TRUE)
    {
        exec('git pull origin master');
        exec('git log --reverse --pretty=format:"%H" --date=short',$commit_list);
        exec('git log --reverse --pretty=format:"%ct" --date=short',$date_list);
        if(count($commit_list)==count($date_list))
        {
            $asso=array(array_combine($commit_list,$date_list))[0];
            $asso_reverse=array(array_combine($date_list,$commit_list))[0];
        }
    }
    
    chdir($pwd);
}

if(isset($_GET['failed']))
{
    if(!is_dir('failed/'))
        if(!mkdir('failed/',0700,true))
            die('Unable to create the folder failed');

    $file='failed/'.$_GET['commit'].'.data';
    $failed_list=array();
    if(is_file($file))
    {
        $content=file_get_contents($file);
        if($content!='')
            $failed_list=unserialize($content);
    }
    if(!isset($failed_list[$_GET['platform']]))
        $failed_list[$_GET['platform']]=array();
    if(!in_array($_GET['details'],$failed_list[$_GET['platform']]))
        $failed_list[$_GET['platform']][]=$_GET['details'];
    filewrite($file,serialize($failed_list));
}
else
{
    //results
    $results_to_check=array('connectAllPlayer','idle','move','chat');
    $results=array();
    foreach($results_to_check as $result)
    {
        if(isset($_GET[$result]))
        {
            if(!preg_match('#^[0-9]+$#',$_GET[$result]))
                die('Var value '.$_GET[$result].' wrong: '.$_GET[$result]);
            else
                $results[$result]=$_GET[$result];
        }
    }
    if(count($results)<=0)
        die('No result send');

    $folder='results/'.$_GET['platform'].'/';
    $file=$folder.crc32($_GET['details']).'.data';
    $file_json=$folder.crc32($_GET['details']).'.json';
    if(!is_dir($folder))
        if(!mkdir($folder,0700,true))
            die('Unable to create the folder '.$folder);

    $json_result=array();
    if(is_file($file))
    {
        $content=file_get_contents($file);
        if($content!='')
            $json_result=unserialize($content);
    }

    if(!isset($json_result['details']))
        $json_result['details']=$_GET['details'];
    foreach($results as $key=>$result)
    {
        if(isset($asso[$_GET['commit']]))
        {
            $commit_key=$asso[$_GET['commit']];
            if(!isset($json_result[$key]))
                $json_result[$key]=array();
            if(!isset($json_result[$key][$commit_key]))
                $json_result[$key][$commit_key]=array('commit'=>$_GET['commit'],'result'=>array());
            //for average with the || true
            if($result>3000 || true/*for minimal value*/)
                $json_result[$key][$commit_key]['result'][]=$result;
            else
            {
                if(count($json_result[$key][$commit_key])<=0)
                    $json_result[$key][$commit_key]['result'][]=$result;
            }
            ksort($json_result[$key]);
        }
        else
            echo 'commit not found: '.$_GET['commit'];
    }
    ksort($json_result);

    filewrite($file,serialize($json_result));
    filewrite($file_json,json_encode($json_result));
}
