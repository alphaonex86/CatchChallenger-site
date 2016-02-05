<?php
//session_start(); -> wrong in CLI only
$is_up=true;
if(file_exists('config.php'))
    require 'config.php';
else
    require '../config.php';

if(isset($_SERVER['SERVER_ADDR']) && isset($_SERVER['REMOTE_ADDR']))
    if($_SERVER['SERVER_ADDR']!=$_SERVER['REMOTE_ADDR'])
        die('denied');

if(!isset($argc) || $argc<1 || php_sapi_name()!=='cli')
    die('need be started by CLI');

if(!isset($datapack_explorer_local_path))
	die('$datapack_explorer_local_path not set');

if(!is_dir($datapack_explorer_local_path))
	if(!mkdir($datapack_explorer_local_path))
		exit;

$lang_to_load=array('en');

$automaticallygen='<div id="automaticallygen">Automatically generated from ';
if(isset($datapack_source_url) && $datapack_source_url!='')
	$automaticallygen.='<a href="'.$datapack_source_url.'">';
$automaticallygen.='the datapack';
if(isset($datapack_source_url) && $datapack_source_url!='')
	$automaticallygen.='</a>';
$automaticallygen.='</div>';

if(!file_exists('/usr/bin/pngcrush'))
    echo 'Better if you install /usr/bin/pngcrush'."\n";
if(!file_exists('/usr/bin/pngquant'))
    echo 'Better if you install /usr/bin/pngquant'."\n";

$datapackexplorergeneratorinclude=true;

$time_start=microtime(true);
require 'datapack-explorer-generator/function.php';
require 'datapack-explorer-generator/functions/reputation.php';
require 'datapack-explorer-generator/functions/quests.php';
require 'datapack-explorer-generator/functions/monsters.php';
require 'datapack-explorer-generator/functions/maps.php';
require 'datapack-explorer-generator/functions/bots.php';
require 'datapack-explorer-generator/translation/en.php';

$template=file_get_contents('template.html');
if(preg_match('#/home/user/#isU',$_SERVER['PWD']))
    $template=str_replace('stat.first-world.info','localhost',$template);

if(isset($wikivarsapp))
{    
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
}
else
{
    foreach($lang_to_load as $temp_lang)
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
require 'datapack-explorer-generator/load/informations.php';
echo 'Datapack loaded'."\n";
echo 'Done into '.ceil(microtime(true)-$time_start).'s'."\n";

$current_lang='en';
$time_start=microtime(true);
require 'datapack-explorer-generator/generator/map_preview.php';
echo 'Map preview done'."\n";
echo 'Done into '.ceil(microtime(true)-$time_start).'s'."\n";

if($argc<=1 || in_array('wiki',$argv))
    if(isset($wikivarsapp) && count($wikivarsapp)>0)
    {
        require 'datapack-explorer-generator/generator/wiki/pre.php';
        foreach($wikivarsapp as $wikivars)
        {
            $current_lang=$wikivars['lang'];
            echo 'Lang for the wiki '.$wikivars['wikiFolder'].': '.$current_lang."\n";
            $time_start=microtime(true);
            require 'datapack-explorer-generator/generator/wiki/init.php';

            if($wiki_error)
                echo 'wiki error, skip'."\n";
            else
            {
                $sub_time_start=microtime(true);
                require 'datapack-explorer-generator/generator/wiki/map.php';
                echo 'Map generated into '.ceil(microtime(true)-$sub_time_start).'s for '.$wikivars['wikiFolder']."\n";
                $sub_time_start=microtime(true);
                require 'datapack-explorer-generator/generator/wiki/items.php';
                echo 'Item generated into '.ceil(microtime(true)-$sub_time_start).'s for '.$wikivars['wikiFolder']."\n";
                $sub_time_start=microtime(true);
                require 'datapack-explorer-generator/generator/wiki/zone.php';
                echo 'Zone generated into '.ceil(microtime(true)-$sub_time_start).'s for '.$wikivars['wikiFolder']."\n";
                require 'datapack-explorer-generator/generator/wiki/items-index.php';
                $sub_time_start=microtime(true);
                require 'datapack-explorer-generator/generator/wiki/bots.php';
                echo 'Bot generated into '.ceil(microtime(true)-$sub_time_start).'s for '.$wikivars['wikiFolder']."\n";
                $sub_time_start=microtime(true);
                require 'datapack-explorer-generator/generator/wiki/monsters.php';
                echo 'Monster generated into '.ceil(microtime(true)-$sub_time_start).'s for '.$wikivars['wikiFolder']."\n";
                $sub_time_start=microtime(true);
                require 'datapack-explorer-generator/generator/wiki/buffs.php';
                echo 'Buffs generated into '.ceil(microtime(true)-$sub_time_start).'s for '.$wikivars['wikiFolder']."\n";
                require 'datapack-explorer-generator/generator/wiki/crafting.php';
                require 'datapack-explorer-generator/generator/wiki/plants.php';
                $sub_time_start=microtime(true);
                require 'datapack-explorer-generator/generator/wiki/skills.php';
                echo 'Skill generated into '.ceil(microtime(true)-$sub_time_start).'s for '.$wikivars['wikiFolder']."\n";
                $sub_time_start=microtime(true);
                require 'datapack-explorer-generator/generator/wiki/types.php';
                echo 'Type generated into '.ceil(microtime(true)-$sub_time_start).'s for '.$wikivars['wikiFolder']."\n";
                require 'datapack-explorer-generator/generator/wiki/start.php';
                $sub_time_start=microtime(true);
                require 'datapack-explorer-generator/generator/wiki/quests.php';
                echo 'Quests generated into '.ceil(microtime(true)-$sub_time_start).'s for '.$wikivars['wikiFolder']."\n";
                $sub_time_start=microtime(true);
                require 'datapack-explorer-generator/generator/wiki/industries.php';
                echo 'Industries generated into '.ceil(microtime(true)-$sub_time_start).'s for '.$wikivars['wikiFolder']."\n";

                $sub_time_start=microtime(true);
                require 'datapack-explorer-generator/generator/wiki/post.php';
                echo 'All done into '.ceil(microtime(true)-$time_start).'s'."\n";
            }
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
    require 'datapack-explorer-generator/generator/tree.php';
    
    require 'datapack-explorer-generator/generator/jsonstat.php';
    //require 'datapack-explorer-generator/generator/reputation.php';
    echo 'Explorer generation done '.ceil(microtime(true)-$time_start).'s'."\n";
}

//require 'datapack-explorer-generator/tools/map-fix-broken-links.php';
//require 'datapack-explorer-generator/tools/rename-map-file-name.php';

//session_destroy();
echo 'All is done'."\n";