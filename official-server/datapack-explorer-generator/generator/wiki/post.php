<?php
if(!isset($datapackexplorergeneratorinclude))
    die('abort into wiki post'."\n");

if(isset($wikivarsapp['apiURL']) && isset($wikivarsapp['username']) && isset($wikivarsapp['password']))
{
    if(isset($wikidblink))
    {
        mysql_query('TRUNCATE `l10n_cache`;',$wikidblink) or die(mysql_error());
        mysql_query('TRUNCATE `objectcache`;',$wikidblink) or die(mysql_error());
    }

    unlink($wikivarsapp['cookiefile']);
    session_destroy();
}
