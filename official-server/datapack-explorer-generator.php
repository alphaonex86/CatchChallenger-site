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

if(!is_dir('datapack-explorer'))
	if(!mkdir('datapack-explorer'))
		exit;

function filewrite($file,$content)
{
	if($filecurs=fopen($file, 'w'))
	{
		if(fwrite($filecurs,$content) === false)
			die('Unable to write the file: '.$file);
		fclose($filecurs);
	}
	else
		die('Unable to write or create the file: '.$file);
}

$template=file_get_contents('template.html');

$item_meta=array();
if(file_exists('../datapack/items/items.xml'))
{
	$content=file_get_contents('../datapack/items/items.xml');
	preg_match_all('#<item[^>]*>.*</item>#isU',$content,$temp_text_list);
	foreach($temp_text_list[0] as $entry)
	{
		if(!preg_match('#<item[^>]*id="[0-9]+".*</item>#isU',$entry))
			continue;
		$id=preg_replace('#^.*<item[^>]*id="([0-9]+)".*</item>.*$#isU','$1',$entry);
		$price=0;
		if(preg_match('#<item[^>]*price="[0-9]+".*</item>#isU',$entry))
			$price=preg_replace('#^.*<item[^>]*price="([0-9]+)".*</item>.*$#isU','$1',$entry);
		if(preg_match('#<item[^>]*image="[^"]+".*</item>#isU',$entry))
			$image=preg_replace('#^.*<item[^>]*image="([^"]+)".*</item>.*$#isU','$1',$entry);
		else
			$image='';
		if(!preg_match('#<name( lang="en")?>.*</name>#isU',$entry))
			continue;
		$name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$entry);
		if(!preg_match('#<description( lang="en")?>.*</description>#isU',$entry))
			continue;
		$description=preg_replace('#^.*<description( lang="en")?>(.*)</description>.*$#isU','$2',$entry);
		$item_meta[$id]=array('price'=>$price,'image'=>$image,'name'=>$name,'description'=>$description);
	}
}

$crafting_meta=array();
if(file_exists('../datapack/crafting/recipes.xml'))
{
	$content=file_get_contents('../datapack/crafting/recipes.xml');
	preg_match_all('#<recipe[^>]*>.*</recipe>#isU',$content,$temp_text_list);
	foreach($temp_text_list[0] as $entry)
	{
		if(!preg_match('#<recipe[^>]*id="[0-9]+".*</recipe>#isU',$entry))
			continue;
		$id=preg_replace('#^.*<recipe[^>]*id="([0-9]+)".*</recipe>.*$#isU','$1',$entry);
		if(!preg_match('#<recipe[^>]*itemToLearn="[0-9]+".*</recipe>#isU',$entry))
			continue;
		$itemToLearn=preg_replace('#^.*<recipe[^>]*itemToLearn="([0-9]+)".*</recipe>.*$#isU','$1',$entry);
		if(!preg_match('#<recipe[^>]*doItemId="[0-9]+".*</recipe>#isU',$entry))
			continue;
		$doItemId=preg_replace('#^.*<recipe[^>]*doItemId="([0-9]+)".*</recipe>.*$#isU','$1',$entry);
		$material=array();
		preg_match_all('#<material itemId="([0-9]+)" quantity="([0-9]+)" />#isU',$entry,$temp_material_list);
		foreach($temp_material_list[0] as $material_text)
		{
			$itemId=preg_replace('#^.*<material itemId="([0-9]+)".*$#isU','$1',$material_text);
			$quantity=1;
			if(preg_match('#<material[^>]+quantity="([0-9]+)"#isU',$material_text))
				$quantity=preg_replace('#^.*<material[^>]+quantity="([0-9]+)".*$#isU','$1',$material_text);
			$material[$itemId]=$quantity;
		}
		$crafting_meta[$id]=array('itemToLearn'=>$itemToLearn,'doItemId'=>$doItemId,'material'=>$material);
	}
}

$skill_meta=array();
if(file_exists('../datapack/monsters/skill.xml'))
{
	$content=file_get_contents('../datapack/monsters/skill.xml');
	preg_match_all('#<skill id="[0-9]+".*</skill>#isU',$content,$temp_text_list);
	foreach($temp_text_list[0] as $entry)
	{
		if(!preg_match('#<skill id="[0-9]+".*</skill>#isU',$entry))
			continue;
		$id=preg_replace('#^.*<skill id="([0-9]+)".*</skill>.*$#isU','$1',$entry);
		$type='normal';
		if(preg_match('#type="[^"]+"#isU',$entry))
			$type=preg_replace('#^.*type="([^"]+)".*$#isU','$1',$entry);
		if(!preg_match('#<name( lang="en")?>.*</name>#isU',$entry))
			continue;
		$name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$entry);
		$level_list=array();
		preg_match_all('#<level number="([0-9]+)".*</level>#isU',$entry,$temp_level_list);
		foreach($temp_level_list[0] as $level_text)
		{
			$number=preg_replace('#^.*<level number="([0-9]+)".*</level>.*$#isU','$1',$level_text);
			$endurance=40;
			if(preg_match('#endurance="[0-9]+"#isU',$level_text))
				$endurance=preg_replace('#^.*endurance="([0-9]+)".*$#isU','$1',$level_text);
			$level_list[$number]=array('endurance'=>$endurance);
		}
		$skill_meta[$id]=array('type'=>$type,'name'=>$name,'level_list'=>$level_list);
	}
}

$industrie_meta=array();
$industrie_link_meta=array();
if($handle = opendir('../datapack/industries/')) {
	while(false !== ($entry = readdir($handle))) {
	if($entry != '.' && $entry != '..') {
			$content=file_get_contents('../datapack/industries/'.$entry);
			preg_match_all('#<link[^>]+/>#isU',$content,$entry_list);
			foreach($entry_list[0] as $entry)
			{
				if(!preg_match('#<link[^>]*industrialrecipe="([0-9]+)"[^>]*/>#isU',$entry))
					continue;
				if(!preg_match('#<link[^>]*industry="([0-9]+)"[^>]*/>#isU',$entry))
					continue;
				$industry_id=preg_replace('#^.*<link[^>]*industrialrecipe="([0-9]+)"[^>]*/>.*$#isU','$1',$entry);
				$factory_id=preg_replace('#^.*<link[^>]*industry="([0-9]+)"[^>]*/>.*$#isU','$1',$entry);
				$industrie_link_meta[$factory_id]=$industry_id;
			}
			preg_match_all('#<industrialrecipe[^>]+>.*</industrialrecipe>#isU',$content,$entry_list);
			foreach($entry_list[0] as $entry)
			{
				if(!preg_match('#<industrialrecipe[^>]*id="([0-9]+)"#isU',$entry))
					continue;
				if(!preg_match('#<industrialrecipe[^>]*time="([0-9]+)"#isU',$entry))
					continue;
				if(!preg_match('#<industrialrecipe[^>]*cycletobefull="([0-9]+)"#isU',$entry))
					continue;
				$id=preg_replace('#^.*<industrialrecipe[^>]*id="([0-9]+)".*$#isU','$1',$entry);
				$time=preg_replace('#^.*<industrialrecipe[^>]*time="([0-9]+)".*$#isU','$1',$entry);
				$cycletobefull=preg_replace('#^.*<industrialrecipe[^>]*cycletobefull="([0-9]+)".*$#isU','$1',$entry);
				//resource
				$resources=array();
				preg_match_all('#<resource[^>]+/>#isU',$entry,$temp_text_list);
				foreach($temp_text_list[0] as $resource)
				{
					if(!preg_match('#<resource[^>]*id="([0-9]+)"#isU',$resource))
						continue;
					$quantity=1;
					$item=preg_replace('#^.*<resource[^>]*id="([0-9]+)".*$#isU','$1',$resource);
					if(!preg_match('#<resource[^>]*quantity="([0-9]+)"#isU',$resource))
						$quantity=preg_replace('#^.*<resource[^>]*quantity="([0-9]+)".*$#isU','$1',$resource);
					$resources[]=array('item'=>$item,'quantity'=>$quantity);
				}
				//product
				$products=array();
				preg_match_all('#<product[^>]+/>#isU',$entry,$temp_text_list);
				foreach($temp_text_list[0] as $product)
				{
					if(!preg_match('#<product[^>]*id="([0-9]+)"#isU',$product))
						continue;
					$quantity=1;
					$item=preg_replace('#^.*<product[^>]*id="([0-9]+)".*$#isU','$1',$product);
					if(!preg_match('#<product[^>]*quantity="([0-9]+)"#isU',$product))
						$quantity=preg_replace('#^.*<product[^>]*quantity="([0-9]+)".*$#isU','$1',$product);
					$products[]=array('item'=>$item,'quantity'=>$quantity);
				}
				$industrie_meta[$id]=array('time'=>$time,'cycletobefull'=>$cycletobefull,'resources'=>$resources,'products'=>$products);
			}
		}
	}
	closedir($handle);
}

$plant_meta=array();
if(file_exists('../datapack/plants/plants.xml'))
{
	$content=file_get_contents('../datapack/plants/plants.xml');
	preg_match_all('#<plant[^>]+>.*</plant>#isU',$content,$entry_list);
	foreach($entry_list[0] as $entry)
	{
		if(!preg_match('#<plant[^>]+id="[0-9]+"#isU',$entry))
			continue;
		if(!preg_match('#<plant[^>]+itemUsed="[0-9]+"#isU',$entry))
			continue;
		if(!preg_match('#<fruits>([0-9]+)</fruits>#isU',$entry))
			continue;
		if(!preg_match('#<quantity>([0-9]+(\\.[0-9]+)?)</quantity>#isU',$entry))
			continue;
		$id=preg_replace('#^.*<plant[^>]+id="([0-9]+)".*</plant>.*$#isU','$1',$entry);
		$itemUsed=preg_replace('#^.*<plant[^>]+itemUsed="([0-9]+)".*</plant>.*$#isU','$1',$entry);
		$fruits=preg_replace('#^.*<fruits>([0-9]+)</fruits>.*$#isU','$1',$entry)*60;
		$quantity=preg_replace('#^.*<quantity>([0-9]+(\\.[0-9]+)?)</quantity>.*$#isU','$1',$entry);
		$plant_meta[$id]=array('itemUsed'=>$itemUsed,'fruits'=>$fruits,'quantity'=>$quantity);
	}
}

$type_meta=array();
if(file_exists('../datapack/monsters/type.xml'))
{
	$content=file_get_contents('../datapack/monsters/type.xml');
	preg_match_all('#<type[^>]+>.*</type>#isU',$content,$entry_list);
	foreach($entry_list[0] as $entry)
	{
		$multiplicator=array();
		if(!preg_match('#name="([^"]+)"#isU',$entry))
			continue;
		$name=preg_replace('#^.*name="([^"]+)".*$#isU','$1',$entry);
		$english_name='Unknown name';
		if(preg_match('#<name lang="en">([^<]+)</name>#isU',$entry))
			$english_name=preg_replace('#^.*<name lang="en">([^<]+)</name>.*$#isU','$1',$entry);
		elseif(preg_match('#<name>([^<]+)</name>#isU',$entry))
			$english_name=preg_replace('#^.*<name>([^<]+)</name>.*$#isU','$1',$entry);
		preg_match_all('#<multiplicator number="([^"]+)" to="([^"]+)" />#isU',$entry,$multiplicator_list);
		foreach($multiplicator_list[0] as $tempmultiplicator)
		{
			$number=(float)preg_replace('#^.*<multiplicator number="([^"]+)" to="([^"]+)" />.*$#isU','$1',$tempmultiplicator);
			$to=preg_replace('#^.*<multiplicator number="([^"]+)" to="([^"]+)" />.*$#isU','$2',$tempmultiplicator);
			$to_list=explode(';',$to);;
			foreach($to_list as $to)
				$multiplicator[$to]=$number;
		}
		$type_meta[$name]=array('english_name'=>$english_name,'multiplicator'=>$multiplicator);
	}
}

$monster_meta=array();
$item_to_monster=array();
if(file_exists('../datapack/monsters/monster.xml'))
{
	$content=file_get_contents('../datapack/monsters/monster.xml');
	preg_match_all('#<monster id="[0-9]+".*</monster>#isU',$content,$entry_list);
	foreach($entry_list[0] as $entry)
	{
		if(!preg_match('#<monster id="[0-9]+".*</monster>#isU',$entry))
			continue;
		$id=preg_replace('#^.*<monster id="([0-9]+)".*</monster>.*$#isU','$1',$entry);
		if(!preg_match('#<name( lang="en")?>.*</name>#isU',$entry))
			continue;
		$name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$entry);
		if(!preg_match('#<description( lang="en")?>.*</description>#isU',$entry))
			continue;
		$ratio_gender="50";
		if(preg_match('#ratio_gender="([0-9]+)%?"#isU',$entry))
			$ratio_gender=preg_replace('#^.*ratio_gender="([0-9]+)%?".*$#isU','$1',$entry);

		$height="0";
		if(preg_match('#height="([0-9]+(\.[0-9]+)?)m?"#isU',$entry))
			$height=preg_replace('#^.*height="([0-9]+(\.[0-9]+)?)m?".*$#isU','$1',$entry);
		$weight="0";
		if(preg_match('#weight="([0-9]+(\.[0-9]+)?)(kg)?"#isU',$entry))
			$weight=preg_replace('#^.*weight="([0-9]+(\.[0-9]+)?)(kg)?".*$#isU','$1',$entry);
		$egg_step="0";
		if(preg_match('#egg_step="([0-9]+)"#isU',$entry))
			$egg_step=preg_replace('#^.*egg_step="([0-9]+)".*$#isU','$1',$entry);

		$hp="0";
		if(preg_match('#hp="([0-9]+)"#isU',$entry))
			$hp=preg_replace('#^.*hp="([0-9]+)".*$#isU','$1',$entry);
		$attack="0";
		if(preg_match('#attack="([0-9]+)"#isU',$entry))
			$attack=preg_replace('#^.*attack="([0-9]+)".*$#isU','$1',$entry);
		$defense="0";
		if(preg_match('#defense="([0-9]+)"#isU',$entry))
			$defense=preg_replace('#^.*defense="([0-9]+)".*$#isU','$1',$entry);
		$special_attack="0";
		if(preg_match('#special_attack="([0-9]+)"#isU',$entry))
			$special_attack=preg_replace('#^.*special_attack="([0-9]+)".*$#isU','$1',$entry);
		$special_defense="0";
		if(preg_match('#special_defense="([0-9]+)"#isU',$entry))
			$special_defense=preg_replace('#^.*special_defense="([0-9]+)".*$#isU','$1',$entry);
		$speed="0";
		if(preg_match('#speed="([0-9]+)"#isU',$entry))
			$speed=preg_replace('#^.*speed="([0-9]+)".*$#isU','$1',$entry);

		$catch_rate="100";
		if(preg_match('#catch_rate="([0-9]+)"#isU',$entry))
			$catch_rate=preg_replace('#^.*catch_rate="([0-9]+)".*$#isU','$1',$entry);
		$type=array('normal');
		if(preg_match('#type="([^"]+)"#isU',$entry))
			$type=explode(';',preg_replace('#^.*type="([^"]+)".*$#isU','$1',$entry));
		if(preg_match('#type2="([^"]+)"#isU',$entry))
			$type=array_merge($type,explode(';',preg_replace('#^.*type2="([^"]+)".*$#isU','$1',$entry)));
		if(count($type)<=0)
			$type=array('normal');
		$description=preg_replace('#^.*<description( lang="en")?>(.*)</description>.*$#isU','$2',$entry);
		$attack_list=array();
		preg_match_all('#<attack[^>]+/>#isU',$entry,$temp_text_list);
		foreach($temp_text_list[0] as $attack_text)
		{
			if(!preg_match('#<attack[^>]*id="[0-9]+"[^>]*>#isU',$attack_text))
				continue;
			$skill_id=preg_replace('#^.*<attack[^>]*id="([0-9]+)"[^>]*>.*$#isU','$1',$attack_text);
			if(!preg_match('#<attack[^>]*level="[0-9]+"[^>]*>#isU',$attack_text))
				continue;
			$level=preg_replace('#^.*<attack[^>]*level="([0-9]+)"[^>]*>.*$#isU','$1',$attack_text);
			if(preg_match('#<attack[^>]*attack_level="[0-9]+"[^>]*>#isU',$attack_text))
				$attack_level=preg_replace('#^.*<attack[^>]*attack_level="([0-9]+)"[^>]*>.*$#isU','$1',$attack_text);
			else
				$attack_level='1';
			if(!isset($attack_list[$level]))
				$attack_list[$level]=array();
			$attack_list[$level][]=array('id'=>$skill_id,'attack_level'=>$attack_level);
		}
		$evolution_list=array();
		preg_match_all('#<evolution [^>]+/>#isU',$entry,$temp_text_list);
		foreach($temp_text_list[0] as $attack_text)
		{
			if(!preg_match('#level="([0-9]+)" type="([^"]+)" evolveTo="([0-9]+)"#isU',$attack_text))
				continue;
			$level=preg_replace('#^.*level="([0-9]+)" type="([^"]+)" evolveTo="([0-9]+)".*$#isU','$1',$attack_text);
			$type=preg_replace('#^.*level="([0-9]+)" type="([^"]+)" evolveTo="([0-9]+)".*$#isU','$2',$attack_text);
			$evolveTo=preg_replace('#^.*level="([0-9]+)" type="([^"]+)" evolveTo="([0-9]+)".*$#isU','$3',$attack_text);
			$evolution_list[]=array('level'=>$level,'type'=>$type,'evolveTo'=>$evolveTo);
		}
		$drops_list=array();
		preg_match_all('#<drop[^>]+/>#isU',$entry,$temp_text_list);
		foreach($temp_text_list[0] as $attack_text)
		{
			$quantity_min=1;
			$quantity_max=1;
			$luck=1;
			if(!preg_match('#<drop[^>]*item="[0-9]+"[^>]*>#isU',$attack_text))
				continue;
			$item=preg_replace('#^.*<drop[^>]*item="([0-9]+)"[^>]*>.*$#isU','$1',$attack_text);
			if(preg_match('#<drop[^>]*quantity="[0-9]+"[^>]*>#isU',$attack_text))
			{
				$quantity_min=preg_replace('#^.*<drop[^>]*quantity="([0-9]+)"[^>]*>.*$#isU','$1',$attack_text);
				$quantity_max=$quantity_min;
			}
			if(preg_match('#<drop[^>]*quantity_min="[0-9]+"[^>]*>#isU',$attack_text))
				$quantity_min=preg_replace('#^.*<drop[^>]*quantity_min="([0-9]+)"[^>]*>.*$#isU','$1',$attack_text);
			if(preg_match('#<drop[^>]*quantity_max="[0-9]+"[^>]*>#isU',$attack_text))
				$quantity_max=preg_replace('#^.*<drop[^>]*quantity_max="([0-9]+)"[^>]*>.*$#isU','$1',$attack_text);
			if(preg_match('#<drop[^>]*luck="[0-9]+%?"[^>]*>#isU',$attack_text))
				$luck=preg_replace('#^.*<drop[^>]*luck="([0-9]+)%?"[^>]*>.*$#isU','$1',$attack_text);
			$drops_list[]=array('item'=>$item,'quantity_min'=>$quantity_min,'quantity_max'=>$quantity_max,'luck'=>$luck);
			if(!isset($item_to_monster[$item]))
				$item_to_monster[$item]=array();
			$item_to_monster[$item][]=array('monster'=>$id,'quantity_min'=>$quantity_min,'quantity_max'=>$quantity_max,'luck'=>$luck);
		}
		krsort($attack_list);
		$monster_meta[$id]=array('name'=>$name,'type'=>$type,'description'=>$description,'attack_list'=>$attack_list,'drops'=>$drops_list,'evolution_list'=>$evolution_list,'ratio_gender'=>$ratio_gender,'catch_rate'=>$catch_rate,
		'height'=>$height,'weight'=>$weight,'egg_step'=>$egg_step,'hp'=>$hp,'attack'=>$attack,'defense'=>$defense,'special_attack'=>$special_attack,'special_defense'=>$special_defense,'speed'=>$speed,
		);
	}
}

function getTmxList($dir,$sub_dir='')
{
	$files_list=array();
	if($handle = opendir($dir.$sub_dir)) {
		while(false !== ($entry = readdir($handle))) {
		if($entry != '.' && $entry != '..') {
				if(is_dir($dir.$sub_dir.$entry))
					$files_list=array_merge($files_list,getTmxList($dir,$sub_dir.$entry.'/'));
				else if(preg_match('#\\.tmx$#',$entry))
					$files_list[]=$sub_dir.$entry;
			}
		}
		closedir($handle);
	}
	return $files_list;
}

$maps_list=array();
$maps_name_to_file=array();
$temp_maps=getTmxList('../datapack/map/');
foreach($temp_maps as $map)
{
	$width=0;
	$height=0;
	$pixelwidth=0;
	$pixelheight=0;
	$map_folder=preg_replace('#/[^/]+$#','',$map).'/';
	$map_meta=str_replace('.tmx','.xml',$map);
	$borders=array();
	$doors=array();
	$content=file_get_contents('../datapack/map/'.$map);
	if(preg_match('#orientation="orthogonal" width="([0-9]+)" height="([0-9]+)" tilewidth="([0-9]+)" tileheight="([0-9]+)"#isU',$content))
	{
		$width=(int)preg_replace('#^.*orientation="orthogonal" width="([0-9]+)" height="([0-9]+)" tilewidth="([0-9]+)" tileheight="([0-9]+)".*$#isU','$1',$content);
		$height=(int)preg_replace('#^.*orientation="orthogonal" width="([0-9]+)" height="([0-9]+)" tilewidth="([0-9]+)" tileheight="([0-9]+)".*$#isU','$2',$content);
		$tilewidth=(int)preg_replace('#^.*orientation="orthogonal" width="([0-9]+)" height="([0-9]+)" tilewidth="([0-9]+)" tileheight="([0-9]+)".*$#isU','$3',$content);
		$tileheight=(int)preg_replace('#^.*orientation="orthogonal" width="([0-9]+)" height="([0-9]+)" tilewidth="([0-9]+)" tileheight="([0-9]+)".*$#isU','$4',$content);
		$pixelwidth=$width*$tilewidth;
		$pixelheight=$height*$tileheight;
	}
	preg_match_all('#<object [^>]+type="border-(left|right|top|bottom)".*</object>#isU',$content,$temp_text_list);
	foreach($temp_text_list[0] as $border_text)
	{
		if(preg_match('#type="border-(left|right|top|bottom)"#isU',$border_text))
		{
			$border_orientation=preg_replace('#^.*type="border-(left|right|top|bottom).*$#isU','$1',$border_text);
			$border_orientation=preg_replace("#[\n\r\t]+#is",'',$border_orientation);
			if(preg_match('#<property name="map" value="([^"]+)"/>#isU',$border_text))
			{
				$border_map=preg_replace('#^.*<property name="map" value="([^"]+)"/>.*$#isU','$1',$border_text);
				$border_map=$map_folder.$border_map;
				if(!preg_match('#\\.tmx$#',$border_map))
					$border_map.='.tmx';
				$border_map=preg_replace('#/[^/]+/\\.\\./#isU','/',$border_map);
				$border_map=preg_replace('#^[^/]+/\\.\\./#isU','',$border_map);
				$border_map=preg_replace("#[\n\r\t]+#is",'',$border_map);
				$borders[$border_orientation]=$border_map;
			}
		}
	}
	preg_match_all('#<object [^>]+type="door".*</object>#isU',$content,$temp_text_list);
	foreach($temp_text_list[0] as $door_text)
	{
		if(preg_match('#type="door"#isU',$door_text))
		{
			if(preg_match('#<property name="map" value="([^"]+)"/>#isU',$door_text))
			{
				$door_map=preg_replace('#^.*<property name="map" value="([^"]+)"/>.*$#isU','$1',$door_text);
				$door_map=$map_folder.$door_map;
				if(!preg_match('#\\.tmx$#',$door_map))
					$door_map.='.tmx';
				$door_map=preg_replace('#/[^/]+/\\.\\./#isU','/',$door_map);
				$door_map=preg_replace('#^[^/]+/\\.\\./#isU','',$door_map);
				$door_map=preg_replace("#[\n\r\t]+#is",'',$door_map);
				$doors[]=$door_map;
			}
		}
	}
	$grass=array();
	$water=array();
	$cave=array();
	$type='outdoor';
	$name='Unknown name ('.$map.')';
	$shortdescription='';
	$description='';
	$dropcount=0;
	if(file_exists('../datapack/map/'.$map_meta))
	{
		$content=file_get_contents('../datapack/map/'.$map_meta);
		if(preg_match('#type="(outdoor|city|cave)"#isU',$content))
			$type=preg_replace('#^.*type="(outdoor|city|cave)".*$#isU','$1',$content);
		if(preg_match('#<name lang="en">[^<]+</name>#isU',$content))
			$name=preg_replace('#^.*<name lang="en">([^<]+)</name>.*$#isU','$1',$content);
		elseif(preg_match('#<name>[^<]+</name>#isU',$content))
			$name=preg_replace('#^.*<name>([^<]+)</name>.*$#isU','$1',$content);
		if(preg_match('#<shortdescription lang="en">[^<]+</shortdescription>#isU',$content))
			$shortdescription=preg_replace('#^.*<shortdescription lang="en">([^<]+)</shortdescription>.*$#isU','$1',$content);
		elseif(preg_match('#<shortdescription>[^<]+</shortdescription>#isU',$content))
			$shortdescription=preg_replace('#^.*<shortdescription>([^<]+)</shortdescription>.*$#isU','$1',$content);
		if(preg_match('#<description lang="en">[^<]+</description>#isU',$content))
			$description=preg_replace('#^.*<description lang="en">([^<]+)</description>.*$#isU','$1',$content);
		elseif(preg_match('#<description>[^<]+</description>#isU',$content))
			$description=preg_replace('#^.*<description>([^<]+)</description>.*$#isU','$1',$content);
		$type=preg_replace("#[\n\r\t]+#is",'',$type);
		$name=preg_replace("#[\n\r\t]+#is",'',$name);
		$shortdescription=preg_replace("#[\n\r\t]+#is",'',$shortdescription);
		$description=preg_replace("#[\n\r\t]+#is",'',$description);
		//grass
		if(preg_match('#<grass>(.*)</grass>#isU',$content))
		{
			$grass_text=preg_replace('#^.*<grass>(.*)</grass>.*$#isU','$1',$content);
			preg_match_all('#<monster minLevel="([0-9]+)" maxLevel="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>#isU',$grass_text,$temp_text_list);
			foreach($temp_text_list[0] as $grass_text_entry)
			{
				$minLevel=preg_replace('#^.*<monster minLevel="([0-9]+)" maxLevel="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>.*$#isU','$1',$grass_text_entry);
				$maxLevel=preg_replace('#^.*<monster minLevel="([0-9]+)" maxLevel="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>.*$#isU','$2',$grass_text_entry);
				$luck=preg_replace('#^.*<monster minLevel="([0-9]+)" maxLevel="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>.*$#isU','$3',$grass_text_entry);
				$id=preg_replace('#^.*<monster minLevel="([0-9]+)" maxLevel="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>.*$#isU','$4',$grass_text_entry);
				if(isset($monster_meta[$id]))
				{
					$grass[]=array('id'=>$id,'minLevel'=>$minLevel,'maxLevel'=>$maxLevel,'luck'=>$luck);
					$dropcount+=count($monster_meta[$id]['drops']);
				}
			}
			preg_match_all('#<monster level="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>#isU',$grass_text,$temp_text_list);
			foreach($temp_text_list[0] as $grass_text_entry)
			{
				$minLevel=preg_replace('#^.*<monster level="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>.*$#isU','$1',$grass_text_entry);
				$maxLevel=preg_replace('#^.*<monster level="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>.*$#isU','$1',$grass_text_entry);
				$luck=preg_replace('#^.*<monster level="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>.*$#isU','$2',$grass_text_entry);
				$id=preg_replace('#^.*<monster level="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>.*$#isU','$3',$grass_text_entry);
				if(isset($monster_meta[$id]))
				{
					$grass[]=array('id'=>$id,'minLevel'=>$minLevel,'maxLevel'=>$maxLevel,'luck'=>$luck);
					$dropcount+=count($monster_meta[$id]['drops']);
				}
			}
		}
		//water
		if(preg_match('#<water>(.*)</water>#isU',$content))
		{
			$grass_text=preg_replace('#^.*<water>(.*)</water>.*$#isU','$1',$content);
			preg_match_all('#<monster minLevel="([0-9]+)" maxLevel="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>#isU',$grass_text,$temp_text_list);
			foreach($temp_text_list[0] as $grass_text_entry)
			{
				$minLevel=preg_replace('#^.*<monster minLevel="([0-9]+)" maxLevel="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>.*$#isU','$1',$grass_text_entry);
				$maxLevel=preg_replace('#^.*<monster minLevel="([0-9]+)" maxLevel="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>.*$#isU','$2',$grass_text_entry);
				$luck=preg_replace('#^.*<monster minLevel="([0-9]+)" maxLevel="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>.*$#isU','$3',$grass_text_entry);
				$id=preg_replace('#^.*<monster minLevel="([0-9]+)" maxLevel="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>.*$#isU','$4',$grass_text_entry);
				if(isset($monster_meta[$id]))
				{
					$water[]=array('id'=>$id,'minLevel'=>$minLevel,'maxLevel'=>$maxLevel,'luck'=>$luck);
					$dropcount+=count($monster_meta[$id]['drops']);
				}
			}
			preg_match_all('#<monster level="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>#isU',$grass_text,$temp_text_list);
			foreach($temp_text_list[0] as $grass_text_entry)
			{
				$minLevel=preg_replace('#^.*<monster level="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>.*$#isU','$1',$grass_text_entry);
				$maxLevel=preg_replace('#^.*<monster level="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>.*$#isU','$1',$grass_text_entry);
				$luck=preg_replace('#^.*<monster level="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>.*$#isU','$2',$grass_text_entry);
				$id=preg_replace('#^.*<monster level="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>.*$#isU','$3',$grass_text_entry);
				if(isset($monster_meta[$id]))
				{
					$water[]=array('id'=>$id,'minLevel'=>$minLevel,'maxLevel'=>$maxLevel,'luck'=>$luck);
					$dropcount+=count($monster_meta[$id]['drops']);
				}
			}
		}
		//cave
		if(preg_match('#<cave>(.*)</cave>#isU',$content))
		{
			$grass_text=preg_replace('#^.*<cave>(.*)</cave>.*$#isU','$1',$content);
			preg_match_all('#<monster minLevel="([0-9]+)" maxLevel="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>#isU',$grass_text,$temp_text_list);
			foreach($temp_text_list[0] as $grass_text_entry)
			{
				$minLevel=preg_replace('#^.*<monster minLevel="([0-9]+)" maxLevel="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>.*$#isU','$1',$grass_text_entry);
				$maxLevel=preg_replace('#^.*<monster minLevel="([0-9]+)" maxLevel="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>.*$#isU','$2',$grass_text_entry);
				$luck=preg_replace('#^.*<monster minLevel="([0-9]+)" maxLevel="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>.*$#isU','$3',$grass_text_entry);
				$id=preg_replace('#^.*<monster minLevel="([0-9]+)" maxLevel="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>.*$#isU','$4',$grass_text_entry);
				if(isset($monster_meta[$id]))
				{
					$cave[]=array('id'=>$id,'minLevel'=>$minLevel,'maxLevel'=>$maxLevel,'luck'=>$luck);
					$dropcount+=count($monster_meta[$id]['drops']);
				}
			}
			preg_match_all('#<monster level="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>#isU',$grass_text,$temp_text_list);
			foreach($temp_text_list[0] as $grass_text_entry)
			{
				$minLevel=preg_replace('#^.*<monster level="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>.*$#isU','$1',$grass_text_entry);
				$maxLevel=preg_replace('#^.*<monster level="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>.*$#isU','$1',$grass_text_entry);
				$luck=preg_replace('#^.*<monster level="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>.*$#isU','$2',$grass_text_entry);
				$id=preg_replace('#^.*<monster level="([0-9]+)" luck="([0-9]+)" id="([0-9]+)"/>.*$#isU','$3',$grass_text_entry);
				if(isset($monster_meta[$id]))
				{
					$cave[]=array('id'=>$id,'minLevel'=>$minLevel,'maxLevel'=>$maxLevel,'luck'=>$luck);
					$dropcount+=count($monster_meta[$id]['drops']);
				}
			}
		}
	}
	$maps_list[$map]=array('borders'=>$borders,'doors'=>$doors,'name'=>$name,'shortdescription'=>$shortdescription,'description'=>$description,'type'=>$type,'grass'=>$grass,'water'=>$water,'cave'=>$cave,
	'width'=>$width,'height'=>$height,'pixelwidth'=>$pixelwidth,'pixelheight'=>$pixelheight,'dropcount'=>$dropcount,
	);
}
if(!is_dir('datapack-explorer/maps/'))
	mkdir('datapack-explorer/maps/');

/*foreach($temp_maps as $map)
{
	$map_folder=preg_replace('#/[^/]+$#','',$map).'/';
	$map_html=str_replace('.tmx','.html',$map);
	$map_image=str_replace('.tmx','.png',$map);
	if(file_exists($map_image))
		unlink($map_image);
	if(!is_dir('datapack-explorer/maps/'.$map_folder))
		mkdir('datapack-explorer/maps/'.$map_folder);
	echo '/home/user/Desktop/CatchChallenger/tools/build-map2pngGUI-Qt5-Debug/map2pngGUI ../datapack/map/'.$map.' datapack-explorer/maps/'.$map_image;
	exec('/home/user/Desktop/CatchChallenger/tools/build-map2pngGUI-Qt5-Debug/map2pngGUI ../datapack/map/'.$map.' datapack-explorer/maps/'.$map_image);
	$content=$template;
	$content=str_replace('${TITLE}',$maps_list[$map]['name'],$content);
	$map_descriptor='';

	$map_descriptor.='<div class="map map_type_'.$maps_list[$map]['type'].'">';
		$map_descriptor.='<div class="subblock"><h1>'.$maps_list[$map]['name'].'</h1>';
		if($maps_list[$map]['shortdescription']!='')
			$map_descriptor.='<h2>'.$maps_list[$map]['shortdescription'].'</h2>';
		$map_descriptor.='</div>';
		if(file_exists('datapack-explorer/maps/'.$map_image))
			$map_descriptor.='<div class="value mapscreenshot"><a href="/official-server/datapack-explorer/maps/'.$map_image.'"><img src="/official-server/datapack-explorer/maps/'.$map_image.'" alt="Screenshot of '.$maps_list[$map]['name'].'" title="Screenshot of '.$maps_list[$map]['name'].'" width="'.($maps_list[$map]['pixelwidth']/2).'" height="'.($maps_list[$map]['pixelheight']/2).'" /></a></div>';
		if($maps_list[$map]['description']!='')
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Map description</div><div class="value">'.$maps_list[$map]['description'].'</div></div>';
		if(count($maps_list[$map]['borders'])>0 || count($maps_list[$map]['doors'])>0)
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Linked locations</div><div class="value"><ul>';
			foreach($maps_list[$map]['borders'] as $bordertype=>$border)
			{
				if(isset($maps_list[$border]))
					$map_descriptor.='<li>Border '.$bordertype.': <a href="/official-server/datapack-explorer/maps/'.str_replace('.tmx','.html',$border).'">'.$maps_list[$border]['name'].'</a></li>';
				else
					$map_descriptor.='<li>Border '.$bordertype.': <span class="mapnotfound">'.$border.'</span></li>';
			}
			foreach($maps_list[$map]['doors'] as $door)
			{
				if(isset($maps_list[$door]))
					$map_descriptor.='<li>Door: <a href="/official-server/datapack-explorer/maps/'.str_replace('.tmx','.html',$door).'">'.$maps_list[$door]['name'].'</a></li>';
				else
					$map_descriptor.='<li>Door: <span class="mapnotfound">'.$door.'</span></li>';
			}
			$map_descriptor.='</ul></div></div>';
		}
	$map_descriptor.='</div>';
	
	if($maps_list[$map]['dropcount']>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$maps_list[$map]['type'].'">
		<tr class="item_list_title item_list_title_type_'.$maps_list[$map]['type'].'">
			<th colspan="2">Item</th>
			<th>Location</th>
		</tr>';
		$monster_list=array_merge($maps_list[$map]['grass'],$maps_list[$map]['water'],$maps_list[$map]['cave']);
		foreach($monster_list as $monster)
		{
			if(isset($monster_meta[$monster['id']]))
			{
				$drops=$monster_meta[$monster['id']]['drops'];
				foreach($drops as $drop)
				{
					if(isset($item_meta[$drop['item']]))
					{
						$link='/official-server/datapack-explorer/items/'.str_replace(' ','-',strtolower($item_meta[$drop['item']]['name'])).'.html';
						$name=$item_meta[$drop['item']]['name'];
						if($item_meta[$drop['item']]['image']!='')
							$image='/datapack/items/'.$item_meta[$drop['item']]['image'];
						else
							$image='';
					}
					else
					{
						$link='';
						$name='';
						$image='';
					}
					$quantity_text='';
					if($drop['quantity_min']!=$drop['quantity_max'])
						$quantity_text=$drop['quantity_min'].' to '.$drop['quantity_max'].' ';
					elseif($drop['quantity_min']>1)
						$quantity_text=$drop['quantity_min'].' ';
					$map_descriptor.='<tr class="value">
						<td>';
						if($image!='')
						{
							if($link!='')
								$map_descriptor.='<a href="'.$link.'">';
							$map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
							if($link!='')
								$map_descriptor.='</a>';
						}
						$map_descriptor.='</td>
						<td>';
						if($link!='')
							$map_descriptor.='<a href="'.$link.'">';
						if($name!='')
							$map_descriptor.=$quantity_text.$name;
						else
							$map_descriptor.=$quantity_text.'Unknown item';
						if($link!='')
							$map_descriptor.='</a>';
						$map_descriptor.='</td>';
						$map_descriptor.='<td>Drop on <a href="/official-server/datapack-explorer/monsters/'.str_replace(' ','-',strtolower($monster_meta[$monster['id']]['name'])).'.html" title="'.$monster_meta[$monster['id']]['name'].'">'.$monster_meta[$monster['id']]['name'].'</a> with luck of '.$drop['luck'].'%</td>
					</tr>';
				}
			}
		}
		$map_descriptor.='<tr>
			<td colspan="3" class="item_list_endline item_list_title_type_'.$maps_list[$map]['type'].'"></td>
		</tr>
		</table>';
	}

	if(count($maps_list[$map]['grass'])>0 || count($maps_list[$map]['water'])>0 || count($maps_list[$map]['cave'])>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$maps_list[$map]['type'].'">
		<tr class="item_list_title item_list_title_type_'.$maps_list[$map]['type'].'">
			<th colspan="2">Monster</th>
			<th>Location</th>
			<th>Levels</th>
			<th colspan="3">Rate</th>
		</tr>';
		if(count($maps_list[$map]['grass'])>0)
		{
			$map_descriptor.='<tr class="item_list_title_type_'.$maps_list[$map]['type'].'">
					<th colspan="7">Grass</th>
				</tr>';
			foreach($maps_list[$map]['grass'] as $monster)
			{
				if(isset($monster_meta[$monster['id']]))
				{
					$name=$monster_meta[$monster['id']]['name'];
					$link='/official-server/datapack-explorer/monsters/'.str_replace(' ','-',strtolower($name)).'.html';
					$map_descriptor.='<tr class="value">
						<td>';
						if(file_exists('../datapack/monsters/'.$monster['id'].'/small.png'))
							$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="/datapack/monsters/'.$monster['id'].'/small.png" width="32" height="32" alt="'.$monster_meta[$monster['id']]['name'].'" title="'.$monster_meta[$monster['id']]['name'].'" /></a></div>';
						else if(file_exists('../datapack/monsters/'.$monster['id'].'/small.gif'))
							$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="/datapack/monsters/'.$monster['id'].'/small.gif" width="32" height="32" alt="'.$monster_meta[$monster['id']]['name'].'" title="'.$monster_meta[$monster['id']]['name'].'" /></a></div>';
						$map_descriptor.='</td>
						<td><a href="'.$link.'">'.$name.'</a></td>
						<td><img src="/images/datapack-explorer/grass.png" alt="" class="locationimg">Grass</td>
						<td>';
						if($monster['minLevel']==$monster['maxLevel'])
							$map_descriptor.=$monster['minLevel'];
						else
							$map_descriptor.=$monster['minLevel'].'-'.$monster['maxLevel'];
						$map_descriptor.='</td>';
						$map_descriptor.='<td colspan="3">'.$monster['luck'].'%</td>
					</tr>';
				}
			}
		}
		if(count($maps_list[$map]['water'])>0)
		{
			$map_descriptor.='<tr class="item_list_title_type_'.$maps_list[$map]['type'].'">
					<th colspan="7">Water</th>
				</tr>';
			foreach($maps_list[$map]['water'] as $monster)
			{
				if(isset($monster_meta[$monster['id']]))
				{
					$name=$monster_meta[$monster['id']]['name'];
					$link='/official-server/datapack-explorer/monsters/'.str_replace(' ','-',strtolower($name)).'.html';
					$map_descriptor.='<tr class="value">
						<td>';
						if(file_exists('../datapack/monsters/'.$monster['id'].'/small.png'))
							$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="/datapack/monsters/'.$monster['id'].'/small.png" width="32" height="32" alt="'.$monster_meta[$monster['id']]['name'].'" title="'.$monster_meta[$monster['id']]['name'].'" /></a></div>';
						else if(file_exists('../datapack/monsters/'.$monster['id'].'/small.gif'))
							$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="/datapack/monsters/'.$monster['id'].'/small.gif" width="32" height="32" alt="'.$monster_meta[$monster['id']]['name'].'" title="'.$monster_meta[$monster['id']]['name'].'" /></a></div>';
						$map_descriptor.='</td>
						<td><a href="'.$link.'">'.$name.'</a></td>
						<td><img src="/images/datapack-explorer/water.png" alt="" class="locationimg">Water</td>
						<td>';
						if($monster['minLevel']==$monster['maxLevel'])
							$map_descriptor.=$monster['minLevel'];
						else
							$map_descriptor.=$monster['minLevel'].'-'.$monster['maxLevel'];
						$map_descriptor.='</td>';
						$map_descriptor.='<td colspan="3">'.$monster['luck'].'%</td>
					</tr>';
				}
			}
		}
		if(count($maps_list[$map]['cave'])>0)
		{
			$map_descriptor.='<tr class="item_list_title_type_'.$maps_list[$map]['type'].'">
					<th colspan="7">Cave</th>
				</tr>';
			foreach($maps_list[$map]['cave'] as $monster)
			{
				if(isset($monster_meta[$monster['id']]))
				{
					$name=$monster_meta[$monster['id']]['name'];
					$link='/official-server/datapack-explorer/monsters/'.str_replace(' ','-',strtolower($name)).'.html';
					$map_descriptor.='<tr class="value">
						<td>';
						if(file_exists('../datapack/monsters/'.$monster['id'].'/small.png'))
							$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="/datapack/monsters/'.$monster['id'].'/small.png" width="32" height="32" alt="'.$monster_meta[$monster['id']]['name'].'" title="'.$monster_meta[$monster['id']]['name'].'" /></a></div>';
						else if(file_exists('../datapack/monsters/'.$monster['id'].'/small.gif'))
							$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="/datapack/monsters/'.$monster['id'].'/small.gif" width="32" height="32" alt="'.$monster_meta[$monster['id']]['name'].'" title="'.$monster_meta[$monster['id']]['name'].'" /></a></div>';
						$map_descriptor.='</td>
						<td><a href="'.$link.'">'.$name.'</a></td>
						<td><img src="/images/datapack-explorer/cave.png" alt="" class="locationimg">Cave</td>
						<td>';
						if($monster['minLevel']==$monster['maxLevel'])
							$map_descriptor.=$monster['minLevel'];
						else
							$map_descriptor.=$monster['minLevel'].'-'.$monster['maxLevel'];
						$map_descriptor.='</td>';
						$map_descriptor.='<td colspan="3">'.$monster['luck'].'%</td>
					</tr>';
				}
			}
		}
		$map_descriptor.='<tr>
			<td colspan="7" class="item_list_endline item_list_title_type_'.$maps_list[$map]['type'].'"></td>
		</tr>
		</table>';
	}
	
	$content=str_replace('${CONTENT}',$map_descriptor,$content);
	filewrite('datapack-explorer/maps/'.$map_html,$content);
}*/

/*$content=$template;
$content=str_replace('${TITLE}','Map list',$content);
$map_descriptor='';
$map_descriptor.='<ul>';
foreach($temp_maps as $map)
{
	$map_folder=preg_replace('#/[^/]+$#','',$map).'/';
	$map_html=str_replace('.tmx','.html',$map);
	$map_descriptor.='<li><a href="/official-server/datapack-explorer/maps/'.$map_html.'" title="'.$maps_list[$map]['name'].'">'.$maps_list[$map]['name'].'</a></li>';
}
$map_descriptor.='</ul>';
$content=str_replace('${CONTENT}',$map_descriptor,$content);
filewrite('datapack-explorer/maps.html',$content);*/

/*foreach($monster_meta as $id=>$monster)
{
	if(!is_dir('datapack-explorer/monsters/'))
		mkdir('datapack-explorer/monsters/');
	$content=$template;
	$content=str_replace('${TITLE}',$monster['name'],$content);
	$map_descriptor='';

	$effectiveness_list=array();
	foreach($type_meta as $realtypeindex=>$typecontent)
	{
		$effectiveness=(float)1.0;
		foreach($monster['type'] as $type)
			if(isset($typecontent['multiplicator'][$type]))
				$effectiveness*=$typecontent['multiplicator'][$type];
		if($effectiveness!=1.0)
		{
			if(!isset($effectiveness_list[(string)$effectiveness]))
				$effectiveness_list[(string)$effectiveness]=array();
			$effectiveness_list[(string)$effectiveness][]=$realtypeindex;
		}
	}
	$map_descriptor.='<div class="map monster_type_'.$monster['type'][0].'">';
		$map_descriptor.='<div class="subblock"><h1>'.$monster['name'].'</h1>';
		$map_descriptor.='<h2>#'.$id.'</h2>';
		$map_descriptor.='</div>';
		$map_descriptor.='<div class="value mapscreenshot">';
		if(file_exists('../datapack/monsters/'.$id.'/front.png'))
			$map_descriptor.='<img src="/datapack/monsters/'.$id.'/front.png" width="80" height="80" alt="'.$monster['name'].'" title="'.$monster['name'].'" />';
		else if(file_exists('../datapack/monsters/'.$id.'/front.gif'))
			$map_descriptor.='<img src="/datapack/monsters/'.$id.'/front.gif" width="80" height="80" alt="'.$monster['name'].'" title="'.$monster['name'].'" />';
		$map_descriptor.='</div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Type</div><div class="value">';
		$type_list=array();
		foreach($monster['type'] as $type)
			if(isset($type_meta[$type]))
				$type_list[]='<span class="type_label type_label_'.$type.'"><a href="/official-server/datapack-explorer/monsters/type-'.$type.'.html">'.$type_meta[$type]['english_name'].'</a></span>';
		$map_descriptor.='<div class="type_label_list">'.implode(' ',$type_list).'</div></div></div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Gender ratio</div><div class="value">'.$monster['ratio_gender'].'% male, '.(100-$monster['ratio_gender']).'% female</div></div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Catch rate</div><div class="value">'.$monster['catch_rate'].'</div></div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Egg step</div><div class="value">'.$monster['egg_step'].'</div></div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Body</div><div class="value">Height: '.$monster['height'].'m, width: '.$monster['weight'].'kg</div></div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Stat</div><div class="value">Hp: <i>'.$monster['hp'].'</i>, Attack: <i>'.$monster['attack'].'</i>, Defense: <i>'.$monster['defense'].'</i>, Special attack: <i>'.$monster['special_attack'].'</i>, Special defense: <i>'.$monster['special_defense'].'</i>, Speed: <i>'.$monster['speed'].'</i></div></div>';
		if(isset($effectiveness_list['4']) || isset($effectiveness_list['2']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Weak to</div><div class="value">';
			$type_list=array();
			if(isset($effectiveness_list['2']))
				foreach($effectiveness_list['2'] as $type)
					$type_list[]='<span class="type_label type_label_'.$type.'">2x: <a href="/official-server/datapack-explorer/monsters/type-'.$type.'.html">'.$type_meta[$type]['english_name'].'</a></span>';
			if(isset($effectiveness_list['4']))
				foreach($effectiveness_list['4'] as $type)
					$type_list[]='<span class="type_label type_label_'.$type.'">4x: <a href="/official-server/datapack-explorer/monsters/type-'.$type.'.html">'.$type_meta[$type]['english_name'].'</a></span>';
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>';
		}
		if(isset($effectiveness_list['0.25']) || isset($effectiveness_list['0.5']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Resistant to</div><div class="value">';
			$type_list=array();
			if(isset($effectiveness_list['0.25']))
				foreach($effectiveness_list['0.25'] as $type)
					$type_list[]='<span class="type_label type_label_'.$type.'">0.25x: <a href="/official-server/datapack-explorer/monsters/type-'.$type.'.html">'.$type_meta[$type]['english_name'].'</a></span>';
			if(isset($effectiveness_list['0.5']))
				foreach($effectiveness_list['0.5'] as $type)
					$type_list[]='<span class="type_label type_label_'.$type.'">0.5x: <a href="/official-server/datapack-explorer/monsters/type-'.$type.'.html">'.$type_meta[$type]['english_name'].'</a></span>';
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>';
		}
		if(isset($effectiveness_list['0']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Immune to</div><div class="value">';
			$type_list=array();
			foreach($effectiveness_list['0'] as $type)
				$type_list[]='<span class="type_label type_label_'.$type.'"><a href="/official-server/datapack-explorer/monsters/type-'.$type.'.html">'.$type_meta[$type]['english_name'].'</a></span>';
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>';
		}
	$map_descriptor.='</div>';
	
	if(count($monster['drops'])>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$monster['type'][0].'">
		<tr class="item_list_title item_list_title_type_'.$monster['type'][0].'">
			<th colspan="2">Item</th>
			<th>Location</th>
		</tr>';
		$drops=$monster['drops'];
		foreach($drops as $drop)
		{
			if(isset($item_meta[$drop['item']]))
			{
				$link='/official-server/datapack-explorer/items/'.str_replace(' ','-',strtolower($item_meta[$drop['item']]['name'])).'.html';
				$name=$item_meta[$drop['item']]['name'];
				if($item_meta[$drop['item']]['image']!='')
					$image='/datapack/items/'.$item_meta[$drop['item']]['image'];
				else
					$image='';
			}
			else
			{
				$link='';
				$name='';
				$image='';
			}
			$quantity_text='';
			if($drop['quantity_min']!=$drop['quantity_max'])
				$quantity_text=$drop['quantity_min'].' to '.$drop['quantity_max'].' ';
			elseif($drop['quantity_min']>1)
				$quantity_text=$drop['quantity_min'].' ';
			$map_descriptor.='<tr class="value">
				<td>';
				if($image!='')
				{
					if($link!='')
						$map_descriptor.='<a href="'.$link.'">';
					$map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
					if($link!='')
						$map_descriptor.='</a>';
				}
				$map_descriptor.='</td>
				<td>';
				if($link!='')
					$map_descriptor.='<a href="'.$link.'">';
				if($name!='')
					$map_descriptor.=$quantity_text.$name;
				else
					$map_descriptor.=$quantity_text.'Unknown item';
				if($link!='')
					$map_descriptor.='</a>';
				$map_descriptor.='</td>';
				$map_descriptor.='<td>Drop luck of '.$drop['luck'].'%</td>
			</tr>';
		}
		$map_descriptor.='<tr>
			<td colspan="3" class="item_list_endline item_list_title_type_'.$monster['type'][0].'"></td>
		</tr>
		</table>';
	}

	if(count($monster['attack_list'])>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$monster['type'][0].'">
		<tr class="item_list_title item_list_title_type_'.$monster['type'][0].'">
			<th>Level</th>
			<th>Skill</th>
			<th>Type</th>
			<th>Endurance</th>
		</tr>';
		$attack_list=$monster['attack_list'];
		foreach($attack_list as $level=>$attack_at_level)
		{
			foreach($attack_at_level as $attack)
			{
				if(isset($skill_meta[$attack['id']]))
				{
					$map_descriptor.='<tr class="value">';
					$map_descriptor.='<td>';
					if($level==0)
						$map_descriptor.='Start';
					else
						$map_descriptor.=$level;
					$map_descriptor.='</td>';
					$map_descriptor.='<td><a href="/official-server/datapack-explorer/monsters/skills/'.str_replace(' ','-',strtolower($skill_meta[$attack['id']]['name'])).'.html">'.$skill_meta[$attack['id']]['name'];
					if($attack['attack_level']>1)
						$map_descriptor.=' at level '.$attack['attack_level'];
					$map_descriptor.='</a></td>';
					$map_descriptor.='<td><span class="type_label type_label_'.$skill_meta[$attack['id']]['type'].'"><a href="/official-server/datapack-explorer/monsters/type-'.$skill_meta[$attack['id']]['type'].'.html">'.$type_meta[$skill_meta[$attack['id']]['type']]['english_name'].'</a></span></td>';
					$map_descriptor.='<td>'.$skill_meta[$attack['id']]['level_list'][$attack['attack_level']]['endurance'].'</td>';
					$map_descriptor.='</tr>';
				}
			}
		}
		$map_descriptor.='<tr>
			<td colspan="4" class="item_list_endline item_list_title_type_'.$monster['type'][0].'"></td>
		</tr>
		</table>';
	}

	$content=str_replace('${CONTENT}',$map_descriptor,$content);
	filewrite('datapack-explorer/monsters/'.str_replace(' ','-',strtolower($monster['name'])).'.html',$content);
}

$content=$template;
$content=str_replace('${TITLE}','Monster list',$content);
$map_descriptor='';
$map_descriptor.='<ul>';
foreach($monster_meta as $monster)
	$map_descriptor.='<li><a href="/official-server/datapack-explorer/monsters/'.str_replace(' ','-',strtolower($monster['name'])).'.html" title="'.$monster['name'].'">'.$monster['name'].'</a></li>';
$map_descriptor.='</ul>';
$content=str_replace('${CONTENT}',$map_descriptor,$content);
filewrite('datapack-explorer/monsters.html',$content);
*/

/*foreach($item_meta as $id=>$item)
{
	if(!is_dir('datapack-explorer/items/'))
		mkdir('datapack-explorer/items/');
	$content=$template;
	$content=str_replace('${TITLE}',$item['name'],$content);
	$map_descriptor='';

	$map_descriptor.='<div class="map item_details">';
		$map_descriptor.='<div class="subblock"><h1>'.$item['name'].'</h1>';
		$map_descriptor.='<h2>#'.$id.'</h2>';
		$map_descriptor.='</div>';
		$map_descriptor.='<div class="value mapscreenshot">';
		$map_descriptor.='<img src="/datapack/items/'.$item['image'].'" width="24" height="24" alt="'.$item['name'].'" title="'.$item['name'].'" />';
		$map_descriptor.='</div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Price</div><div class="value">'.$item['price'].'</div></div>';
	$map_descriptor.='</div>';
	
	if(isset($item_to_monster[$id]))
	{
		$map_descriptor.='<table class="item_list item_list_type_normal">
		<tr class="item_list_title item_list_title_type_normal">
			<th colspan="2">Monster</th>
			<th>Quantity</th>
			<th>Luck</th>
		</tr>';
		foreach($item_to_monster[$id] as $item_to_monster_list)
		{
			if(isset($monster_meta[$item_to_monster_list['monster']]))
			{
				if($item_to_monster_list['quantity_min']!=$item_to_monster_list['quantity_max'])
					$quantity_text=$item_to_monster_list['quantity_min'].' to '.$item_to_monster_list['quantity_max'];
				else
					$quantity_text=$item_to_monster_list['quantity_min'];
				$name=$monster_meta[$item_to_monster_list['monster']]['name'];
				$link='/official-server/datapack-explorer/monsters/'.str_replace(' ','-',strtolower($name)).'.html';
				$map_descriptor.='<tr class="value">';
				$map_descriptor.='<td>';
				if(file_exists('../datapack/monsters/'.$item_to_monster_list['monster'].'/small.png'))
					$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="/datapack/monsters/'.$item_to_monster_list['monster'].'/small.png" width="32" height="32" alt="'.$monster_meta[$item_to_monster_list['monster']]['name'].'" title="'.$monster_meta[$item_to_monster_list['monster']]['name'].'" /></a></div>';
				else if(file_exists('../datapack/monsters/'.$item_to_monster_list['monster'].'/small.gif'))
					$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="/datapack/monsters/'.$item_to_monster_list['monster'].'/small.gif" width="32" height="32" alt="'.$monster_meta[$item_to_monster_list['monster']]['name'].'" title="'.$monster_meta[$item_to_monster_list['monster']]['name'].'" /></a></div>';
				$map_descriptor.='</td>
				<td><a href="'.$link.'">'.$name.'</a></td>';
				$map_descriptor.='<td>'.$quantity_text.'</td>';
				$map_descriptor.='<td>'.$item_to_monster_list['luck'].'%</td>';
				$map_descriptor.='</tr>';
			}
		}
		$map_descriptor.='<tr>
			<td colspan="4" class="item_list_endline item_list_title_type_normal"></td>
		</tr>
		</table>';
	}

	$content=str_replace('${CONTENT}',$map_descriptor,$content);
	filewrite('datapack-explorer/items/'.str_replace(' ','-',strtolower($item['name'])).'.html',$content);
}

$content=$template;
$content=str_replace('${TITLE}','Item list',$content);
$map_descriptor='';
$map_descriptor.='<ul>';
foreach($item_meta as $id=>$item)
	$map_descriptor.='<li><a href="/official-server/datapack-explorer/items/'.str_replace(' ','-',strtolower($item['name'])).'.html" title="'.$item['name'].'">'.$item['name'].'</a></li>';
$map_descriptor.='</ul>';
$content=str_replace('${CONTENT}',$map_descriptor,$content);
filewrite('datapack-explorer/items.html',$content);*/

foreach($crafting_meta as $id=>$crafting)
{
	if(!is_dir('datapack-explorer/crafting/'))
		mkdir('datapack-explorer/crafting/');
	$content=$template;
	$content=str_replace('${TITLE}',$item_meta[$crafting['itemToLearn']]['name'],$content);
	$map_descriptor='';

	$map_descriptor.='<div class="map item_details">';
		$map_descriptor.='<div class="subblock"><h1>'.$item_meta[$crafting['itemToLearn']]['name'].'</h1>';
		$map_descriptor.='<h2>#'.$id.'</h2>';
		$map_descriptor.='</div>';
		$map_descriptor.='<div class="value mapscreenshot">';
		$map_descriptor.='<a href="/official-server/datapack-explorer/items/'.str_replace(' ','-',strtolower($item_meta[$crafting['doItemId']]['name'])).'.html" title="'.$item_meta[$crafting['doItemId']]['name'].'">';
		$map_descriptor.='<img src="/datapack/items/'.$item_meta[$crafting['itemToLearn']]['image'].'" width="24" height="24" alt="'.$item_meta[$crafting['itemToLearn']]['name'].'" title="'.$item_meta[$crafting['itemToLearn']]['name'].'" />';
		$map_descriptor.='</a>';
		$map_descriptor.='</div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Price</div><div class="value">'.$item_meta[$crafting['itemToLearn']]['price'].'</div></div>';
		/*if($crafting['success']!='100')
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Success</div><div class="value">'.$crafting['success'].'%</div></div>';*/

		$map_descriptor.='<div class="subblock"><div class="valuetitle">Do the item</div><div class="value">';
		$map_descriptor.='<a href="/official-server/datapack-explorer/items/'.str_replace(' ','-',strtolower($item_meta[$crafting['doItemId']]['name'])).'.html" title="'.$item_meta[$crafting['doItemId']]['name'].'">';
		$map_descriptor.='<table><tr><td><img src="/datapack/items/'.$item_meta[$crafting['doItemId']]['image'].'" width="24" height="24" alt="'.$item_meta[$crafting['doItemId']]['name'].'" title="'.$item_meta[$crafting['doItemId']]['name'].'" /></td><td>'.$item_meta[$crafting['doItemId']]['name'].'</td></tr></table>';
		$map_descriptor.='</a>';
		$map_descriptor.='</div></div>';

		$map_descriptor.='<div class="subblock"><div class="valuetitle">Material</div><div class="value">';
		foreach($crafting['material'] as $material=>$quantity)
		{
			$map_descriptor.='<a href="/official-server/datapack-explorer/items/'.str_replace(' ','-',strtolower($item_meta[$material]['name'])).'.html" title="'.$item_meta[$material]['name'].'">';
			$map_descriptor.='<table><tr><td><img src="/datapack/items/'.$item_meta[$material]['image'].'" width="24" height="24" alt="'.$item_meta[$material]['name'].'" title="'.$item_meta[$material]['name'].'" /></td><td>';
			if($quantity>1)
				$map_descriptor.=$quantity.'x ';
			$map_descriptor.=$item_meta[$material]['name'].'</td></tr></table>';
			$map_descriptor.='</a>';
		}
		$map_descriptor.='</div></div>';
	$map_descriptor.='</div>';
	
	if(isset($item_to_monster[$crafting['itemToLearn']]))
	{
		$map_descriptor.='<table class="item_list item_list_type_normal">
		<tr class="item_list_title item_list_title_type_normal">
			<th colspan="2">Monster</th>
			<th>Quantity</th>
			<th>Luck</th>
		</tr>';
		foreach($item_to_monster[$crafting['itemToLearn']] as $item_to_monster_list)
		{
			if(isset($monster_meta[$item_to_monster_list['monster']]))
			{
				if($item_to_monster_list['quantity_min']!=$item_to_monster_list['quantity_max'])
					$quantity_text=$item_to_monster_list['quantity_min'].' to '.$item_to_monster_list['quantity_max'];
				else
					$quantity_text=$item_to_monster_list['quantity_min'];
				$name=$monster_meta[$item_to_monster_list['monster']]['name'];
				$link='/official-server/datapack-explorer/monsters/'.str_replace(' ','-',strtolower($name)).'.html';
				$map_descriptor.='<tr class="value">';
				$map_descriptor.='<td>';
				if(file_exists('../datapack/monsters/'.$item_to_monster_list['monster'].'/small.png'))
					$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="/datapack/monsters/'.$item_to_monster_list['monster'].'/small.png" width="32" height="32" alt="'.$monster_meta[$item_to_monster_list['monster']]['name'].'" title="'.$monster_meta[$item_to_monster_list['monster']]['name'].'" /></a></div>';
				else if(file_exists('../datapack/monsters/'.$item_to_monster_list['monster'].'/small.gif'))
					$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="/datapack/monsters/'.$item_to_monster_list['monster'].'/small.gif" width="32" height="32" alt="'.$monster_meta[$item_to_monster_list['monster']]['name'].'" title="'.$monster_meta[$item_to_monster_list['monster']]['name'].'" /></a></div>';
				$map_descriptor.='</td>
				<td><a href="'.$link.'">'.$name.'</a></td>';
				$map_descriptor.='<td>'.$quantity_text.'</td>';
				$map_descriptor.='<td>'.$item_to_monster_list['luck'].'%</td>';
				$map_descriptor.='</tr>';
			}
		}
		$map_descriptor.='<tr>
			<td colspan="4" class="item_list_endline item_list_title_type_normal"></td>
		</tr>
		</table>';
	}

	$content=str_replace('${CONTENT}',$map_descriptor,$content);
	filewrite('datapack-explorer/crafting/'.str_replace(' ','-',strtolower($item_meta[$crafting['itemToLearn']]['name'])).'.html',$content);
}

$content=$template;
$content=str_replace('${TITLE}','Crafting list',$content);
$map_descriptor='';
$map_descriptor.='<ul>';
foreach($crafting_meta as $id=>$crafting)
	$map_descriptor.='<li><a href="/official-server/datapack-explorer/crafting/'.str_replace(' ','-',strtolower($item_meta[$crafting['itemToLearn']]['name'])).'.html" title="'.$item_meta[$crafting['itemToLearn']]['name'].'">'.$item_meta[$crafting['itemToLearn']]['name'].'</a></li>';
$map_descriptor.='</ul>';
$content=str_replace('${CONTENT}',$map_descriptor,$content);
filewrite('datapack-explorer/crafting.html',$content);