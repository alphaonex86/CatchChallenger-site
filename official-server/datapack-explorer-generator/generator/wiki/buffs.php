<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator buffs'."\n");

foreach($buff_meta as $buff_id=>$buff)
{
	$map_descriptor='';

	$map_descriptor.='<div class="map monster_type_normal">'."\n";
		$map_descriptor.='<div class="subblock"><h1>'.$buff['name'][$current_lang].'</h1></div>'."\n";
		foreach($buff['level_list'] as $level=>$effect)
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'."\n";
			if(file_exists($datapack_path.'/monsters/buff/'.$buff_id.'.png'))
				$map_descriptor.='<center><img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/buff/'.$buff_id.'.png" alt="" width="16" height="16" /></center>'."\n";
            if(count($buff['level_list'])>1)
                $map_descriptor.=str_replace('[level]',$level,$translation_list[$current_lang]['Level [level]']);
            $map_descriptor.='</div><div class="value">'."\n";
			if($effect['capture_bonus']!=1)
				$map_descriptor.=$translation_list[$current_lang]['Capture bonus: '].$effect['capture_bonus'].'<br />'."\n";
			if($effect['duration']=='ThisFight')
				$map_descriptor.=$translation_list[$current_lang]['This buff is only valid for this fight'].'<br />'."\n";
			else if($effect['duration']=='Always')
				$map_descriptor.=$translation_list[$current_lang]['This buff is always valid'].'<br />'."\n";
			else if($effect['duration']=='NumberOfTurn')
				$map_descriptor.=str_replace('[turns]',$effect['durationNumberOfTurn'],$translation_list[$current_lang]['This buff is valid during [turns] turns']).'<br />'."\n";

			if(count($effect['effect']['inFight'])>0)
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['In fight'].'</div><div class="value">'."\n";
				if(isset($effect['effect']['inFight']['hp']))
					$map_descriptor.=str_replace('[hp]',$effect['effect']['inFight']['hp']['value'],$translation_list[$current_lang]['The hp change of [hp]']).'<br />'."\n";
				if(isset($effect['effect']['inFight']['defense']))
					$map_descriptor.=str_replace('[defense]',$effect['effect']['inFight']['defense']['value'],$translation_list[$current_lang]['The defense change of [defense]']).'<br />'."\n";
				if(isset($effect['effect']['inFight']['attack']))
					$map_descriptor.=str_replace('[attack]',$effect['effect']['inFight']['attack']['value'],$translation_list[$current_lang]['The attack change of [attack]']).'<br />'."\n";
				$map_descriptor.='</div></div>'."\n";
			}
			if(count($effect['effect']['inWalk'])>0)
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['In walk'].'</div><div class="value">'."\n";
				if(isset($effect['effect']['inWalk']['hp']))
					$map_descriptor.=str_replace('[turns]',$effect['effect']['inWalk']['hp']['steps'],str_replace('[hp]',$effect['effect']['inWalk']['hp']['value'],$translation_list[$current_lang]['The hp change of <b>[hp]</b> during <b>[turns] steps</b>'])).'<br />'."\n";
				if(isset($effect['effect']['inWalk']['defense']))
					$map_descriptor.=str_replace('[turns]',$effect['effect']['inWalk']['hp']['steps'],str_replace('[defense]',$effect['effect']['inWalk']['defense']['value'],$translation_list[$current_lang]['The defense change of <b>[defense]</b> during <b>[turns] steps</b>'])).'<br />'."\n";
				if(isset($effect['effect']['inWalk']['attack']))
					$map_descriptor.=str_replace('[turns]',$effect['effect']['inWalk']['hp']['steps'],str_replace('[attack]',$effect['effect']['inWalk']['attack']['value'],$translation_list[$current_lang]['The attack change of <b>[attack]</b> during <b>[turns] steps</b>'])).'<br />'."\n";
				$map_descriptor.='</div></div>'."\n";
			}

			$map_descriptor.='</div></div>'."\n";
		}
	$map_descriptor.='</div>'."\n";
	$buff_level_displayed=0;
	if(isset($buff_to_monster[$buff_id]) && count($buff_to_monster[$buff_id])>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_normal">
		<tr class="item_list_title item_list_title_type_normal">
			<th colspan="2">'.$translation_list[$current_lang]['Monster'].'</th>
			<th>'.$translation_list[$current_lang]['Type'].'</th>'."\n";
			if(count($buff_to_monster[$buff_id])>1)
				$map_descriptor.='<th>'.$translation_list[$current_lang]['Skill level'].'</th>'."\n";
		$map_descriptor.='</tr>'."\n";
		foreach($buff_to_monster[$buff_id] as $buff_level=>$monster_list_content)
		{
			if($buff_level_displayed!=$buff_level && count($buff_to_monster[$buff_id])>1)
			{
				$map_descriptor.='<tr class="item_list_title_type_normal"><th colspan="4">';
                $map_descriptor.=str_replace('[level]',$buff_level,$translation_list[$current_lang]['Level [level]']);
                $map_descriptor.='</th></tr>'."\n";
				$buff_level_displayed=$buff_level;
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
						if(count($buff_to_monster[$buff_id])>1)
							$map_descriptor.='<td>'.$buff_level.'</td>'."\n";
					$map_descriptor.='</tr>'."\n";
				}
			}
		}
		$map_descriptor.='<tr>
			<td colspan="';
			if(count($buff_to_monster[$buff_id])>1)
				$map_descriptor.='4';
			else
				$map_descriptor.='3';
			$map_descriptor.='" class="item_list_endline item_list_title_type_normal"></td>
		</tr>
		</table>'."\n";
	}

    savewikipage('Template:buffs_'.$buff_id,$map_descriptor,false);$map_descriptor='';

    $map_descriptor.='{{Template:buffs_'.$buff_id.'}}'."\n";
    savewikipage($translation_list[$current_lang]['Buffs:'].$buff['name'][$current_lang],$map_descriptor,!$wikivars['generatefullpage']);
}

$map_descriptor='';
$map_descriptor.='<table class="item_list item_list_type_normal">
<tr class="item_list_title item_list_title_type_normal">
	<th colspan="2">'.$translation_list[$current_lang]['Buff'].'</th>
</tr>'."\n";
foreach($buff_meta as $buff_id=>$buff)
{
	$map_descriptor.='<tr class="value">'."\n";
	$map_descriptor.='<td>'."\n";
	if(file_exists($datapack_path.'/monsters/buff/'.$buff_id.'.png'))
		$map_descriptor.='<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/buff/'.$buff_id.'.png" alt="" width="16" height="16" />'."\n";
	else
		$map_descriptor.='&nbsp;';
	$map_descriptor.='</td>'."\n";
	$map_descriptor.='<td>[['.$translation_list[$current_lang]['Buffs:'].$buff['name'][$current_lang].'|'.$buff['name'][$current_lang].']]</td>'."\n";
	$map_descriptor.='</tr>'."\n";
}
$map_descriptor.='<tr>
	<td colspan="2" class="item_list_endline item_list_title_type_normal"></td>
</tr>
</table>'."\n";

savewikipage('Template:buffs_list',$map_descriptor,false);$map_descriptor='';

$map_descriptor.='{{Template:buffs_list}}'."\n";
savewikipage($translation_list[$current_lang]['Buffs list'],$map_descriptor,!$wikivars['generatefullpage']);
