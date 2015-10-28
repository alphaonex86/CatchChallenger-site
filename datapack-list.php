<?php
header('Content-type: text/plain');
function listFolder($folder)
{
    if(preg_match('#/diff/?$#isU',$folder) || preg_match('#^diff/?$#isU',$folder) || preg_match('#/pack/?$#isU',$folder) || preg_match('#^pack/?$#isU',$folder))
        return array();
    if(!preg_match('#/$#',$folder))
        $folder.='/';
    $arr=array();
    if($handle = opendir($folder)) {
        while(false !== ($entry = readdir($handle)))
        {
            if($entry != '.' && $entry != '..')
            {
                if(preg_match('#^[0-9/a-z\\.\\- _]+$#',$folder))
                {
                    if(is_file($folder.$entry))
                    {
                        if(preg_match('#\\.(tmx|xml|tsx|js|png|jpg|gif|ogg|qml|qm|ts|txt)$#',$entry))
                        {
                            if(preg_match('#^[0-9/a-z\\.\\- _]*[0-9a-z]\\.[a-z]{2,4}$#',$folder.$entry))
                                $arr[]=$folder.$entry;
                        }
                    }
                    else if(is_dir($folder.$entry))
                        $arr=array_merge($arr,listFolder($folder.$entry.'/'));
                }
            }
        }
        closedir($handle);
    }
    return $arr;
}

if(isset($argv))
    foreach ($argv as $arg)
    {
        $e=explode("=",$arg);
        if(count($e)==2)
            $_GET[$e[0]]=$e[1];
        else    
            $_GET[$e[0]]=0;
    }

if(!isset($_GET['main']))
    $folder='datapack/';
else
{
    if(!preg_match('#^[a-z0-9]+$#',$_GET['main']))
        die('main with wrong char');
    if(!isset($_GET['sub']))
        $folder='datapack/map/main/'.$_GET['main'].'/';
    else
    {
        if(!preg_match('#^[a-z0-9]+$#',$_GET['sub']))
            die('sub with wrong char: "'.$_GET['sub'].'"');
        $folder='datapack/map/main/'.$_GET['main'].'/sub/'.$_GET['sub'].'/';
    }
}
if(!is_dir($folder))
{
    echo 'The folder '.$folder.' don\'t exists!';
    exit;
}

/** \note substr(hash_file('sha224',$file),0,8) vs substr(hash_file('sha224',$file,true),0,4)
Binary 7% size drop for gzip
\n drop for hash allow 10% reduction into xz, 7% into gzip
If split into 2 file gain 100Byte but this 100Byte is lost by the http header
Mostly is for base:
* 7KB for file list
* 500Bytes is for the file size
* 12KB for partial hash (non compressible)
Mostly is for main:
* 2200Bytes for file list
* 500Bytes is for the file size
* 2200Bytes for partial hash (non compressible)
*/

function partialhashfile($file)
{
    $binary=true;
    if($binary==true)
        return substr(hash_file('sha224',$file,true),0,4);
    else
        return substr(hash_file('sha224',$file),0,8);
}

$arr=listFolder($folder);
sort($arr);
if(!isset($_GET['main']))
{
    //base
    foreach($arr as $file)
    {
        //datapack/datapack-list/base.txt
        if(!preg_match('#^[\\/]*datapack[\\/]+datapack-list[\\/]+#',$file) && !preg_match('#^[\\/]*datapack[\\/]+map[\\/]+main[\\/]+#',$file))
            echo str_replace($folder,'',$file).' '.(int)ceil(filesize($file)/1000)."\n";
    }
    echo '-'."\n";
    foreach($arr as $file)
    {
        //datapack/datapack-list/base.txt
        if(!preg_match('#^[\\/]*datapack[\\/]+datapack-list[\\/]+#',$file) && !preg_match('#^[\\/]*datapack[\\/]+map[\\/]+main[\\/]+#',$file))
            echo partialhashfile($file);
    }
}
else if(!isset($_GET['sub']))
{
    //main
    foreach($arr as $file)
    {
        if(!preg_match('#^[\\/]*datapack[\\/]+map[\\/]+main[\\/]+'.$_GET['main'].'[\\/]+sub[\\/]+#',$file))
            echo str_replace($folder,'',$file).' '.(int)ceil(filesize($file)/1000)."\n";
    }
    echo '-'."\n";
    foreach($arr as $file)
    {
        if(!preg_match('#^[\\/]*datapack[\\/]+map[\\/]+main[\\/]+'.$_GET['main'].'[\\/]+sub[\\/]+#',$file))
            echo partialhashfile($file);
    }
}
else
{
    //sub
    foreach($arr as $file)
        echo str_replace($folder,'',$file).' '.(int)ceil(filesize($file)/1000)."\n";
    echo '-'."\n";
    foreach($arr as $file)
        echo partialhashfile($file);
}