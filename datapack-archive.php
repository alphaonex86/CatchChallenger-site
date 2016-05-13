<?php
$cachebasepath='/tmp/';
$cache=false;

header('Content-type: application/x-xz');

if(!is_dir($cachebasepath))
    mkdir($cachebasepath,0755,true);
if(!isset($_GET['main']))
{
    $folder='datapack/';
    $cachetar=$cachebasepath.'datapack.tar';
    $cachetarxz=$cachetar.'.xz';
}
else
{
    if(!preg_match('#^[a-z0-9]+$#',$_GET['main']))
        die('main with wrong char');
    if(!isset($_GET['sub']))
    {
        $folder='datapack/map/main/'.$_GET['main'].'/';
        $cachetar=$cachebasepath.'datapack-main-'.$_GET['main'].'.tar';
        $cachetarxz=$cachetar.'.xz';
    }
    else
    {
        if(!preg_match('#^[a-z0-9]+$#',$_GET['sub']))
            die('sub with wrong char: "'.$_GET['sub'].'"');

        $folder='datapack/map/main/'.$_GET['main'].'/sub/'.$_GET['sub'].'/';
        $cachetar=$cachebasepath.'datapack-sub-'.$_GET['main'].'-'.$_GET['sub'].'.tar';
        $cachetarxz=$cachetar.'.xz';
    }
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

if($cache)
    if(file_exists($cachetar))
        if(filemtime($cachetar)<=time() && filemtime($cachetar)>(time()-5*60))
        {
            echo file_get_contents($cachetar);
            exit;
        }

exec('./datapack-archive.sh');

header('Content-type: application/x-xz');
echo file_get_contents($cachetarxz);
