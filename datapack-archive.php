<?php
$cachebasepath='/tmp/';
$cache=false;

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

$file = fopen("/tmp/cc-gen-tar","w+");
if(flock($file,LOCK_EX))
{
    if($cache)
    {
        if(file_exists($cachetar))
        {
            if(filemtime($cachetar)<=time() && filemtime($cachetar)>(time()-5*60))
            {
                header('Content-type: application/x-xz');
                header('From-cache: yes');
                echo file_get_contents($cachetar);
                flock($file,LOCK_UN);
                fclose($file);
                exit;
            }
            else
                header('From-cache: too old');
        }
        else
            header('From-cache: no file');
    }
    else
        header('From-cache: not probed');

    ob_start("callback");
    @exec('./datapack-archive.sh');
    ob_end_flush();
    flock($file,LOCK_UN);
    fclose($file);
}
else
    die('Error locking file!');

header('Content-type: application/x-xz');
echo file_get_contents($cachetarxz);
