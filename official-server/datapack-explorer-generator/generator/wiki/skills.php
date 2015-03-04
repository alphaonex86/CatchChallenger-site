<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator skills'."\n");

$only_one_level=true;
foreach($skill_meta as $skill_id=>$skill)
{
    foreach($skill['level_list'] as $level=>$effect)
    {
        if(count($skill['level_list'])>1)
            $only_one_level=false;
    }
}


foreach($skill_meta as $skill_id=>$skill)
{
	$map_descriptor='';

	$type=$skill['type'];
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
	$map_descriptor.='<div class="map monster_type_'.$type.'">'."\n";
		$map_descriptor.='<div class="subblock"><h1>'.$skill['name'][$current_lang].'</h1></div>'."\n";
		$map_descriptor.='<div class="type_label_list"><span class="type_label type_label_'.$type.'">[['.$translation_list[$current_lang]['Monsters type:'].$type_meta[$type]['name'][$current_lang].'|'.$type_meta[$type]['name'][$current_lang].']]</span></div>'."\n";
		if(isset($effectiveness_list['2']) || isset($effectiveness_list['4']))
		{
			
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Effective against</div><div class="value">'."\n";
			$type_list=array();
			if(isset($effectiveness_list['2']))
				foreach($effectiveness_list['2'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">2x: [['.$translation_list[$current_lang]['Monsters type:'].$type_meta[$type_effectiveness]['name'][$current_lang].'|'.$type_meta[$type_effectiveness]['name'][$current_lang].']]</span>'."\n";
			if(isset($effectiveness_list['4']))
				foreach($effectiveness_list['4'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">4x: [['.$translation_list[$current_lang]['Monsters type:'].$type_meta[$type_effectiveness]['name'][$current_lang].'|'.$type_meta[$type_effectiveness]['name'][$current_lang].']]</span>'."\n";
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>'."\n";
		}
		if(isset($effectiveness_list['0.25']) || isset($effectiveness_list['0.5']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Not effective against</div><div class="value">'."\n";
			$type_list=array();
			if(isset($effectiveness_list['0.25']))
				foreach($effectiveness_list['0.25'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">0.25x: [['.$translation_list[$current_lang]['Monsters type:'].$type_meta[$type_effectiveness]['name'][$current_lang].'|'.$type_meta[$type_effectiveness]['name'][$current_lang].']]</span>'."\n";
			if(isset($effectiveness_list['0.5']))
				foreach($effectiveness_list['0.5'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">0.5x: [['.$translation_list[$current_lang]['Monsters type:'].$type_meta[$type_effectiveness]['name'][$current_lang].'|'.$type_meta[$type_effectiveness]['name'][$current_lang].']]</span>'."\n";
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>'."\n";
		}
		if(isset($effectiveness_list['0']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Useless against</div><div class="value">'."\n";
			$type_list=array();
			foreach($effectiveness_list['0'] as $type_effectiveness)
				if(isset($type_meta[$type_effectiveness]))
					$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">[['.$translation_list[$current_lang]['Monsters type:'].$type_meta[$type_effectiveness]['name'][$current_lang].'|'.$type_meta[$type_effectiveness]['name'][$current_lang].']]</span>'."\n";
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>'."\n";
		}
		foreach($skill['level_list'] as $level=>$effect)
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Level '.$level.'</div><div class="value">'."\n";
			$map_descriptor.='Endurance: '.$effect['endurance'].'<br />'."\n";
			if($effect['sp']!='0')
				$map_descriptor.='Skill point/SP (to learn): '.$effect['sp'].'<br />'."\n";
			else
				$map_descriptor.='You can\'t learn this skill<br />'."\n";
			if($effect['life_quantity']!='0' || $effect['life_quantity']!='0%')
				$map_descriptor.='Life quantity: '.$effect['life_quantity'].'<br />'."\n";
			if(count($effect['buff'])>0)
			{
				$map_descriptor.='Add buff:';
				$map_descriptor.='<center><table class="item_list item_list_type_'.$type.'">
				<tr class="item_list_title item_list_title_type_'.$type.'">
					<th colspan="2">Buff</th>
					<th>Success</th>
				</tr>'."\n";
				foreach($effect['buff'] as $buff)
				{
					$buff_id=$buff['id'];
					$map_descriptor.='<tr class="value"><td>'."\n";
					if(file_exists($datapack_path.'/monsters/buff/'.$buff_id.'.png'))
						$map_descriptor.='<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/buff/'.$buff_id.'.png" alt="" width="16" height="16" />'."\n";
					else
						$map_descriptor.='&nbsp;';
					$map_descriptor.='</td>'."\n";
					$map_descriptor.='<td>[['.$translation_list[$current_lang]['Buffs:'].$buff_meta[$buff_id]['name'][$current_lang].'|'.$buff_meta[$buff_id]['name'][$current_lang].']]</td>'."\n";
					$map_descriptor.='<td>'.$buff['success'].'%</td>'."\n";
					$map_descriptor.='</tr>'."\n";
				}
				$map_descriptor.='<tr>
				<td colspan="3" class="item_list_endline item_list_title_type_'.$type.'"></td>
				</tr>
				</table></center>'."\n";
			}
			if($effect['base_level_luck']!='100')
				$map_descriptor.='Luck: '.$effect['base_level_luck'].'%<br />'."\n";
			$map_descriptor.='</div></div>'."\n";
		}
	$map_descriptor.='</div>'."\n";
    savewikipage('Template:skill_'.$skill_id.'_HEADER',$map_descriptor);$map_descriptor='';
	$skill_level_displayed=0;
	if(isset($skill_to_monster[$skill_id]) && count($skill_to_monster[$skill_id])>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$type.'">
		<tr class="item_list_title item_list_title_type_'.$type.'">
			<th colspan="2">Monster</th>
			<th>Type</th>'."\n";
			if(count($skill_to_monster[$skill_id])>1)
				$map_descriptor.='<th>Skill level</th>'."\n";
		$map_descriptor.='</tr>'."\n";
		foreach($skill_to_monster[$skill_id] as $skill_level=>$monster_list_content)
		{
			if($skill_level_displayed!=$skill_level && count($skill_to_monster[$skill_id])>1)
			{
				$map_descriptor.='<tr class="item_list_title_type_'.$type.'"><th colspan="4">Level '.$skill_level.'</th></tr>'."\n";
				$skill_level_displayed=$skill_level;
			}
			foreach($monster_list_content as $monster)
			{
				if(isset($monster_meta[$monster]))
				{
					$name=$monster_meta[$monster]['name'][$current_lang];
					$link=$base_datapack_site_http.$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($name).'.html';
					$map_descriptor.='<tr class="value">
						<td>'."\n";
						if(file_exists($datapack_path.'monsters/'.$monster.'/small.png'))
							$map_descriptor.='<div class="monstericon">[['.$translation_list[$current_lang]['Monsters:'].$name.'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$monster.'/small.png" width="32" height="32" alt="'.$name.'" title="'.$name.'" />]]</div>'."\n";
						else if(file_exists($datapack_path.'monsters/'.$monster.'/small.gif'))
							$map_descriptor.='<div class="monstericon">[['.$translation_list[$current_lang]['Monsters:'].$name.'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$monster.'/small.gif" width="32" height="32" alt="'.$name.'" title="'.$name.'" />]]</div>'."\n";
						$map_descriptor.='</td>
						<td>[['.$translation_list[$current_lang]['Monsters:'].$name.'|'.$name.']]</td>'."\n";
						$type_list=array();
						foreach($monster_meta[$monster]['type'] as $type_monster)
							if(isset($type_meta[$type_monster]))
								$type_list[]='<span class="type_label type_label_'.$type_monster.'">[['.$translation_list[$current_lang]['Monsters type:'].$type_meta[$type_monster]['name'][$current_lang].'|'.$type_meta[$type_monster]['name'][$current_lang].']]</span>'."\n";
						$map_descriptor.='<td><div class="type_label_list">'.implode(' ',$type_list).'</div></td>'."\n";
						if(count($skill_to_monster[$skill_id])>1)
							$map_descriptor.='<td>'.$skill_level.'</td>'."\n";
					$map_descriptor.='</tr>'."\n";
				}
			}
		}
		$map_descriptor.='<tr>
			<td colspan="';
			if(count($skill_to_monster[$skill_id])>1)
				$map_descriptor.='4';
			else
				$map_descriptor.='3';
			$map_descriptor.='" class="item_list_endline item_list_title_type_'.$type.'"></td>
		</tr>
		</table>'."\n";
        savewikipage('Template:skill_'.$skill_id.'_MONSTERS',$map_descriptor);$map_descriptor='';
	}

    if($wikivars['generatefullpage'])
    {
        $map_descriptor.='{{Template:skill_'.$skill_id.'_HEADER}}'."\n";
        if(isset($skill_to_monster[$skill_id]) && count($skill_to_monster[$skill_id])>0)
            $map_descriptor.='{{Template:skill_'.$skill_id.'_MONSTERS}}'."\n";
        savewikipage($translation_list[$current_lang]['Skills:'].$skill['name'][$current_lang],$map_descriptor);
    }
}

$map_descriptor='';
$map_descriptor.='<table class="item_list item_list_type_normal">
<tr class="item_list_title item_list_title_type_normal">
	<th>Skill</th>
	<th>Type</th>
	<th>Endurance</th>'."\n";
if(!$only_one_level)
	$map_descriptor.='<th>Number of level</th>'."\n";
$map_descriptor.='</tr>'."\n";
foreach($skill_meta as $skill_id=>$skill)
{
	if(count($skill['level_list'])>0)
	{
		$map_descriptor.='<tr class="value">'."\n";
		$map_descriptor.='<td>[['.$translation_list[$current_lang]['Skills:'].$skill['name'][$current_lang].'|'.$skill['name'][$current_lang].']]</td>'."\n";
		if(isset($type_meta[$skill['type']]))
			$map_descriptor.='<td><span class="type_label type_label_'.$skill['type'].'">[['.$translation_list[$current_lang]['Monsters type:'].$type_meta[$skill['type']]['name'][$current_lang].'|'.$type_meta[$skill['type']]['name'][$current_lang].']]</span></td>'."\n";
		else
			$map_descriptor.='<td>&nbsp;</td>'."\n";
		if(isset($skill['level_list'][1]))
			$map_descriptor.='<td>'.$skill['level_list'][1]['endurance'].'</td>'."\n";
		else
			$map_descriptor.='<td>&nbsp;</td>'."\n";
        if(!$only_one_level)
            $map_descriptor.='<td>'.count($skill['level_list']).'</td>'."\n";
		$map_descriptor.='</tr>'."\n";
	}
}
$map_descriptor.='<tr>
	<td colspan="';
if(!$only_one_level)
    $map_descriptor.='4';
else
    $map_descriptor.='3';
$map_descriptor.='" class="item_list_endline item_list_title_type_normal"></td>
</tr>
</table>'."\n";

savewikipage('Template:skills_list',$map_descriptor);$map_descriptor='';

if($wikivars['generatefullpage'])
{
    $map_descriptor.='{{Template:skills_list}}'."\n";
    savewikipage($translation_list[$current_lang]['Skills list'],$map_descriptor);
}
