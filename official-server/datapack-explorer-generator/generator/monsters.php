<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator monsters'."\n");

foreach($monster_meta as $id=>$monster)
{
	$resolved_type=$monster['type'][0];
	if(!isset($type_meta[$resolved_type]))
	{
		if(!isset($type_meta['normal']) || count($type_meta)<=0)
			$resolved_type='normal';
		else
		{
			foreach($type_meta as $type=>$type_content)
			{
				$resolved_type=$type;
				break;
			}
		}
	}
	if(!is_dir($datapack_explorer_local_path.$translation_list[$current_lang]['monsters/']))
		mkdir($datapack_explorer_local_path.$translation_list[$current_lang]['monsters/']);
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
	$map_descriptor.='<div class="map monster_type_'.$resolved_type.'">';
		$map_descriptor.='<div class="subblock"><h1>'.$monster['name'][$current_lang].'</h1>';
		$map_descriptor.='<h2>#'.$id.'</h2>';
		$map_descriptor.='</div>';
		$map_descriptor.='<div class="value datapackscreenshot"><center>';
		if(file_exists($datapack_path.'monsters/'.$id.'/front.png'))
			$map_descriptor.='<img src="'.$base_datapack_site_path.'monsters/'.$id.'/front.png" width="80" height="80" alt="'.$monster['name'][$current_lang].'" title="'.$monster['name'][$current_lang].'" />';
		else if(file_exists($datapack_path.'monsters/'.$id.'/front.gif'))
			$map_descriptor.='<img src="'.$base_datapack_site_path.'monsters/'.$id.'/front.gif" width="80" height="80" alt="'.$monster['name'][$current_lang].'" title="'.$monster['name'][$current_lang].'" />';
		$map_descriptor.='</center></div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Type'].'</div><div class="value">';
		$type_list=array();
		foreach($monster['type'] as $type)
			if(isset($type_meta[$type]))
				$type_list[]='<span class="type_label type_label_'.$type.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type.'.html">'.ucfirst($type_meta[$type]['name'][$current_lang]).'</a></span>';
		$map_descriptor.='<div class="type_label_list">'.implode(' ',$type_list).'</div></div></div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Gender ratio'].'</div><div class="value">';
		if($monster['ratio_gender']<0 || $monster['ratio_gender']>100)
		{
			$map_descriptor.='<center><table class="genderbar"><tr><td class="genderbarunknown" style="width:100%"></td></tr></table></center>';
			$map_descriptor.=$translation_list[$current_lang]['Unknown gender'];
		}
		else
		{
			$map_descriptor.='<center><table class="genderbar"><tr><td class="genderbarmale" style="width:'.$monster['ratio_gender'].'%"></td><td class="genderbarfemale" style="width:'.(100-$monster['ratio_gender']).'%"></td></tr></table></center>';
			$map_descriptor.=$monster['ratio_gender'].'% '.$translation_list[$current_lang]['male'].', '.(100-$monster['ratio_gender']).'% '.$translation_list[$current_lang]['female'];
		}
		$map_descriptor.='</div></div>';
		if($monster['description'][$current_lang]!='')
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Description'].'</div><div class="value">'.$monster['description'][$current_lang].'</div></div>';
		if($monster['kind'][$current_lang]!='')
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Kind'].'</div><div class="value">'.$monster['kind'][$current_lang].'</div></div>';
		if($monster['habitat'][$current_lang]!='')
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Habitat'].'</div><div class="value">'.$monster['habitat'][$current_lang].'</div></div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Catch rate'].'</div><div class="value">'.$monster['catch_rate'].'</div></div>';

        if(count($monster['game']))
        {
            $map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Game to catch it'].'</div><div class="value">';
            foreach($monster['game'] as $maindatapackcode=>$tempSubList)
            foreach($tempSubList as $tempSub)
            {
                $text_temp_sub='?';
                $color_temp_sub='';
                if($tempSub=='')
                {
                    if(isset($informations_meta['main'][$maindatapackcode]))
                    {
                        $text_temp_sub=$informations_meta['main'][$maindatapackcode]['initial'];
                        $color_temp_sub=$informations_meta['main'][$maindatapackcode]['color'];
                    }
                }
                else
                {
                    if(isset($informations_meta['main'][$maindatapackcode]['sub'][$tempSub]))
                    {
                        $text_temp_sub=$informations_meta['main'][$maindatapackcode]['sub'][$tempSub]['initial'];
                        $color_temp_sub=$informations_meta['main'][$maindatapackcode]['sub'][$tempSub]['color'];
                    }
                }
                if($color_temp_sub!='')
                    $map_descriptor.='<span style="background-color:'.$color_temp_sub.';" class="datapackinital">'.$text_temp_sub.'</span>';
                else
                    $map_descriptor.='<span class="datapackinital">'.$text_temp_sub.'</span>';
            }
            $map_descriptor.='</div></div>';
        }

        $map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Rarity'].'</div><div class="value">';
        if(isset($exclusive_monster_reverse[$id]))
            $map_descriptor.=$translation_list[$current_lang]['Version exclusive!'];
        else if(!isset($monster_to_map[$id]))
            $map_descriptor.=$translation_list[$current_lang]['Not found on any map'];
        else
        {
            if(!isset($monster_to_rarity[$id]))
                $map_descriptor.=$translation_list[$current_lang]['Very rare'];
            else
            {
                $percent=100*($monster_to_rarity[$id]['position'])/count($monster_to_rarity);
                if($percent>10)
                    $map_descriptor.=$translation_list[$current_lang]['Very common'];
                else if($percent>70)
                    $map_descriptor.=$translation_list[$current_lang]['Common'];
                else if($percent>40)
                    $map_descriptor.=$translation_list[$current_lang]['Less common'];
                else if($percent>10)
                    $map_descriptor.=$translation_list[$current_lang]['Rare'];
                else
                    $map_descriptor.=$translation_list[$current_lang]['Very rare'];
            }
        }
        $map_descriptor.='</div></div>';

		$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Steps for hatching'].'</div><div class="value">'.$monster['egg_step'].'</div></div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Body'].'</div><div class="value">'.$translation_list[$current_lang]['Height'].': '.$monster['height'].'m, '.$translation_list[$current_lang]['width'].': '.$monster['weight'].'kg</div></div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Stat'].'</div><div class="value">'.$translation_list[$current_lang]['Hp'].': <i>'.$monster['hp'].'</i>, '.$translation_list[$current_lang]['Attack'].': <i>'.$monster['attack'].'</i>, '.$translation_list[$current_lang]['Defense'].': <i>'.$monster['defense'].'</i>, '.$translation_list[$current_lang]['Special attack'].': <i>'.$monster['special_attack'].'</i>, '.$translation_list[$current_lang]['Special defense'].': <i>'.$monster['special_defense'].'</i>, '.$translation_list[$current_lang]['Speed'].': <i>'.$monster['speed'].'</i></div></div>';
		if(isset($effectiveness_list['4']) || isset($effectiveness_list['2']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Weak to'].'</div><div class="value">';
			$type_list=array();
			if(isset($effectiveness_list['2']))
				foreach($effectiveness_list['2'] as $type)
					if(isset($type_meta[$type]))
						$type_list[]='<span class="type_label type_label_'.$type.'">2x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type.'.html">'.ucfirst($type_meta[$type]['name'][$current_lang]).'</a></span>';
			if(isset($effectiveness_list['4']))
				foreach($effectiveness_list['4'] as $type)
					if(isset($type_meta[$type]))
						$type_list[]='<span class="type_label type_label_'.$type.'">4x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type.'.html">'.ucfirst($type_meta[$type]['name'][$current_lang]).'</a></span>';
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>';
		}
		if(isset($effectiveness_list['0.25']) || isset($effectiveness_list['0.5']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Resistant to'].'</div><div class="value">';
			$type_list=array();
			if(isset($effectiveness_list['0.25']))
				foreach($effectiveness_list['0.25'] as $type)
					if(isset($type_meta[$type]))
						$type_list[]='<span class="type_label type_label_'.$type.'">0.25x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type.'.html">'.ucfirst($type_meta[$type]['name'][$current_lang]).'</a></span>';
			if(isset($effectiveness_list['0.5']))
				foreach($effectiveness_list['0.5'] as $type)
					if(isset($type_meta[$type]))
						$type_list[]='<span class="type_label type_label_'.$type.'">0.5x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type.'.html">'.ucfirst($type_meta[$type]['name'][$current_lang]).'</a></span>';
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>';
		}
		if(isset($effectiveness_list['0']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Immune to'].'</div><div class="value">';
			$type_list=array();
			foreach($effectiveness_list['0'] as $type)
				if(isset($type_meta[$type]))
					$type_list[]='<span class="type_label type_label_'.$type.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type.'.html">'.ucfirst($type_meta[$type]['name'][$current_lang]).'</a></span>';
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>';
		}
	$map_descriptor.='</div>';
	
	if(count($monster['drops'])>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$resolved_type.'">
		<tr class="item_list_title item_list_title_type_'.$resolved_type.'">
			<th colspan="2">'.$translation_list[$current_lang]['Item'].'</th>
			<th>'.$translation_list[$current_lang]['Location'].'</th>
		</tr>';
		$drops=$monster['drops'];
		foreach($drops as $drop)
		{
			if(isset($item_meta[$drop['item']]))
			{
				$link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$drop['item']]['name'][$current_lang]).'.html';
				$name=$item_meta[$drop['item']]['name'][$current_lang];
				if($item_meta[$drop['item']]['image']!='')
					$image=$base_datapack_site_path.'/items/'.$item_meta[$drop['item']]['image'];
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
				$quantity_text=$drop['quantity_min'].' to '.$drop['quantity_max'].' ';
			elseif($drop['quantity_min']>1)
				$quantity_text=$drop['quantity_min'].' ';
			$map_descriptor.='<tr class="value">
				<td>';
				if($image!='')
				{
					if($link!='')
						$map_descriptor.='<a href="'.$link.'">';
					$map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
					if($link!='')
						$map_descriptor.='</a>';
				}
				$map_descriptor.='</td>
				<td>';
				if($link!='')
					$map_descriptor.='<a href="'.$link.'">';
				if($name!='')
					$map_descriptor.=$quantity_text.$name;
				else
					$map_descriptor.=$quantity_text.$translation_list[$current_lang]['Unknown item'];
				if($link!='')
					$map_descriptor.='</a>';
				$map_descriptor.='</td>';
				$map_descriptor.='<td>'.str_replace('[luck]',$drop['luck'],$translation_list[$current_lang]['Drop luck of [luck]%']).'</td>
			</tr>';
		}
		$map_descriptor.='<tr>
			<td colspan="3" class="item_list_endline item_list_title_type_'.$resolved_type.'"></td>
		</tr>
		</table>';
	}

	if(count($monster['attack_list'])>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$resolved_type.'">
		<tr class="item_list_title item_list_title_type_'.$resolved_type.'">
			<th>'.$translation_list[$current_lang]['Level'].'</th>
			<th>'.$translation_list[$current_lang]['Skill'].'</th>
			<th>'.$translation_list[$current_lang]['Type'].'</th>
			<th>'.$translation_list[$current_lang]['Endurance'].'</th>
		</tr>';
		$attack_list=$monster['attack_list'];
		foreach($attack_list as $level=>$attack_at_level)
		{
			foreach($attack_at_level as $attack)
			{
				if(isset($skill_meta[$attack['id']]))
				{
					$map_descriptor.='<tr class="value">';
					$map_descriptor.='<td>';
					if($level==0)
						$map_descriptor.='Start';
					else
						$map_descriptor.=$level;
					$map_descriptor.='</td>';
					$map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.'monsters/skills/'.text_operation_do_for_url($skill_meta[$attack['id']]['name'][$current_lang]).'.html">'.$skill_meta[$attack['id']]['name'][$current_lang];
					if($attack['attack_level']>1)
						$map_descriptor.=' at level '.$attack['attack_level'];
					$map_descriptor.='</a></td>';
					if(isset($type_meta[$skill_meta[$attack['id']]['type']]))
						$map_descriptor.='<td><span class="type_label type_label_'.$skill_meta[$attack['id']]['type'].'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$skill_meta[$attack['id']]['type'].'.html">'.$type_meta[$skill_meta[$attack['id']]['type']]['name'][$current_lang].'</a></span></td>';
					else
						$map_descriptor.='<td>&nbsp;</td>';
					if(isset($skill_meta[$attack['id']]['level_list'][$attack['attack_level']]))
						$map_descriptor.='<td>'.$skill_meta[$attack['id']]['level_list'][$attack['attack_level']]['endurance'].'</td>';
					else
						$map_descriptor.='<td>&nbsp;</td>';
					$map_descriptor.='</tr>';
				}
			}
		}
		$map_descriptor.='<tr>
			<td colspan="4" class="item_list_endline item_list_title_type_'.$resolved_type.'"></td>
		</tr>
		</table>';
	}
	if(count($monster['attack_list_byitem'])>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$resolved_type.'">
		<tr class="item_list_title item_list_title_type_'.$resolved_type.'">
			<th colspan="2">'.$translation_list[$current_lang]['Item'].'</th>
			<th>'.$translation_list[$current_lang]['Skill'].'</th>
			<th>'.$translation_list[$current_lang]['Type'].'</th>
			<th>'.$translation_list[$current_lang]['Endurance'].'</th>
		</tr>';
		$attack_list_byitem=$monster['attack_list_byitem'];
		foreach($attack_list_byitem as $item=>$attack_at_level)
		{
			foreach($attack_at_level as $attack)
			{
				if(isset($skill_meta[$attack['id']]))
				{
					$map_descriptor.='<tr class="value">';
					if(isset($item_meta[$item]))
					{
						$link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$item]['name'][$current_lang]).'.html';
						$name=$item_meta[$item]['name'][$current_lang];
						if($item_meta[$item]['image']!='')
							$image=$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
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
					<td>';
					if($image!='')
					{
						if($link!='')
							$map_descriptor.='<a href="'.$link.'">';
						$map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
						if($link!='')
							$map_descriptor.='</a>';
					}
					$map_descriptor.='</td>
					<td>';
					if($link!='')
						$map_descriptor.='<a href="'.$link.'">';
					if($name!='')
						$map_descriptor.=$name;
					else
						$map_descriptor.=$translation_list[$current_lang]['Unknown item'];
					if($link!='')
						$map_descriptor.='</a>';
					$map_descriptor.='</td>';
					$map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.'monsters/skills/'.text_operation_do_for_url($skill_meta[$attack['id']]['name'][$current_lang]).'.html">'.$skill_meta[$attack['id']]['name'][$current_lang];
					if($attack['attack_level']>1)
						$map_descriptor.=' at level '.$attack['attack_level'];
					$map_descriptor.='</a></td>';
					if(isset($type_meta[$skill_meta[$attack['id']]['type']]))
						$map_descriptor.='<td><span class="type_label type_label_'.$skill_meta[$attack['id']]['type'].'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$skill_meta[$attack['id']]['type'].'.html">'.$type_meta[$skill_meta[$attack['id']]['type']]['name'][$current_lang].'</a></span></td>';
					else
						$map_descriptor.='<td>&nbsp;</td>';
					if(isset($skill_meta[$attack['id']]['level_list'][$attack['attack_level']]))
						$map_descriptor.='<td>'.$skill_meta[$attack['id']]['level_list'][$attack['attack_level']]['endurance'].'</td>';
					else
						$map_descriptor.='<td>&nbsp;</td>';
					$map_descriptor.='</tr>';
				}
			}
		}
		$map_descriptor.='<tr>
			<td colspan="5" class="item_list_endline item_list_title_type_'.$resolved_type.'"></td>
		</tr>
		</table>';
	}

	if(isset($monster_to_quests[$id]))
	{
		$map_descriptor.='<table class="item_list item_list_type_outdoor"><tr class="item_list_title item_list_title_type_outdoor">';
		$map_descriptor.='<th colspan="2">'.$translation_list[$current_lang]['Item'].'</th><th>'.$translation_list[$current_lang]['Quests'].'</th><th>'.$translation_list[$current_lang]['Luck'].'</th></tr>';
		foreach($monster_to_quests[$id] as $maindatapackcode=>$monsterlist)
        foreach($monsterlist as $quests_monsters_details)
		{
			$map_descriptor.='<tr class="value"><td>';
			if(isset($item_meta[$quests_monsters_details['item']]))
			{
				$link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$quests_monsters_details['item']]['name'][$current_lang]).'.html';
				$name=$item_meta[$quests_monsters_details['item']]['name'][$current_lang];
				if($item_meta[$quests_monsters_details['item']]['image']!='')
					$image=$base_datapack_site_path.'/items/'.$item_meta[$quests_monsters_details['item']]['image'];
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
				$quantity_text=$quests_monsters_details['quantity'].' ';
			if($image!='')
			{
				if($link!='')
					$map_descriptor.='<a href="'.$link.'">';
				$map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
				if($link!='')
					$map_descriptor.='</a>';
			}
			$map_descriptor.='</td><td>';
			if($link!='')
				$map_descriptor.='<a href="'.$link.'">';
			if($name!='')
				$map_descriptor.=$quantity_text.$name;
			else
				$map_descriptor.=$quantity_text.$translation_list[$current_lang]['Unknown item'];
			if($link!='')
				$map_descriptor.='</a>';
			$map_descriptor.='</td>';

			$map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['quests/'].$quests_monsters_details['quest'].'-'.text_operation_do_for_url($quests_meta[$maindatapackcode][$quests_monsters_details['quest']]['name'][$current_lang]).'.html" title="'.$quests_meta[$maindatapackcode][$quests_monsters_details['quest']]['name'][$current_lang].'">';
			$map_descriptor.=$quests_meta[$maindatapackcode][$quests_monsters_details['quest']]['name'][$current_lang];
			$map_descriptor.='</a></td>';
			$map_descriptor.='<td>'.$quests_monsters_details['rate'].'%</td>';
			$map_descriptor.='</tr>';
		}
		$map_descriptor.='<tr>
		<td colspan="4" class="item_list_endline item_list_title_type_outdoor"></td>
		</tr></table>';
	}

	if(count($monster['evolution_list'])>0 || isset($reverse_evolution[$id]))
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$resolved_type.'">
		<tr class="item_list_title item_list_title_type_'.$resolved_type.'">
			<th>';
		if(isset($reverse_evolution[$id]))
			$map_descriptor.=$translation_list[$current_lang]['Evolve from'];
		$map_descriptor.='</th>
		</tr>';

		if(isset($reverse_evolution[$id]))
		{
			$map_descriptor.='<tr class="value">';
			$map_descriptor.='<td>';
			$map_descriptor.='<table class="monsterforevolution">';
			foreach($reverse_evolution[$id] as $evolution)
			{
				if(file_exists($datapack_path.'monsters/'.$evolution['evolveFrom'].'/front.png'))
					$map_descriptor.='<tr><td><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($monster_meta[$evolution['evolveFrom']]['name'][$current_lang]).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$evolution['evolveFrom'].'/front.png" width="80" height="80" alt="'.$monster_meta[$evolution['evolveFrom']]['name'][$current_lang].'" title="'.$monster_meta[$evolution['evolveFrom']]['name'][$current_lang].'" /></a></td></tr>';
				else if(file_exists($datapack_path.'monsters/'.$evolution['evolveFrom'].'/front.gif'))
					$map_descriptor.='<tr><td><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($monster_meta[$evolution['evolveFrom']]['name'][$current_lang]).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$evolution['evolveFrom'].'/front.gif" width="80" height="80" alt="'.$monster_meta[$evolution['evolveFrom']]['name'][$current_lang].'" title="'.$monster_meta[$evolution['evolveFrom']]['name'][$current_lang].'" /></a></td></tr>';
				$map_descriptor.='<tr><td class="evolution_name"><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($monster_meta[$evolution['evolveFrom']]['name'][$current_lang]).'.html">'.$monster_meta[$evolution['evolveFrom']]['name'][$current_lang].'</a></td></tr>';
				if($evolution['type']=='level')
					$map_descriptor.='<tr><td class="evolution_type">'.$translation_list[$current_lang]['At level'].' '.$evolution['level'].'</td></tr>';
				elseif($evolution['type']=='item')
				{
					if(isset($item_meta[$evolution['level']]))
					{
						$link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$evolution['level']]['name'][$current_lang]).'.html';
						$name=$item_meta[$evolution['level']]['name'][$current_lang];
						$map_descriptor.='<tr><td class="evolution_type">'.$translation_list[$current_lang]['Evolve with'].'<br /><a href="'.$link.'" title="'.$name.'">';
						if($item_meta[$evolution['level']]['image']!='')
							$map_descriptor.='<img src="'.$base_datapack_site_path.'items/'.$item_meta[$evolution['level']]['image'].'" alt="'.$name.'" title="'.$name.'" style="float:left;" />';
						$map_descriptor.=$name.'</a></td></tr>';
					}
					else
						$map_descriptor.='<tr><td class="evolution_type">'.$translation_list[$current_lang]['With unknown item'].'</td></tr>';
				}
				elseif($evolution['type']=='trade')
					$map_descriptor.='<tr><td class="evolution_type">'.$translation_list[$current_lang]['After trade'].'</td></tr>';
				else
					$map_descriptor.='<tr><td class="evolution_type">&nbsp;</td></tr>';
			}
			$map_descriptor.='</table>';
			$map_descriptor.='</td>';
			$map_descriptor.='</tr>';
		}

		$map_descriptor.='<tr class="value">';
		$map_descriptor.='<td>';
		$map_descriptor.='<table class="monsterforevolution">';
		if(file_exists($datapack_path.'monsters/'.$id.'/front.png'))
			$map_descriptor.='<tr><td><img src="'.$base_datapack_site_path.'monsters/'.$id.'/front.png" width="80" height="80" alt="'.$monster['name'][$current_lang].'" title="'.$monster['name'][$current_lang].'" /></td></tr>';
		else if(file_exists($datapack_path.'monsters/'.$id.'/front.gif'))
			$map_descriptor.='<tr><td><img src="'.$base_datapack_site_path.'monsters/'.$id.'/front.gif" width="80" height="80" alt="'.$monster['name'][$current_lang].'" title="'.$monster['name'][$current_lang].'" /></td></tr>';
		$map_descriptor.='<tr><td class="evolution_name">'.$monster['name'][$current_lang].'</td></tr>';
		$map_descriptor.='</table>';
		$map_descriptor.='</td>';
		$map_descriptor.='</tr>';

		if(count($monster['evolution_list'])>0)
		{
			$map_descriptor.='<tr class="value">';
			$map_descriptor.='<td>';
			$map_descriptor.='<table class="monsterforevolution">';
			foreach($monster['evolution_list'] as $evolution)
			{
				if(file_exists($datapack_path.'monsters/'.$evolution['evolveTo'].'/front.png'))
					$map_descriptor.='<tr><td><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($monster_meta[$evolution['evolveTo']]['name'][$current_lang]).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$evolution['evolveTo'].'/front.png" width="80" height="80" alt="'.$monster_meta[$evolution['evolveTo']]['name'][$current_lang].'" title="'.$monster_meta[$evolution['evolveTo']]['name'][$current_lang].'" /></a></td></tr>';
				else if(file_exists($datapack_path.'monsters/'.$evolution['evolveTo'].'/front.gif'))
					$map_descriptor.='<tr><td><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($monster_meta[$evolution['evolveTo']]['name'][$current_lang]).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$evolution['evolveTo'].'/front.gif" width="80" height="80" alt="'.$monster_meta[$evolution['evolveTo']]['name'][$current_lang].'" title="'.$monster_meta[$evolution['evolveTo']]['name'][$current_lang].'" /></a></td></tr>';
				$map_descriptor.='<tr><td class="evolution_name"><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($monster_meta[$evolution['evolveTo']]['name'][$current_lang]).'.html">'.$monster_meta[$evolution['evolveTo']]['name'][$current_lang].'</a></td></tr>';
				if($evolution['type']=='level')
					$map_descriptor.='<tr><td class="evolution_type">'.$translation_list[$current_lang]['At level'].' '.$evolution['level'].'</td></tr>';
				elseif($evolution['type']=='item')
				{
					if(isset($item_meta[$evolution['level']]))
					{
						$link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$evolution['level']]['name'][$current_lang]).'.html';
						$name=$item_meta[$evolution['level']]['name'][$current_lang];
						$map_descriptor.='<tr><td class="evolution_type">'.$translation_list[$current_lang]['Evolve with'].'<br /><a href="'.$link.'" title="'.$name.'">';
						if($item_meta[$evolution['level']]['image']!='')
							$map_descriptor.='<img src="'.$base_datapack_site_path.'items/'.$item_meta[$evolution['level']]['image'].'" alt="'.$name.'" title="'.$name.'" style="float:left;" />';
						$map_descriptor.=$name.'</a></td></tr>';
					}
					else
						$map_descriptor.='<tr><td class="evolution_type">'.$translation_list[$current_lang]['With unknown item'].'</td></tr>';
				}
				elseif($evolution['type']=='trade')
					$map_descriptor.='<tr><td class="evolution_type">'.$translation_list[$current_lang]['After trade'].'</td></tr>';
				else
					$map_descriptor.='<tr><td class="evolution_type">&nbsp;</td></tr>';
			}
			$map_descriptor.='</table>';
			$map_descriptor.='</td>';
			$map_descriptor.='</tr>';
		}

		$map_descriptor.='<tr>
			<th class="item_list_endline item_list_title item_list_title_type_'.$resolved_type.'">';
		if(count($monster['evolution_list'])>0)
			$map_descriptor.=$translation_list[$current_lang]['Evolve to'];
		$map_descriptor.='</th>
		</tr>
		</table>';
	}

	if(isset($monster_to_map[$id]))
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$resolved_type.'">
		<tr class="item_list_title item_list_title_type_'.$resolved_type.'">
			<th colspan="2">'.$translation_list[$current_lang]['Map'].'</th>
			<th>'.$translation_list[$current_lang]['Location'].'</th>
			<th>'.$translation_list[$current_lang]['Levels'].'</th>
			<th colspan="3">'.$translation_list[$current_lang]['Rate'].'</th>
		</tr>';


        foreach($monster_to_map[$id] as $monsterType=>$monster_list_temp)
        foreach($monster_list_temp as $maindatapackcode=>$map_list)
        {
            $full_monsterType_name='Cave';
            if(isset($layer_event[$monsterType]))
            {
                if($layer_event[$monsterType]['layer']!='')
                    $full_monsterType_name=$layer_event[$monsterType]['layer'];
                $monsterType_top=$layer_event[$monsterType]['monsterType'];
                $full_monsterType_name_top='Cave';
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
                    <th colspan="7">';
            $link='';
            $name='';
            $image='';
            if(isset($layer_meta[$monsterType_top]['item']) && $item_meta[$layer_meta[$monsterType_top]['item']])
            {
                $link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$layer_meta[$monsterType_top]['item']]['name'][$current_lang]).'.html';
                $name=$item_meta[$layer_meta[$monsterType_top]['item']]['name'][$current_lang];
                if($item_meta[$layer_meta[$monsterType_top]['item']]['image']!='')
                    $image=$base_datapack_site_path.'/items/'.$item_meta[$layer_meta[$monsterType_top]['item']]['image'];
                else
                    $image='';
                $map_descriptor.='<center><table><tr>';
                
                if($link!='')
                    $map_descriptor.='<td><a href="'.$link.'">';
                if($image!='')
                    $map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
                if($link!='')
                    $map_descriptor.='</a></td>';

                if($link!='')
                    $map_descriptor.='<td><a href="'.$link.'">';
                $map_descriptor.=$item_meta[$layer_meta[$monsterType_top]['item']]['name'][$current_lang];
                if($link!='')
                    $map_descriptor.='</a></td>';

                $map_descriptor.='</tr></table></center>';
            }
            else
                $map_descriptor.=$full_monsterType_name;
            if(isset($layer_event[$monsterType]))
            {
                if($layer_event[$monsterType]['id']=='day' && $layer_event[$monsterType]['value']=='night')
                    $map_descriptor.=' at night';
                else
                    $map_descriptor.=' condition '.$layer_event[$monsterType]['id'].' at '.$layer_event[$monsterType]['value'];
            }
            $map_descriptor.='</th>
                </tr>';
            foreach($map_list as $map=>$subdatapackcode_list)
            foreach($subdatapackcode_list as $subdatapackcode=>$monster_on_map)
            {
                $map_descriptor.='<tr class="value">';
                if(isset($maps_list[$maindatapackcode][$map]))
                {
                    if(isset($zone_meta[$maindatapackcode][$maps_list[$maindatapackcode][$map]['zone']]))
                    {
                        $map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['maps/'].$maindatapackcode.'/'.str_replace('.tmx','.html',$map).'" title="'.$maps_list[$maindatapackcode][$map]['name'][$current_lang].'">'.$maps_list[$maindatapackcode][$map]['name'][$current_lang].'</a></td>';
                        $map_descriptor.='<td>'.$zone_meta[$maindatapackcode][$maps_list[$maindatapackcode][$map]['zone']]['name'][$current_lang].'</td>';
                    }
                    else
                        $map_descriptor.='<td colspan="2"><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['maps/'].$maindatapackcode.'/'.str_replace('.tmx','.html',$map).'" title="'.$maps_list[$maindatapackcode][$map]['name'][$current_lang].'">'.$maps_list[$maindatapackcode][$map]['name'][$current_lang].'</a></td>';
                }
                else
                    $map_descriptor.='<td colspan="2">'.$translation_list[$current_lang]['Unknown map'].'</td>';
                $map_descriptor.='<td>';
                $map_descriptor.='<img src="/images/datapack-explorer/'.$full_monsterType_name_top.'.png" alt="" class="locationimg">'.$full_monsterType_name_top;
                $map_descriptor.='</td>
                <td>';
                if($monster_on_map['minLevel']==$monster_on_map['maxLevel'])
                    $map_descriptor.=$monster_on_map['minLevel'];
                else
                    $map_descriptor.=$monster_on_map['minLevel'].'-'.$monster_on_map['maxLevel'];
                $map_descriptor.='</td>';
                $map_descriptor.='<td colspan="3">'.$monster_on_map['luck'].'%</td>
                </tr>';
                break;
            }
        }

		$map_descriptor.='<tr>
			<td colspan="7" class="item_list_endline item_list_title_type_'.$resolved_type.'"></td>
		</tr>
		</table>';
	}

	$content=$template;
	$content=str_replace('${TITLE}',$monster['name'][$current_lang],$content);
	$content=str_replace('${CONTENT}',$map_descriptor,$content);
	$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
	$content=clean_html($content);
    $filedestination=$datapack_explorer_local_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($monster['name'][$current_lang]).'.html';
    if(file_exists($filedestination))
        die('The file already exists: '.$filedestination);
    filewrite($filedestination,$content);
}

$map_descriptor='';
$map_descriptor.='<table class="item_list item_list_type_normal monster_list">
<tr class="item_list_title item_list_title_type_normal">
	<th colspan="3">'.$translation_list[$current_lang]['Monster'].'</th>
</tr>';
$monster_count=0;
foreach($monster_meta as $id=>$monster)
{
	$name=$monster['name'][$current_lang];
	$link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($name).'.html';
	$map_descriptor.='<tr class="value">';
	$map_descriptor.='<td>';
	if(file_exists($datapack_path.'monsters/'.$id.'/small.png'))
		$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$id.'/small.png" width="32" height="32" alt="'.$monster['name'][$current_lang].'" title="'.$monster['name'][$current_lang].'" /></a></div>';
	else if(file_exists($datapack_path.'monsters/'.$id.'/small.gif'))
		$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$id.'/small.gif" width="32" height="32" alt="'.$monster['name'][$current_lang].'" title="'.$monster['name'][$current_lang].'" /></a></div>';
	$map_descriptor.='</td>
	<td><a href="'.$link.'">'.$name.'</a></td>';
	$map_descriptor.='<td>';
	$type_list=array();
	foreach($monster['type'] as $type)
		if(isset($type_meta[$type]))
			$type_list[]='<span class="type_label type_label_'.$type.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type.'.html">'.ucfirst($type_meta[$type]['name'][$current_lang]).'</a></span>';
	$map_descriptor.='<div class="type_label_list">'.implode(' ',$type_list).'</div>';
	$map_descriptor.='</td>';
	$map_descriptor.='</tr>';
    $monster_count++;
    if($monster_count%20==0)
    {
        $map_descriptor.='<tr>
            <td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
        </tr>
        </table>';
        $map_descriptor.='<table class="item_list item_list_type_normal monster_list">
        <tr class="item_list_title item_list_title_type_normal">
            <th colspan="3">'.$translation_list[$current_lang]['Monster'].'</th>
        </tr>';
    }
}
$map_descriptor.='<tr>
	<td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
</tr>
</table>';
$content=$template;
$content=str_replace('${TITLE}',$translation_list[$current_lang]['Monsters list'],$content);
$content=str_replace('${CONTENT}',$map_descriptor,$content);
$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
$content=clean_html($content);
$filedestination=$datapack_explorer_local_path.$translation_list[$current_lang]['monsters.html'];
if(file_exists($filedestination))
    die('The file already exists: '.$filedestination);
filewrite($filedestination,$content);
