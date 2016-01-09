<?php
if (!function_exists('curl_init')){
        die('Sorry cURL is not installed!');
    }

require '../config.php';
$mirrorserverlisttemp=array();
foreach($mirrorserverlist as $host)
    $mirrorserverlisttemp[$host]=array('state'=>'up');

function listFolder($folder,$foldersuffix='')
{
    if(!preg_match('#/$#',$folder))
        $folder.='/';
    if(!preg_match('#/$#',$foldersuffix) && $foldersuffix!='')
        $foldersuffix.='/';
    $arr=array();
    if($handle = opendir($folder.$foldersuffix)) {
        while(false !== ($entry = readdir($handle)))
        {
            if($entry != '.' && $entry != '..')
            {
                if(preg_match('#^[0-9/a-z\\.\\- _]+$#',$foldersuffix) || $foldersuffix=='')
                {
                    if(is_file($folder.$foldersuffix.$entry))
                    {
                        if(preg_match('#\\.(tmx|xml|tsx|js|png|jpg|gif|ogg|qml|qm|ts|txt)$#',$entry))
                        {
                            if(preg_match('#^[0-9/a-z\\.\\- _]*[0-9a-z]\\.[a-z]{2,4}$#',$foldersuffix.$entry))
                                $arr[]=$foldersuffix.$entry;
                        }
                    }
                    else if(is_dir($folder.$foldersuffix.$entry))
                        $arr=array_merge($arr,listFolder($folder,$foldersuffix.$entry.'/'));
                }
            }
        }
        closedir($handle);
    }
    return $arr;
}
$arr=listFolder($datapack_path);
sort($arr);

foreach($arr as $file)
{
    $contentlocal=file_get_contents($datapack_path.$file);
    foreach($mirrorserverlisttemp as $server=>$content)
    {
        if($content['state']=='up')
        {
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$server.$file);
            curl_setopt($ch, CURLOPT_HEADER,0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT,10);
            $contentremote = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $errno = curl_errno($ch);
            curl_close($ch);
            
            if($errno)
            {
                $error_message = curl_strerror($errno);
                echo "cURL error ({$errno}):\n {$error_message}";
                $mirrorserverlisttemp[$server]['state']='down';
            }
            else if($httpcode!=200)
                $mirrorserverlisttemp[$server]['state']='corrupted';
            else
            {
                if($contentlocal!=$contentremote)
                    $mirrorserverlisttemp[$server]['state']='corrupted';
            }
        }
    }
}

echo json_encode($mirrorserverlisttemp);
