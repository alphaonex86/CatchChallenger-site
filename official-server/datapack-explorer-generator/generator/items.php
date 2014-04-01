<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator items');

foreach($item_meta as $id=>$item)
{
	if(!is_dir($datapack_explorer_local_path.'items/'))
		mkdir($datapack_explorer_local_path.'items/');
	$map_descriptor='';

	$map_descriptor.='<div class="map item_details">';
		$map_descriptor.='<div class="subblock"><h1>'.$item['name'].'</h1>';
		$map_descriptor.='<h2>#'.$id.'</h2>';
		$map_descriptor.='</div>';
		$map_descriptor.='<div class="value datapackscreenshot">';
		if($item['image']!='' && file_exists($datapack_path.'items/'.$item['image']))
			$map_descriptor.='<img src="'.$base_datapack_site_path.'items/'.$item['image'].'" width="24" height="24" alt="'.$item['name'].'" title="'.$item['name'].'" />';
		$map_descriptor.='</div>';
		if($item['price']>0)
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Price</div><div class="value">'.$item['price'].'$</div></div>';
		else
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Price</div><div class="value">Can\'t be sold</div></div>';
		if($item['description']!='')
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Description</div><div class="value">'.$item['description'].'</div></div>';
		if(isset($item['trap']))
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Trap</div><div class="value">Bonus rate: '.$item['trap'].'x</div></div>';
		if(isset($item['repel']))
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Repel</div><div class="value">Repel the monsters during '.$item['repel'].' steps</div></div>';
		if(isset($item_to_plant[$id]) && isset($plant_meta[$item_to_plant[$id]]))
		{
			$image='';
			if(file_exists($datapack_path.'plants/'.$item_to_plant[$id].'.png'))
				$image.=$base_datapack_site_path.'plants/'.htmlspecialchars($item_to_plant[$id]).'.png';
			elseif(file_exists($datapack_path.'plants/'.$item_to_plant[$id].'.gif'))
				$image.=$base_datapack_site_path.'plants/'.htmlspecialchars($item_to_plant[$id]).'.gif';
			if($image!='')
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle">Plant</div><div class="value">';
				$map_descriptor.='After <b>'.($plant_meta[$item_to_plant[$id]]['fruits']/60).'</b> minutes you will have <b>'.$plant_meta[$item_to_plant[$id]]['quantity'].'</b> fruits';
				$map_descriptor.='<table class="item_list item_list_type_normal">
				<tr class="item_list_title item_list_title_type_normal">
					<th>Seed</th>
					<th>Sprouted</th>
					<th>Taller</th>
					<th>Flowering</th>
					<th>Fruits</th>
				</tr><tr class="value">';
				$map_descriptor.='<td><center><div style="width:16px;height:32px;background-image:url(\''.$image.'\');background-repeat:no-repeat;background-position:0px 0px;"></div></center></td>';
				$map_descriptor.='<td><center><div style="width:16px;height:32px;background-image:url(\''.$image.'\');background-repeat:no-repeat;background-position:-16px 0px;"></div></center></td>';
				$map_descriptor.='<td><center><div style="width:16px;height:32px;background-image:url(\''.$image.'\');background-repeat:no-repeat;background-position:-32px 0px;"></div></center></td>';
				$map_descriptor.='<td><center><div style="width:16px;height:32px;background-image:url(\''.$image.'\');background-repeat:no-repeat;background-position:-48px 0px;"></div></center></td>';
				$map_descriptor.='<td><center><div style="width:16px;height:32px;background-image:url(\''.$image.'\');background-repeat:no-repeat;background-position:-64px 0px;"></div></center></td>';
				$map_descriptor.='</tr><tr>
				<td colspan="5" class="item_list_endline item_list_title_type_normal"></td>
				</tr>
				</table>';
				$map_descriptor.='</div></div>';
			}
		}
		if(isset($item['effect']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Effect</div><div class="value"><ul>';
			if(isset($item['effect']['regeneration']))
			{
				if($item['effect']['regeneration']=='all')
					$map_descriptor.='<li>Regenerate all the hp</li>';
				else
					$map_descriptor.='<li>Regenerate '.$item['effect']['regeneration'].' hp</li>';
			}
			if(isset($item['effect']['buff']))
			{
				if($item['effect']['buff']=='all')
					$map_descriptor.='<li>Remove all debuff</li>';
				else
					$map_descriptor.='<li>Remove the debuff '.$item['effect']['buff'].'</li>';
			}
			$map_descriptor.='</ul></div></div>';
		}

		if(isset($item_to_crafting[$id]))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Do the item</div><div class="value">';
			$map_descriptor.='<a href="'.$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$item_to_crafting[$id]['doItemId']]['name']).'.html" title="'.$item_meta[$item_to_crafting[$id]['doItemId']]['name'].'">';
				$map_descriptor.='<table><tr><td>';
				if($item_meta[$item_to_crafting[$id]['doItemId']]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$item_to_crafting[$id]['doItemId']]['image']))
					$map_descriptor.='<img src="'.$base_datapack_site_path.'items/'.$item_meta[$item_to_crafting[$id]['doItemId']]['image'].'" width="24" height="24" alt="'.$item_meta[$item_to_crafting[$id]['doItemId']]['name'].'" title="'.$item_meta[$item_to_crafting[$id]['doItemId']]['name'].'" />';
				$map_descriptor.='</td><td>'.$item_meta[$item_to_crafting[$id]['doItemId']]['name'].'</td></tr></table>';
			$map_descriptor.='</a>';
			$map_descriptor.='</div></div>';

			$map_descriptor.='<div class="subblock"><div class="valuetitle">Material</div><div class="value">';
			foreach($item_to_crafting[$id]['material'] as $material=>$quantity)
			{
				$map_descriptor.='<a href="'.$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$material]['name']).'.html" title="'.$item_meta[$material]['name'].'">';
					$map_descriptor.='<table><tr><td>';
					if($item_meta[$material]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$material]['image']))
						$map_descriptor.='<img src="'.$base_datapack_site_path.'items/'.$item_meta[$material]['image'].'" width="24" height="24" alt="'.$item_meta[$material]['name'].'" title="'.$item_meta[$material]['name'].'" />';
					$map_descriptor.='</td><td>';
				if($quantity>1)
					$map_descriptor.=$quantity.'x ';
				$map_descriptor.=$item_meta[$material]['name'].'</td></tr></table>';
				$map_descriptor.='</a>';
			}
			$map_descriptor.='</div></div>';
		}

		if(isset($item_to_evolution[$id]) && count($item_to_evolution[$id])>0)
		{
			$count_evol=0;
			foreach($item_to_evolution[$id] as $evolution)
			{
				if(isset($monster_meta[$evolution['from']]) && isset($monster_meta[$evolution['to']]))
					$count_evol++;
			}
			foreach($item_to_evolution[$id] as $evolution)
			{
				if(isset($monster_meta[$evolution['from']]) && isset($monster_meta[$evolution['to']]))
				{
					$map_descriptor.='<table class="item_list item_list_type_normal">
					<tr class="item_list_title item_list_title_type_normal">
						<th colspan="'.$count_evol.'">Evolve from</th>
					</tr>';
					$map_descriptor.='<tr class="value">';
					$map_descriptor.='<td>';
					$map_descriptor.='<table class="monsterforevolution">';
					if(file_exists($datapack_path.'monsters/'.$evolution['from'].'/front.png'))
						$map_descriptor.='<tr><td><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_meta[$evolution['from']]['name']).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$evolution['from'].'/front.png" width="80" height="80" alt="'.$monster_meta[$evolution['from']]['name'].'" title="'.$monster_meta[$evolution['from']]['name'].'" /></a></td></tr>';
					else if(file_exists($datapack_path.'monsters/'.$evolution['from'].'/front.gif'))
						$map_descriptor.='<tr><td><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_meta[$evolution['from']]['name']).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$evolution['from'].'/front.gif" width="80" height="80" alt="'.$monster_meta[$evolution['from']]['name'].'" title="'.$monster_meta[$evolution['from']]['name'].'" /></a></td></tr>';
					$map_descriptor.='<tr><td class="evolution_name"><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_meta[$evolution['from']]['name']).'.html">'.$monster_meta[$evolution['from']]['name'].'</a></td></tr>';
					$map_descriptor.='</table>';
					$map_descriptor.='</td>';
					$map_descriptor.='</tr>';

					$map_descriptor.='<tr><td class="evolution_type">Evolve with<br /><a href="'.$link.'" title="'.$item_meta[$id]['name'].'">';
					if($item_meta[$id]['image']!='')
						$map_descriptor.='<img src="'.$base_datapack_site_path.'items/'.$item_meta[$id]['image'].'" alt="'.$item_meta[$id]['name'].'" title="'.$item_meta[$id]['name'].'" style="float:left;" />';
					$map_descriptor.=$item_meta[$id]['name'].'</a></td></tr>';

					$map_descriptor.='<tr class="value">';
					$map_descriptor.='<td>';
					$map_descriptor.='<table class="monsterforevolution">';
					if(file_exists($datapack_path.'monsters/'.$evolution['to'].'/front.png'))
						$map_descriptor.='<tr><td><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_meta[$evolution['to']]['name']).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$evolution['to'].'/front.png" width="80" height="80" alt="'.$monster_meta[$evolution['to']]['name'].'" title="'.$monster_meta[$evolution['to']]['name'].'" /></a></td></tr>';
					else if(file_exists($datapack_path.'monsters/'.$evolution['to'].'/front.gif'))
						$map_descriptor.='<tr><td><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_meta[$evolution['to']]['name']).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$evolution['to'].'/front.gif" width="80" height="80" alt="'.$monster_meta[$evolution['to']]['name'].'" title="'.$monster_meta[$evolution['to']]['name'].'" /></a></td></tr>';
					$map_descriptor.='<tr><td class="evolution_name"><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_meta[$evolution['to']]['name']).'.html">'.$monster_meta[$evolution['to']]['name'].'</a></td></tr>';
					$map_descriptor.='</table>';
					$map_descriptor.='</td>';
					$map_descriptor.='</tr>';

					$map_descriptor.='<tr>
						<th colspan="'.$count_evol.'" class="item_list_endline item_list_title item_list_title_type_normal">Evolve to</th>
					</tr>
					</table>';
				}
			}
		}

	$map_descriptor.='</div>';

	if(isset($item_to_monster[$id]))
	{
		$map_descriptor.='<table class="item_list item_list_type_normal">
		<tr class="item_list_title item_list_title_type_normal">
			<th colspan="2">Monster</th>
			<th>Quantity</th>
			<th>Luck</th>
		</tr>';
		foreach($item_to_monster[$id] as $item_to_monster_list)
		{
			if(isset($monster_meta[$item_to_monster_list['monster']]))
			{
				if($item_to_monster_list['quantity_min']!=$item_to_monster_list['quantity_max'])
					$quantity_text=$item_to_monster_list['quantity_min'].' to '.$item_to_monster_list['quantity_max'];
				else
					$quantity_text=$item_to_monster_list['quantity_min'];
				$name=$monster_meta[$item_to_monster_list['monster']]['name'];
				$link=$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($name).'.html';
				$map_descriptor.='<tr class="value">';
				$map_descriptor.='<td>';
				if(file_exists($datapack_path.'monsters/'.$item_to_monster_list['monster'].'/small.png'))
					$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$item_to_monster_list['monster'].'/small.png" width="32" height="32" alt="'.$monster_meta[$item_to_monster_list['monster']]['name'].'" title="'.$monster_meta[$item_to_monster_list['monster']]['name'].'" /></a></div>';
				else if(file_exists($datapack_path.'monsters/'.$item_to_monster_list['monster'].'/small.gif'))
					$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$item_to_monster_list['monster'].'/small.gif" width="32" height="32" alt="'.$monster_meta[$item_to_monster_list['monster']]['name'].'" title="'.$monster_meta[$item_to_monster_list['monster']]['name'].'" /></a></div>';
				$map_descriptor.='</td>
				<td><a href="'.$link.'">'.$name.'</a></td>';
				$map_descriptor.='<td>'.$quantity_text.'</td>';
				$map_descriptor.='<td>'.$item_to_monster_list['luck'].'%</td>';
				$map_descriptor.='</tr>';
			}
		}
		$map_descriptor.='<tr>
			<td colspan="4" class="item_list_endline item_list_title_type_normal"></td>
		</tr>
		</table>';
	}

	if(isset($items_to_quests[$id]))
	{
		$map_descriptor.='<table class="item_list item_list_type_normal">
		<tr class="item_list_title item_list_title_type_normal">
			<th>Quests</th>
			<th>Quantity rewarded</th>
		</tr>';
		foreach($items_to_quests[$id] as $quest_id=>$quantity)
		{
			if(isset($quests_meta[$quest_id]))
			{
				$map_descriptor.='<tr class="value">';
				$map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.'quests/'.$quest_id.'-'.text_operation_do_for_url($quests_meta[$quest_id]['name']).'.html" title="'.$quests_meta[$quest_id]['name'].'">';
				$map_descriptor.=$quests_meta[$quest_id]['name'];
				$map_descriptor.='</a></td>';
				$map_descriptor.='<td>'.$quantity.'</td>';
				$map_descriptor.='</tr>';
			}
		}
		$map_descriptor.='<tr>
			<td colspan="2" class="item_list_endline item_list_title_type_normal"></td>
		</tr>
		</table>';
	}
	if(isset($items_to_quests_for_step[$id]))
	{
		$full_details=false;
		foreach($items_to_quests_for_step[$id] as $items_to_quests_for_step_details)
		{
			if(isset($quests_meta[$items_to_quests_for_step_details['quest']]))
				if(isset($items_to_quests_for_step_details['monster']) && isset($monster_meta[$items_to_quests_for_step_details['monster']]))
					$full_details=true;
		}
		if($full_details)
			$map_descriptor.='<table class="item_list item_list_type_normal">
			<tr class="item_list_title item_list_title_type_normal">
				<th>Quests</th>
				<th>Quantity needed</th>
				<th colspan="2">Monster</th>
				<th>Luck</th>
			</tr>';
		else
			$map_descriptor.='<table class="item_list item_list_type_normal">
			<tr class="item_list_title item_list_title_type_normal">
				<th>Quests</th>
				<th>Quantity needed</th>
			</tr>';
		foreach($items_to_quests_for_step[$id] as $items_to_quests_for_step_details)
		{
			if(isset($quests_meta[$items_to_quests_for_step_details['quest']]))
			{
				$map_descriptor.='<tr class="value">';
				$map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.'quests/'.$items_to_quests_for_step_details['quest'].'-'.text_operation_do_for_url($quests_meta[$items_to_quests_for_step_details['quest']]['name']).'.html" title="'.$quests_meta[$items_to_quests_for_step_details['quest']]['name'].'">';
				$map_descriptor.=$quests_meta[$items_to_quests_for_step_details['quest']]['name'];
				$map_descriptor.='</a></td>';
				$map_descriptor.='<td>'.$items_to_quests_for_step_details['quantity'].'</td>';
				if(isset($items_to_quests_for_step_details['monster']) && isset($monster_meta[$items_to_quests_for_step_details['monster']]))
				{
					$name=$monster_meta[$items_to_quests_for_step_details['monster']]['name'];
					$link=$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($name).'.html';
					$map_descriptor.='<td>';
					if(file_exists($datapack_path.'monsters/'.$items_to_quests_for_step_details['monster'].'/small.png'))
						$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$items_to_quests_for_step_details['monster'].'/small.png" width="32" height="32" alt="'.$monster_meta[$items_to_quests_for_step_details['monster']]['name'].'" title="'.$monster_meta[$items_to_quests_for_step_details['monster']]['name'].'" /></a></div>';
					else if(file_exists($datapack_path.'monsters/'.$items_to_quests_for_step_details['monster'].'/small.gif'))
						$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$items_to_quests_for_step_details['monster'].'/small.gif" width="32" height="32" alt="'.$monster_meta[$items_to_quests_for_step_details['monster']]['name'].'" title="'.$monster_meta[$items_to_quests_for_step_details['monster']]['name'].'" /></a></div>';
					$map_descriptor.='</td>
					<td><a href="'.$link.'">'.$name.'</a></td>';
					$map_descriptor.='<td>'.$items_to_quests_for_step_details['rate'].'%</td>';
				}
				else if($full_details)
					$map_descriptor.='<td></td><td></td><td></td>';
				$map_descriptor.='</tr>';
			}
		}
		if($full_details)
			$map_descriptor.='<tr>
				<td colspan="5" class="item_list_endline item_list_title_type_normal"></td>
			</tr>
			</table>';
		else
			$map_descriptor.='<tr>
				<td colspan="2" class="item_list_endline item_list_title_type_normal"></td>
			</tr>
			</table>';
	}

	if(isset($item_consumed_by[$id]))
	{
		$map_descriptor.='<table class="item_list item_list_type_normal">
		<tr class="item_list_title item_list_title_type_normal">
			<th>Resource of industry</th>
			<th>Quantity</th>
		</tr>';
		foreach($item_consumed_by[$id] as $industry_id=>$quantity)
		{
			if(isset($industries_meta[$industry_id]))
			{
				$map_descriptor.='<tr class="value">';
				$map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.'industries/'.$industry_id.'.html">';
				$map_descriptor.='Industry #'.$industry_id;
				$map_descriptor.='</a></td>';
				$map_descriptor.='<td>'.$quantity.'</td>';
				$map_descriptor.='</tr>';
			}
		}
		$map_descriptor.='<tr>
			<td colspan="2" class="item_list_endline item_list_title_type_normal"></td>
		</tr>
		</table>';
	}

	if(isset($item_produced_by[$id]))
	{
		$map_descriptor.='<table class="item_list item_list_type_normal">
		<tr class="item_list_title item_list_title_type_normal">
			<th>Product of industry</th>
			<th>Quantity</th>
		</tr>';
		foreach($item_produced_by[$id] as $industry_id=>$quantity)
		{
			if(isset($industries_meta[$industry_id]))
			{
				$map_descriptor.='<tr class="value">';
				$map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.'industries/'.$industry_id.'.html">';
				$map_descriptor.='Industry #'.$industry_id;
				$map_descriptor.='</a></td>';
				$map_descriptor.='<td>'.$quantity.'</td>';
				$map_descriptor.='</tr>';
			}
		}
		$map_descriptor.='<tr>
			<td colspan="2" class="item_list_endline item_list_title_type_normal"></td>
		</tr>
		</table>';
	}

	if(isset($item_to_skill_of_monster[$id]))
	{
		$map_descriptor.='<table class="item_list item_list_type_normal">
		<tr class="item_list_title item_list_title_type_normal">
			<th colspan="3">Monster</th>
			<th>Skill</th>
			<th>Type</th>
		</tr>';
		$attack_list_byitem=$monster['attack_list_byitem'];
		foreach($item_to_skill_of_monster[$id] as $entry)
		{
			$map_descriptor.='<tr class="value">';
			if(isset($monster_meta[$entry['monster']]))
			{
				$name=$monster_meta[$entry['monster']]['name'];
				$link=$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($name).'.html';
				$map_descriptor.='<td>';
				if(file_exists($datapack_path.'monsters/'.$entry['monster'].'/small.png'))
					$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$entry['monster'].'/small.png" width="32" height="32" alt="'.$monster_meta[$entry['monster']]['name'].'" title="'.$monster_meta[$entry['monster']]['name'].'" /></a></div>';
				else if(file_exists($datapack_path.'monsters/'.$entry['monster'].'/small.gif'))
					$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$entry['monster'].'/small.gif" width="32" height="32" alt="'.$monster_meta[$entry['monster']]['name'].'" title="'.$monster_meta[$entry['monster']]['name'].'" /></a></div>';
				$map_descriptor.='</td>
				<td><a href="'.$link.'">'.$name.'</a></td>';
				$type_list=array();
				foreach($monster_meta[$entry['monster']]['type'] as $type_monster)
					if(isset($type_meta[$type_monster]))
						$type_list[]='<span class="type_label type_label_'.$type_monster.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_monster.'.html">'.$type_meta[$type_monster]['english_name'].'</a></span>';
				$map_descriptor.='<td><div class="type_label_list">'.implode(' ',$type_list).'</div></td>';
			}
			if(isset($skill_meta[$entry['id']]))
			{
				
				$map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.'monsters/skills/'.text_operation_do_for_url($skill_meta[$entry['id']]['name']).'.html">'.$skill_meta[$entry['id']]['name'];
				if($entry['attack_level']>1)
					$map_descriptor.=' at level '.$entry['attack_level'];
				$map_descriptor.='</a></td>';
				if(isset($type_meta[$skill_meta[$entry['id']]['type']]))
					$map_descriptor.='<td><span class="type_label type_label_'.$skill_meta[$entry['id']]['type'].'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$skill_meta[$entry['id']]['type'].'.html">'.$type_meta[$skill_meta[$entry['id']]['type']]['english_name'].'</a></span></td>';
				else
					$map_descriptor.='<td>&nbsp;</td>';
			}
			$map_descriptor.='</tr>';
		}
		$map_descriptor.='<tr>
			<td colspan="5" class="item_list_endline item_list_title_type_normal"></td>
		</tr>
		</table>';
	}

	$content=$template;
	$content=str_replace('${TITLE}',$item['name'],$content);
	$content=str_replace('${CONTENT}',$map_descriptor,$content);
	$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
	$content=preg_replace("#[\r\n\t]+#isU",'',$content);
	filewrite($datapack_explorer_local_path.'items/'.text_operation_do_for_url($item['name']).'.html',$content);
}