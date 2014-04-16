<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into tools map fix broken links'."\n");
if($duplicate_map_file_name)
	die('this tools can\'t work if 2 map have the map file name'."\n");

foreach($temp_maps as $map)
{
	$map_html=str_replace('.tmx','.html',$map);
	$map_image=str_replace('.tmx','.png',$map);
	$map_folder='';
	if(preg_match('#/#isU',$map))
		$map_folder=preg_replace('#/[^/]+$#','',$map).'/';
	$content=file_get_contents($datapack_path.'map/'.$map);
	preg_match_all('#<property name="map" value="([^"]+)" ?/>#isU',$content,$temp_text_list);
	foreach($temp_text_list[1] as $offset=>$link)
	{
		$remaked_link=$link;
		if(!preg_match('#\.tmx$#',$remaked_link))
			$remaked_link.='.tmx';
		if(!file_exists($datapack_path.'map/'.$map_folder.$remaked_link))
		{
			if(isset($map_short_path_to_path[$remaked_link]))
			{
				$source_folder=explode('/',$map_folder);
				$destination_folder=explode('/',$map_short_path_to_path[$remaked_link]);
				while(isset($source_folder[0]) && isset($destination_folder[0]) && $source_folder[0]==$destination_folder[0])
				{
					unset($source_folder[0]);
					unset($destination_folder[0]);
					$source_folder_temp=implode('/',$source_folder);
					$destination_folder_temp=implode('/',$destination_folder);
					$source_folder=explode('/',$source_folder_temp);
					$destination_folder=explode('/',$destination_folder_temp);
				}
				$source_folder=implode('/',$source_folder);
				$destination_folder=implode('/',$destination_folder);
				/*if(preg_match('#tin-tower#isU',$destination_folder))
					{print_r($map);echo "\n";print_r($source_folder);echo "\n";print_r($destination_folder);echo "\n";exit;}*/
				$parent_folder='';
				$count=substr_count($map_folder,'/');
				$index=0;
				while($index<(substr_count($source_folder,'/')))
				{
					if(!isset($source_folder[$index]) || !isset($destination_folder[$index]) || $source_folder[$index]!=$destination_folder[$index])
						$parent_folder.='../';
					$index++;
				}
				echo $map.': '.$link.' broken but found at '.$parent_folder.$destination_folder."\n";
				$content=str_replace($temp_text_list[0][$offset],'<property name="map" value="'.$parent_folder.$destination_folder.'"/>',$content);
				filewrite($datapack_path.'map/'.$map,$content);
			}
			else
				echo $map.': '.$link.' broken'."\n";
		}
	}
}
