<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator plants'."\n");

$map_descriptor='';
$map_descriptor.='<table class="item_list item_list_type_normal plant_list">
<tr class="item_list_title item_list_title_type_normal">
	<th colspan="2">'.$translation_list[$current_lang]['Plant'].'</th>
	<th colspan="2">'.$translation_list[$current_lang]['Time to grow'].'</th>
	<th>'.$translation_list[$current_lang]['Fruits produced'].'</th>
</tr>';
$plant_count=0;
foreach($plant_meta as $id=>$plant)
{
    if($plant_count%15==0 && $plant_count!=0)
    {
        $map_descriptor.='<tr>
            <td colspan="5" class="item_list_endline item_list_title_type_normal"></td>
        </tr>
        </table>';
        $map_descriptor.='<table class="item_list item_list_type_normal plant_list">
        <tr class="item_list_title item_list_title_type_normal">
            <th colspan="2">'.$translation_list[$current_lang]['Plant'].'</th>
            <th colspan="2">'.$translation_list[$current_lang]['Time to grow'].'</th>
            <th>'.$translation_list[$current_lang]['Fruits produced'].'</th>
        </tr>';
    }
    $plant_count++;
    if(isset($item_meta[$plant['itemUsed']]))
    {
        $link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$plant['itemUsed']]['name'][$current_lang]).'.html';
        $name=$item_meta[$plant['itemUsed']]['name'][$current_lang];
        if($item_meta[$plant['itemUsed']]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$plant['itemUsed']]['image']))
            $image=$base_datapack_site_path.'/items/'.$item_meta[$plant['itemUsed']]['image'];
        else
            $image='';
    }
    else
    {
        $link='';
        $name='???';
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
	$map_descriptor.='<td>';
	if(file_exists($datapack_path.'plants/'.$id.'.png'))
		$map_descriptor.='<img src="'.$base_datapack_site_path.'plants/'.$id.'.png" width="80" height="32" alt="'.$name.'" title="'.$name.'" />';
	$map_descriptor.='</td>';
	$map_descriptor.='<td><b>'.($plant['fruits']/60).'</b> '.$translation_list[$current_lang]['minutes'].'</td>';
	$map_descriptor.='<td>'.$plant['quantity'].'</td>';
	$map_descriptor.='</tr>';
}
$map_descriptor.='<tr>
	<td colspan="5" class="item_list_endline item_list_title_type_normal"></td>
</tr>
</table>';

$content=$template;
$content=str_replace('${TITLE}',$translation_list[$current_lang]['Plants list'],$content);
$content=str_replace('${CONTENT}',$map_descriptor,$content);
$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
$content=clean_html($content);
$filedestination=$datapack_explorer_local_path.$translation_list[$current_lang]['plants.html'];
if(file_exists($filedestination))
    die('The file already exists: '.$filedestination);
filewrite($filedestination,$content);
