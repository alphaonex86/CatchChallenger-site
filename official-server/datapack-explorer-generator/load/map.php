<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into load map');

$maps_list=array();
$maps_name_to_file=array();
$zone_to_map=array();
$monster_to_map=array();
$temp_maps=getTmxList($datapack_path.'map/');
foreach($temp_maps as $map)
{
	$width=0;
	$height=0;
	$pixelwidth=0;
	$pixelheight=0;
	if(preg_match('#/[^/]+$#',$map))
		$map_folder=preg_replace('#/[^/]+$#','',$map).'/';
	else
		$map_folder='';
	$map_xml_meta=str_replace('.tmx','.xml',$map);
	$borders=array();
	$tp=array();
	$doors=array();
	$content=file_get_contents($datapack_path.'map/'.$map);
	if(preg_match('#orientation="orthogonal" width="([0-9]+)" height="([0-9]+)" tilewidth="([0-9]+)" tileheight="([0-9]+)"#isU',$content))
	{
		$width=(int)preg_replace('#^.*orientation="orthogonal" width="([0-9]+)" height="([0-9]+)" tilewidth="([0-9]+)" tileheight="([0-9]+)".*$#isU','$1',$content);
		$height=(int)preg_replace('#^.*orientation="orthogonal" width="([0-9]+)" height="([0-9]+)" tilewidth="([0-9]+)" tileheight="([0-9]+)".*$#isU','$2',$content);
		$tilewidth=(int)preg_replace('#^.*orientation="orthogonal" width="([0-9]+)" height="([0-9]+)" tilewidth="([0-9]+)" tileheight="([0-9]+)".*$#isU','$3',$content);
		$tileheight=(int)preg_replace('#^.*orientation="orthogonal" width="([0-9]+)" height="([0-9]+)" tilewidth="([0-9]+)" tileheight="([0-9]+)".*$#isU','$4',$content);
		$pixelwidth=$width*$tilewidth;
		$pixelheight=$height*$tileheight;
	}
	preg_match_all('#<object[^>]+type="border-(left|right|top|bottom)".*</object>#isU',$content,$temp_text_list);
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
	preg_match_all('#<object[^>]+type="teleport( on [a-z]+)?".*</object>#isU',$content,$temp_text_list);
	foreach($temp_text_list[0] as $border_text)
	{
		if(preg_match('#<property name="map" value="([^"]+)"/>#isU',$border_text))
		{
			$border_map=preg_replace('#^.*<property name="map" value="([^"]+)"/>.*$#isU','$1',$border_text);
			$border_map=$map_folder.$border_map;
			if(!preg_match('#\\.tmx$#',$border_map))
				$border_map.='.tmx';
			$border_map=preg_replace('#/[^/]+/\\.\\./#isU','/',$border_map);
			$border_map=preg_replace('#^[^/]+/\\.\\./#isU','',$border_map);
			$border_map=preg_replace("#[\n\r\t]+#is",'',$border_map);
			$tp[]=$border_map;
		}
	}
	preg_match_all('#<object[^>]+type="door".*</object>#isU',$content,$temp_text_list);
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
	$zone='';
	$dropcount=0;
	if(file_exists($datapack_path.'map/'.$map_xml_meta))
	{
		$content_meta_map=file_get_contents($datapack_path.'map/'.$map_xml_meta);
		if(preg_match('#type="(outdoor|city|cave|indoor)"#isU',$content_meta_map))
			$type=preg_replace('#^.*type="(outdoor|city|cave|indoor)".*$#isU','$1',$content_meta_map);
		if(preg_match('#zone="([^"]+)"#isU',$content_meta_map))
			$zone=preg_replace('#^.*zone="([^"]+)".*$#isU','$1',$content_meta_map);
		if(preg_match('#<name lang="en">[^<]+</name>#isU',$content_meta_map))
			$name=preg_replace('#^.*<name lang="en">([^<]+)</name>.*$#isU','$1',$content_meta_map);
		elseif(preg_match('#<name>[^<]+</name>#isU',$content_meta_map))
			$name=preg_replace('#^.*<name>([^<]+)</name>.*$#isU','$1',$content_meta_map);
		if(preg_match('#<shortdescription lang="en">[^<]+</shortdescription>#isU',$content_meta_map))
			$shortdescription=preg_replace('#^.*<shortdescription lang="en">([^<]+)</shortdescription>.*$#isU','$1',$content_meta_map);
		elseif(preg_match('#<shortdescription>[^<]+</shortdescription>#isU',$content_meta_map))
			$shortdescription=preg_replace('#^.*<shortdescription>([^<]+)</shortdescription>.*$#isU','$1',$content_meta_map);
		if(preg_match('#<description lang="en">[^<]+</description>#isU',$content_meta_map))
			$description=text_operation_first_letter_upper(preg_replace('#^.*<description lang="en">([^<]+)</description>.*$#isU','$1',$content_meta_map));
		elseif(preg_match('#<description>[^<]+</description>#isU',$content_meta_map))
			$description=text_operation_first_letter_upper(preg_replace('#^.*<description>([^<]+)</description>.*$#isU','$1',$content_meta_map));
		$type=preg_replace("#[\n\r\t]+#is",'',$type);
		$name=preg_replace("#[\n\r\t]+#is",'',$name);
		$zone=preg_replace("#[\n\r\t]+#is",'',$zone);
		$shortdescription=preg_replace("#[\n\r\t]+#is",'',$shortdescription);
		$description=preg_replace("#[\n\r\t]+#is",'',$description);
		//grass
		if(preg_match('#<grass>(.*)</grass>#isU',$content_meta_map) && preg_match('#<layer name="Grass"#isU',$content))
		{
			$grass_text=preg_replace('#^.*<grass>(.*)</grass>.*$#isU','$1',$content_meta_map);
			preg_match_all('#<monster[^>]+/>#isU',$grass_text,$temp_text_list);
			foreach($temp_text_list[0] as $grass_text_entry)
			{
				if(preg_match('#level="([0-9]+)"#isU',$grass_text_entry))
				{
					$minLevel=preg_replace('#^.*level="([0-9]+)".*$#isU','$1',$grass_text_entry);
					$maxLevel=preg_replace('#^.*level="([0-9]+)".*$#isU','$1',$grass_text_entry);
				}
				elseif(preg_match('#minLevel="([0-9]+)"#isU',$grass_text_entry) && preg_match('maxLevel="([0-9]+)"#isU',$grass_text_entry))
				{
					$minLevel=preg_replace('#^.*minLevel="([0-9]+)".*$#isU','$1',$grass_text_entry);
					$maxLevel=preg_replace('#^.*maxLevel="([0-9]+)".*$#isU','$1',$grass_text_entry);
				}
				else
					continue;
				if(preg_match('#luck="([0-9]+)"#isU',$grass_text_entry))
					$luck=preg_replace('#^.*luck="([0-9]+)".*$#isU','$1',$grass_text_entry);
				else
					continue;
				if(preg_match('#id="([0-9]+)"#isU',$grass_text_entry))
					$id=preg_replace('#^.*id="([0-9]+)".*$#isU','$1',$grass_text_entry);
				else
					continue;
				if(isset($monster_meta[$id]))
				{
					$grass[]=array('id'=>$id,'minLevel'=>$minLevel,'maxLevel'=>$maxLevel,'luck'=>$luck);
					if(!isset($monster_to_map[$id]))
						$monster_to_map[$id]=array();
					if(!isset($monster_to_map[$id]['grass']))
						$monster_to_map[$id]['grass']=array();
					$monster_to_map[$id]['grass'][]=array('map'=>$map,'minLevel'=>$minLevel,'maxLevel'=>$maxLevel,'luck'=>$luck);
					$dropcount+=count($monster_meta[$id]['drops']);
				}
			}
		}
		//water
		if(preg_match('#<water>(.*)</water>#isU',$content_meta_map) && preg_match('#<layer name="Water"#isU',$content))
		{
			$grass_text=preg_replace('#^.*<water>(.*)</water>.*$#isU','$1',$content_meta_map);
			preg_match_all('#<monster[^>]+/>#isU',$grass_text,$temp_text_list);
			foreach($temp_text_list[0] as $grass_text_entry)
			{
				if(preg_match('#level="([0-9]+)"#isU',$grass_text_entry))
				{
					$minLevel=preg_replace('#^.*level="([0-9]+)".*$#isU','$1',$grass_text_entry);
					$maxLevel=preg_replace('#^.*level="([0-9]+)".*$#isU','$1',$grass_text_entry);
				}
				elseif(preg_match('#minLevel="([0-9]+)"#isU',$grass_text_entry) && preg_match('maxLevel="([0-9]+)"#isU',$grass_text_entry))
				{
					$minLevel=preg_replace('#^.*minLevel="([0-9]+)".*$#isU','$1',$grass_text_entry);
					$maxLevel=preg_replace('#^.*maxLevel="([0-9]+)".*$#isU','$1',$grass_text_entry);
				}
				else
					continue;
				if(preg_match('#luck="([0-9]+)"#isU',$grass_text_entry))
					$luck=preg_replace('#^.*luck="([0-9]+)".*$#isU','$1',$grass_text_entry);
				else
					continue;
				if(preg_match('#id="([0-9]+)"#isU',$grass_text_entry))
					$id=preg_replace('#^.*id="([0-9]+)".*$#isU','$1',$grass_text_entry);
				else
					continue;
				if(isset($monster_meta[$id]))
				{
					$water[]=array('id'=>$id,'minLevel'=>$minLevel,'maxLevel'=>$maxLevel,'luck'=>$luck);
					if(!isset($monster_to_map[$id]))
						$monster_to_map[$id]=array();
					if(!isset($monster_to_map[$id]['water']))
						$monster_to_map[$id]['water']=array();
					$monster_to_map[$id]['water'][]=array('map'=>$map,'minLevel'=>$minLevel,'maxLevel'=>$maxLevel,'luck'=>$luck);
					$dropcount+=count($monster_meta[$id]['drops']);
				}
			}
		}
		//cave
		if(preg_match('#<cave>(.*)</cave>#isU',$content_meta_map))
		{
			$grass_text=preg_replace('#^.*<cave>(.*)</cave>.*$#isU','$1',$content_meta_map);
			preg_match_all('#<monster[^>]+/>#isU',$grass_text,$temp_text_list);
			foreach($temp_text_list[0] as $grass_text_entry)
			{
				if(preg_match('#level="([0-9]+)"#isU',$grass_text_entry))
				{
					$minLevel=preg_replace('#^.*level="([0-9]+)".*$#isU','$1',$grass_text_entry);
					$maxLevel=preg_replace('#^.*level="([0-9]+)".*$#isU','$1',$grass_text_entry);
				}
				elseif(preg_match('#minLevel="([0-9]+)"#isU',$grass_text_entry) && preg_match('maxLevel="([0-9]+)"#isU',$grass_text_entry))
				{
					$minLevel=preg_replace('#^.*minLevel="([0-9]+)".*$#isU','$1',$grass_text_entry);
					$maxLevel=preg_replace('#^.*maxLevel="([0-9]+)".*$#isU','$1',$grass_text_entry);
				}
				else
					continue;
				if(preg_match('#luck="([0-9]+)"#isU',$grass_text_entry))
					$luck=preg_replace('#^.*luck="([0-9]+)".*$#isU','$1',$grass_text_entry);
				else
					continue;
				if(preg_match('#id="([0-9]+)"#isU',$grass_text_entry))
					$id=preg_replace('#^.*id="([0-9]+)".*$#isU','$1',$grass_text_entry);
				else
					continue;
				if(isset($monster_meta[$id]))
				{
					$cave[]=array('id'=>$id,'minLevel'=>$minLevel,'maxLevel'=>$maxLevel,'luck'=>$luck);
					if(!isset($monster_to_map[$id]))
						$monster_to_map[$id]=array();
					if(!isset($monster_to_map[$id]['cave']))
						$monster_to_map[$id]['cave']=array();
					$monster_to_map[$id]['cave'][]=array('map'=>$map,'minLevel'=>$minLevel,'maxLevel'=>$maxLevel,'luck'=>$luck);
					$dropcount+=count($monster_meta[$id]['drops']);
				}
			}
		}
	}
	$maps_list[$map]=array('borders'=>$borders,'tp'=>$tp,'doors'=>$doors,'name'=>$name,'shortdescription'=>$shortdescription,'description'=>$description,'type'=>$type,'grass'=>$grass,'water'=>$water,'cave'=>$cave,
	'width'=>$width,'height'=>$height,'pixelwidth'=>$pixelwidth,'pixelheight'=>$pixelheight,'dropcount'=>$dropcount,'zone'=>$zone
	);
	if(!isset($zone_to_map[$zone]))
		$zone_to_map[$zone]=array();
	$zone_to_map[$zone][$map]=$name;
}