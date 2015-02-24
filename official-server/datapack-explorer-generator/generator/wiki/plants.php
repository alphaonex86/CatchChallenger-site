<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator plants'."\n");

$map_descriptor='';
$map_descriptor.='<table class="item_list item_list_type_normal">
<tr class="item_list_title item_list_title_type_normal">
	<th colspan="2">Plant</th>
	<th colspan="2">Time to grow</th>
	<th>Fruits produced</th>
</tr>'."\n";
foreach($plant_meta as $id=>$plant)
{
	$link=$base_datapack_site_http.$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$plant['itemUsed']]['name']).'.html';
	$name=$item_meta[$plant['itemUsed']]['name'];
	if($item_meta[$plant['itemUsed']]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$plant['itemUsed']]['image']))
		$image=$base_datapack_site_http.$base_datapack_site_path.'/items/'.$item_meta[$plant['itemUsed']]['image'];
	else
		$image='';
	$map_descriptor.='<tr class="value">
	<td>'."\n";
	if($image!='')
	{
		if($link!='')
			$map_descriptor.='[[Items:'.$name.'|';
		$map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
		if($link!='')
			$map_descriptor.=']]'."\n";
	}
	$map_descriptor.='</td>
	<td>'."\n";
	if($link!='')
		$map_descriptor.='[[Items:'.$name.'|';
	if($name!='')
		$map_descriptor.=$name;
	else
		$map_descriptor.='Unknown item';
	if($link!='')
		$map_descriptor.=']]'."\n";
	$map_descriptor.='</td>'."\n";
	$map_descriptor.='<td>'."\n";
	if(file_exists($datapack_path.'plants/'.$id.'.png'))
		$map_descriptor.='<img src="'.$base_datapack_site_http.$base_datapack_site_path.'plants/'.$id.'.png" width="80" height="32" alt="'.$name.'" title="'.$name.'" />'."\n";
	$map_descriptor.='</td>'."\n";
	$map_descriptor.='<td><b>'.($plant['fruits']/60).'</b> minutes</td>'."\n";
	$map_descriptor.='<td>'.$plant['quantity'].'</td>'."\n";
	$map_descriptor.='</tr>'."\n";
}
$map_descriptor.='<tr>
	<td colspan="5" class="item_list_endline item_list_title_type_normal"></td>
</tr>
</table>'."\n";

savewikipage('Template:plants_list',$map_descriptor);$map_descriptor='';

if($wikivarsapp['generatefullpage'])
{
    $map_descriptor.='{{Template:plants_list}}'."\n";
    savewikipage('Plants_list',$map_descriptor);
}
