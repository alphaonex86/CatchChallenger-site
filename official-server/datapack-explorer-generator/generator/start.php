<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator start'."\n");

$map_descriptor='';
$index=1;
$loadSkinPreview=array();
foreach($start_meta as $entry)
{
	$map_descriptor.='
	<fieldset>
	<legend><h2><strong>'.htmlspecialchars($entry['name']).'</strong></h2></legend>
	<b>'.htmlspecialchars($entry['description']).'</b><br />';
	$map_name='';
	$zone_code='';
	$map_meta='datapack/map/'.str_replace('.tmx','.xml',$entry['map']);
	if(file_exists($map_meta))
	{
		$content=file_get_contents($map_meta);
		if(preg_match('#<name( lang="en")?>.*</name>#isU',$content))
			$map_name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$content);
		else if(preg_match('#<map[^>]*zone="[^"]+"#isU',$content))
		{
			$zone_code=preg_replace('#<map[^>]*zone="([^"]+)"#isU','$1',$content);
			$zone_meta='datapack/map/zone/'.$zone_code.'.xml';
			if(file_exists($zone_meta))
			{
				$content=file_get_contents($zone_meta);
				if(preg_match('#<name( lang="en")?>.*</name>#isU',$content))
					$map_name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$content);
			}
		}
	}
	if($map_name!='')
		$map_descriptor.='Map: <i>'.htmlspecialchars($map_name).'</i><br />';
	$skin_count=0;
	if ($handle = opendir($datapack_path.'skin/fighter/')) {
		while (false !== ($inode = readdir($handle)))
		{
			if(file_exists($datapack_path.'skin/fighter/'.$inode.'/front.png') || file_exists($datapack_path.'skin/fighter/'.$inode.'/front.gif'))
				if(count($entry['forcedskin'])==0 || in_array($inode,$entry['forcedskin']))
					$skin_count++;
		}
		closedir($handle);
	}
	if($skin_count>0)
	{
		$map_descriptor.='Skin: <div id="skin_preview_'.$index.'">';
		if ($handle = opendir($datapack_path.'skin/fighter/')) {
			while (false !== ($inode = readdir($handle)))
			{
				if(file_exists($datapack_path.'skin/fighter/'.$inode.'/front.png') || file_exists($datapack_path.'skin/fighter/'.$inode.'/front.gif'))
					if(count($entry['forcedskin'])==0 || in_array($inode,$entry['forcedskin']))
					{
						if(file_exists($datapack_path.'skin/fighter/'.$inode.'/front.png'))
							$map_descriptor.='<img src="'.$base_datapack_site_path.'skin/fighter/'.$inode.'/front.png" width="80" height="80" alt="Front" style="float:left" />';
						else
							$map_descriptor.='<img src="'.$base_datapack_site_path.'skin/fighter/'.$inode.'/front.gif" width="80" height="80" alt="Front" style="float:left" />';
					}
			}
			closedir($handle);
		}
		$map_descriptor.='</div><br style="clear:both" />';
	}
	else
		$map_descriptor.='Skin: No skin found<br />';
	if($entry['cash']>0)
		$map_descriptor.='Cash: <i>'.htmlspecialchars($entry['cash']).'$</i><br />';
	$map_descriptor.='Monster: <ul style="margin:0px;">';
	foreach($entry['monsters'] as $monster)
		if(array_key_exists($monster['id'],$monster_meta))
		{
			$map_descriptor.='<li>';
			$map_descriptor.='<a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_meta[$monster['id']]['name']).'.html">';
			if(file_exists($datapack_path.'monsters/'.$monster['id'].'/front.png'))
				$map_descriptor.='<img src="'.$base_datapack_site_path.'monsters/'.$monster['id'].'/front.png" width="80" height="80" alt="'.htmlspecialchars($monster_meta[$monster['id']]['name']).'" title="'.htmlspecialchars($monster_meta[$monster['id']]['description']).'" /><br />';
			elseif(file_exists($datapack_path.'monsters/'.$monster['id'].'/front.gif'))
				$map_descriptor.='<img src="'.$base_datapack_site_path.'monsters/'.$monster['id'].'/front.gif" width="80" height="80" alt="'.htmlspecialchars($monster_meta[$monster['id']]['name']).'" title="'.htmlspecialchars($monster_meta[$monster['id']]['description']).'" /><br />';
			else
				$map_descriptor.='No skin found!';
			$map_descriptor.='<b>'.htmlspecialchars($monster_meta[$monster['id']]['name']).'</b> level <i>'.htmlspecialchars($monster['level']).'</i>';
			$map_descriptor.='</a>';
			$map_descriptor.='</li>';
		}
		else
			$map_descriptor.='<li>No monster information!</li>';
	$map_descriptor.='</ul>';
	if(count($entry['reputations'])>0)
	{
		$map_descriptor.='Reputations: <ul style="margin:0px;">';
		foreach($entry['reputations'] as $reputation)
		{
			if(array_key_exists($reputation['type'],$reputation_meta))
			{
				if(array_key_exists($reputation['level'],$reputation_meta[$reputation['type']]))
					$map_descriptor.='<li>'.htmlspecialchars($reputation_meta[$reputation['type']][$reputation['level']]).'</li>';
				else
					$map_descriptor.='<li>Unknown reputation '.htmlspecialchars($reputation['type']).' level: '.htmlspecialchars($reputation['level']).'</li>';
			}
			else
				$map_descriptor.='<li>Unknown reputation type: '.htmlspecialchars($reputation['type']).'</li>';
		}
		$map_descriptor.='</ul>';
	}
	if(count($entry['items'])>0)
	{
		$map_descriptor.='Items: <ul style="margin:0px;">';
		foreach($entry['items'] as $item)
		{
			if($item['quantity']<=1)
				$quantity='';
			else
				$quantity=htmlspecialchars($item['quantity']).' ';
			if(array_key_exists($item['id'],$item_meta))
			{
				$map_descriptor.='<li>';
				$map_descriptor.='<a href="'.$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$item['id']]['name']).'.html" title="'.$item_meta[$item['id']]['name'].'">';
				if($item_meta[$item['id']]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$item['id']]['image']))
					$map_descriptor.='<li><img src="'.$base_datapack_site_path.'items/'.htmlspecialchars($item_meta[$item['id']]['image']).'" width="24" height="24" alt="'.htmlspecialchars($item_meta[$item['id']]['description']).'" title="'.htmlspecialchars($item_meta[$item['id']]['description']).'" />'.$quantity.htmlspecialchars($item_meta[$item['id']]['name']).'</li>';
				else
					$map_descriptor.='<li>'.$quantity.htmlspecialchars($item_meta[$item['id']]['name']).'</li>';
				$map_descriptor.='</a>';
				$map_descriptor.='</li>';
			}
			else
				$map_descriptor.='<li>'.$quantity.'unknown item ('.htmlspecialchars($item['id']).')</li>';
		}
		$map_descriptor.='</ul>';
	}
	$map_descriptor.='</fieldset>';
	$index++;
}
$content=$template;
$content=str_replace('${TITLE}','Starter characters',$content);
$content=str_replace('${CONTENT}',$map_descriptor,$content);
$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
$content=preg_replace("#[\r\n\t]+#isU",'',$content);
filewrite($datapack_explorer_local_path.'start.html',$content);