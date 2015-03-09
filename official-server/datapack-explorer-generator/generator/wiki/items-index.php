<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator items index'."\n");

$map_descriptor='';

$item_by_group=array();
foreach($item_meta as $id=>$item)
{
	if($item['group']!='')
		$group_name=$item_group[$item['group']]['name'][$current_lang];
	else if(isset($item_to_skill_of_monster[$id]))
		$group_name='Learn'."\n";
	else if(isset($item_to_crafting[$id]))
		$group_name='Crafting'."\n";
	else if(isset($item_to_plant[$id]))
		$group_name='Plant'."\n";
	else if(isset($item_to_regeneration[$id]))
		$group_name='Regeneration'."\n";
	else if(isset($item_to_trap[$id]))
		$group_name='Trap'."\n";
	else if(isset($item_to_evolution[$id]))
		$group_name='Evolution'."\n";
	else
		$group_name='Items'."\n";
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
		<th colspan="2">'.$translation_list[$current_lang]['Item'].'</th>
		<th>'.$translation_list[$current_lang]['Price'].'</th>
	</tr>'."\n";
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
			</table>'."\n";
			$map_descriptor.='<table class="item_list item_list_type_normal map_list">
			<tr class="item_list_title item_list_title_type_normal">
				<th colspan="3">'.$group_name.'</th>
			</tr>
			<tr class="item_list_title item_list_title_type_normal">
				<th colspan="2">'.$translation_list[$current_lang]['Item'].'</th>
				<th>'.$translation_list[$current_lang]['Price'].'</th>
			</tr>'."\n";
			$item_count_list=1;
		}
		$link=$base_datapack_site_http.$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item['name'][$current_lang]).'.html'."\n";
		$name=$item['name'][$current_lang];
		if($item['image']!='' && file_exists($datapack_path.'items/'.$item['image']))
			$image=$base_datapack_site_http.$base_datapack_site_path.'/items/'.$item['image'];
		else
			$image='';
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
		if($item['price']>0)
			$map_descriptor.='<td>'.$item['price'].'$</td>'."\n";
		else
			$map_descriptor.='<td>&nbsp;</td>'."\n";
		$map_descriptor.='</tr>'."\n";

	}
	$map_descriptor.='<tr>
		<td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
	</tr>
	</table>'."\n";
}

savewikipage('Template:Items_list',$map_descriptor,false);$map_descriptor='';

$lang_template='';
if(count($wikivarsapp)>1)
{
    foreach($wikivarsapp as $wikivars2)
        if($wikivars2['lang']!=$current_lang)
            $lang_template.='[['.$wikivars2['lang'].':'.$translation_list[$wikivars2['lang']]['Items list'].']]'."\n";
    savewikipage('Template:Items_LANG',$lang_template,false);$lang_template='';
    $map_descriptor.='{{Template:Items_LANG}}'."\n";
}

$map_descriptor.='{{Template:Items_list}}'."\n";
savewikipage($translation_list[$current_lang]['Items list'],$map_descriptor,!$wikivars['generatefullpage']);
