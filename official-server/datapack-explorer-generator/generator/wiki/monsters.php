<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator monsters'."\n");

foreach($monster_meta as $id=>$monster)
{
	$resolved_type=$monster['type'][0];
	if(!isset($type_meta[$resolved_type]))
	{
		if(!isset($type_meta['normal']) || count($type_meta)<=0)
			$resolved_type='normal'."\n";
		else
		{
			foreach($type_meta as $type=>$type_content)
			{
				$resolved_type=$type;
				break;
			}
		}
	}
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
	$map_descriptor.='<div class="map monster_type_'.$resolved_type.'">'."\n";
		$map_descriptor.='<div class="subblock"><h1>'.$monster['name'][$current_lang].'</h1>'."\n";
		$map_descriptor.='<h2>#'.$id.'</h2>'."\n";
		$map_descriptor.='</div>'."\n";
		$map_descriptor.='<div class="value datapackscreenshot"><center>'."\n";
		if(file_exists($datapack_path.'monsters/'.$id.'/front.png'))
			$map_descriptor.='<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$id.'/front.png" width="80" height="80" alt="'.$monster['name'][$current_lang].'" title="'.$monster['name'][$current_lang].'" />'."\n";
		else if(file_exists($datapack_path.'monsters/'.$id.'/front.gif'))
			$map_descriptor.='<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$id.'/front.gif" width="80" height="80" alt="'.$monster['name'][$current_lang].'" title="'.$monster['name'][$current_lang].'" />'."\n";
		$map_descriptor.='</center></div>'."\n";
		$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Type'].'</div><div class="value">'."\n";
		$type_list=array();
		foreach($monster['type'] as $type)
			if(isset($type_meta[$type]))
				$type_list[]='<span class="type_label type_label_'.$type.'">[['.$translation_list[$current_lang]['Monsters type:'].$type_meta[$type]['name'][$current_lang].'|'.$type_meta[$type]['name'][$current_lang].']]</span>'."\n";
		$map_descriptor.='<div class="type_label_list">'.implode(' ',$type_list).'</div></div></div>'."\n";
		$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Gender ratio'].'</div><div class="value">'."\n";
		if($monster['ratio_gender']<0 || $monster['ratio_gender']>100)
		{
			$map_descriptor.='<center><table class="genderbar"><tr><td class="genderbarunknown" style="width:100%"></td></tr></table></center>'."\n";
			$map_descriptor.=$translation_list[$current_lang]['Unknown gender']."\n";
		}
		else
		{
			$map_descriptor.='<center><table class="genderbar"><tr><td class="genderbarmale" style="width:'.$monster['ratio_gender'].'%"></td><td class="genderbarfemale" style="width:'.(100-$monster['ratio_gender']).'%"></td></tr></table></center>'."\n";
			$map_descriptor.=$monster['ratio_gender'].'% '.$translation_list[$current_lang]['male'].', '.(100-$monster['ratio_gender']).'% '.$translation_list[$current_lang]['female']."\n";
		}
		$map_descriptor.='</div></div>'."\n";
		if($monster['description'][$current_lang]!='')
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Description'].'</div><div class="value">'.$monster['description'][$current_lang].'</div></div>'."\n";
		if($monster['kind'][$current_lang]!='')
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Kind'].'</div><div class="value">'.$monster['kind'][$current_lang].'</div></div>'."\n";
		if($monster['habitat'][$current_lang]!='')
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Habitat'].'</div><div class="value">'.$monster['habitat'][$current_lang].'</div></div>'."\n";
		$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Catch rate'].'</div><div class="value">'.$monster['catch_rate'].'</div></div>'."\n";
		$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Steps for hatching'].'</div><div class="value">'.$monster['egg_step'].'</div></div>'."\n";
		$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Body'].'</div><div class="value">'.$translation_list[$current_lang]['Height'].': '.$monster['height'].'m, '.$translation_list[$current_lang]['width'].': '.$monster['weight'].'kg</div></div>'."\n";
		$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Stat'].'</div><div class="value">'.$translation_list[$current_lang]['Hp'].': <i>'.$monster['hp'].'</i>, '.$translation_list[$current_lang]['Attack'].': <i>'.$monster['attack'].'</i>, '.$translation_list[$current_lang]['Defense'].': <i>'.$monster['defense'].'</i>, '.$translation_list[$current_lang]['Special attack'].': <i>'.$monster['special_attack'].'</i>, '.$translation_list[$current_lang]['Special defense'].': <i>'.$monster['special_defense'].'</i>, '.$translation_list[$current_lang]['Speed'].': <i>'.$monster['speed'].'</i></div></div>'."\n";
		if(isset($effectiveness_list['4']) || isset($effectiveness_list['2']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Weak to'].'</div><div class="value">'."\n";
			$type_list=array();
			if(isset($effectiveness_list['2']))
				foreach($effectiveness_list['2'] as $type)
					if(isset($type_meta[$type]))
						$type_list[]='<span class="type_label type_label_'.$type.'">2x: [['.$translation_list[$current_lang]['Monsters type:'].$type_meta[$type]['name'][$current_lang].'|'.$type_meta[$type]['name'][$current_lang].']]</span>'."\n";
			if(isset($effectiveness_list['4']))
				foreach($effectiveness_list['4'] as $type)
					if(isset($type_meta[$type]))
						$type_list[]='<span class="type_label type_label_'.$type.'">4x: [['.$translation_list[$current_lang]['Monsters type:'].$type_meta[$type]['name'][$current_lang].'|'.$type_meta[$type]['name'][$current_lang].']]</span>'."\n";
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>'."\n";
		}
		if(isset($effectiveness_list['0.25']) || isset($effectiveness_list['0.5']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Resistant to'].'</div><div class="value">'."\n";
			$type_list=array();
			if(isset($effectiveness_list['0.25']))
				foreach($effectiveness_list['0.25'] as $type)
					if(isset($type_meta[$type]))
						$type_list[]='<span class="type_label type_label_'.$type.'">0.25x: [['.$translation_list[$current_lang]['Monsters type:'].$type_meta[$type]['name'][$current_lang].'|'.$type_meta[$type]['name'][$current_lang].']]</span>'."\n";
			if(isset($effectiveness_list['0.5']))
				foreach($effectiveness_list['0.5'] as $type)
					if(isset($type_meta[$type]))
						$type_list[]='<span class="type_label type_label_'.$type.'">0.5x: [['.$translation_list[$current_lang]['Monsters type:'].$type_meta[$type]['name'][$current_lang].'|'.$type_meta[$type]['name'][$current_lang].']]</span>'."\n";
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>'."\n";
		}
		if(isset($effectiveness_list['0']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Immune to'].'</div><div class="value">'."\n";
			$type_list=array();
			foreach($effectiveness_list['0'] as $type)
				if(isset($type_meta[$type]))
					$type_list[]='<span class="type_label type_label_'.$type.'">[['.$translation_list[$current_lang]['Monsters type:'].$type_meta[$type]['name'][$current_lang].'|'.$type_meta[$type]['name'][$current_lang].']]</span>'."\n";
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>'."\n";
		}
	$map_descriptor.='</div>'."\n";

    savewikipage('Template:monster_'.$id.'_HEADER',$map_descriptor);$map_descriptor='';
	
	if(count($monster['drops'])>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$resolved_type.'">
		<tr class="item_list_title item_list_title_type_'.$resolved_type.'">
			<th colspan="2">'.$translation_list[$current_lang]['Item'].'</th>
			<th>'.$translation_list[$current_lang]['Location'].'</th>
		</tr>'."\n";
		$drops=$monster['drops'];
		foreach($drops as $drop)
		{
			if(isset($item_meta[$drop['item']]))
			{
				$link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$drop['item']]['name'][$current_lang]).'.html'."\n";
				$name=$item_meta[$drop['item']]['name'][$current_lang];
				if($item_meta[$drop['item']]['image']!='')
					$image=$base_datapack_site_http.$base_datapack_site_path.'/items/'.$item_meta[$drop['item']]['image'];
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
				$quantity_text=$drop['quantity_min'].' to '.$drop['quantity_max'].' '."\n";
			elseif($drop['quantity_min']>1)
				$quantity_text=$drop['quantity_min'].' '."\n";
			$map_descriptor.='<tr class="value">
				<td>'."\n";
				if($image!='')
				{
					if($link!='')
						$map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$name.'|';
					$map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
					if($link!='')
						$map_descriptor.=']]'."\n";
				}
				$map_descriptor.='</td>
				<td>'."\n";
				if($link!='')
					$map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$name.'|';
				if($name!='')
					$map_descriptor.=$quantity_text.$name;
				else
					$map_descriptor.=$quantity_text.$translation_list[$current_lang]['Unknown item'];
				if($link!='')
					$map_descriptor.=']]'."\n";
				$map_descriptor.='</td>'."\n";
				$map_descriptor.='<td>Drop luck of '.$drop['luck'].'%</td>
			</tr>'."\n";
		}
		$map_descriptor.='<tr>
			<td colspan="3" class="item_list_endline item_list_title_type_'.$resolved_type.'"></td>
		</tr>
		</table>'."\n";
        savewikipage('Template:monster_'.$id.'_DROP',$map_descriptor);$map_descriptor='';
	}

	if(count($monster['attack_list'])>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$resolved_type.'">
		<tr class="item_list_title item_list_title_type_'.$resolved_type.'">
			<th>'.$translation_list[$current_lang]['Level'].'</th>
			<th>'.$translation_list[$current_lang]['Skill'].'</th>
			<th>'.$translation_list[$current_lang]['Type'].'</th>
			<th>'.$translation_list[$current_lang]['Endurance'].'</th>
		</tr>'."\n";
		$attack_list=$monster['attack_list'];
		foreach($attack_list as $level=>$attack_at_level)
		{
			foreach($attack_at_level as $attack)
			{
				if(isset($skill_meta[$attack['id']]))
				{
					$map_descriptor.='<tr class="value">'."\n";
					$map_descriptor.='<td>'."\n";
					if($level==0)
						$map_descriptor.='Start'."\n";
					else
						$map_descriptor.=$level;
					$map_descriptor.='</td>'."\n";
					$map_descriptor.='<td>[['.$translation_list[$current_lang]['Skills:'].$skill_meta[$attack['id']]['name'][$current_lang].'|'.$skill_meta[$attack['id']]['name'][$current_lang];
					if($attack['attack_level']>1)
						$map_descriptor.=' at level '.$attack['attack_level'];
					$map_descriptor.=']]</td>'."\n";
					if(isset($type_meta[$skill_meta[$attack['id']]['type']]))
						$map_descriptor.='<td><span class="type_label type_label_'.$skill_meta[$attack['id']]['type'].'">[['.$translation_list[$current_lang]['Monsters type:'].$type_meta[$skill_meta[$attack['id']]['type']]['name'][$current_lang].'|'.$type_meta[$skill_meta[$attack['id']]['type']]['name'][$current_lang].']]</span></td>'."\n";
					else
						$map_descriptor.='<td>&nbsp;</td>'."\n";
					if(isset($skill_meta[$attack['id']]['level_list'][$attack['attack_level']]))
						$map_descriptor.='<td>'.$skill_meta[$attack['id']]['level_list'][$attack['attack_level']]['endurance'].'</td>'."\n";
					else
						$map_descriptor.='<td>&nbsp;</td>'."\n";
					$map_descriptor.='</tr>'."\n";
				}
			}
		}
		$map_descriptor.='<tr>
			<td colspan="4" class="item_list_endline item_list_title_type_'.$resolved_type.'"></td>
		</tr>
		</table>'."\n";
        savewikipage('Template:monster_'.$id.'_ATTACK',$map_descriptor);$map_descriptor='';
	}
	if(count($monster['attack_list_byitem'])>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$resolved_type.'">
		<tr class="item_list_title item_list_title_type_'.$resolved_type.'">
			<th colspan="2">'.$translation_list[$current_lang]['Item'].'</th>
			<th>'.$translation_list[$current_lang]['Skill'].'</th>
			<th>'.$translation_list[$current_lang]['Type'].'</th>
			<th>'.$translation_list[$current_lang]['Endurance'].'</th>
		</tr>'."\n";
		$attack_list_byitem=$monster['attack_list_byitem'];
		foreach($attack_list_byitem as $item=>$attack_at_level)
		{
			foreach($attack_at_level as $attack)
			{
				if(isset($skill_meta[$attack['id']]))
				{
					$map_descriptor.='<tr class="value">'."\n";
					if(isset($item_meta[$item]))
					{
						$link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$item]['name'][$current_lang]).'.html'."\n";
						$name=$item_meta[$item]['name'][$current_lang];
						if($item_meta[$item]['image']!='')
							$image=$base_datapack_site_http.$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
						else
							$image='';
					}
					else
					{
						$link='';
						$name='';
						$image='';
					}
					$map_descriptor.='<tr class="value">
					<td>'."\n";
					if($image!='')
					{
						if($link!='')
							$map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$name.'|';
						$map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />'."\n";
						if($link!='')
							$map_descriptor.=']]'."\n";
					}
					$map_descriptor.='</td>
					<td>'."\n";
					if($link!='')
						$map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$name.'|';
					if($name!='')
						$map_descriptor.=$name;
					else
						$map_descriptor.='Unknown item'."\n";
					if($link!='')
						$map_descriptor.=']]'."\n";
					$map_descriptor.='</td>'."\n";
					$map_descriptor.='<td>[['.$translation_list[$current_lang]['Skills:'].$skill_meta[$attack['id']]['name'][$current_lang].'|'.$skill_meta[$attack['id']]['name'][$current_lang];
					if($attack['attack_level']>1)
						$map_descriptor.=' at level '.$attack['attack_level'];
					$map_descriptor.=']]</td>'."\n";
					if(isset($type_meta[$skill_meta[$attack['id']]['type']]))
						$map_descriptor.='<td><span class="type_label type_label_'.$skill_meta[$attack['id']]['type'].'">[['.$translation_list[$current_lang]['Monsters type:'].$type_meta[$skill_meta[$attack['id']]['type']]['name'][$current_lang].'|'.$type_meta[$skill_meta[$attack['id']]['type']]['name'][$current_lang].']]</span></td>'."\n";
					else
						$map_descriptor.='<td>&nbsp;</td>'."\n";
					if(isset($skill_meta[$attack['id']]['level_list'][$attack['attack_level']]))
						$map_descriptor.='<td>'.$skill_meta[$attack['id']]['level_list'][$attack['attack_level']]['endurance'].'</td>'."\n";
					else
						$map_descriptor.='<td>&nbsp;</td>'."\n";
					$map_descriptor.='</tr>'."\n";
				}
			}
		}
		$map_descriptor.='<tr>
			<td colspan="5" class="item_list_endline item_list_title_type_'.$resolved_type.'"></td>
		</tr>
		</table>'."\n";
        savewikipage('Template:monster_'.$id.'_ATTACKBYITEM',$map_descriptor);$map_descriptor='';
	}

	if(isset($monster_to_quests[$id]))
	{
		$map_descriptor.='<table class="item_list item_list_type_outdoor"><tr class="item_list_title item_list_title_type_outdoor">'."\n";
		$map_descriptor.='<th colspan="2">'.$translation_list[$current_lang]['Item'].'</th><th>'.$translation_list[$current_lang]['Quests'].'</th><th>'.$translation_list[$current_lang]['Luck'].'</th></tr>'."\n";
		foreach($monster_to_quests[$id] as $quests_monsters_details)
		{
			$map_descriptor.='<tr class="value"><td>'."\n";
			if(isset($item_meta[$quests_monsters_details['item']]))
			{
				$link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$quests_monsters_details['item']]['name'][$current_lang]).'.html'."\n";
				$name=$item_meta[$quests_monsters_details['item']]['name'][$current_lang];
				if($item_meta[$quests_monsters_details['item']]['image']!='')
					$image=$base_datapack_site_http.$base_datapack_site_path.'/items/'.$item_meta[$quests_monsters_details['item']]['image'];
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
			if($quests_monsters_details['quantity']>1)
				$quantity_text=$quests_monsters_details['quantity'].' '."\n";
			if($image!='')
			{
				if($link!='')
					$map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$name.'|';
				$map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
				if($link!='')
					$map_descriptor.=']]'."\n";
			}
			$map_descriptor.='</td><td>'."\n";
			if($link!='')
				$map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$name.'|';
			if($name!='')
				$map_descriptor.=$quantity_text.$name;
			else
				$map_descriptor.=$quantity_text.'Unknown item'."\n";
			if($link!='')
				$map_descriptor.=']]'."\n";
			$map_descriptor.='</td>'."\n";

			$map_descriptor.='<td>[['.$translation_list[$current_lang]['Quests:'].$quests_monsters_details['quest'].' '.$quests_meta[$quests_monsters_details['quest']]['name'][$current_lang].'|'.$quests_meta[$quests_monsters_details['quest']]['name'][$current_lang].']]</td>'."\n";
			$map_descriptor.='<td>'.$quests_monsters_details['rate'].'%</td>'."\n";
			$map_descriptor.='</tr>'."\n";
		}
		$map_descriptor.='<tr>
		<td colspan="4" class="item_list_endline item_list_title_type_outdoor"></td>
		</tr></table>'."\n";
        savewikipage('Template:monster_'.$id.'_QUEST',$map_descriptor);$map_descriptor='';
	}

	if(count($monster['evolution_list'])>0 || isset($reverse_evolution[$id]))
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$resolved_type.'">
		<tr class="item_list_title item_list_title_type_'.$resolved_type.'">
			<th>'."\n";
		if(isset($reverse_evolution[$id]))
			$map_descriptor.=$translation_list[$current_lang]['Evolve from']."\n";
		$map_descriptor.='</th>
		</tr>'."\n";

		if(isset($reverse_evolution[$id]))
		{
			$map_descriptor.='<tr class="value">'."\n";
			$map_descriptor.='<td>'."\n";
			$map_descriptor.='<table class="monsterforevolution">'."\n";
			foreach($reverse_evolution[$id] as $evolution)
			{
				if(file_exists($datapack_path.'monsters/'.$evolution['evolveFrom'].'/front.png'))
					$map_descriptor.='<tr><td>[['.$translation_list[$current_lang]['Monsters:'].$monster_meta[$evolution['evolveFrom']]['name'][$current_lang].'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$evolution['evolveFrom'].'/front.png" width="80" height="80" alt="'.$monster_meta[$evolution['evolveFrom']]['name'][$current_lang].'" title="'.$monster_meta[$evolution['evolveFrom']]['name'][$current_lang].'" />]]</td></tr>'."\n";
				else if(file_exists($datapack_path.'monsters/'.$evolution['evolveFrom'].'/front.gif'))
					$map_descriptor.='<tr><td>[['.$translation_list[$current_lang]['Monsters:'].$monster_meta[$evolution['evolveFrom']]['name'][$current_lang].'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$evolution['evolveFrom'].'/front.gif" width="80" height="80" alt="'.$monster_meta[$evolution['evolveFrom']]['name'][$current_lang].'" title="'.$monster_meta[$evolution['evolveFrom']]['name'][$current_lang].'" />]]</td></tr>'."\n";
				$map_descriptor.='<tr><td class="evolution_name">[['.$translation_list[$current_lang]['Monsters:'].$monster_meta[$evolution['evolveFrom']]['name'][$current_lang].'|'.$monster_meta[$evolution['evolveFrom']]['name'][$current_lang].']]</td></tr>'."\n";
				if($evolution['type']=='level')
					$map_descriptor.='<tr><td class="evolution_type">'.$translation_list[$current_lang]['At level'].' '.$evolution['level'].'</td></tr>'."\n";
				elseif($evolution['type']=='item')
				{
					if(isset($item_meta[$evolution['level']]))
					{
						$link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$evolution['level']]['name'][$current_lang]).'.html'."\n";
						$name=$item_meta[$evolution['level']]['name'][$current_lang];
						$map_descriptor.='<tr><td class="evolution_type">'.$translation_list[$current_lang]['Evolve with'].'<br />[['.$translation_list[$current_lang]['Items:'].$name.'|';
						if($item_meta[$evolution['level']]['image']!='')
							$map_descriptor.='<img src="'.$base_datapack_site_http.$base_datapack_site_path.'items/'.$item_meta[$evolution['level']]['image'].'" alt="'.$name.'" title="'.$name.'" style="float:left;" />';
						$map_descriptor.=$name.']]</td></tr>'."\n";
					}
					else
						$map_descriptor.='<tr><td class="evolution_type">'.$translation_list[$current_lang]['With unknown item'].'</td></tr>'."\n";
				}
				elseif($evolution['type']=='trade')
					$map_descriptor.='<tr><td class="evolution_type">'.$translation_list[$current_lang]['After trade'].'</td></tr>'."\n";
				else
					$map_descriptor.='<tr><td class="evolution_type">&nbsp;</td></tr>'."\n";
			}
			$map_descriptor.='</table>'."\n";
			$map_descriptor.='</td>'."\n";
			$map_descriptor.='</tr>'."\n";
		}

		$map_descriptor.='<tr class="value">'."\n";
		$map_descriptor.='<td>'."\n";
		$map_descriptor.='<table class="monsterforevolution">'."\n";
		if(file_exists($datapack_path.'monsters/'.$id.'/front.png'))
			$map_descriptor.='<tr><td><img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$id.'/front.png" width="80" height="80" alt="'.$monster['name'][$current_lang].'" title="'.$monster['name'][$current_lang].'" /></td></tr>'."\n";
		else if(file_exists($datapack_path.'monsters/'.$id.'/front.gif'))
			$map_descriptor.='<tr><td><img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$id.'/front.gif" width="80" height="80" alt="'.$monster['name'][$current_lang].'" title="'.$monster['name'][$current_lang].'" /></td></tr>'."\n";
		$map_descriptor.='<tr><td class="evolution_name">'.$monster['name'][$current_lang].'</td></tr>'."\n";
		$map_descriptor.='</table>'."\n";
		$map_descriptor.='</td>'."\n";
		$map_descriptor.='</tr>'."\n";

		if(count($monster['evolution_list'])>0)
		{
			$map_descriptor.='<tr class="value">'."\n";
			$map_descriptor.='<td>'."\n";
			$map_descriptor.='<table class="monsterforevolution">'."\n";
			foreach($monster['evolution_list'] as $evolution)
			{
				if(file_exists($datapack_path.'monsters/'.$evolution['evolveTo'].'/front.png'))
					$map_descriptor.='<tr><td>[['.$translation_list[$current_lang]['Monsters:'].$monster_meta[$evolution['evolveTo']]['name'][$current_lang].'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$evolution['evolveTo'].'/front.png" width="80" height="80" alt="'.$monster_meta[$evolution['evolveTo']]['name'][$current_lang].'" title="'.$monster_meta[$evolution['evolveTo']]['name'][$current_lang].'" />]]</td></tr>'."\n";
				else if(file_exists($datapack_path.'monsters/'.$evolution['evolveTo'].'/front.gif'))
					$map_descriptor.='<tr><td>[['.$translation_list[$current_lang]['Monsters:'].$monster_meta[$evolution['evolveTo']]['name'][$current_lang].'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$evolution['evolveTo'].'/front.gif" width="80" height="80" alt="'.$monster_meta[$evolution['evolveTo']]['name'][$current_lang].'" title="'.$monster_meta[$evolution['evolveTo']]['name'][$current_lang].'" />]]</td></tr>'."\n";
				$map_descriptor.='<tr><td class="evolution_name">[['.$translation_list[$current_lang]['Monsters:'].$monster_meta[$evolution['evolveTo']]['name'][$current_lang].'|'.$monster_meta[$evolution['evolveTo']]['name'][$current_lang].']]</td></tr>'."\n";
				if($evolution['type']=='level')
					$map_descriptor.='<tr><td class="evolution_type">'.$translation_list[$current_lang]['At level'].' '.$evolution['level'].'</td></tr>'."\n";
				elseif($evolution['type']=='item')
				{
					if(isset($item_meta[$evolution['level']]))
					{
						$link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$evolution['level']]['name'][$current_lang]).'.html'."\n";
						$name=$item_meta[$evolution['level']]['name'][$current_lang];
						$map_descriptor.='<tr><td class="evolution_type">'.$translation_list[$current_lang]['Evolve with'].'<br />[['.$translation_list[$current_lang]['Items:'].$name.'|';
						if($item_meta[$evolution['level']]['image']!='')
							$map_descriptor.='<img src="'.$base_datapack_site_http.$base_datapack_site_path.'items/'.$item_meta[$evolution['level']]['image'].'" alt="'.$name.'" title="'.$name.'" style="float:left;" />';
						$map_descriptor.=$name.']]</td></tr>'."\n";
					}
					else
						$map_descriptor.='<tr><td class="evolution_type">'.$translation_list[$current_lang]['With unknown item'].'</td></tr>'."\n";
				}
				elseif($evolution['type']=='trade')
					$map_descriptor.='<tr><td class="evolution_type">'.$translation_list[$current_lang]['After trade'].'</td></tr>'."\n";
				else
					$map_descriptor.='<tr><td class="evolution_type">&nbsp;</td></tr>'."\n";
			}
			$map_descriptor.='</table>'."\n";
			$map_descriptor.='</td>'."\n";
			$map_descriptor.='</tr>'."\n";
		}

		$map_descriptor.='<tr>
			<th class="item_list_endline item_list_title item_list_title_type_'.$resolved_type.'">'."\n";
		if(count($monster['evolution_list'])>0)
			$map_descriptor.='Evolve to'."\n";
		$map_descriptor.='</th>
		</tr>
		</table>'."\n";
        savewikipage('Template:monster_'.$id.'_EVOL',$map_descriptor);$map_descriptor='';
	}

	if(isset($monster_to_map[$id]))
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$resolved_type.'">
		<tr class="item_list_title item_list_title_type_'.$resolved_type.'">
			<th colspan="2">'.$translation_list[$current_lang]['Map'].'</th>
			<th>'.$translation_list[$current_lang]['Location'].'</th>
			<th>'.$translation_list[$current_lang]['Levels'].'</th>
			<th colspan="3">'.$translation_list[$current_lang]['Rate'].'</th>
		</tr>'."\n";


        foreach($monster_to_map[$id] as $monsterType=>$monster_list)
        {
            $full_monsterType_name='Cave'."\n";
            if(isset($layer_event[$monsterType]))
            {
                if($layer_event[$monsterType]['layer']!='')
                    $full_monsterType_name=$layer_event[$monsterType]['layer'];
                $monsterType_top=$layer_event[$monsterType]['monsterType'];
                $full_monsterType_name_top='Cave'."\n";
                if(isset($layer_meta[$monsterType_top]))
                    if($layer_meta[$monsterType_top]['layer']!='')
                        $full_monsterType_name_top=$layer_meta[$monsterType_top]['layer'];
            }
            elseif(isset($layer_meta[$monsterType]))
            {
                if($layer_meta[$monsterType]['layer']!='')
                    $full_monsterType_name=$layer_meta[$monsterType]['layer'];
                $monsterType_top=$monsterType;
                $full_monsterType_name_top=$full_monsterType_name;
            }
            $map_descriptor.='<tr class="item_list_title_type_'.$resolved_type.'">
                    <th colspan="7">'."\n";
            $link='';
            $name='';
            $image='';
            if(isset($layer_meta[$monsterType_top]['item']) && $item_meta[$layer_meta[$monsterType_top]['item']])
            {
                $link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$layer_meta[$monsterType_top]['item']]['name'][$current_lang]).'.html'."\n";
                $name=$item_meta[$layer_meta[$monsterType_top]['item']]['name'][$current_lang];
                if($item_meta[$layer_meta[$monsterType_top]['item']]['image']!='')
                    $image=$base_datapack_site_http.$base_datapack_site_path.'/items/'.$item_meta[$layer_meta[$monsterType_top]['item']]['image'];
                else
                    $image='';
                $map_descriptor.='<center><table><tr>'."\n";
                
                if($link!='')
                    $map_descriptor.='<td>[['.$translation_list[$current_lang]['Items:'].$name.'|';
                if($image!='')
                    $map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
                if($link!='')
                    $map_descriptor.=']]</td>'."\n";

                if($link!='')
                    $map_descriptor.='<td>[['.$translation_list[$current_lang]['Items:'].$name.'|';
                $map_descriptor.=$item_meta[$layer_meta[$monsterType_top]['item']]['name'][$current_lang];
                if($link!='')
                    $map_descriptor.=']]</td>'."\n";

                $map_descriptor.='</tr></table></center>'."\n";
            }
            else
                $map_descriptor.=$full_monsterType_name;
            if(isset($layer_event[$monsterType]))
            {
                if($layer_event[$monsterType]['id']=='day' && $layer_event[$monsterType]['value']=='night')
                    $map_descriptor.=' at night'."\n";
                else
                    $map_descriptor.=' condition '.$layer_event[$monsterType]['id'].' at '.$layer_event[$monsterType]['value'];
            }
            $map_descriptor.='</th>
                </tr>'."\n";
            foreach($monster_list as $monster_on_map)
            {
                $map_descriptor.='<tr class="value">'."\n";
                if(isset($maps_list[$monster_on_map['map']]))
                {
                    if(isset($zone_meta[$maps_list[$monster_on_map['map']]['zone']]))
                    {
                        $map_descriptor.='<td>[['.$translation_list[$current_lang]['Maps:'].map_to_wiki_name($monster_on_map['map']).'|'.$maps_list[$monster_on_map['map']]['name'][$current_lang].']]</td>'."\n";
                        $map_descriptor.='<td>'.$zone_meta[$maps_list[$monster_on_map['map']]['zone']]['name'][$current_lang].'</td>'."\n";
                    }
                    else
                        $map_descriptor.='<td colspan="2">[['.$translation_list[$current_lang]['Maps:'].map_to_wiki_name($monster_on_map['map']).'|'.$maps_list[$monster_on_map['map']]['name'][$current_lang].']]</td>'."\n";
                }
                else
                    $map_descriptor.='<td colspan="2">'.$translation_list[$current_lang]['Unknown map'].'</td>'."\n";
                $map_descriptor.='<td>'."\n";
                $map_descriptor.='<img src="'.$base_datapack_site_http.'/images/datapack-explorer/'.$full_monsterType_name_top.'.png" alt="" class="locationimg">'.$full_monsterType_name_top;
                $map_descriptor.='</td>
                <td>'."\n";
                if($monster_on_map['minLevel']==$monster_on_map['maxLevel'])
                    $map_descriptor.=$monster_on_map['minLevel'];
                else
                    $map_descriptor.=$monster_on_map['minLevel'].'-'.$monster_on_map['maxLevel'];
                $map_descriptor.='</td>'."\n";
                $map_descriptor.='<td colspan="3">'.$monster_on_map['luck'].'%</td>
                </tr>'."\n";
            }
        }

		/*if(isset($monster_to_map[$id]))
		{
			$map_descriptor.='<tr class="item_list_title_type_'.$resolved_type.'">
					<th colspan="7">Grass</th>
				</tr>'."\n";
			foreach($monster_to_map[$id] as $monsterType=>$monster_list)
			{
				$map_descriptor.='<tr class="value">'."\n";
					if(isset($maps_list[$monster_on_map['map']]))
					{
						if(isset($zone_meta[$maps_list[$monster_on_map['map']]['zone']]))
						{
							$map_descriptor.='<td>[['.$translation_list[$current_lang]['Maps:'].map_to_wiki_name($monster_on_map['map']).'|'.$maps_list[$monster_on_map['map']]['name'][$current_lang].']]</td>'."\n";
							$map_descriptor.='<td>'.$zone_meta[$maps_list[$monster_on_map['map']]['zone']]['name'][$current_lang].'</td>'."\n";
						}
						else
							$map_descriptor.='<td colspan="2">[['.$translation_list[$current_lang]['Maps:'].map_to_wiki_name($monster_on_map['map']).'|'.$maps_list[$monster_on_map['map']]['name'][$current_lang].']]</td>'."\n";
					}
					else
						$map_descriptor.='<td>Unknown map</td><td>&nbsp;</td>'."\n";
					$map_descriptor.='<td><img src="'.$base_datapack_site_http.'/images/datapack-explorer/grass.png" alt="" class="locationimg">Grass</td>
					<td>'."\n";
					if($monster_on_map['minLevel']==$monster_on_map['maxLevel'])
						$map_descriptor.=$monster_on_map['minLevel'];
					else
						$map_descriptor.=$monster_on_map['minLevel'].'-'.$monster_on_map['maxLevel'];
					$map_descriptor.='</td>'."\n";
					$map_descriptor.='<td colspan="3">'.$monster_on_map['luck'].'%</td>
				</tr>'."\n";
			}
		}*/

		$map_descriptor.='<tr>
			<td colspan="7" class="item_list_endline item_list_title_type_'.$resolved_type.'"></td>
		</tr>
		</table>'."\n";
        savewikipage('Template:monster_'.$id.'_MAP',$map_descriptor);$map_descriptor='';
	}

    if($wikivars['generatefullpage'])
    {
        $map_descriptor.='{{Template:monster_'.$id.'_HEADER}}'."\n";

        if(count($monster['drops'])>0)
            $map_descriptor.='{{Template:monster_'.$id.'_DROP}}'."\n";
        if(count($monster['attack_list'])>0)
            $map_descriptor.='{{Template:monster_'.$id.'_ATTACK}}'."\n";
        if(count($monster['attack_list_byitem'])>0)
            $map_descriptor.='{{Template:monster_'.$id.'_ATTACKBYITEM}}'."\n";
        if(isset($monster_to_quests[$id]))
            $map_descriptor.='{{Template:monster_'.$id.'_QUEST}}'."\n";
        if(count($monster['evolution_list'])>0 || isset($reverse_evolution[$id]))
            $map_descriptor.='{{Template:monster_'.$id.'_EVOL}}'."\n";
        if(isset($monster_to_map[$id]))
            $map_descriptor.='{{Template:monster_'.$id.'_MAP}}'."\n";

        savewikipage($translation_list[$current_lang]['Monsters:'].$monster['name'][$current_lang],$map_descriptor);
    }
}

$map_descriptor='';
$map_descriptor.='<table class="item_list item_list_type_normal">
<tr class="item_list_title item_list_title_type_normal">
	<th colspan="3">Monster</th>
</tr>'."\n";
foreach($monster_meta as $id=>$monster)
{
	$name=$monster['name'][$current_lang];
	$link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($name).'.html'."\n";
	$map_descriptor.='<tr class="value">'."\n";
	$map_descriptor.='<td>'."\n";
	if(file_exists($datapack_path.'monsters/'.$id.'/small.png'))
		$map_descriptor.='<div class="monstericon">[['.$translation_list[$current_lang]['Monsters:'].$monster['name'][$current_lang].'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$id.'/small.png" width="32" height="32" alt="'.$monster['name'][$current_lang].'" title="'.$monster['name'][$current_lang].'" />]]</div>'."\n";
	else if(file_exists($datapack_path.'monsters/'.$id.'/small.gif'))
		$map_descriptor.='<div class="monstericon">[['.$translation_list[$current_lang]['Monsters:'].$monster['name'][$current_lang].'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$id.'/small.gif" width="32" height="32" alt="'.$monster['name'][$current_lang].'" title="'.$monster['name'][$current_lang].'" />]]</div>'."\n";
	$map_descriptor.='</td>
	<td>[['.$translation_list[$current_lang]['Monsters:'].$monster['name'][$current_lang].'|'.$name.']]</td>'."\n";
	$map_descriptor.='<td>'."\n";
	$type_list=array();
	foreach($monster['type'] as $type)
		if(isset($type_meta[$type]))
			$type_list[]='<span class="type_label type_label_'.$type.'">[['.$translation_list[$current_lang]['Monsters type:'].$type_meta[$type]['name'][$current_lang].'|'.$type_meta[$type]['name'][$current_lang].']]</span>'."\n";
	$map_descriptor.='<div class="type_label_list">'.implode(' ',$type_list).'</div>'."\n";
	$map_descriptor.='</td>'."\n";
	$map_descriptor.='</tr>'."\n";
}
$map_descriptor.='<tr>
	<td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
</tr>
</table>'."\n";

savewikipage('Template:monsters_list',$map_descriptor);$map_descriptor='';

if($wikivars['generatefullpage'])
{
    $map_descriptor.='{{Template:monsters_list}}'."\n";
    savewikipage($translation_list[$current_lang]['Monsters list'],$map_descriptor);
}