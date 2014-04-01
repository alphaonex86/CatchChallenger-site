<?php
$is_up=true;
require '../config.php';
$mysql_link=@mysql_connect($mysql_host,$mysql_login,$mysql_pass,true);
if($mysql_link===NULL)
	$is_up=false;
else if(!@mysql_select_db($mysql_db))
	$is_up=false;
if(!$is_up)
	exit;

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

require 'datapack-explorer-generator/function.php';

$template=file_get_contents('template.html');

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
require 'datapack-explorer-generator/load/map.php';

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
