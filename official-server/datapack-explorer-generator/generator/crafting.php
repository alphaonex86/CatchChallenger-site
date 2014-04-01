<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator crafting'."\n");

$map_descriptor='';

$map_descriptor.='<table class="item_list item_list_type_normal">
<tr class="item_list_title item_list_title_type_normal">
	<th colspan="2">Item</th>
	<th>Price</th>
</tr>';
foreach($crafting_meta as $id=>$crafting)
{
	if(isset($item_meta[$crafting['itemToLearn']]))
	{
		$link=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$crafting['itemToLearn']]['name']).'.html';
		//$link=$base_datapack_explorer_site_path.'crafting/'.text_operation_do_for_url($item_meta[$crafting['itemToLearn']]['name']).'.html';
		$name=$item_meta[$crafting['itemToLearn']]['name'];
		if($item_meta[$crafting['itemToLearn']]['image']!='')
			$image=$base_datapack_site_path.'/items/'.$item_meta[$crafting['itemToLearn']]['image'];
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
		$map_descriptor.='<td>'.$item_meta[$crafting['itemToLearn']]['price'].'$</td>
		</tr>';
	}
	else
		$map_descriptor.='<tr class="value"><td colspan="3">Item to learn missing: '.$crafting['itemToLearn'].'</td></tr>';
}
$map_descriptor.='<tr>
	<td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
</tr>
</table>';
$content=$template;
$content=str_replace('${TITLE}','Crafting list',$content);
$content=str_replace('${CONTENT}',$map_descriptor,$content);
$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
$content=preg_replace("#[\r\n\t]+#isU",'',$content);
filewrite($datapack_explorer_local_path.'crafting.html',$content); 
