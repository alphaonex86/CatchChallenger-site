<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator types'."\n");

foreach($type_meta as $type=>$type_content)
{
	if(!is_dir($datapack_explorer_local_path.$translation_list[$current_lang]['monsters/']))
		mkdir($datapack_explorer_local_path.$translation_list[$current_lang]['monsters/']);
	$map_descriptor='';

	$effectiveness_list=array();
	foreach($type_meta as $realtypeindex=>$typecontent)
	{
		$effectiveness=(float)1.0;
		if(isset($typecontent['multiplicator'][$type]))
			$effectiveness*=$typecontent['multiplicator'][$type];
		if($effectiveness!=1.0)
		{
			if(!isset($effectiveness_list[(string)$effectiveness]))
				$effectiveness_list[(string)$effectiveness]=array();
			$effectiveness_list[(string)$effectiveness][]=$realtypeindex;
		}
	}
	$map_descriptor.='<div class="map monster_type_'.$type.'">';
		$map_descriptor.='<div class="subblock"><h1>'.$type_content['name'][$current_lang].'</h1></div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Type</div><div class="value">';
		$map_descriptor.='<div class="type_label_list"><span class="type_label type_label_'.$type.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type.'.html">'.$type_meta[$type]['name'][$current_lang].'</a></span></div></div></div>';
		if(isset($effectiveness_list['4']) || isset($effectiveness_list['2']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Weak to</div><div class="value">';
			$type_list=array();
			if(isset($effectiveness_list['2']))
				foreach($effectiveness_list['2'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">2x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.$type_meta[$type_effectiveness]['name'][$current_lang].'</a></span>';
			if(isset($effectiveness_list['4']))
				foreach($effectiveness_list['4'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">4x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.$type_meta[$type_effectiveness]['name'][$current_lang].'</a></span>';
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>';
		}
		if(isset($effectiveness_list['0.25']) || isset($effectiveness_list['0.5']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Resistant to</div><div class="value">';
			$type_list=array();
			if(isset($effectiveness_list['0.25']))
				foreach($effectiveness_list['0.25'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">0.25x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.$type_meta[$type_effectiveness]['name'][$current_lang].'</a></span>';
			if(isset($effectiveness_list['0.5']))
				foreach($effectiveness_list['0.5'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">0.5x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.$type_meta[$type_effectiveness]['name'][$current_lang].'</a></span>';
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>';
		}
		if(isset($effectiveness_list['0']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Immune to</div><div class="value">';
			$type_list=array();
			foreach($effectiveness_list['0'] as $type_effectiveness)
				if(isset($type_meta[$type_effectiveness]))
					$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.$type_meta[$type_effectiveness]['name'][$current_lang].'</a></span>';
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>';
		}
		$effectiveness_list=array();
		if(isset($type_meta[$type]))
		{
			foreach($type_meta[$type]['multiplicator'] as $temp_type=>$multiplicator)
			{
				if(!isset($effectiveness_list[(string)$multiplicator]))
					$effectiveness_list[(string)$multiplicator]=array();
				$effectiveness_list[(string)$multiplicator][]=$temp_type;
			}
		}
		if(isset($effectiveness_list['2']) || isset($effectiveness_list['4']))
		{
			
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Effective against</div><div class="value">';
			$type_list=array();
			if(isset($effectiveness_list['2']))
				foreach($effectiveness_list['2'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">2x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.$type_meta[$type_effectiveness]['name'][$current_lang].'</a></span>';
			if(isset($effectiveness_list['4']))
				foreach($effectiveness_list['4'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">4x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.$type_meta[$type_effectiveness]['name'][$current_lang].'</a></span>';
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>';
		}
		if(isset($effectiveness_list['0.25']) || isset($effectiveness_list['0.5']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Not effective against</div><div class="value">';
			$type_list=array();
			if(isset($effectiveness_list['0.25']))
				foreach($effectiveness_list['0.25'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">0.25x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.$type_meta[$type_effectiveness]['name'][$current_lang].'</a></span>';
			if(isset($effectiveness_list['0.5']))
				foreach($effectiveness_list['0.5'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">0.5x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.$type_meta[$type_effectiveness]['name'][$current_lang].'</a></span>';
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>';
		}
		if(isset($effectiveness_list['0']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Useless against</div><div class="value">';
			$type_list=array();
			foreach($effectiveness_list['0'] as $type_effectiveness)
				if(isset($type_meta[$type_effectiveness]))
					$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.$type_meta[$type_effectiveness]['name'][$current_lang].'</a></span>';
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>';
		}
	$map_descriptor.='</div>';
	$second_type_displayed='';
	if(isset($type_to_monster[$type]) && count($type_to_monster[$type])>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$type.'">
		<tr class="item_list_title item_list_title_type_'.$type.'">
			<th colspan="2">Monster</th>
			<th>Type</th>
		</tr>';
		foreach($type_to_monster[$type] as $second_type=>$second_type_content)
		{
			if($second_type_displayed!=$second_type)
			{
				if($second_type==$type)
					$map_descriptor.='<tr class="item_list_title_type_'.$second_type.'"><th colspan="3">'.$type_meta[$second_type]['name'][$current_lang].'</th></tr>';
				else
					$map_descriptor.='<tr class="item_list_title_type_'.$second_type.'"><th colspan="3">'.$type_meta[$type]['name'][$current_lang].' - '.$type_meta[$second_type]['name'][$current_lang].'</th></tr>';
				$second_type_displayed=$second_type;
			}
			foreach($second_type_content as $monster)
			{
				if(isset($monster_meta[$monster]))
				{
					$name=$monster_meta[$monster]['name'][$current_lang];
					$link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($name).'.html';
					$map_descriptor.='<tr class="value">
						<td>';
						if(file_exists($datapack_path.'monsters/'.$monster.'/small.png'))
							$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$monster.'/small.png" width="32" height="32" alt="'.$name.'" title="'.$name.'" /></a></div>';
						else if(file_exists($datapack_path.'monsters/'.$monster.'/small.gif'))
							$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$monster.'/small.gif" width="32" height="32" alt="'.$name.'" title="'.$name.'" /></a></div>';
						$map_descriptor.='</td>
						<td><a href="'.$link.'">'.$name.'</a></td>';
						$type_list=array();
						foreach($monster_meta[$monster]['type'] as $type_monster)
							if(isset($type_meta[$type_monster]))
								$type_list[]='<span class="type_label type_label_'.$type_monster.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_monster.'.html">'.$type_meta[$type_monster]['name'][$current_lang].'</a></span>';
						$map_descriptor.='<td><div class="type_label_list">'.implode(' ',$type_list).'</div></td>';
					$map_descriptor.='</tr>';
				}
			}
		}
		$map_descriptor.='<tr>
			<td colspan="3" class="item_list_endline item_list_title_type_'.$type.'"></td>
		</tr>
		</table>';
	}

	$content=$template;
	$content=str_replace('${TITLE}',$type_content['name'][$current_lang],$content);
	$content=str_replace('${CONTENT}',$map_descriptor,$content);
	$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
	$content=clean_html($content);
	filewrite($datapack_explorer_local_path.'monsters/type-'.$type.'.html',$content);
}

$map_descriptor='';
$map_descriptor.='<table class="item_list item_list_type_normal">
<tr class="item_list_title item_list_title_type_normal">
	<th>Type</th>
	<th>Monster with this type</th>
</tr>';
foreach($type_meta as $type=>$type_content)
{
	$map_descriptor.='<tr class="value">';
	$map_descriptor.='<td><div class="type_label_list"><span class="type_label type_label_'.$type.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type.'.html">'.$type_content['name'][$current_lang].'</a></span></div></td>';
	$count=0;
	if(isset($type_to_monster[$type]))
		foreach($type_to_monster[$type] as $second_type=>$second_type_content)
			$count+=count($second_type_content);
	//foreach($type_to_monster as $first_type=>$first_type_content)
		//foreach($first_type_content as $second_type=>$second_type_content)
	$map_descriptor.='<td>'.$count.'</td>';
	$map_descriptor.='</tr>';
}
$map_descriptor.='<tr>
	<td colspan="2" class="item_list_endline item_list_title_type_normal"></td>
</tr>
</table>';
$map_descriptor.='<table class="item_list item_list_type_normal">
<tr class="item_list_title item_list_title_type_normal">
	<th class="item_list_title_corner">Effective against</th>';
foreach($type_meta as $type=>$type_content)
	$map_descriptor.='<th><div class="type_label_list"><span class="type_label type_label_'.$type.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type.'.html">'.$type_content['name'][$current_lang].'</a></span></div></th>';
foreach($type_meta as $type=>$type_content)
{
	$map_descriptor.='<tr class="value"><td class="item_list_title_left item_list_title_type_normal"><div class="type_label_list"><span class="type_label type_label_'.$type.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type.'.html">'.$type_content['name'][$current_lang].'</a></span></div></td>';
	foreach($type_meta as $type2=>$type_content2)
	{
		$effectiveness=(float)1.0;
		if(isset($type_content['multiplicator'][$type2]))
			$effectiveness*=$type_content['multiplicator'][$type2];
		if($effectiveness>1.0)
			$map_descriptor.='<td class="very_effective">'.$effectiveness.'</td>';
		elseif($effectiveness==1.0)
			$map_descriptor.='<td class="normal_effective">'.$effectiveness.'</td>';
		elseif($effectiveness==0.0)
			$map_descriptor.='<td class="no_effective">'.$effectiveness.'</td>';
		elseif($effectiveness<1.0)
			$map_descriptor.='<td class="not_very_effective">'.$effectiveness.'</td>';
	}
	$map_descriptor.='</tr>';
}
$map_descriptor.='</table>';
$content=$template;
$content=str_replace('${TITLE}',$translation_list[$current_lang]['Monsters types'],$content);
$content=str_replace('${CONTENT}',$map_descriptor,$content);
$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
$content=clean_html($content);
filewrite($datapack_explorer_local_path.$translation_list[$current_lang]['types.html'],$content);
