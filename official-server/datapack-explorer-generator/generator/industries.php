<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator industries'."\n");

foreach($industries_meta as $id=>$industry)
{
	if(!is_dir($datapack_explorer_local_path.'industries/'))
		mkdir($datapack_explorer_local_path.'industries/');
	$map_descriptor='';

	$map_descriptor.='<div class="map item_details">';
		$map_descriptor.='<div class="subblock"><h1>Industry #'.$id.'</h1>';
		$map_descriptor.='</div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Time to complet a cycle</div><div class="value">';
		if($industry['time']<(60*2))
			$map_descriptor.=$industry['time'].'s';
		elseif($industry['time']<(60*60*2))
			$map_descriptor.=($industry['time']/60).'mins';
		elseif($industry['time']<(60*60*24*2))
			$map_descriptor.=($industry['time']/(60*60)).'hours';
		else
			$map_descriptor.=($industry['time']/(60*60*24)).'days';
		$map_descriptor.='</div></div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Cycle to be full</div><div class="value">'.$industry['cycletobefull'].'</div></div>';

		$map_descriptor.='<div class="subblock"><div class="valuetitle">Resources</div><div class="value">';
		foreach($industry['resources'] as $material=>$quantity)
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

		$map_descriptor.='<div class="subblock"><div class="valuetitle">Products</div><div class="value">';
		foreach($industry['products'] as $material=>$quantity)
		{
			$map_descriptor.='<a href="'.$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$material]['name']).'.html" title="'.$item_meta[$material]['name'].'">';
			if($item_meta[$material]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$material]['image']))
				$map_descriptor.='<img src="'.$base_datapack_site_path.'items/'.$item_meta[$material]['image'].'" width="24" height="24" alt="'.$item_meta[$material]['name'].'" title="'.$item_meta[$material]['name'].'" />';
			$map_descriptor.='</td><td>';
			if($quantity>1)
				$map_descriptor.=$quantity.'x ';
			$map_descriptor.=$item_meta[$material]['name'].'</td></tr></table>';
			$map_descriptor.='</a>';
		}
		$map_descriptor.='</div></div>';
	$map_descriptor.='</div>';

	$content=$template;
	$content=str_replace('${TITLE}','Industry #'.$id,$content);
	$content=str_replace('${CONTENT}',$map_descriptor,$content);
	$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
	$content=preg_replace("#[\r\n\t]+#isU",'',$content);
	filewrite($datapack_explorer_local_path.'industries/'.$id.'.html',$content);
}

$map_descriptor='';

$map_descriptor.='<table class="item_list item_list_type_normal">
<tr class="item_list_title item_list_title_type_normal">
	<th>Industry</th>
	<th>Resources</th>
	<th>Products</th>
</tr>';
foreach($industries_meta as $id=>$industry)
{
	$map_descriptor.='<tr class="value">';
	$map_descriptor.='<td>';
	$map_descriptor.='<a href="'.$base_datapack_explorer_site_path.'industries/'.$id.'.html">#'.$id.'</a>';
	$map_descriptor.='</td>';
	$map_descriptor.='<td>';
	foreach($industry['resources'] as $item=>$quantity)
	{
		$link=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$item]['name']).'.html';
		$name=$item_meta[$item]['name'];
		if($item_meta[$item]['image']!='')
			$image=$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
		else
			$image='';
		$map_descriptor.='<div style="float:left;text-align:center;">';
		if($image!='')
		{
			if($link!='')
				$map_descriptor.='<a href="'.$link.'">';
			$map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
			if($link!='')
				$map_descriptor.='</a>';
		}
		if($link!='')
			$map_descriptor.='<a href="'.$link.'">';
		if($name!='')
			$map_descriptor.=$name;
		else
			$map_descriptor.='Unknown item';
		if($link!='')
			$map_descriptor.='</a></div>';
	}
	$map_descriptor.='</td>';
	$map_descriptor.='<td>';
	foreach($industry['products'] as $item=>$quantity)
	{
		$link=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$item]['name']).'.html';
		$name=$item_meta[$item]['name'];
		if($item_meta[$item]['image']!='')
			$image=$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
		else
			$image='';
		$map_descriptor.='<div style="float:left;text-align:middle;">';
		if($image!='')
		{
			if($link!='')
				$map_descriptor.='<a href="'.$link.'">';
			$map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
			if($link!='')
				$map_descriptor.='</a>';
		}
		if($link!='')
			$map_descriptor.='<a href="'.$link.'">';
		if($name!='')
			$map_descriptor.=$name;
		else
			$map_descriptor.='Unknown item';
		if($link!='')
			$map_descriptor.='</a></div>';
	}
	$map_descriptor.='</td>';
	$map_descriptor.='</tr>';
}
$map_descriptor.='<tr>
	<td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
</tr>
</table>';
$content=$template;
$content=str_replace('${TITLE}','Industries list',$content);
$content=str_replace('${CONTENT}',$map_descriptor,$content);
$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
$content=preg_replace("#[\r\n\t]+#isU",'',$content);
filewrite($datapack_explorer_local_path.'industries.html',$content);