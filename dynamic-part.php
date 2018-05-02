<?php
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

function displayServer($server,$topList,$charactersGroup)
{
    if(is_array($server) && isset($server['charactersGroup']) && isset($server['state']) && isset($server['uniqueKey']) && isset($server['xml']))
    {
        $name='';
        if(preg_match('#<name( lang="en")?>.*</name>#isU',$server['xml']))
        {
            $name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$server['xml']);
            $name=str_replace('<![CDATA[','',str_replace(']]>','',$name));
        }
        if($name=='')
            $name='Default server';
        $description='';
        if(preg_match('#<description( lang="en")?>.*</description>#isU',$server['xml']))
        {
            $description=preg_replace('#^.*<description( lang="en")?>(.*)</description>.*$#isU','$2',$server['xml']);
            $description=str_replace('<![CDATA[','',str_replace(']]>','',$description));
        }
        if(count($charactersGroup)>1)
            $flag='<div style="width:18px;height:12px;background-image:url(/images/charGroupFlags.png);background-repeat:no-repeat;background-position:'.(-18*($server['charactersGroup']%4)).'px 0px;float:left;margin-right:7px;" title="Character group '.($server['charactersGroup']+1).'"></div>';
        else
            $flag='';
        if($server['state']!='up')//not found
            echo '<div class="divBackground" title="'.htmlspecialchars($description).'">'.$flag.'<strong>'.htmlspecialchars($name).'</strong> - <span style="color:red;">down<!-- '.$server['uniqueKey'].', '.$server['state'].' --></span></div>';
        else
        {
            if(isset($server['connectedPlayer']))
            {
                if(isset($server['maxPlayer']) && $server['maxPlayer']<65534 && $server['maxPlayer']>0 && $server['connectedPlayer']<=$server['maxPlayer'])
                {
                    echo '<div class="divBackground" title="'.htmlentities($description).'">';
                    
                    //display the top mark
                    if(isset($topList[$server['charactersGroup'].'-'.$server['uniqueKey']]))
                    {
                        $topNumber=$topList[$server['charactersGroup'].'-'.$server['uniqueKey']];
                        if(($topNumber<=2 || $topNumber<=(count($topList)/5)) && $server['connectedPlayer']>0)
                            echo '<div class="labelDatapackTop"></div>';
                    }
                        
                    echo $flag;
                    echo '<progress class="progress'.ceil(4*$server['connectedPlayer']/$server['maxPlayer']).' droplowwidth" title="'.playerwithunit($server['connectedPlayer']).'/'.playerwithunit($server['maxPlayer']).' players" value="'.$server['connectedPlayer'].'" max="'.$server['maxPlayer'].'"></progress>';
                    echo ' <strong>'.htmlentities($name).'</strong> - <strong>'.playerwithunit($server['connectedPlayer']).'</strong> players - <span style="color:green;">online</span></div>'."\n";
                }
                else
                    echo '<div class="divBackground" title="'.htmlentities($description).'">'.$flag.'<strong>'.htmlentities($name).'</strong> - <span style="color:green;">online</span></div>';
            }
            else
                echo '<div class="divBackground" title="'.htmlentities($description).'">'.$flag.'<strong>'.htmlentities($name).'</strong></div>';
        }
    }
    return;
}
function displayServerTree($treeServer,$topList,$charactersGroup)
{
    echo '<div class="divBackground">';
    if(isset($treeServer['xml']))
        if(preg_match('#<name( lang="en")?>.*</name>#isU',$treeServer['xml']))
        {
            $name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$treeServer['xml']);
            $name=str_replace('<![CDATA[','',str_replace(']]>','',$name));
            echo '<strong>'.$name.'</strong>';
        }
    echo '<ul>';
    if(isset($treeServer['servers']))
    {
        foreach($treeServer['servers'] as $server)
        {
            echo '<li>';
            displayServer($server,$topList,$charactersGroup);
            echo '</li>';
        }
    }
    if(isset($treeServer['groups']))
    {
        foreach($treeServer['groups'] as $group)
        {
            echo '<li>';
            displayServerTree($group,$topList,$charactersGroup);
            echo '</li>';
        }
    }
    echo '</ul></div>';
    return;
}
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
function genTreeServer($treeServer,$arr)
{
    foreach($arr as $logicalGroup=>$entry_list)
    {
        $logicalGroupList=array_filter(explode('/',$logicalGroup), function($value) { return $value !== ''; });
        $indexLogicalGroup=0;
        $treeleaf=&$treeServer;//php 7+
        while($indexLogicalGroup<count($logicalGroupList))
        {
            if(!isset($treeleaf['groups']))
                $treeleaf['groups']=array();
            if(!isset($treeleaf['groups'][$logicalGroupList[$indexLogicalGroup]]))
                $treeleaf['groups'][$logicalGroupList[$indexLogicalGroup]]=array();
            $treeleaf=&$treeleaf['groups'][$logicalGroupList[$indexLogicalGroup]];
            $indexLogicalGroup++;
        }
        if(isset($entry_list['xml']))
            $treeleaf['xml']=$entry_list['xml'];
        if(isset($entry_list['servers']))
        {
            foreach($entry_list['servers'] as $entry)
            {
                if(is_array($entry) && !isset($treeleaf['servers'][$entry['charactersGroup'].'-'.$entry['uniqueKey']]))
                {
                    if(!isset($entry['maxPlayer']))
                    {
                        $entry['maxPlayer']=65535;
                        $arr[$logicalGroup][$entry['uniqueKey']]['maxPlayer']=65535;
                    }
                    if(!isset($entry['state']))
                        $entry['state']='up';
                    if(isset($entry['charactersGroup']) && isset($entry['uniqueKey']))
                    {
                        $treeleaf['servers'][$entry['charactersGroup'].'-'.$entry['uniqueKey']]=$entry;
                        ksort($treeleaf['servers'][$entry['charactersGroup'].'-'.$entry['uniqueKey']]);
                    }
                }
            }
        }
    }
    return $treeServer;
}

$filecurs='';
if(isset($gameserverfile))
{
    if(file_exists($gameserverfile) && $filecurs=file_get_contents($gameserverfile))
    {}
    else
    $filecurs='';
}
if(isset($gameserversock))
{
    $fp = @fsockopen('unix://'.$gameserversock,0,$errno, $errstr, 1);
    if (!$fp) {
        echo "<!-- $errstr ($errno) -->\n";
    } else {
        stream_set_blocking($fp,FALSE);
        while (!feof($fp)) {
            $filecurs.=fgets($fp, 4096);
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
            while($data = pg_fetch_array($reply))
                $arrDB[$data['logicalGroup']]['servers'][]=array(
                    'xml'=>$data['xml'],
                    'charactersGroup'=>$data['charactersGroup'],
                    'uniqueKey'=>$data['uniquekey']
                );
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
        
        $treeServer=genTreeServer($treeServer,$arr);
        $treeServer=genTreeServer($treeServer,$arrDB);
        
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
        displayServerTree($treeServer,$topList,$charactersGroup);
        
        $string_array=array();
        if($gameserver_up>0)
            $string_array[]='<strong>'.$gameserver_up.'</strong> <span style="color:green;">online</span>';
        if($gameserver_down>0)
            $string_array[]='<strong>'.$gameserver_down.'</strong> <span style="color:red;">offline</span>';
        $total_string_array[]='Game server: '.implode('/',$string_array);
    }
    else
        echo '<p class="text">The official server list is actually in <b>Unknown state</b>.</p>';
}
else
    echo '<p class="text">The official server list is actually in <b>Unknown state</b>.</p>';
?>
<?php
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

if(isset($backupfile))
{
    $string_array=array();
    $backup_up=0;
    $backup_corrupted=0;
    $backup_down=0;
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
    }
    if($backup_up>0)
        $string_array[]='<strong>'.$backup_up.'</strong> <span style="color:green;">online</span>';
    if($backup_corrupted>0)
        $string_array[]='<strong>'.$backup_corrupted.'</strong> <span style="color:brown;">corrupted</span>';
    if($backup_down>0)
        $string_array[]='<strong>'.$backup_down.'</strong> <span style="color:red;">offline</span>';
    if($backup_up==0 && $backup_corrupted==0 && $backup_down==0)
        $string_array[]='<strong><span style="color:red;">No backup</span></strong>';
    $total_string_array[]='Backup: '.implode('/',$string_array);
}

if(isset($otherjsonfile))
{
    $string_array=array();
    $otherjson_up=0;
    $otherjson_corrupted=0;
    $otherjson_down=0;
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
        $string_array[]='<strong>'.$otherjson_up.'</strong> <span style="color:green;">online</span>';
    if($otherjson_corrupted>0)
        $string_array[]='<strong>'.$otherjson_corrupted.'</strong> <span style="color:brown;">corrupted</span>';
    if($otherjson_down>0)
        $string_array[]='<strong>'.$otherjson_down.'</strong> <span style="color:red;">offline</span>';
    if($otherjson_up==0 && $otherjson_corrupted==0 && $otherjson_down==0)
        $string_array[]='No other checks';
    $total_string_array[]='Other: '.implode('/',$string_array);
}
