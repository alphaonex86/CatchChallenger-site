<ul><?php
$total_string_array=array();
$gameserver_up=0;
$gameserver_down=0;
$server_count=0;
$player_count=0;
$maxplayer_count=0;
$previously_know_server_changed=false;

if(file_exists($gameserverfile) && $filecurs=file_get_contents($gameserverfile))
{
    $arr=json_decode($filecurs,true);
    if(is_array($arr))
    {
        ksort($arr);
        $previously_know_server=array();
        if($is_up)
        {
            $reply = pg_query($postgres_link_site,'SELECT uniquekey,"groupIndex",name,description FROM gameservers ORDER BY "groupIndex",uniquekey') or die(pg_last_error());
            while($data = pg_fetch_array($reply))
                $previously_know_server[$data['groupIndex']][$data['uniquekey']]=$data;
        }
        else
        {
            if(file_exists($previously_know_server_file) && $filecurs=file_get_contents($previously_know_server_file))
            {
                $previously_know_server=json_decode($filecurs,true);
                if(!is_array($arr))
                    $previously_know_server=array();
            }
            else
                $previously_know_server=array();
        }
        ksort($previously_know_server);
        //do the top
        $topListTemp=array();
        foreach($arr as $groupIndex=>$uniqueKey_list)
        {
            ksort($uniqueKey_list);
            foreach($uniqueKey_list as $uniqueKey=>$server)
                if(isset($server['xml']) && isset($server['connectedPlayer']) && isset($server['maxPlayer']))
                    if($server['maxPlayer']<65534 && $server['maxPlayer']>0 && $server['connectedPlayer']<=$server['maxPlayer'])
                        $topListTemp[$groupIndex.'-'.$uniqueKey]=$server['connectedPlayer'];
        }
        $indexTop=1;
        arsort($topListTemp);
        $topList=array();
        foreach($topListTemp as $key=>$players)
        {
            $topList[$key]=$indexTop;
            $indexTop++;
        }
        $topListTemp=array();

        $db_server_found=array();
        foreach($previously_know_server as $groupIndex=>$uniqueKey_list)
        {
            ksort($uniqueKey_list);
            foreach($uniqueKey_list as $uniqueKey=>$data)
            {
                $db_server_found[]=$data['groupIndex'].'-'.$data['uniquekey'];
                if(!isset($arr[$data['groupIndex']][$data['uniquekey']]))//not found
                {
                    echo '<li><div class="divBackground" title="'.htmlspecialchars($data['description']).'"><div class="labelDatapackMap"></div><strong>'.htmlspecialchars($data['name']).'</strong> - <span style="color:red;">down</span></div></li>';
                    $gameserver_down++;
                }
                else
                {
                    $server_count++;
                    $server=$arr[$data['groupIndex']][$data['uniquekey']];
                    if(isset($server['xml']) && isset($server['connectedPlayer']) && isset($server['maxPlayer']))
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
                        if($server['maxPlayer']<65534 && $server['maxPlayer']>0 && $server['connectedPlayer']<=$server['maxPlayer'])
                        {
                            $topNumber=$topList[$data['groupIndex'].'-'.$data['uniquekey']];
                            echo '<li><div class="divBackground" title="'.htmlentities($description).'"><div class="';
                            if(($topNumber<=2 || $topNumber<=(count($topList)/5)) && $server['connectedPlayer']>0)
                                echo 'labelDatapackTop';
                            else
                                echo 'labelDatapackMap';
                            echo '"></div><progress class="progress'.ceil(4*$server['connectedPlayer']/$server['maxPlayer']).'" title="'.playerwithunit($server['connectedPlayer']).'/'.playerwithunit($server['maxPlayer']).' players" value="'.$server['connectedPlayer'].'" max="'.$server['maxPlayer'].'"></progress>';
                            echo ' <strong>'.htmlentities($name).'</strong> - <strong>'.playerwithunit($server['connectedPlayer']).'</strong> players - <span style="color:green;">online</span></div></li>'."\n";
                            $player_count+=$server['connectedPlayer'];
                            $maxplayer_count+=$server['maxPlayer'];
                        }
                        else
                            echo '<li><div class="divBackground" title="'.htmlentities($description).'"><div class="labelDatapackMap"></div><strong>'.htmlentities($name).'</strong> - <span style="color:green;">online</span></div></li>';
                        $gameserver_up++;
                        if($data['name']!=$name || $data['description']!=$description)
                        {
                            if($is_up)
                                pg_query($postgres_link_site,'UPDATE gameservers SET name=\''.addslashes($name).'\',description=\''.addslashes($description).'\' WHERE uniquekey='.$data['uniquekey']) or die(pg_last_error());
                            else
                            {
                                $previously_know_server_changed=true;
                                $previously_know_server[$groupIndex][$uniqueKey]['name']=$name;
                                $previously_know_server[$groupIndex][$uniqueKey]['description']=$description;
                            }
                        }
                    }
                    else
                        echo '<li><div class="divBackground" title="'.htmlentities($data['description']).'"><div class="labelDatapackMap"></div><strong>'.htmlentities($data['name']).'</strong></div></li>';
                }
            }
        }
        foreach($arr as $groupIndex=>$uniqueKey_list)
        {
            ksort($uniqueKey_list);
            foreach($uniqueKey_list as $uniqueKey=>$server)
            {
                if(!in_array($groupIndex.'-'.$uniqueKey,$db_server_found))
                {
                    $server_count++;
                    if(isset($server['xml']) && isset($server['connectedPlayer']) && isset($server['maxPlayer']))
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
                        if($server['maxPlayer']<65534 && $server['maxPlayer']>0 && $server['connectedPlayer']<=$server['maxPlayer'])
                        {
                            $topNumber=$topList[$groupIndex.'-'.$uniqueKey];
                            echo '<li><div class="divBackground" title="'.htmlentities($description).'"><div class="';
                            if(($topNumber<=2 || $topNumber<=(count($topList)/5)) && $server['connectedPlayer']>0)
                                echo 'labelDatapackTop';
                            else
                                echo 'labelDatapackMap';
                            echo '"></div><progress class="progress'.ceil(4*$server['connectedPlayer']/$server['maxPlayer']).'" title="'.playerwithunit($server['connectedPlayer']).'/'.playerwithunit($server['maxPlayer']).' players" value="'.$server['connectedPlayer'].'" max="'.$server['maxPlayer'].'"></progress>';
                            echo ' <strong>'.htmlentities($name).'</strong> - <strong>'.playerwithunit($server['connectedPlayer']).'</strong> players - <span style="color:green;">online</span></div></li>'."\n";
                            $player_count+=$server['connectedPlayer'];
                            $maxplayer_count+=$server['maxPlayer'];
                        }
                        else
                            echo '<li><div class="divBackground" title="'.htmlentities($description).'"><div class="labelDatapackMap"></div><strong>'.htmlentities($name).'</strong> - <span style="color:green;">online</span></div></li>';
                        $gameserver_up++;
                        if($is_up)
                            pg_query($postgres_link_site,'INSERT INTO gameservers(uniquekey,"groupIndex",name,description) VALUES ('.addslashes($uniqueKey).','.addslashes($groupIndex).',\''.addslashes($name).'\',\''.addslashes($description).'\');') or die(pg_last_error());
                        else
                        {
                            $previously_know_server_changed=true;
                            $previously_know_server[$groupIndex][$uniqueKey]=array('uniquekey'=>$uniqueKey,'groupIndex'=>$groupIndex,'name'=>$name,'description'=>$description);
                            ksort($previously_know_server[$groupIndex]);
                            ksort($previously_know_server);
                        }
                    }
                    else
                        echo '<li><div class="divBackground"><div class="labelDatapackMap"></div><strong>Default server</strong></div></li>';
                }
            }
        }
        
        $string_array=array();
        if($gameserver_up>0)
            $string_array[]='<strong>'.$gameserver_up.'</strong> <span style="color:green;">online</span>';
        if($gameserver_down>0)
            $string_array[]='<strong>'.$gameserver_down.'</strong> <span style="color:red;">offline</span>';
        $total_string_array[]='Game server: '.implode('/',$string_array);

        if(!$is_up && $previously_know_server_changed)
            filewrite($previously_know_server_file,json_encode($previously_know_server));
    }
    else
        echo '<li><p class="text">The official server list is actually in <b>Unknown state</b>.</p></li>';
}
else
    echo '<li><p class="text">The official server list is actually in <b>Unknown state</b>.</p></li>';
?>
</ul>
<?php
$loginserver_up=0;
$loginserver_down=0;
if(file_exists($loginserverfile) && $filecurs=file_get_contents($loginserverfile))
{
    $arr=json_decode($filecurs,true);
    if(is_array($arr))
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
}

$mirrorserver_up=0;
$mirrorserver_down=0;
$mirrorserver_corrupted=0;
if(file_exists($mirrorserverfile) && $filecurs=file_get_contents($mirrorserverfile))
{
    $arr=json_decode($filecurs,true);
    if(is_array($arr))
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
}

{
    $string_array=array();
    $backup_up=0;
    $backup_corrupted=0;
    $backup_down=0;
    if(file_exists($backupfile) && $filecurs=file_get_contents($backupfile))
    {
        $arr=json_decode($filecurs,true);
        if(is_array($arr))
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

{
    $string_array=array();
    $otherjson_up=0;
    $otherjson_corrupted=0;
    $otherjson_down=0;
    if(file_exists($otherjsonfile) && $filecurs=file_get_contents($otherjsonfile))
    {
        $arr=json_decode($filecurs,true);
        if(is_array($arr))
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