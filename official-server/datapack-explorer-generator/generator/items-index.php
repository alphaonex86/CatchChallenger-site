<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator items index');

$map_descriptor='';

$map_descriptor.='<table class="item_list item_list_type_normal map_list">
<tr class="item_list_title item_list_title_type_normal">
	<th colspan="3">Item</th>
</tr>
<tr class="item_list_title item_list_title_type_normal">
	<th colspan="2">Item</th>
	<th>Price</th>
</tr>';
foreach($item_meta as $id=>$item)
{
	if(!isset($item_to_skill_of_monster[$id]) && !isset($item_to_crafting[$id]) && !isset($item_to_plant[$id]) && !isset($item_to_regeneration[$id]) && !isset($item_to_trap[$id]) && !isset($item_to_evolution[$id]))
	{
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
}
$map_descriptor.='<tr>
	<td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
</tr>
</table>';

if(count($item_to_skill_of_monster)>0)
{
	$map_descriptor.='<table class="item_list item_list_type_normal map_list">
	<tr class="item_list_title item_list_title_type_normal">
		<th colspan="3">Learn</th>
	</tr>
	<tr class="item_list_title item_list_title_type_normal">
		<th colspan="2">Item</th>
		<th>Price</th>
	</tr>';
	foreach($item_meta as $id=>$item)
	{
		if(isset($item_to_skill_of_monster[$id]))
		{
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
	}
	$map_descriptor.='<tr>
		<td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
	</tr>
	</table>';
}
if(count($item_to_crafting)>0)
{
	$map_descriptor.='<table class="item_list item_list_type_normal map_list">
	<tr class="item_list_title item_list_title_type_normal">
		<th colspan="3">Crafting recipe</th>
	</tr>
	<tr class="item_list_title item_list_title_type_normal">
		<th colspan="2">Item</th>
		<th>Price</th>
	</tr>';
	foreach($item_meta as $id=>$item)
	{
		if(isset($item_to_crafting[$id]))
		{
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
	}
	$map_descriptor.='<tr>
		<td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
	</tr>
	</table>';
}
if(count($item_to_plant)>0)
{
	$map_descriptor.='<table class="item_list item_list_type_normal map_list">
	<tr class="item_list_title item_list_title_type_normal">
		<th colspan="3">Plant</th>
	</tr>
	<tr class="item_list_title item_list_title_type_normal">
		<th colspan="2">Item</th>
		<th>Price</th>
	</tr>';
	foreach($item_meta as $id=>$item)
	{
		if(isset($item_to_plant[$id]))
		{
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
	}
	$map_descriptor.='<tr>
		<td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
	</tr>
	</table>';
}
if(count($item_to_trap)>0)
{
	$map_descriptor.='<table class="item_list item_list_type_normal map_list">
	<tr class="item_list_title item_list_title_type_normal">
		<th colspan="3">Trap</th>
	</tr>
	<tr class="item_list_title item_list_title_type_normal">
		<th colspan="2">Item</th>
		<th>Price</th>
	</tr>';
	foreach($item_meta as $id=>$item)
	{
		if(isset($item_to_trap[$id]))
		{
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
	}
	$map_descriptor.='<tr>
		<td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
	</tr>
	</table>';
}
if(count($item_to_regeneration)>0)
{
	$map_descriptor.='<table class="item_list item_list_type_normal map_list">
	<tr class="item_list_title item_list_title_type_normal">
		<th colspan="3">Regeneration</th>
	</tr>
	<tr class="item_list_title item_list_title_type_normal">
		<th colspan="2">Item</th>
		<th>Price</th>
	</tr>';
	foreach($item_meta as $id=>$item)
	{
		if(isset($item_to_regeneration[$id]))
		{
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
	}
	$map_descriptor.='<tr>
		<td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
	</tr>
	</table>';
}
if(count($item_to_evolution)>0)
{
	$map_descriptor.='<table class="item_list item_list_type_normal map_list">
	<tr class="item_list_title item_list_title_type_normal">
		<th colspan="3">Evolution</th>
	</tr>
	<tr class="item_list_title item_list_title_type_normal">
		<th colspan="2">Item</th>
		<th>Price</th>
	</tr>';
	foreach($item_meta as $id=>$item)
	{
		if(isset($item_to_evolution[$id]))
		{
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
	}
	$map_descriptor.='<tr>
		<td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
	</tr>
	</table>';
}


$content=$template;
$content=str_replace('${TITLE}','Item list',$content);
$content=str_replace('${CONTENT}',$map_descriptor,$content);
$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
$content=preg_replace("#[\r\n\t]+#isU",'',$content);
filewrite($datapack_explorer_local_path.'items.html',$content);