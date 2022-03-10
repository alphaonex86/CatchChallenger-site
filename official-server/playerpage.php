<?php
$is_up=true;
require '../config.php';
$https=false;
if(isset($_SERVER['HTTP_X_FORWARDED_PORT']) && $_SERVER['HTTP_X_FORWARDED_PORT']=='443')
    $https=true;
if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO']=='https')
    $https=true;
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on')
    $https=true;
if(isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME']=='https')
    $https=true;
if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT']=='443')
    $https=true;

if($postgres_db_base['host']!='localhost')
    $postgres_link_base = pg_connect('dbname='.$postgres_db_base['database'].' user='.$postgres_login.' password='.$postgres_pass.' host='.$postgres_db_base['host']);
else
    $postgres_link_base = pg_connect('dbname='.$postgres_db_base['database'].' user='.$postgres_login.' password='.$postgres_pass);
if($postgres_link_base===FALSE)
    $is_up=false;

if($is_up)
{
    $is_up=false;
    foreach($postgres_db_tree as $common_server_content)
    {
        $is_up=true;
        if($common_server_content['host']!='localhost')
            $postgres_link_common = pg_connect('dbname='.$common_server_content['database'].' user='.$postgres_login.' password='.$postgres_pass.' host='.$common_server_content['host']);
        else
            $postgres_link_common = pg_connect('dbname='.$common_server_content['database'].' user='.$postgres_login.' password='.$postgres_pass);
        if($postgres_link_common===FALSE)
            $is_up=false;
        else
        {
            $is_up=false;
            foreach($common_server_content['servers'] as $server_content)
            {
                $is_up=true;
                if($server_content['host']!='localhost')
                    $postgres_link_server = pg_connect('dbname='.$server_content['database'].' user='.$postgres_login.' password='.$postgres_pass.' host='.$server_content['host']);
                else
                    $postgres_link_server = pg_connect('dbname='.$server_content['database'].' user='.$postgres_login.' password='.$postgres_pass);
                if($postgres_link_server===FALSE)
                    $is_up=false;
                break;
            }
        }
        break;
    }
}
?>
<?php
$title='Players - CatchChallenger MMORPG';
$description='Players - CatchChallenger, Independent Old school Opensource MMORPG/Single player game';
$keywords='player,catchchallenger,catch challenger,catch challenger,community';
include '../template/top.php';
include '../template/top2.php';
include '../template/topdatapack.php';

$pseudo=hex2bin(substr($_SERVER['REQUEST_URI'],8));
$skin_list=array();
$reply = pg_query_params($postgres_link_common,'SELECT * FROM character WHERE pseudo=$1',array($pseudo)) or die(pg_last_error());
if($data = pg_fetch_array($reply))
{
    if(isset($skin_list[$data['skin']]))
        $skin=$skin_list[$data['skin']];
    else
    {
        $reply_skin = pg_query_params($postgres_link_base,'SELECT skin FROM dictionary_skin WHERE id=$1',array($data['skin'])) or die(pg_last_error());
        if($data_skin = pg_fetch_array($reply_skin))
            $skin=$data_skin['skin'];
        else
            $skin='default';
        $skin_list[$data['skin']]=$skin;
    }
        
    echo '<center><div style="color:#fff;border: 1px solid #ddd;background-color:#fff;padding:10px 15px;width:800px;">
    <h3 style="border-top:2px solid #ddd;border-bottom:2px solid #ddd;color:#666;padding:7px;margin:0px;"><center><table><tr><td>';
    if(file_exists('../datapack/skin/fighter/'.$skin.'/trainer.png'))
        echo '<div style="width:16px;height:24px;background-image:url(\'../datapack/skin/fighter/'.htmlspecialchars($skin).'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></div>';
    elseif(file_exists('../datapack/skin/fighter/'.$skin.'/trainer.gif'))
        echo '<div style="width:16px;height:24px;background-image:url(\'../datapack/skin/fighter/'.htmlspecialchars($skin).'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></div>';
    echo '</td><td>'.htmlspecialchars($pseudo).'</td></tr></table></center></h3>
    <div style="background-color:#eee;color:#666;padding:7px;"><table><tr><td>';
    if(file_exists('../datapack/skin/fighter/'.$skin.'/front.png'))
        echo '<img src="../datapack/skin/fighter/'.htmlspecialchars($skin).'/front.png" width="80px" height="80px" ></div>';
    elseif(file_exists('../datapack/skin/fighter/'.$skin.'/front.gif'))
        echo '<img src="../datapack/skin/fighter/'.htmlspecialchars($skin).'/front.gif" width="80px" height="80px" ></div>';
    echo '</td><td>';
    echo 'Registred the '.date('jS \of F Y',$data['date'])."<br />\n";
    echo 'Last connect the '.date('jS \of F Y',$data['last_connect'])."<br />\n";
    echo 'Cash: '.htmlspecialchars($data['cash']).'$'."<br />\n";
    echo 'Monsters see into encyclopedia: <b>'.strlen($data['encyclopedia_monster']).'</b>'."<br />\n";
    echo 'Played time: <b>';
    if($data['played_time']<60)
        echo $data['played_time'].'s';
    elseif($data['played_time']<60*60)
        echo ceil($data['played_time']/60).'m';
    elseif($data['played_time']<72*60*60)
        echo ceil($data['played_time']/(60*60)).'h';
    else
        echo ceil($data['played_time']/(24*60*60)).'d';
    echo '</b>';
    echo "<br />\n";
    echo '</td></tr></table></div>
    </div>';
    echo '<a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=';
    if($https)
        echo 'https:';
    else
        echo 'http:';
    echo urlencode('//'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']).'"><img src="/images/share-facebook.png" width="83px" height="94px" alt="Share on Facebook" title="Share on Facebook" /></a>';
    echo '</center>';
}
else
    echo 'Player not found';
include '../template/bottom2.php';
include '../template/bottom.php';
