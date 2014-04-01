<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator quests');

foreach($quests_meta as $id=>$quest)
{
	if(!is_dir($datapack_explorer_local_path.'quests/'))
		mkdir($datapack_explorer_local_path.'quests/');
	$map_descriptor='';

	$map_descriptor.='<div class="map item_details">';
		$map_descriptor.='<div class="subblock"><h1>'.$quest['name'];
		if($quest['repeatable'])
			$map_descriptor.=' (repeatable)';
		else
			$map_descriptor.=' (one time)';
		$map_descriptor.='</h1>';
		$map_descriptor.='<h2>#'.$id.'</h2>';
		$map_descriptor.='</div>';

		if(count($quest['requirements'])>0)
		{
			if(isset($quest['requirements']['quests']))
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle">Requirements</div><div class="value">';
				foreach($quest['requirements']['quests'] as $quest_id)
				{
					$map_descriptor.='Quest: <a href="'.$base_datapack_explorer_site_path.'quests/'.$quest_id.'-'.text_operation_do_for_url($quests_meta[$quest_id]['name']).'.html" title="'.$quests_meta[$quest_id]['name'].'">';
					$map_descriptor.=$quests_meta[$quest_id]['name'];
					$map_descriptor.='</a><br />';
				}
				$map_descriptor.='</div></div>';
			}
		}
		if(count($quest['steps'])>0)
		{
			foreach($quest['steps'] as $id_step=>$step)
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle">Step #'.$id_step.'</div><div class="value">';
				$map_descriptor.=$step['text'];
				if(count($step['items']))
				{
					$show_full=false;
					foreach($step['items'] as $item)
					{
						if(isset($item['monster']))
						{
							if(isset($monster_meta[$item['monster']]))
								$show_full=true;
						}
					}
					$map_descriptor.='<table class="item_list item_list_type_outdoor"><tr class="item_list_title item_list_title_type_outdoor">';
					if($show_full)
						$map_descriptor.='<th colspan="2">Item</th><th colspan="2">Monster</th><th>Luck</th></tr>';
					else
						$map_descriptor.='<th colspan="2">Item</th></tr>';
					foreach($step['items'] as $item)
					{
						$map_descriptor.='<tr class="value"><td>';
						if(isset($item_meta[$item['item']]))
						{
							$link=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$item['item']]['name']).'.html';
							$name=$item_meta[$item['item']]['name'];
							if($item_meta[$item['item']]['image']!='')
								$image=$base_datapack_site_path.'/items/'.$item_meta[$item['item']]['image'];
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
						if($item['quantity']>1)
							$quantity_text=$item['quantity'].' ';
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
							$map_descriptor.=$quantity_text.'Unknown item';
						if($link!='')
							$map_descriptor.='</a>';
						$map_descriptor.='</td>';
						if(isset($item['monster']))
						{
							if(isset($monster_meta[$item['monster']]))
							{
								$name=$monster_meta[$item['monster']]['name'];
								$link=$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($name).'.html';
								$map_descriptor.='<td>';
								if(file_exists($datapack_path.'monsters/'.$item['monster'].'/small.png'))
									$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$item['monster'].'/small.png" width="32" height="32" alt="'.$monster_meta[$item['monster']]['name'].'" title="'.$monster_meta[$item['monster']]['name'].'" /></a></div>';
								else if(file_exists($datapack_path.'monsters/'.$item['monster'].'/small.gif'))
									$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$item['monster'].'/small.gif" width="32" height="32" alt="'.$monster_meta[$item['monster']]['name'].'" title="'.$monster_meta[$item['monster']]['name'].'" /></a></div>';
								$map_descriptor.='</td>
								<td><a href="'.$link.'">'.$name.'</a></td>';
								$map_descriptor.='<td>'.$item['rate'].'%</td>';
							}
							else if($show_full)
								$map_descriptor.='<td></td><td></td><td></td>';
						}
						else if($show_full)
							$map_descriptor.='<td></td><td></td><td></td>';
						$map_descriptor.='</tr>';
					}
					if($show_full)
						$map_descriptor.='<tr>
						<td colspan="5" class="item_list_endline item_list_title_type_outdoor"></td>
						</tr></table>';
					else
						$map_descriptor.='<tr>
						<td colspan="2" class="item_list_endline item_list_title_type_outdoor"></td>
						</tr></table>';
					$map_descriptor.='<br />';
				}
				$map_descriptor.='</div></div>';
			}
		}
		if(count($quest['rewards'])>0)
		{
			if(isset($quest['rewards']['items']) || isset($quest['rewards']['reputation']))
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle">Rewards</div><div class="value">';
				if(isset($quest['rewards']['items']))
				{
					$map_descriptor.='<table class="item_list item_list_type_outdoor"><tr class="item_list_title item_list_title_type_outdoor">
					<th colspan="2">Item</th></tr>';
					foreach($quest['rewards']['items'] as $item)
					{
						$map_descriptor.='<tr class="value"><td>';
						if(isset($item_meta[$item['item']]))
						{
							$link=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$item['item']]['name']).'.html';
							$name=$item_meta[$item['item']]['name'];
							if($item_meta[$item['item']]['image']!='')
								$image=$base_datapack_site_path.'/items/'.$item_meta[$item['item']]['image'];
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
						if($item['quantity']>1)
							$quantity_text=$item['quantity'].' ';
						
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
							$map_descriptor.=$quantity_text.'Unknown item';
						if($link!='')
							$map_descriptor.='</a>';
						$map_descriptor.='</td></tr>';
					}
					$map_descriptor.='<tr>
					<td colspan="2" class="item_list_endline item_list_title_type_outdoor"></td>
					</tr></table>';
				}
				if(isset($quest['rewards']['reputation']))
					foreach($quest['rewards']['reputation'] as $reputation)
					{
						if($reputation['point']<0)
							$map_descriptor.='Less reputation in: '.$reputation['type'];
						else
							$map_descriptor.='More reputation in: '.$reputation['type'];
					}
				$map_descriptor.='</div></div>';
			}
		}
	$map_descriptor.='</div>';

	$content=$template;
	$content=str_replace('${TITLE}',$quest['name'],$content);
	$content=str_replace('${CONTENT}',$map_descriptor,$content);
	$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
	$content=preg_replace("#[\r\n\t]+#isU",'',$content);
	filewrite($datapack_explorer_local_path.'quests/'.$id.'-'.text_operation_do_for_url($quest['name']).'.html',$content);
}

$map_descriptor='';

$map_descriptor.='<table class="item_list item_list_type_normal">
<tr class="item_list_title item_list_title_type_normal">
	<th>Quests</th>
</tr>';
foreach($quests_meta as $id=>$quest)
{
	$map_descriptor.='<tr class="value">
	<td><a href="'.$base_datapack_explorer_site_path.'quests/'.$id.'-'.text_operation_do_for_url($quest['name']).'.html" title="'.$quest['name'].'">'.$quest['name'].'</a></td>';
	$map_descriptor.='</tr>';
}
$map_descriptor.='<tr>
	<td colspan="1" class="item_list_endline item_list_title_type_normal"></td>
</tr>
</table>';

$content=$template;
$content=str_replace('${TITLE}','Quests list',$content);
$content=str_replace('${CONTENT}',$map_descriptor,$content);
$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
$content=preg_replace("#[\r\n\t]+#isU",'',$content);
filewrite($datapack_explorer_local_path.'quests.html',$content);