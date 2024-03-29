<?php
$is_up=true;
require 'config.php';
if($postgres_db_site['host']!='localhost')
    $postgres_link_site = @pg_connect('dbname='.$postgres_db_site['database'].' user='.$postgres_login.' password='.$postgres_pass.' host='.$postgres_db_site['host']);
else
    $postgres_link_site = @pg_connect('dbname='.$postgres_db_site['database'].' user='.$postgres_login.' password='.$postgres_pass);
if($postgres_link_site===FALSE)
    $is_up=false;

$total_string_array=array();
$gameserver_up=0;
$gameserver_down=0;
$server_count=0;
$player_count=0;
$maxplayer_count=0;
$previously_know_server_changed=false;
$version = explode('.', PHP_VERSION);
if($version[0]<7)
    die('this script use reference, for this it need php7+');
function serverTreeToUniqueId($arr)
{
    $serverTreeUniqueId=array();
    foreach($arr as $logicalGroup=>$entry_list)
        if(isset($entry_list['servers']))
            foreach($entry_list['servers'] as $entry)
                if(is_array($entry))
                    if(isset($entry['charactersGroup']) && isset($entry['uniqueKey']))
                    {
                        $entry['logicalGroup']=$logicalGroup;
                        $serverTreeUniqueId[$entry['charactersGroup'].'-'.$entry['uniqueKey']]=$entry;
                    }
    return $serverTreeUniqueId;
}
$filecurs='';
if(isset($gameserverfile))
{
    if(file_exists($gameserverfile) && $filecurs=file_get_contents($gameserverfile))
    {}
    else
    $filecurs='';
}
if(isset($statsserversock))
{
    if($_SERVER['HTTP_HOST']=='amber')
        $fp = fsockopen('unix://'.$statsserversock,0,$errno, $errstr, 5);
    else
        $fp = @fsockopen('unix://'.$statsserversock,0,$errno, $errstr, 5);
    if ($fp===FALSE)
    {
        //second try
        if($_SERVER['HTTP_HOST']=='amber')
            $fp = fsockopen('unix://'.$statsserversock,0,$errno, $errstr, 5);
        else
            $fp = @fsockopen('unix://'.$statsserversock,0,$errno, $errstr, 5);
        /*if ($fp===FALSE)
            echo "<!-- $errstr ($errno) on ".$statsserversock." -->\n";*/
    } else {
        $filecurs.=fgets($fp, 4096);
        stream_set_blocking($fp,FALSE);
        $i=0;
        while (!feof($fp) && $i<1024/* limit file size to 4MB */) {
            $filecurs.=fgets($fp, 4096);
            $i++;
        }
        fclose($fp);
    }
}
if($filecurs!='')
{
    $arr=json_decode($filecurs,true);
    //echo '<!-- ';print_r($filecurs);echo ' -->';
    if($arr!==NULL && is_array($arr))
    {
        ksort($arr);

        //do the top
        $treeServer=array();
        $topListTemp=array();
        
        //load server from db too
        $arrDB=array();
        if($is_up)
        {
            $reply = pg_query($postgres_link_site,'SELECT uniquekey,"charactersGroup",xml,"logicalGroup" FROM gameservers ORDER BY "charactersGroup",uniquekey') or die(pg_last_error());
            $i=0;
            while($data = pg_fetch_array($reply) && $i<1024/*Limit to 1024 entry*/)
            {
                $arrDB[$data['logicalGroup']]['servers'][]=array(
                    'xml'=>$data['xml'],
                    'charactersGroup'=>$data['charactersGroup'],
                    'uniqueKey'=>$data['uniquekey']
                );
                $i++;
            }
        }
        else
        {
            if(file_exists($previously_know_server_file) && $filecurs=file_get_contents($previously_know_server_file))
            {
                $arrDB=json_decode($filecurs,true);
                if(!is_array($arr))
                    $arrDB=array();
            }
            else
                $arrDB=array();
        }
        foreach($arrDB as $logicalGroup=>$entry_list)
            foreach($entry_list['servers'] as $idserver=>$entry)
                if(is_array($entry))
                    $arrDB[$logicalGroup]['servers'][$idserver]['state']='down';
        ksort($arrDB);
        
        $uniqueKeysDbServer=serverTreeToUniqueId($arrDB);
        $uniqueKeysGameServer=serverTreeToUniqueId($arr);
                
        //save the server list
        $charactersGroup=array();
        foreach($uniqueKeysGameServer as $uniqueKeyMerge=>$data)
        {
            if(isset($data['uniqueKey']) && isset($data['charactersGroup']) && isset($data['xml']) && isset($data['logicalGroup']))
            {
                $charactersGroup[$data['charactersGroup']]=true;
                if(!isset($uniqueKeysDbServer[$uniqueKeyMerge]))
                {
                    //save the new server
                    if($is_up)
                        pg_query_params($postgres_link_site,'INSERT INTO gameservers(uniquekey,"charactersGroup",xml,"logicalGroup") VALUES ($1,$2,$3,$4);',array($data['uniqueKey'],$data['charactersGroup'],$data['xml'],$data['logicalGroup'])) or die(pg_last_error());
                    else
                        $previously_know_server_changed=true;
                }
                else
                {
                    //update the current server
                    if($data['xml']!=$uniqueKeysDbServer[$uniqueKeyMerge]['xml'] || $data['logicalGroup']!=$uniqueKeysDbServer[$uniqueKeyMerge]['logicalGroup'])
                    {
                        if($is_up)
                            pg_query_params($postgres_link_site,'UPDATE gameservers SET xml=$1,"logicalGroup"=$2 WHERE uniquekey=$3 AND "charactersGroup"=$4',array($data['xml'],$data['logicalGroup'],$data['uniqueKey'],$data['charactersGroup'])) or die(pg_last_error());
                        else
                            $previously_know_server_changed=true;
                    }
                }
                if(isset($data['connectedPlayer']))
                {
                    $topList[$data['charactersGroup'].'-'.$data['uniqueKey']]=$data['connectedPlayer'];
                    $player_count+=$data['connectedPlayer'];
                }
                if(isset($data['maxPlayer']))
                    $maxplayer_count+=$data['maxPlayer'];
                $previously_know_server[$data['logicalGroup']]['servers'][]=array(
                    'xml'=>$data['xml'],
                    'charactersGroup'=>$data['charactersGroup'],
                    'uniqueKey'=>$data['uniqueKey']
                );
                ksort($previously_know_server);
                $gameserver_up++;
            }
        }
        
        //count the server down
        foreach($uniqueKeysDbServer as $uniqueKeyMerge=>$data)
        {
            if(isset($data['uniqueKey']) && isset($data['charactersGroup']) && isset($data['xml']) && isset($data['logicalGroup']))
            {
                $charactersGroup[$data['charactersGroup']]=true;
                if(!isset($uniqueKeysGameServer[$uniqueKeyMerge]))
                {
                    $previously_know_server[$data['logicalGroup']]['servers'][]=array(
                        'xml'=>$data['xml'],
                        'charactersGroup'=>$data['charactersGroup'],
                        'uniqueKey'=>$data['uniqueKey']
                    );
                    ksort($previously_know_server);
                    $gameserver_down++;
                }
            }
        }
        $server_count=$gameserver_up+$gameserver_down;
        
        //if json file and have change, then add the offline server to save, and save it
        if(!$is_up && $previously_know_server_changed)
            filewrite($previously_know_server_file,json_encode($previously_know_server));
        
        arsort($topList);
        $string_array=array();
        if($gameserver_up>0)
            $string_array[]='<strong>'.$gameserver_up.'</strong> <span style="color:green;">online</span>';
        if($gameserver_down>0)
            $string_array[]='<strong>'.$gameserver_down.'</strong> <span style="color:red;">offline</span>';
        $total_string_array[]='Game server: '.implode('/',$string_array);
    }
    else
    {
        if($_SERVER['HTTP_HOST']=='amber')
        {
            echo '<p class="text">The official server list is actually in <b>Unknown state</b> (4).<!-- ('.$errstr.', errno '.$errno.') '.$statsserversock.': '.$filecurs.'--></p>';
            echo gettype($arr);
            print_r($arr);
            print_r($filecurs);
        }
        else
            echo '<p class="text">The official server list is actually in <b>Unknown state</b> (2).<!-- ('.$errstr.', errno '.$errno.') '.$statsserversock.': '.$filecurs.'--></p>';
    }
}
else
{
    if($fp==FALSE)
    {
        if($_SERVER['HTTP_HOST']=='amber')
            echo '<p class="text">The official server list is actually in <b>Unknown state</b> ('.$errstr.', errno '.$errno.').<!-- '.$statsserversock.': '.$filecurs.'--></p>';
        else
            echo '<p class="text">The official server list is actually in <b>Unknown state</b> (1).<!-- ('.$errstr.', errno '.$errno.') '.$statsserversock.': '.$filecurs.'--></p>';
    }
    else
        echo '<p class="text">The official server list is actually in <b>Unknown state</b> (3).<!-- ('.$errstr.', errno '.$errno.') '.$statsserversock.': '.$filecurs.'--></p>';
}

$loginserver_up=0;
$loginserver_down=0;
if(isset($loginserverfile))
    if(file_exists($loginserverfile) && $filecurs=file_get_contents($loginserverfile))
    {
        $arr=json_decode($filecurs,true);
        if($arr!==NULL && is_array($arr))
        {
            ksort($arr);
            foreach($arr as $ip=>$server)
            {
                if(isset($server['state']))
                {
                    if($server['state']=='up')
                        $loginserver_up++;
                    else
                        $loginserver_down++;
                }
                else
                    $loginserver_down++;
            }
            $string_array=array();
            if($loginserver_up>0)
                $string_array[]='<strong>'.$loginserver_up.'</strong> <span style="color:green;">online</span>';
            if($loginserver_down>0)
                $string_array[]='<strong>'.$loginserver_down.'</strong> <span style="color:red;">offline</span>';
            $total_string_array[]='Login server: '.implode('/',$string_array);
        }
        else
            $total_string_array[]='Login server: <span style="color:red;">bug</span>';
    }

$mirrorserver_up=0;
$mirrorserver_down=0;
$mirrorserver_corrupted=0;
if(isset($mirrorserverfile))
    if(file_exists($mirrorserverfile) && $filecurs=file_get_contents($mirrorserverfile))
    {
        $arr=json_decode($filecurs,true);
        if($arr!==NULL && is_array($arr))
        {
            ksort($arr['servers']);
            foreach($arr['servers'] as $ip=>$server)
            {
                if(isset($server['state']))
                {
                    if($server['state']=='up')
                        $mirrorserver_up++;
                    else if($server['state']=='corrupted')
                        $mirrorserver_corrupted++;
                    else
                        $mirrorserver_down++;
                }
                else
                    $mirrorserver_down++;
            }
            $string_array=array();
            if($mirrorserver_up>0)
                $string_array[]='<strong>'.$mirrorserver_up.'</strong> <span style="color:green;">online</span>';
            if($mirrorserver_corrupted>0)
                $string_array[]='<strong>'.$mirrorserver_corrupted.'</strong> <span style="color:brown;">corrupted</span>';
            if($mirrorserver_down>0)
                $string_array[]='<strong>'.$mirrorserver_down.'</strong> <span style="color:red;">offline</span>';
            $total_string_array[]='Mirror server: '.implode('/',$string_array);
        }
        else
            $total_string_array[]='Mirror server: <span style="color:red;">bug</span>';
    }

$backup_up=0;
$backup_corrupted=0;
$backup_down=0;
if(isset($backupfile))
{
    $string_array=array();
    if(file_exists($backupfile) && $filecurs=file_get_contents($backupfile))
    {
        $arr=json_decode($filecurs,true);
        if($arr!==NULL && is_array($arr))
        {
            ksort($arr);
            foreach($arr as $ip=>$server)
            {
                if(isset($server['state']))
                {
                    if($server['state']=='up')
                        $backup_up++;
                    else if($server['state']=='corrupted')
                        $backup_corrupted++;
                    else
                        $backup_down++;
                }
                else
                    $backup_down++;
            }
        }
        else
            $backup_down++;
    }
    if($backup_up>0)
        $string_array[]='<strong>'.$backup_up.'</strong> <span style="color:green;">ok</span>';
    if($backup_corrupted>0)
        $string_array[]='<strong>'.$backup_corrupted.'</strong> <span style="color:brown;">corrupted</span>';
    if($backup_down>0)
        $string_array[]='<strong>'.$backup_down.'</strong> <span style="color:red;">bad</span>';
    if($backup_up==0 && $backup_corrupted==0 && $backup_down==0)
        $string_array[]='<strong><span style="color:red;">No backup</span></strong>';
    $total_string_array[]='Backup: '.implode('/',$string_array);
}

$otherjson_up=0;
$otherjson_corrupted=0;
$otherjson_down=0;
if(isset($otherjsonfile))
{
    $string_array=array();
    if(file_exists($otherjsonfile) && $filecurs=file_get_contents($otherjsonfile))
    {
        $arr=json_decode($filecurs,true);
        if($arr!==NULL && is_array($arr))
        {
            ksort($arr);
            foreach($arr as $ip=>$server)
            {
                if(isset($server['state']))
                {
                    if($server['state']=='up')
                        $otherjson_up++;
                    else if($server['state']=='corrupted')
                        $otherjson_corrupted++;
                    else
                        $otherjson_down++;
                }
                else
                    $otherjson_down++;
            }
        }
        else
            $otherjson_down++;
    }
    if($otherjson_up>0)
        $string_array[]='<strong>'.$otherjson_up.'</strong> <span style="color:green;">ok</span>';
    if($otherjson_corrupted>0)
        $string_array[]='<strong>'.$otherjson_corrupted.'</strong> <span style="color:brown;">corrupted</span>';
    if($otherjson_down>0)
        $string_array[]='<strong>'.$otherjson_down.'</strong> <span style="color:red;">bad</span>';
    if($otherjson_up==0 && $otherjson_corrupted==0 && $otherjson_down==0)
        $string_array[]='No other checks';
    $total_string_array[]='Other: '.implode('/',$string_array);
}

if($gameserver_down>0 || $loginserver_down>0 || $otherjson_down>0 || $backup_down>0 || $mirrorserver_down>0)
{
    echo 'Error';
    print_r($string_array);
}
else
    echo 'OK';
