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
        while(false !== ($entry = readdir($handle))) {
            if($entry != '.' && $entry != '..') {
                if(is_file($folder.$entry))
                {
                    if(preg_match('#\\.(tmx|xml|tsx|js|png|jpg|gif|ogg|qml|qm|ts|txt)$#',$entry))
                        if(preg_match('#^[0-9/a-zA-Z\\.\\- _]*[0-9a-zA-Z]\\.[a-z]{2,4}$#',$entry))
                            $arr[]=$folder.$entry;
                }
                else if(is_dir($folder.$entry))
                    $arr=array_merge($arr,listFolder($folder.$entry.'/'));
            }
        }
        closedir($handle);
    }
    return $arr;
}
$folder='datapack/';
$arr=listFolder($folder);
sort($arr);
foreach($arr as $file)
{
    if($file!='datapack/datapack-list.txt')
        echo str_replace($folder,'',$file).' '.substr(hash_file('sha224',$file),0,8).' '.(int)ceil(filesize($file)/1000)."\n";
}