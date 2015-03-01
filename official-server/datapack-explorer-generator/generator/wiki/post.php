<?php
if(!isset($datapackexplorergeneratorinclude))
    die('abort into wiki post'."\n");

if(isset($wikidblink))
{
    mysql_query('TRUNCATE `'.$wgDBprefix.'l10n_cache`;',$wikidblink) or die(mysql_error());
    mysql_query('TRUNCATE `'.$wgDBprefix.'objectcache`;',$wikidblink) or die(mysql_error());
}

mysql_close($wikidblink);
unlink($wikivars['cookiefile']);


