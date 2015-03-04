<?php
session_start();
$is_up=true;
if(file_exists('config.php'))
    require 'config.php';
else
    require '../config.php';

if(isset($_SERVER['SERVER_ADDR']) && isset($_SERVER['REMOTE_ADDR']))
    if($_SERVER['SERVER_ADDR']!=$_SERVER['REMOTE_ADDR'])
        die('denied');

if(!isset($argc) || $argc<1)
    die('need be started by CLI');

if(!isset($datapack_explorer_local_path))
	die('$datapack_explorer_local_path not set');

if(!is_dir($datapack_explorer_local_path))
	if(!mkdir($datapack_explorer_local_path))
		exit;

$automaticallygen='<div id="automaticallygen">Automatically generated from ';
if(isset($datapack_source_url) && $datapack_source_url!='')
	$automaticallygen.='<a href="'.$datapack_source_url.'">';
$automaticallygen.='the datapack';
if(isset($datapack_source_url) && $datapack_source_url!='')
	$automaticallygen.='</a>';
$automaticallygen.='</div>';

$datapackexplorergeneratorinclude=true;

$time_start=microtime(true);
require 'datapack-explorer-generator/function.php';
require 'datapack-explorer-generator/functions/reputation.php';
require 'datapack-explorer-generator/functions/quests.php';
require 'datapack-explorer-generator/functions/monsters.php';
require 'datapack-explorer-generator/functions/maps.php';
require 'datapack-explorer-generator/translation/en.php';

$template=file_get_contents('template.html');
if(preg_match('#/home/user/#isU',$_SERVER['PWD']))
    $template=str_replace('stat.first-world.info','localhost',$template);

$lang_to_load=array('en');
foreach($wikivarsapp as $wikivars)
{
    $temp_lang=$wikivars['lang'];
    if(!in_array($temp_lang,$lang_to_load))
    {
        require 'datapack-explorer-generator/translation/'.$temp_lang.'.php';
        $lang_to_load[]=$temp_lang;
        foreach($translation_list['en'] as $original_text=>$translated_text)
            if(!isset($translation_list[$temp_lang][$original_text]))
                $translation_list[$temp_lang][$original_text]=$translated_text;
    }
}

require 'datapack-explorer-generator/load/items.php';
require 'datapack-explorer-generator/load/type.php';
require 'datapack-explorer-generator/load/reputation.php';
require 'datapack-explorer-generator/load/recipes.php';
require 'datapack-explorer-generator/load/buff.php';
require 'datapack-explorer-generator/load/skill.php';
require 'datapack-explorer-generator/load/industries.php';
require 'datapack-explorer-generator/load/plants.php';
require 'datapack-explorer-generator/load/monster.php';
require 'datapack-explorer-generator/load/other.php';
require 'datapack-explorer-generator/load/shops.php';
require 'datapack-explorer-generator/load/layer.php';
require 'datapack-explorer-generator/load/map.php';
require 'datapack-explorer-generator/load/fights.php';
require 'datapack-explorer-generator/load/bots.php';
require 'datapack-explorer-generator/load/team.php';
echo 'Datapack loaded'."\n";
echo 'Done into '.(microtime(true)-$time_start).'s'."\n";

$current_lang='en';
$time_start=microtime(true);
require 'datapack-explorer-generator/generator/map_preview.php';
echo 'Map preview done'."\n";
echo 'Done into '.(microtime(true)-$time_start).'s'."\n";

if($argc<=1 || in_array('wiki',$argv))
    if(count($wikivarsapp)>0)
    {
        require 'datapack-explorer-generator/generator/wiki/pre.php';
        foreach($wikivarsapp as $wikivars)
        {
            $time_start=microtime(true);
            $current_lang=$wikivars['lang'];
            require 'datapack-explorer-generator/generator/wiki/init.php';
            require 'datapack-explorer-generator/generator/wiki/map.php';
            require 'datapack-explorer-generator/generator/wiki/items.php';
            require 'datapack-explorer-generator/generator/wiki/zone.php';
            require 'datapack-explorer-generator/generator/wiki/items-index.php';
            require 'datapack-explorer-generator/generator/wiki/bots.php';
            require 'datapack-explorer-generator/generator/wiki/monsters.php';
            require 'datapack-explorer-generator/generator/wiki/buffs.php';
            require 'datapack-explorer-generator/generator/wiki/crafting.php';
            require 'datapack-explorer-generator/generator/wiki/plants.php';
            require 'datapack-explorer-generator/generator/wiki/skills.php';
            require 'datapack-explorer-generator/generator/wiki/types.php';
            require 'datapack-explorer-generator/generator/wiki/start.php';
            require 'datapack-explorer-generator/generator/wiki/quests.php';
            require 'datapack-explorer-generator/generator/wiki/industries.php';
            require 'datapack-explorer-generator/generator/wiki/post.php';
            echo 'Lang for the wiki '.$wikivars['wikiFolder'].':'.$current_lang."\n";
            echo 'Done into '.(microtime(true)-$time_start).'s'."\n";
        }
        require 'datapack-explorer-generator/generator/wiki/close.php';
    }

if($argc<=1 || in_array('explorer',$argv))
{
    $time_start=microtime(true);
    $current_lang='en';
    require 'datapack-explorer-generator/generator/map.php';
    require 'datapack-explorer-generator/generator/buffs.php';
    require 'datapack-explorer-generator/generator/skills.php';
    require 'datapack-explorer-generator/generator/monsters.php';
    require 'datapack-explorer-generator/generator/items.php';
    require 'datapack-explorer-generator/generator/items-index.php';
    require 'datapack-explorer-generator/generator/crafting.php';
    require 'datapack-explorer-generator/generator/industries.php';
    require 'datapack-explorer-generator/generator/start.php';
    require 'datapack-explorer-generator/generator/quests.php';
    require 'datapack-explorer-generator/generator/types.php';
    require 'datapack-explorer-generator/generator/bots.php';
    require 'datapack-explorer-generator/generator/zone.php';
    require 'datapack-explorer-generator/generator/plants.php';
    //require 'datapack-explorer-generator/generator/reputation.php';
    echo 'Explorer generation done '.(microtime(true)-$time_start).'s'."\n";
    echo 'Done into '.(microtime(true)-$time_start).'s'."\n";
}

//require 'datapack-explorer-generator/tools/map-fix-broken-links.php';
//require 'datapack-explorer-generator/tools/rename-map-file-name.php';

session_destroy();
echo 'All is done'."\n";