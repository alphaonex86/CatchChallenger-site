<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator start'."\n");

$map_descriptor='';
$index=1;
$loadSkinPreview=array();
foreach($start_meta as $entry)
{
	$map_descriptor.='
	<h2><strong>'.htmlspecialchars($entry['name'][$current_lang]).'</strong></h2>
	<b>'.htmlspecialchars($entry['description'][$current_lang]).'</b><br />'."\n";
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
		$map_descriptor.=$translation_list[$current_lang]['Map'].': <i>'.htmlspecialchars($map_name).'</i><br />'."\n";
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
		$map_descriptor.=$translation_list[$current_lang]['Skin'].': <div id="skin_preview_'.$index.'">'."\n";
		if ($handle = opendir($datapack_path.'skin/fighter/')) {
			while (false !== ($inode = readdir($handle)))
			{
				if(file_exists($datapack_path.'skin/fighter/'.$inode.'/front.png') || file_exists($datapack_path.'skin/fighter/'.$inode.'/front.gif'))
					if(count($entry['forcedskin'])==0 || in_array($inode,$entry['forcedskin']))
					{
						if(file_exists($datapack_path.'skin/fighter/'.$inode.'/front.png'))
							$map_descriptor.='<img src="'.$base_datapack_site_http.$base_datapack_site_path.'skin/fighter/'.$inode.'/front.png" width="80" height="80" alt="Front" style="float:left" />'."\n";
						else
							$map_descriptor.='<img src="'.$base_datapack_site_http.$base_datapack_site_path.'skin/fighter/'.$inode.'/front.gif" width="80" height="80" alt="Front" style="float:left" />'."\n";
					}
			}
			closedir($handle);
		}
		$map_descriptor.='</div><br style="clear:both" />'."\n";
	}
	else
		$map_descriptor.='Skin: No skin found<br />'."\n";
	if($entry['cash']>0)
		$map_descriptor.=$translation_list[$current_lang]['Cash'].': <i>'.htmlspecialchars($entry['cash']).'$</i><br />'."\n";
	$map_descriptor.=$translation_list[$current_lang]['Monster'].': <ul style="margin:0px;">'."\n";
	foreach($entry['monsters'] as $monster)
    {
		if(array_key_exists($monster['id'],$monster_meta))
		{
			$map_descriptor.='<li>'."\n";
			$map_descriptor.='[['.$translation_list[$current_lang]['Monsters:'].$monster_meta[$monster['id']]['name'][$current_lang].'|';
			if(file_exists($datapack_path.'monsters/'.$monster['id'].'/front.png'))
				$map_descriptor.='<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$monster['id'].'/front.png" width="80" height="80" alt="'.htmlspecialchars($monster_meta[$monster['id']]['name'][$current_lang]).'" title="'.htmlspecialchars($monster_meta[$monster['id']]['description'][$current_lang]).'" /><br />'."\n";
			elseif(file_exists($datapack_path.'monsters/'.$monster['id'].'/front.gif'))
				$map_descriptor.='<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$monster['id'].'/front.gif" width="80" height="80" alt="'.htmlspecialchars($monster_meta[$monster['id']]['name'][$current_lang]).'" title="'.htmlspecialchars($monster_meta[$monster['id']]['description'][$current_lang]).'" /><br />'."\n";
			else
				$map_descriptor.='No skin found!';
			$map_descriptor.='<b>'.htmlspecialchars($monster_meta[$monster['id']]['name'][$current_lang]).'</b> level <i>'.htmlspecialchars($monster['level']).'</i>'."\n";
			$map_descriptor.=']]'."\n";
			$map_descriptor.='</li>'."\n";
		}
		else
			$map_descriptor.='<li>No monster information!</li>'."\n";
    }
	$map_descriptor.='</ul>'."\n";
    savewikipage('Template:Starter_'.$index.'_HEADER',$map_descriptor);$map_descriptor='';

	if(count($entry['reputations'])>0)
	{
		$map_descriptor.=$translation_list[$current_lang]['Reputations'].': <ul style="margin:0px;">'."\n";
		foreach($entry['reputations'] as $reputation)
		{
			if(array_key_exists($reputation['type'],$reputation_meta))
			{
				if(array_key_exists($reputation['level'],$reputation_meta[$reputation['type']]))
					$map_descriptor.='<li>'.htmlspecialchars($reputation_meta[$reputation['type']][$reputation['level']]).'</li>'."\n";
				else
					$map_descriptor.='<li>Unknown reputation '.htmlspecialchars($reputation['type']).' level: '.htmlspecialchars($reputation['level']).'</li>'."\n";
			}
			else
				$map_descriptor.='<li>Unknown reputation type: '.htmlspecialchars($reputation['type']).'</li>'."\n";
		}
		$map_descriptor.='</ul>'."\n";
        savewikipage('Template:Starter_'.$index.'_REP',$map_descriptor);$map_descriptor='';
	}
	if(count($entry['items'])>0)
	{
		$map_descriptor.=$translation_list[$current_lang]['Items'].': <ul style="margin:0px;">'."\n";
		foreach($entry['items'] as $item)
		{
			if($item['quantity']<=1)
				$quantity='';
			else
				$quantity=htmlspecialchars($item['quantity']).' ';
			if(array_key_exists($item['id'],$item_meta))
			{
				$map_descriptor.='<li>'."\n";
				$map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$item_meta[$item['id']]['name'][$current_lang].'|';
				if($item_meta[$item['id']]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$item['id']]['image']))
					$map_descriptor.='<img src="'.$base_datapack_site_http.$base_datapack_site_path.'items/'.htmlspecialchars($item_meta[$item['id']]['image']).'" width="24" height="24" alt="'.htmlspecialchars($item_meta[$item['id']]['description'][$current_lang]).'" title="'.htmlspecialchars($item_meta[$item['id']]['description'][$current_lang]).'" />'.$quantity.htmlspecialchars($item_meta[$item['id']]['name'][$current_lang]);
				else
					$map_descriptor.=$quantity.htmlspecialchars($item_meta[$item['id']]['name'][$current_lang]);
				$map_descriptor.=']]'."\n";
				$map_descriptor.='</li>'."\n";
			}
			else
				$map_descriptor.='<li>'.$quantity.'unknown item ('.htmlspecialchars($item['id']).')</li>'."\n";
		}
		$map_descriptor.='</ul>'."\n";
        savewikipage('Template:Starter_'.$index.'_ITEMS',$map_descriptor);$map_descriptor='';
	}

	$index++;
}

$index=1;
foreach($start_meta as $entry)
{
    if($wikivars['generatefullpage'])
    {
        $map_descriptor.='{{Template:Starter_'.$index.'_HEADER}}'."\n";
        if(count($entry['reputations'])>0)
            $map_descriptor.='{{Template:Starter_'.$index.'_REP}}'."\n";
        if(count($entry['items'])>0)
            $map_descriptor.='{{Template:Starter_'.$index.'_ITEMS}}'."\n";
    }
    $index++;
}
savewikipage($translation_list[$current_lang]['Starters'],$map_descriptor);
