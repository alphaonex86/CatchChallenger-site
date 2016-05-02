<?php
$filecurs=file_get_contents('http://catchchallenger.first-world.info/forum/feed.php?f=7');
echo str_replace('Announce • ','',$filecurs);
?>