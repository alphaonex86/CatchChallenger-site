<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator items index'."\n");

$map_descriptor='';

$item_by_group=array();
foreach($item_meta as $id=>$item)
{
	if($item['group']!='')
		$group_name=$item_group[$item['group']];
	else if(isset($item_to_skill_of_monster[$id]))
		$group_name='Learn';
	else if(isset($item_to_crafting[$id]))
		$group_name='Crafting';
	else if(isset($item_to_plant[$id]))
		$group_name='Plant';
	else if(isset($item_to_regeneration[$id]))
		$group_name='Regeneration';
	else if(isset($item_to_trap[$id]))
		$group_name='Trap';
	else if(isset($item_to_evolution[$id]))
		$group_name='Evolution';
	else
		$group_name='Items';
	$item_by_group[$group_name][$id]=$item;
}
foreach($item_by_group as $group_name=>$item_meta_temp)
{
	$item_count_list=0;
	$map_descriptor.='<table class="item_list item_list_type_normal map_list">
	<tr class="item_list_title item_list_title_type_normal">
		<th colspan="3">'.$group_name.'</th>
	</tr>
	<tr class="item_list_title item_list_title_type_normal">
		<th colspan="2">Item</th>
		<th>Price</th>
	</tr>';
	$max=15;
	if(count($item_meta_temp)>$max)
	{
		$number_of_table=(int)ceil((float)count($item_meta_temp)/(float)$max);
		$max=(int)ceil((float)count($item_meta_temp)/(float)$number_of_table);
	}
	foreach($item_meta_temp as $id=>$item)
	{
		$item_count_list++;
		//to prevent too long list
		if($item_count_list>$max)
		{
			$map_descriptor.='<tr>
				<td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
			</tr>
			</table>';
			$map_descriptor.='<table class="item_list item_list_type_normal map_list">
			<tr class="item_list_title item_list_title_type_normal">
				<th colspan="3">'.$group_name.'</th>
			</tr>
			<tr class="item_list_title item_list_title_type_normal">
				<th colspan="2">Item</th>
				<th>Price</th>
			</tr>';
			$item_count_list=1;
		}
		$link=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item['name']).'.html';
		$name=$item['name'];
		if($item['image']!='' && file_exists($datapack_path.'items/'.$item['image']))
			$image=$base_datapack_site_path.'/items/'.$item['image'];
		else
			$image='';
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
			$map_descriptor.='Unknown item';
		if($link!='')
			$map_descriptor.='</a>';
		$map_descriptor.='</td>';
		if($item['price']>0)
			$map_descriptor.='<td>'.$item['price'].'$</td>';
		else
			$map_descriptor.='<td>&nbsp;</td>';
		$map_descriptor.='</tr>';

	}
	$map_descriptor.='<tr>
		<td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
	</tr>
	</table>';
}

$content=$template;
$content=str_replace('${TITLE}','Items list',$content);
$content=str_replace('${CONTENT}',$map_descriptor,$content);
$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
$content=preg_replace("#[\r\n\t]+#isU",'',$content);
filewrite($datapack_explorer_local_path.'items.html',$content);