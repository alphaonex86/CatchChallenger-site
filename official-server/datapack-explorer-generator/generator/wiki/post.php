<?php
if(!isset($datapackexplorergeneratorinclude))
    die('abort into wiki post'."\n");

if(isset($wikidblink))
{
    mysqli_query($wikidblink,'TRUNCATE `'.$wgDBprefix.'l10n_cache`;') or die(mysqli_error());
    mysqli_query($wikidblink,'TRUNCATE `'.$wgDBprefix.'objectcache`;') or die(mysqli_error());
}

mysqli_close($wikidblink);
unlink($wikivars['cookiefile']);


