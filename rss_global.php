<?php
$filecurs=file_get_contents('http://catchchallenger.herman-brule.com/forum/feed.php?f=7');
echo str_replace('Announce • ','',$filecurs);
?>