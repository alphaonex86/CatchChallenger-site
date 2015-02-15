<?php
if(!isset($datapackexplorergeneratorinclude))
    die('abort into wiki post'."\n");

if(isset($wikivarsapp['apiURL']) && isset($wikivarsapp['username']) && isset($wikivarsapp['password']))
{
    require '../../../w2/LocalSettings.php';
    if($wgDBtype!='mysql')
        echo('Only mysql purge supported');
    else
    {
        $link=mysql_connect($wgDBserver,$wgDBuser,$wgDBpassword,true);
        if(!$link)
            echo('Mysql: unable to connect');
        else if(!mysql_select_db($wgDBname,$link))
            echo('Mysql: unable to select the db');
        else
        {
            mysql_query('TRUNCATE `l10n_cache`;',$link) or die(mysql_error());
            mysql_query('TRUNCATE `objectcache`;',$link) or die(mysql_error());
        }
    }

    unlink($wikivarsapp['cookiefile']);
    session_destroy();
}