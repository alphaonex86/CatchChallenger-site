<?php
$title='CatchChallenger download - Old school Opensource MMORPG/Single player with multiple gameplay';
$description='CatchChallenger download, Independent Old school Opensource MMORPG/Single player game';
$keywords='catchchallenger,catch challenger,catch challenger,download';
include 'template/top.php';
include 'template/top2.php';
$version=file_get_contents('updater.txt');
?>

				<div id="title">Download <b>CatchChallenger</b></div>
				<br />
				<br />
				<table>
                    <tr>
                        <td class="tiers_img bigbutton"><a href="//cdn.confiared.com/catchchallenger.herman-brule.com/files/<?php echo $version; ?>/catchchallenger-qtcpu800x600-windows-x86-<?php echo $version; ?>-setup.exe"><center><img src="/images/windows.png" width="96" height="96" alt="" />Windows</center></a></td>
                        <td class="tiers_img bigbutton"><a href="//cdn.confiared.com/catchchallenger.herman-brule.com/files/<?php echo $version; ?>/catchchallenger-mac-os-x-<?php echo $version; ?>.dmg"><center><img src="/images/mac.png" width="96" height="96" alt="" />Mac</center></a></td>
                    </tr>
                </table>
				<br />
				<br />
				The sources of the client/server: <a href="https://github.com/alphaonex86/CatchChallenger">https://github.com/alphaonex86/CatchChallenger</a><br />

<?php
include 'template/bottom2.php';
include 'template/bottom.php';
