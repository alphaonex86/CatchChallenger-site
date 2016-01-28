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
        if(!isset($mirrorserverlisttemp[$server]['time']))
            $mirrorserverlisttemp[$server]['time']=0.0;
        if($content['state']=='up')
        {
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$server.$file);
            curl_setopt($ch, CURLOPT_HEADER,0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT,10);
            curl_setopt($ch, CURLOPT_TCP_NODELAY, 1); 
            $time_start = microtime(true);
            $contentremote = curl_exec($ch);
            $time_end = microtime(true);
            $time = $time_end - $time_start;
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $errno = curl_errno($ch);
            curl_close($ch);
            $mirrorserverlisttemp[$server]['time']+=$time;
            
            if($errno)
            {
                $error_message = curl_strerror($errno);
                $mirrorserverlisttemp[$server]['state']='down';
                $mirrorserverlisttemp[$server]['error']="cURL error ({$errno}):\n {$error_message}";
                $mirrorserverlisttemp[$server]['file']=$server.$file;
            }
            else if($httpcode!=200)
            {
                $mirrorserverlisttemp[$server]['state']='corrupted';
                $mirrorserverlisttemp[$server]['error']='http code: '.$httpcode;
                $mirrorserverlisttemp[$server]['file']=$server.$file;
            }
            else
            {
                if($contentlocal!=$contentremote)
                {
                    $mirrorserverlisttemp[$server]['state']='corrupted';
                    $mirrorserverlisttemp[$server]['error']='local and remote file are not same';
                    $mirrorserverlisttemp[$server]['file']=$server.$file;
                }
            }
        }
    }
}

echo json_encode($mirrorserverlisttemp);
