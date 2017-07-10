<?php
$cachebasepath='/tmp/';
$cache=true;

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
{
    if(file_exists($cachetar))
    {
        if(filemtime($cachetar)<=time())
        {
            if(filemtime($cachetar)>(time()-5*60))
            {
                header('Content-type: application/x-xz');
                header('From-cache: yes');
                echo file_get_contents($cachetarxz);
                exit;
            }
            else
                header('From-cache: too old: '.filemtime($cachetar).'>'.(time()-5*60).' for '.$cachetar);
        }
        else
            header('From-cache: into the future');
    }
    else
        header('From-cache: no file');
}
else
    header('From-cache: not probed');
    
$file = fopen("/tmp/cc-gen-tar56465","w+");
if(flock($file,LOCK_EX))
{
    if($cache)
    {
        if(file_exists($cachetar))
        {
            if(filemtime($cachetar)<=time())
            {
                if(filemtime($cachetar)>(time()-5*60))
                {
                    header('Content-type: application/x-xz');
                    header('From-cache: yes');
                    echo file_get_contents($cachetarxz);
                    flock($file,LOCK_UN);
                    fclose($file);
                    exit;
                }
                else
                    header('From-cache: too old: '.filemtime($cachetar).'>'.(time()-5*60).' for '.$cachetar);
            }
            else
                header('From-cache: into the future');
        }
        else
            header('From-cache: no file');
    }
    else
        header('From-cache: not probed');
    ob_start();
    @exec('./datapack-archive.sh',$output);
    ob_end_flush();
    flock($file,LOCK_UN);
    fclose($file);
}
else
    die('Error locking file!');

header('Content-type: application/x-xz');
if($cachetarxz<3*1024*1024)
    filewrite('/tmp/bug2',implode("\n",$output));
else if(count($output)>0)
    filewrite('/tmp/bug3',implode("\n",$output));
if(file_exists($cachetarxz))
    echo file_get_contents($cachetarxz);
else
{
    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
    die();
}
