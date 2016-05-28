<?php
//can be "all", "onlytar", "onlyfile"
$scantype="all";

if (!function_exists('curl_init')){
        die('Sorry cURL is not installed!');
    }

require '../config.php';
$mirrorserverlisttemp=array();
foreach($mirrorserverlist as $host)
    $mirrorserverlisttemp[$host]=array('state'=>'up');

function giveDirList($folder)
{
    if(!preg_match('#/$#',$folder))
        $folder.='/';
    $arr=array();
    if($handle = opendir($folder)) {
        while(false !== ($entry = readdir($handle)))
        {
            if($entry != '.' && $entry != '..')
            {
                if(is_dir($folder.$entry))
                    $arr[]=$entry;
            }
        }
        closedir($handle);
    }
    return $arr;
}

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
if($scantype!="onlyfile")
{
    $arr[]='datapack-list/base.txt';
    $arr[]='pack/datapack.tar.xz';
    $maincodelist=giveDirList($datapack_path.'map/main/');
    foreach($maincodelist as $maincode)
    {
        $arr[]='datapack-list/main-'.$maincode.'.txt';
        $arr[]='pack/datapack-main-'.$maincode.'.tar.xz';
        $subcodelist=giveDirList($datapack_path.'/map/main/'.$maincode.'/sub/');
        foreach($subcodelist as $subcode)
        {
            $arr[]='datapack-list/sub-'.$maincode.'-'.$subcode.'.txt';
            $arr[]='pack/datapack-sub-'.$maincode.'-'.$subcode.'.tar.xz';
        }
    }
}
if($scantype!="onlytar")
    $arr=listFolder($datapack_path);
sort($arr);

/*$curlmasterlist=array();
$missingfilecache=array();
foreach($arr as $file)
{
    if(is_file($datapack_path.$file))
        $contentlocal=file_get_contents($datapack_path.$file);
    if(isset($missingfilecache[$file]))
        $contentlocal=$missingfilecache[$file];
    else if(count($mirrorserverlisttemp)>0)
    {
        foreach($mirrorserverlisttemp as $server=>$content)
        {
            $contentlocal=file_get_contents($server.$file);
            break;
        }
        if($contentlocal=='')
        {
            echo 'Primary mirror is wrong, file problem on: '.$server.$file."\n";
            exit;
        }
        $missingfilecache[$file]=$contentlocal;
    }
    else
        continue;
    foreach($mirrorserverlisttemp as $server=>$content)
    {
        if(!isset($curlmasterlist[$server]['curlmaster']))
            $curlmasterlist[$server]['curlmaster']=curl_multi_init();
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
}*/

function flushcurlcall()
{
    global $curlmaster,$curlList,$datapack_path,$mirrorserverlisttemp;

    do {
        curl_multi_exec($curlmaster,$running);
        usleep(10*1000);
    } while($running > 0);

    foreach($curlList as $file=>$chlist)
    {
        $contentlocal='';
        foreach($chlist as $server=>$ch)
        {
            $content=curl_multi_getcontent($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $errno = curl_errno($ch);
            
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
                if($contentlocal=='')
                {
                    if(is_file($datapack_path.$file))
                        $contentlocal=file_get_contents($datapack_path.$file);
                    if(isset($missingfilecache[$file]))
                        $contentlocal=$missingfilecache[$file];
                    else if(count($mirrorserverlisttemp)>0)
                    {
                        $contentlocal=$content;
                        if($contentlocal=='')
                        {
                            echo 'Primary mirror is wrong, file problem on: '.$server.$file."\n";
                            exit;
                        }
                        $missingfilecache[$file]=$contentlocal;
                    }
                }

                if($contentlocal!=$content)
                {
                    $mirrorserverlisttemp[$server]['state']='corrupted';
                    $mirrorserverlisttemp[$server]['error']='local and remote file are not same';
                    $mirrorserverlisttemp[$server]['file']=$server.$file;
                }
            }
        }
    }
    $curlList=array();
    $missingfilecache=array();

    curl_multi_close($curlmaster);
    $curlmaster=curl_multi_init();
}

$curlmaster=curl_multi_init();
$curlList=array();
$missingfilecache=array();
foreach($arr as $file)
{
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
            curl_multi_add_handle($curlmaster, $ch);
            $curlList[$file][$server]=$ch;
        }
    }
    if((count($curlList)*count($mirrorserverlisttemp))>100)
        flushcurlcall();
}
flushcurlcall();

echo json_encode($mirrorserverlisttemp);
