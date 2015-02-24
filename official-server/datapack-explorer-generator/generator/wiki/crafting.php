<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator crafting'."\n");

$map_descriptor='';

$map_descriptor.='<table class="item_list item_list_type_normal">
<tr class="item_list_title item_list_title_type_normal">
	<th colspan="2">Item</th>
<th>Material</th>
<th>Product</th>
	<th>Price</th>
</tr>'."\n";
foreach($crafting_meta as $id=>$crafting)
{
	if(isset($item_meta[$crafting['itemToLearn']]))
	{
		$link=$base_datapack_site_http.$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$crafting['itemToLearn']]['name']).'.html';
		//$link=$base_datapack_site_http.$base_datapack_explorer_site_path.'crafting/'.text_operation_do_for_url($item_meta[$crafting['itemToLearn']]['name']).'.html';
		$name=$item_meta[$crafting['itemToLearn']]['name'];
		if($item_meta[$crafting['itemToLearn']]['image']!='')
			$image=$base_datapack_site_http.$base_datapack_site_path.'/items/'.$item_meta[$crafting['itemToLearn']]['image'];
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
				$map_descriptor.=']]';
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
        foreach($crafting['material'] as $material=>$quantity)
        {
            $map_descriptor.='<table><tr><td>'."\n";
            if($item_meta[$material]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$material]['image']))
                $map_descriptor.='[[Items:'.$item_meta[$material]['name'].'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'items/'.$item_meta[$material]['image'].'" width="24" height="24" alt="'.$item_meta[$material]['name'].'" title="'.$item_meta[$material]['name'].'" />]]'."\n";
            $map_descriptor.='</td><td>[[Items:'.$item_meta[$material]['name'].'|'."\n";
            if($quantity>1)
                $map_descriptor.=$quantity.'x ';
            $map_descriptor.=$item_meta[$material]['name'].']]</td></tr></table>'."\n";
        }
        $map_descriptor.='</td>'."\n";

        $map_descriptor.='<td>'."\n";
        $map_descriptor.='<table><tr><td>'."\n";
        if($item_meta[$crafting['doItemId']]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$crafting['doItemId']]['image']))
            $map_descriptor.='[[Items:'.$item_meta[$crafting['doItemId']]['name'].'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'items/'.$item_meta[$crafting['doItemId']]['image'].'" width="24" height="24" alt="'.$item_meta[$crafting['doItemId']]['name'].'" title="'.$item_meta[$crafting['doItemId']]['name'].'" />]]'."\n";
        $map_descriptor.='</td><td>[[Items:'.$item_meta[$crafting['doItemId']]['name'].'|'.$item_meta[$crafting['doItemId']]['name'].']]</td></tr></table>'."\n";
        $map_descriptor.='</td>'."\n";

        $map_descriptor.='<td>'.$item_meta[$crafting['itemToLearn']]['price'].'$</td>'."\n";

		$map_descriptor.='</tr>'."\n";
	}
	else
		$map_descriptor.='<tr class="value"><td colspan="3">Item to learn missing: '.$crafting['itemToLearn'].'</td></tr>'."\n";
}
$map_descriptor.='<tr>
	<td colspan="5" class="item_list_endline item_list_title_type_normal"></td>
</tr>
</table>'."\n";

savewikipage('Template:Crafting_list',$map_descriptor);$map_descriptor='';

if($wikivarsapp['generatefullpage'])
{
    $map_descriptor.='{{Template:Crafting_list}}'."\n";
    savewikipage('Crafting_list',$map_descriptor);
}
