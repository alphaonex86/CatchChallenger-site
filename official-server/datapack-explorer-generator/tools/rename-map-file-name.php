<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into tools map fix broken links'."\n");
if($duplicate_map_file_name)
	die('this tools can\'t work if 2 map have the map file name'."\n");

//change the content
foreach($temp_maps as $map)
{
	$map_folder='';
	if(preg_match('#/#isU',$map))
		$map_folder=preg_replace('#/[^/]+$#','',$map).'/';
	$content=file_get_contents($datapack_path.'map/'.$map);
	echo $map.': '."\n";
	foreach($map_short_path_to_name as $name=>$url_name)
	{
		$content=preg_replace('#(name="(map|file)" value="[^"]*)'.$name.'-bots"#isU','$1'.$url_name.'-bots"',$content);
		$content=preg_replace('#(name="(map|file)" value="[^"]*)'.$name.'"#isU','$1'.$url_name.'"',$content);
	}
	filewrite($datapack_path.'map/'.$map,$content);
}
foreach($temp_maps as $map)
{
	$map_folder='';
	if(preg_match('#/#isU',$map))
		$map_folder=preg_replace('#/[^/]+$#','',$map).'/';
	$map=str_replace($map_folder,'',$map);
	$simplified_name=str_replace('.tmx','',$map);
	$new_name=$simplified_name;
	foreach($map_short_path_to_name as $name=>$url_name)
		$new_name=str_replace($name,$url_name,$new_name);
	if($simplified_name!=$new_name)
	{
		if(file_exists($datapack_path.'map/'.$map_folder.$simplified_name.'.tmx'))
			rename($datapack_path.'map/'.$map_folder.$simplified_name.'.tmx',$datapack_path.'map/'.$map_folder.$new_name.'.tmx');
		if(file_exists($datapack_path.'map/'.$map_folder.$simplified_name.'.xml'))
			rename($datapack_path.'map/'.$map_folder.$simplified_name.'.xml',$datapack_path.'map/'.$map_folder.$new_name.'.xml');
		if(file_exists($datapack_path.'map/'.$map_folder.$simplified_name.'-bots.xml'))
			rename($datapack_path.'map/'.$map_folder.$simplified_name.'-bots.xml',$datapack_path.'map/'.$map_folder.$new_name.'-bots.xml');
		echo 'rename '.$datapack_path.'map/'.$map_folder.$simplified_name.' into '.$datapack_path.'map/'.$map_folder.$new_name."\n";
	}
}