<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator buffs'."\n");

foreach($buff_meta as $buff_id=>$buff)
{
	$map_descriptor='';

	$map_descriptor.='<div class="map monster_type_normal">'."\n";
		$map_descriptor.='<div class="subblock"><h1>'.$buff['name'].'</h1></div>'."\n";
		foreach($buff['level_list'] as $level=>$effect)
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'."\n";
			if(file_exists($datapack_path.'/monsters/buff/'.$buff_id.'.png'))
				$map_descriptor.='<center><img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/buff/'.$buff_id.'.png" alt="" width="16" height="16" /></center>'."\n";
			$map_descriptor.='Level '.$level.'</div><div class="value">'."\n";
			if($effect['capture_bonus']!=1)
				$map_descriptor.='Capture bonus: '.$effect['capture_bonus'].'<br />'."\n";
			if($effect['duration']=='ThisFight')
				$map_descriptor.='This buff is valid for this fight<br />'."\n";
			else if($effect['duration']=='Always')
				$map_descriptor.='This buff is always valid<br />'."\n";
			else if($effect['duration']=='NumberOfTurn')
				$map_descriptor.='This buff is valid during '.$effect['durationNumberOfTurn'].' turns<br />'."\n";

			if(count($effect['effect']['inFight'])>0)
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle">In fight</div><div class="value">'."\n";
				if(isset($effect['effect']['inFight']['hp']))
					$map_descriptor.='The hp change <b>'.$effect['effect']['inFight']['hp']['value'].'</b><br />'."\n";
				if(isset($effect['effect']['inFight']['defense']))
					$map_descriptor.='The defense change <b>'.$effect['effect']['inFight']['defense']['value'].'</b><br />'."\n";
				if(isset($effect['effect']['inFight']['attack']))
					$map_descriptor.='The attack change <b>'.$effect['effect']['inFight']['attack']['value'].'</b><br />'."\n";
				$map_descriptor.='</div></div>'."\n";
			}
			if(count($effect['effect']['inWalk'])>0)
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle">In walk</div><div class="value">'."\n";
				if(isset($effect['effect']['inWalk']['hp']))
					$map_descriptor.='The hp change <b>'.$effect['effect']['inWalk']['hp']['value'].'</b> during <b>'.$effect['effect']['inWalk']['hp']['steps'].' steps</b><br />'."\n";
				if(isset($effect['effect']['inWalk']['defense']))
					$map_descriptor.='The defense change <b>'.$effect['effect']['inWalk']['defense']['value'].'</b> during <b>'.$effect['effect']['inWalk']['hp']['steps'].' steps</b><br />'."\n";
				if(isset($effect['effect']['inWalk']['attack']))
					$map_descriptor.='The attack change <b>'.$effect['effect']['inWalk']['attack']['value'].'</b> during <b>'.$effect['effect']['inWalk']['hp']['steps'].' steps</b><br />'."\n";
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
			<th colspan="2">Monster</th>
			<th>Type</th>'."\n";
			if(count($buff_to_monster[$buff_id])>1)
				$map_descriptor.='<th>Skill level</th>'."\n";
		$map_descriptor.='</tr>'."\n";
		foreach($buff_to_monster[$buff_id] as $buff_level=>$monster_list_content)
		{
			if($buff_level_displayed!=$buff_level && count($buff_to_monster[$buff_id])>1)
			{
				$map_descriptor.='<tr class="item_list_title_type_normal"><th colspan="4">Level '.$buff_level.'</th></tr>'."\n";
				$buff_level_displayed=$buff_level;
			}
			foreach($monster_list_content as $monster)
			{
				if(isset($monster_meta[$monster]))
				{
					$name=$monster_meta[$monster]['name'];
					$link=$base_datapack_site_http.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($name).'.html';
					$map_descriptor.='<tr class="value">
						<td>'."\n";
						if(file_exists($datapack_path.'monsters/'.$monster.'/small.png'))
							$map_descriptor.='<div class="monstericon">[[Monsters:'.$name.'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$monster.'/small.png" width="32" height="32" alt="'.$name.'" title="'.$name.'" />]]</div>'."\n";
						else if(file_exists($datapack_path.'monsters/'.$monster.'/small.gif'))
							$map_descriptor.='<div class="monstericon">[[Monsters:'.$name.'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$monster.'/small.gif" width="32" height="32" alt="'.$name.'" title="'.$name.'" />]]</div>'."\n";
						$map_descriptor.='</td>
						<td>[[Monsters:'.$name.'|'.$name.']]</td>'."\n";
						$type_list=array();
						foreach($monster_meta[$monster]['type'] as $type_monster)
							if(isset($type_meta[$type_monster]))
								$type_list[]='<span class="type_label type_label_'.$type_monster.'">[[Monsters type:'.$type_meta[$type_monster]['english_name'].'|'.$type_meta[$type_monster]['english_name'].']]</span>'."\n";
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

    savewikipage('Template:buffs_'.$buff_id,$map_descriptor);$map_descriptor='';

    if($wikivarsapp['generatefullpage'])
    {
        $map_descriptor.='{{Template:buffs_'.$buff_id.'}}'."\n";
        savewikipage('Buffs:'.$buff['name'],$map_descriptor);
    }
}

$map_descriptor='';
$map_descriptor.='<table class="item_list item_list_type_normal">
<tr class="item_list_title item_list_title_type_normal">
	<th colspan="2">Buff</th>
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
	$map_descriptor.='<td>[[Buffs:'.$buff['name'].'|'.$buff['name'].']]</td>'."\n";
	$map_descriptor.='</tr>'."\n";
}
$map_descriptor.='<tr>
	<td colspan="2" class="item_list_endline item_list_title_type_normal"></td>
</tr>
</table>'."\n";

savewikipage('Template:buffs_list',$map_descriptor);$map_descriptor='';

if($wikivarsapp['generatefullpage'])
{
    $map_descriptor.='{{Template:buffs_list}}'."\n";
    savewikipage('Buffs_list',$map_descriptor);
}
