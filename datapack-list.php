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
Binary vs text for gzip have same size, gzip vs xz is small difference, and binary vs text file in xz the difference is minor */

$arr=listFolder($folder);
sort($arr);
if(!isset($_GET['main']))
    foreach($arr as $file)
    {
        //datapack/datapack-list/base.txt
        if(!preg_match('#^[\\/]*datapack[\\/]+datapack-list[\\/]+#',$file) && !preg_match('#^[\\/]*datapack[\\/]+map[\\/]+main[\\/]+#',$file))
            echo str_replace($folder,'',$file).' '.substr(hash_file('sha224',$file),0,8).' '.(int)ceil(filesize($file)/1000)."\n";
    }
else if(!isset($_GET['sub']))
    foreach($arr as $file)
    {
        if(!preg_match('#^[\\/]*datapack[\\/]+map[\\/]+main[\\/]+'.$_GET['main'].'[\\/]+sub[\\/]+#',$file))
            echo str_replace($folder,'',$file).' '.substr(hash_file('sha224',$file),0,8).' '.(int)ceil(filesize($file)/1000)."\n";
    }
else
    foreach($arr as $file)
            echo str_replace($folder,'',$file).' '.substr(hash_file('sha224',$file),0,8).' '.(int)ceil(filesize($file)/1000)."\n";
