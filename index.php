<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title>CatchChallenger - Old school Opensource MMORPG/Single player with multiple gameplay</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF8"/>
		<meta name="robots" content="index,follow"/>
		<meta name="description" content="CatchChallenger project, Independent Old school Opensource MMORPG/Single player game" />
		<meta name="keywords" content="catchchallenger,catch challenger,catch challenger,pokemon,minecraft,crafting,MMORPG,Opensource,Single player,Indie,Independent,game" />
		<link rel="stylesheet" type="text/css" media="screen" href="/css/style.css" />
		<link rel="alternate" type="application/atom+xml" href="/rss_global.xml" title="All news" />
        <meta name="viewport" content="width=device-width" />
        <meta name="Language" content="en" />
        <meta http-equiv="content-language" content="english" />
	</head>
	<body>
		<div id="container">
			<div id="header">
				<div id="logo"></div>
				<div id="back_menu">
					<table>
					<tr>
						<td><a href="/">Home</a></td>
						<td><a href="/official-server.html">Official server</a></td>
						<td><a href="/download.html">Download</a></td>
						<td><a href="/screenshot.html">Screenshot</a></td>
						<td><a href="/shop/">Shop</a></td>
						<td><a href="/community.html">Community</a></td>
						<td><a href="/contact.html">Contact</a></td>
					</tr>
					</table>
				</div>
			</div>
			<div id="body">
				<div id="title">CatchChallenger project</div>
				<br />
				<br />
				<img src="/images/catchchallenger-logo.png" width="96" height="96" style="float:left; margin-right:7px;" class="tiers_img" alt="Catch Challenger logo" title="Catch Challenger logo" />
				<p class="text">This game is a independent <strong>MMORPG, Lan game and a single player game</strong>. You have <strong>fight, farming, explore, crafting, trading, management, competition</strong>... and you are free to play in you style.</p>
				<p class="text">It's a pixel art and old school game. Our work is concentrated on the gameplay, creativity, performance and self hosting. The income is to paid the developing and the artwork. The game is fully <a href="https://github.com/alphaonex86/CatchChallenger">open source (<strong>GPL3</strong>)</a>.</p>
                <p class="text">The game have no real time. That's give clear advantage on 3G/wifi connexion or into the tiers world. You can play <strong>Player vs Player, Team vs Team</strong>, your team can own city and clan hall.</p>
                <p class="text">This project offer a scope to developing new technologies, innovate and analyze into the area of networking, cryptography, compression of datas, cloud/cluster/server/client, performance and protocol.</p>
				<br />
				<br />

<?php
date_default_timezone_set('Europe/Paris');
$filecurs=file_get_contents('http://catchchallenger.first-world.info/forum/feed.php?f=7');
if(preg_match('#^.*<content[^>]*>(.*)</content>.*$#isU',$filecurs))
{
    echo '<img src="/images/hr.png" width="632" height="19" class="separation" style="clear:both;" />
    <br />
    <img src="/images/chip.png" width="64" height="64" style="float:left; margin-right:7px;" class="tiers_img" alt="" />
    <div class="title">News</div>
    <p class="text">';
    $filecurs=preg_replace('#^.*<content[^>]*>(.*)</content>.*$#isU','$1',$filecurs);
    $filecurs=preg_replace('#<p>Statistics:.*$#isU','',$filecurs);
    $filecurs=preg_replace('#<hr />.*$#isU','',$filecurs);
    $filecurs=str_replace('<![CDATA[','',$filecurs);
    $filecurs=str_replace(']]>','',$filecurs);
    if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443))
        $filecurs=str_replace('http://','https://',$filecurs);
    echo htmlspecialchars_decode($filecurs);
    echo '</p>';
}
?>
				<img src="/images/hr.png" width="632" height="19" class="separation" />
				<br />
				<img src="/images/chip.png" width="64" height="64" style="float:left; margin-right:7px;" class="tiers_img" alt="" />
				<p class="text">Against the other MMORPG:
				<ul>
					<li><strong><u>Farming for resell with real money is allowed</u></strong> (<a href="rules.html">see the rules</a>)</li>
					<li>Free to play with multiple account</li>
					<li>You can simply re-open you own server with the same content (like a mirror of the official server, <a href="download.html">see download</a>). Exists multiples mirrors on the web to access of the download if all the officials sources are down.</li>
					<li>Warranty of life by small cost. <strong><u>I pledge to keep open as long as possible the server</u></strong> (<a href="rules.html">see the rules</a>), else see above</li>
					<li><span class="important"><b><?php echo (date('Y')-2012); ?> years</b> working and server up</span></li>
                    <li>Your bots on autorised server</li>
				</ul>
				</p>
				<img src="/images/hr.png" width="632" height="19" class="separation" />
				<div class="title">Parteners</div>
				<center>
				<table>
				<tr>
				<td>&nbsp;<a href="//www.confiared.com/"><img src="/images/confiared-header.png" alt="VPS without connectivity problem, into bolivia, PaaS, backup and more solution" title="VPS without connectivity problem, into bolivia, PaaS, backup and more solution" /></a>&nbsp;</td>
				<td>&nbsp;<img src="/images/IPv6.png" alt="IPv6 full support" title="IPv6 full support" />&nbsp;</td>
				<td>&nbsp;<a href="//ultracopier.first-world.info/"><img src="/images/ultracopier.png" alt="Ultracopier is free and open source software licensed under GPL3 that acts as a replacement for files copy dialogs. Main features include: play/pause, speed limitation, on-error resume, error/collision management ..." title="Ultracopier is free and open source software licensed under GPL3 that acts as a replacement for files copy dialogs. Main features include: play/pause, speed limitation, on-error resume, error/collision management ..." /></a>&nbsp;</td>
				</tr>
				</table>
				</center>
			</div>
			<div id="footer">
				<div id="copyright">CatchChallenger - <span style="color:#777;font-size:80%">Donate Bitcoin: </span><span style="color:#999;font-size:70%">1C4VLs16HX5YBoUeCLxEMJq8TpP24dcUJN</span> <span style="color:#777;font-size:80%">Nextcoin: </span><span style="color:#999;font-size:70%">NXT-MY96-548U-A5V5-BSR7R</span></div>
			</div>
		</div>
<script type="text/javascript">
var _paq=_paq || [];_paq.push(["trackPageView"]);_paq.push(["enableLinkTracking"]);
(function() {
var u=(("https:"==document.location.protocol)?"https":"http")+"://stat.first-world.info/";_paq.push(["setTrackerUrl",u+"piwik.php"]);_paq.push(["setSiteId","22"]);var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];g.type="text/javascript";g.defer=true;g.async=true;g.src=u+"piwik.js";s.parentNode.insertBefore(g,s);
})();
</script>
	</body>
</html>
