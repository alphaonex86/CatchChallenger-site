<?php
$title='CatchChallenger - Old school Opensource MMORPG/Single player with multiple gameplay';
$description='CatchChallenger project, Independent Old school Opensource MMORPG/Single player game';
$keywords='catchchallenger,catch challenger,catch challenger,pokemon,minecraft,crafting,MMORPG,Opensource,Single player,Indie,Independent,game';
$css_list=array('/css/index.min.css');
include 'template/top.php';
?>

<section id="banner">
<img src="/images/catchchallenger-line.png" width="551" height="81" alt="Catch Challenger logo" title="Catch Challenger logo" />
This game is a independent MMORPG or a single player game. You can fight, farming, crafting, trading, ...<br />
<b><?php echo (date('Y')-2012); ?> years</b> working and server up. The game is fully open source (GPL3). <a href="/rules.html">(Read more)</a>
</section>

<section id="link">
<table>
    <tr>
        <td><img src="/images/free-to-play.png" width="267" height="104" alt="Free to play open source" title="Free to play open source" /></td>
        <td><a href="/rules.html" id="rules"><img src="/images/rules.png" width="267" height="104" alt="Rules of Catch challenger" title="Rules of Catch challenger" /></a></td>
        <td id="download"><a href="/download.html"><img src="/images/d.png" width="266" height="116" alt="Download this open source MMORPG" title="Download this open source MMORPG" /><img src="/images/a.png" width="59" height="43" alt="" class="bounce" /></a></td>
    </tr>
</table>
</section>

<section class="subsec">
    <div class="header"><a href="/screenshot.html"><span class="fh">Screen</span>shot</a></div>
    <div class="mscreenl">
    <a href="/screenshot/catchchallenger-ingame.png"><img src="/screenshot/catchchallenger-ingame-mini.jpg" class="screenshot" alt="ingame into the mmorpg client" title="ingame into the mmorpg client" width="200" height="150" /></a>
    <a href="/screenshot/catchchallenger-battle.png"><img src="/screenshot/catchchallenger-battle-mini.png" class="screenshot" alt="battle" title="battle" width="200" height="150" /></a>
    <a href="/screenshot/catchchallenger-crafting.png"><img src="/screenshot/catchchallenger-crafting-mini.jpg" class="screenshot" alt="catchchallenger crafting" title="catchchallenger crafting" width="200" height="150" /></a>
    <br style="clear:both;" />
    </div>
</section>

<?php
date_default_timezone_set('Europe/Paris');
$filecurs=file_get_contents('http://catchchallenger.first-world.info/forum/feed.php?f=7');
if(preg_match('#^.*<content[^>]*>(.*)</content>.*$#isU',$filecurs))
{
    echo '    <section class="subsec">
    <div class="header"><a href="/forum/viewforum.php?f=7"><span class="fh">Ne</span>ws</a></div>
    <div class="bodynews"><p class="text">';
    $filecurs=preg_replace('#^.*<content[^>]*>(.*)</content>.*$#isU','$1',$filecurs);
    $filecurs=preg_replace('#<p>Statistics:.*$#isU','',$filecurs);
    $filecurs=preg_replace('#<hr />.*$#isU','',$filecurs);
    $filecurs=str_replace('<![CDATA[','',$filecurs);
    $filecurs=str_replace(']]>','',$filecurs);
    if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443))
        $filecurs=str_replace('http://','https://',$filecurs);
    echo htmlspecialchars_decode($filecurs);
    echo '</p>    </div>
</section>';
}
?>
<img src="/images/widget01-inside.png" alt="" width="0" height="0" />
<?php
include 'template/bottom.php';
