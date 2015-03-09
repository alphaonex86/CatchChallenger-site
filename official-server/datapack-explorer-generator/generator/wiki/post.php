<?php
if(!isset($datapackexplorergeneratorinclude))
    die('abort into wiki post'."\n");

if(isset($wikidblink))
{
    mysqli_query($wikidblink,'TRUNCATE `'.$wgDBprefix_final.'l10n_cache`;') or die(mysqli_error());
    mysqli_query($wikidblink,'TRUNCATE `'.$wgDBprefix_final.'objectcache`;') or die(mysqli_error());
}

if(count($wikivarsapp)>1)
    foreach($wikivarsapp as $wikivars2)
    {
        if($wikivars2['lang']!=$current_lang)
        {
            unset($wgArticlePath);
            require '../'.$wikivars2['wikiFolder'].'/LocalSettings.php';
            if(!isset($wgArticlePath))
                $final_url='/'.addslashes($wikivars2['wikiFolder']).'/index.php?title=$1';
            else
                $final_url=$wgArticlePath;
            $reply_interwiki = mysqli_query($wikidblink,'SELECT `iw_url` FROM `'.$wgDBprefix_final.'interwiki` WHERE `iw_prefix`=\''.addslashes($wikivars2['lang']).'\'') or die(mysqli_error());
            if($data_interwiki = mysqli_fetch_array($reply_interwiki))
                mysqli_query($wikidblink,'UPDATE `'.$wgDBprefix_final.'interwiki` SET `iw_url`=\''.addslashes($final_url).'\' WHERE `iw_prefix`=\''.addslashes($wikivars2['lang']).'\';') or die(mysqli_error());
            else
                mysqli_query($wikidblink,'INSERT INTO `'.$wgDBprefix_final.'interwiki`(`iw_prefix`,`iw_url`) VALUES(\''.addslashes($wikivars2['lang']).'\',\''.addslashes($final_url).'\');') or die(mysqli_error());
            mysqli_free_result($reply_interwiki);
        }
    }

mysqli_close($wikidblink);
unlink($wikivars['cookiefile']);


