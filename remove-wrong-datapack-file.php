<?php
function rmPath($folder)
{
    if(preg_match('#/diff/?$#isU',$folder) || preg_match('#^diff/?$#isU',$folder) || preg_match('#/pack/?$#isU',$folder) || preg_match('#^pack/?$#isU',$folder))
        return array();
    if(!preg_match('#/$#',$folder))
        $folder.='/';
    $arr=array();
    if(is_dir($folder))
        if($handle = opendir($folder))
        {
            while(false !== ($entry = readdir($handle)))
            {
                if($entry != '.' && $entry != '..')
                {
                    if(is_file($folder.$entry))
                        unlink($folder.$entry);
                    else if(is_dir($folder.$entry))
                        rmPath($folder.$entry.'/');
                }
            }
            closedir($handle);
            rmdir($folder);
        }
}
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
                            if(!preg_match('#^[0-9/a-z\\.\\- _]*[0-9a-z]\\.[a-z]{2,4}$#',$folder.$entry))
                                unlink($folder.$entry);
                        }
                        else
                            unlink($folder.$entry);
                    }
                    else if(is_dir($folder.$entry))
                        $arr=array_merge($arr,listFolder($folder.$entry.'/'));
                }
                else
                    rmPath($folder);
            }
        }
        closedir($handle);
    }
    return $arr;
}
$arr=listFolder('./');
