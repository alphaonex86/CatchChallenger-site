<?php
//can be "all", "onlytar", "onlyfile"
$scantype="all";

if (!function_exists('curl_init')){
        die('Sorry cURL is not installed!');
    }

require '../config.php';
$mirrorserverlisttemp=array();
$arr=array();
foreach($mirrorserverlist as $host)
    $mirrorserverlisttemp['servers'][$host]=array('state'=>'up');

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
    if(!is_dir($datapack_path.'map/main/'))
        die($datapack_path.'map/main/ not found');
    $arr[]='datapack-list/base.txt';
    $arr[]='pack/datapack.tar.zst';
    $maincodelist=giveDirList($datapack_path.'map/main/');
    foreach($maincodelist as $maincode)
    {
        $arr[]='datapack-list/main-'.$maincode.'.txt';
        $arr[]='pack/datapack-main-'.$maincode.'.tar.zst';
        if(is_dir($datapack_path.'/map/main/'.$maincode.'/sub/'))
        {
            $subcodelist=giveDirList($datapack_path.'/map/main/'.$maincode.'/sub/');
            foreach($subcodelist as $subcode)
            {
                $arr[]='datapack-list/sub-'.$maincode.'-'.$subcode.'.txt';
                $arr[]='pack/datapack-sub-'.$maincode.'-'.$subcode.'.tar.zst';
            }
        }
    }
}
if($scantype!="onlytar")
    $arr=array_merge(listFolder($datapack_path),$arr);
sort($arr);

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
                $mirrorserverlisttemp['servers'][$server]['state']='down';
                $mirrorserverlisttemp['servers'][$server]['error']='cURL error ('.$errno.')';
                $mirrorserverlisttemp['servers'][$server]['curl_error']=curl_strerror($errno);
                $mirrorserverlisttemp['servers'][$server]['file']=$server.$file;
            }
            else if($httpcode!=200 && $contentlocal!='')
            {
                $mirrorserverlisttemp['servers'][$server]['state']='corrupted';
                $mirrorserverlisttemp['servers'][$server]['error']='http code: '.$httpcode;
                $mirrorserverlisttemp['servers'][$server]['curl_error']=curl_strerror($errno);
                $mirrorserverlisttemp['servers'][$server]['file']=$server.$file;
            }
            else
            {
                if($contentlocal=='')
                {
                    if(is_file($datapack_path.$file))
                        $contentlocal=@file_get_contents($datapack_path.$file);
                    if(isset($missingfilecache[$file]))
                        $contentlocal=$missingfilecache[$file];
                    else
                    {
                        if(count($mirrorserverlisttemp['servers'])>0)
                        {
                            $contentlocal=$content;
                            if($contentlocal=='')
                            {
                                $url = $server.$file;
                                
                                $curl = curl_init();
                                curl_setopt($curl, CURLOPT_URL, $url);
                                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($curl, CURLOPT_HEADER, false);
                                curl_setopt($ch, CURLOPT_TIMEOUT,10);
                                curl_setopt($ch, CURLOPT_TCP_NODELAY, 1); 
                                $content = curl_exec($curl);
                                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                                $errno = curl_errno($curl);
                                curl_close($curl);

                                if($errno)
                                {
                                    $mirrorserverlisttemp['servers'][$server]['state']='down';
                                    $mirrorserverlisttemp['servers'][$server]['error']='cURL error ('.$errno.')';
                                    $mirrorserverlisttemp['servers'][$server]['curl_error']=curl_strerror($errno);
                                    $mirrorserverlisttemp['servers'][$server]['file']=$server.$file;
                                }
                                else if($httpcode!=200 && $contentlocal!='')
                                {
                                    $mirrorserverlisttemp['servers'][$server]['state']='corrupted';
                                    $mirrorserverlisttemp['servers'][$server]['error']='http code: '.$httpcode;
                                    $mirrorserverlisttemp['servers'][$server]['curl_error']=curl_strerror($errno);
                                    $mirrorserverlisttemp['servers'][$server]['file']=$server.$file;
                                }
                                else
                                {
                                    $contentlocal=$content;
                                    if($contentlocal=='')
                                    {
                                        echo 'Primary mirror is wrong, file problem on: '.$server.$file.' $contentlocal: '.$contentlocal.' , $content: '.$content."\n";
                                        exit;
                                    }
                                }
                            }
                            $missingfilecache[$file]=$contentlocal;
                        }
                    }
                }
                
                if($contentlocal!=$content)
                {
                    if(strlen($contentlocal)<200)
                    {
                        if(preg_match('/[^\x20-\x7f]/', $contentlocal))
                            $contentlocaltext=', content: 0x'.bin2hex($contentlocal);
                        else
                            $contentlocaltext=', content: '.$contentlocal;
                    }
                    else
                        $contentlocaltext='';
                    if(strlen($content)<200)
                    {
                        if(preg_match('/[^\x20-\x7f]/', $content))
                            $contentlocaltext=', content: 0x'.bin2hex($content);
                        else
                            $contentlocaltext=', content: '.$content;
                    }
                    else
                        $contenttext='';
                    $mirrorserverlisttemp['servers'][$server]['state']='corrupted';
                    $mirrorserverlisttemp['servers'][$server]['error']='local (sha256: '.hash('sha256',$contentlocal).', size: '.strlen($contentlocal).$contentlocaltext.') and remote file (sha256: '.hash('sha256',$content).', size: '.strlen($content).$contenttext.') are not same';
                    $mirrorserverlisttemp['servers'][$server]['file']=$server.$file;
                    filewrite('/tmp/bug',$contentlocal);
                }
                else
                {
                    $pos = strpos($file,'datapack-list/');
                    if($pos !== false && $pos==0)
                    {
                        if(strpos($content,"\n-\n")===false)
                        {
                            $mirrorserverlisttemp['servers'][$server]['state']='corrupted';
                            $mirrorserverlisttemp['servers'][$server]['error']='Don\'t have valid structure (1), sha256: '.hash('sha256',$content).', size: '.strlen($content);
                            $mirrorserverlisttemp['servers'][$server]['file']=$server.$file;
                        }
                    }
                }
            }
        }
    }
    $curlList=array();
    $missingfilecache=array();

    curl_multi_close($curlmaster);
    $curlmaster=curl_multi_init();
}

$time_start = microtime(true);
$curlmaster=curl_multi_init();
$curlList=array();
$missingfilecache=array();
foreach($arr as $file)
{
    foreach($mirrorserverlisttemp['servers'] as $server=>$content)
    {
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
    if((count($curlList)*count($mirrorserverlisttemp['servers']))>100)
        flushcurlcall();
}
flushcurlcall();

if(isset($singledatapacklisttest))
{
    foreach($singledatapacklisttest as $url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT,10);
        curl_setopt($ch, CURLOPT_TCP_NODELAY, 1); 
        $content = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $errno = curl_errno($curl);
        curl_close($curl);
        if($errno)
        {
            $mirrorserverlisttemp['servers'][$url]['state']='down';
            $mirrorserverlisttemp['servers'][$url]['error']='cURL error ('.$errno.')';
            $mirrorserverlisttemp['servers'][$url]['curl_error']=curl_strerror($errno);
            $mirrorserverlisttemp['servers'][$url]['file']=$url;
        }
        else if($httpcode!=200)
        {
            $mirrorserverlisttemp['servers'][$url]['state']='corrupted';
            $mirrorserverlisttemp['servers'][$url]['error']='http code: '.$httpcode;
            $mirrorserverlisttemp['servers'][$url]['curl_error']=curl_strerror($errno);
            $mirrorserverlisttemp['servers'][$url]['file']=$url;
        }
        else
        {
            if(strpos($content,"\n-\n")===false)
            {
                $mirrorserverlisttemp['servers'][$url]['state']='corrupted';
                $mirrorserverlisttemp['servers'][$url]['error']='Don\'t have valid structure (2), sha256: '.hash('sha256',$content).', size: '.strlen($content);
            }
            else
                $mirrorserverlisttemp['servers'][$url]['state']='up';
        }
    }
}

$mirrorserverlisttemp['totaltime'] = microtime(true) - $time_start;
$mirrorserverlisttemp['totalfiles']=count($arr);

echo json_encode($mirrorserverlisttemp);
