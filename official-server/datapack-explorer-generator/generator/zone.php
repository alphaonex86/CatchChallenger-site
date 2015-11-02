<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator map'."\n");

if(!is_dir($datapack_explorer_local_path.$translation_list[$current_lang]['zones/']))
	if(!mkdir($datapack_explorer_local_path.$translation_list[$current_lang]['zones/']))
		die('Unable to make: '.$datapack_explorer_local_path.'zone/');

foreach($zone_to_map as $maindatapackcode=>$zone_list)
foreach($zone_list as $zone=>$map_by_zone)
foreach($map_by_zone as $map=>$map_content)
{
	$map_descriptor='';

	if(isset($zone_meta[$zone]))
		$zone_name=$zone_meta[$maindatapackcode][$zone]['name'][$current_lang];
	elseif($zone=='')
		$zone_name=$translation_list[$current_lang]['Unknown zone'];
	else
		$zone_name=$zone;

    if(isset($maps_list[$maindatapackcode][$map]['type']))
        $map_descriptor.='<div class="map map_type_'.$maps_list[$maindatapackcode][$map]['type'].'">';
    else
    {
        echo '$maps_list['.$maindatapackcode.']['.$map.']'." not found\n";
        $map_descriptor.='<div class="map map_type_outdoor">';
    }
	$map_descriptor.='<div class="subblock"><h1>'.$zone_name.'</h1></div>';
	$bot_count=0;
	if(isset($zone_to_bot_count[$zone]))
		$bot_count=$zone_to_bot_count[$zone];
	if($bot_count==0)
		$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Population'].'</div><div class="value">'.$translation_list[$current_lang]['No bots in this zone!'].'</div></div>';
	elseif($bot_count==1)
		$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Population'].'</div><div class="value">'.$translation_list[$current_lang]['1 bot'].'</div></div>';
	else
	{
		$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Population'].'</div><div class="value">'.$zone_to_bot_count[$zone].' '.$translation_list[$current_lang]['bots'].'<br />';

		$map_descriptor.='<center><table class="item_list item_list_type_outdoor"><tr class="item_list_title item_list_title_type_outdoor"><th>'.$translation_list[$current_lang]['Bots list'].'</th></tr>';
		if(isset($zone_to_function[$zone]['shop']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;background-position:-32px 0px;" class="flags flags16" title="Shop"></div>'.$zone_to_function[$zone]['shop'].' '.$translation_list[$current_lang]['shop(s)'].'</td></tr>';
		if(isset($zone_to_function[$zone]['fight']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;background-position:-16px -16px;" class="flags flags16" title="Fight"></div>'.$zone_to_function[$zone]['fight'].' '.$translation_list[$current_lang]['bot(s) of fight'].'</td></tr>';
		if(isset($zone_to_function[$zone]['heal']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;background-position:0px 0px;" class="flags flags16" title="Heal"></div>'.$zone_to_function[$zone]['heal'].' '.$translation_list[$current_lang]['bot(s) of heal'].'</td></tr>';
		if(isset($zone_to_function[$zone]['learn']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;background-position:-48px 0px;" class="flags flags16" title="Learn"></div>'.$zone_to_function[$zone]['learn'].' '.$translation_list[$current_lang]['bot(s) of learn'].'</td></tr>';
		if(isset($zone_to_function[$zone]['warehouse']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;background-position:0px -16px;" class="flags flags16" title="Warehouse"></div>'.$zone_to_function[$zone]['warehouse'].' '.$translation_list[$current_lang]['warehouse(s)'].'</td></tr>';
		if(isset($zone_to_function[$zone]['market']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;background-position:0px -16px;" class="flags flags16" title="Market"></div>'.$zone_to_function[$zone]['market'].' '.$translation_list[$current_lang]['market(s)'].'</td></tr>';
		if(isset($zone_to_function[$zone]['clan']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;background-position:-48px -16px;" class="flags flags16" title="Clan"></div>'.$zone_to_function[$zone]['clan'].' '.$translation_list[$current_lang]['bot(s) to create clan'].'</td></tr>';
		if(isset($zone_to_function[$zone]['sell']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;background-position:-32px 0px;" class="flags flags16" title="Sell"></div>'.$zone_to_function[$zone]['sell'].' '.$translation_list[$current_lang]['bot(s) to sell your objects'].'</td></tr>';
		if(isset($zone_to_function[$zone]['zonecapture']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;background-position:-32px -16px;" class="flags flags16" title="Zone capture"></div>'.$zone_to_function[$zone]['zonecapture'].' '.$translation_list[$current_lang]['bot(s) to capture the zone'].'</td></tr>';
		if(isset($zone_to_function[$zone]['industry']))
            $map_descriptor.='<tr class="value"><td><div style="float:left;background-position:-32px -16px;" class="flags flags16" title="Industry"></div>'.$zone_to_function[$zone]['industry'].' '.$translation_list[$current_lang]['industries'].'</td></tr>';
        if(isset($zone_to_function[$zone]['quests']))
            $map_descriptor.='<tr class="value"><td><div style="float:left;background-position:-16px 0px;" class="flags flags16" title="Quests"></div>'.$zone_to_function[$zone]['quests'].' '.$translation_list[$current_lang]['quests to start'].'</td></tr>';
		$map_descriptor.='<tr>
		<td class="item_list_endline item_list_title_type_outdoor"></td>
		</tr></table></center><br style="clear:both;" />';

		$map_descriptor.='</div></div>';
	}
	$map_descriptor.='</div>';

	$map_descriptor.='<center><table class="item_list item_list_type_outdoor"><tr class="item_list_title item_list_title_type_outdoor">
	<th>';
	$map_descriptor.=$zone_name;
	$map_descriptor.='</th><th>';
	if(isset($zone_to_function[$zone]['shop']))
		$map_descriptor.='<div style="float:left;background-position:-32px 0px;" class="flags flags16" title="Shop"></div>';
	if(isset($zone_to_function[$zone]['fight']))
		$map_descriptor.='<div style="float:left;background-position:-16px -16px;" class="flags flags16" title="Fight"></div>';
	if(isset($zone_to_function[$zone]['heal']))
		$map_descriptor.='<div style="float:left;background-position:0px 0px;" class="flags flags16" title="Heal"></div>';
	if(isset($zone_to_function[$zone]['learn']))
		$map_descriptor.='<div style="float:left;background-position:-48px 0px;" class="flags flags16" title="Learn"></div>';
	if(isset($zone_to_function[$zone]['warehouse']))
		$map_descriptor.='<div style="float:left;background-position:0px -16px;" class="flags flags16" title="Warehouse"></div>';
	if(isset($zone_to_function[$zone]['market']))
		$map_descriptor.='<div style="float:left;background-position:0px -16px;" class="flags flags16" title="Market"></div>';
	if(isset($zone_to_function[$zone]['clan']))
		$map_descriptor.='<div style="float:left;background-position:-48px -16px;" class="flags flags16" title="Clan"></div>';
	if(isset($zone_to_function[$zone]['sell']))
		$map_descriptor.='<div style="float:left;background-position:-32px 0px;" class="flags flags16" title="Sell"></div>';
	if(isset($zone_to_function[$zone]['zonecapture']))
		$map_descriptor.='<div style="float:left;background-position:-32px -16px;" class="flags flags16" title="Zone capture"></div>';
    if(isset($zone_to_function[$zone]['industry']))
        $map_descriptor.='<div style="float:left;background-position:-32px -16px;" class="flags flags16" title="Industry"></div>';
    if(isset($zone_to_function[$zone]['quests']))
        $map_descriptor.='<div style="float:left;background-position:-16px 0px;" class="flags flags16" title="Quests"></div>';
	$map_descriptor.='</th></tr>';
	asort($map_by_zone);
	foreach($map_by_zone as $map=>$name)
	{
        $map_descriptor.='<tr class="value"><td><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['maps/'].str_replace('.tmx','.html',$map).'" title="'.$name[$current_lang].'">'.$name[$current_lang].'</a></td>';
        $map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.$translation_list[$current_lang]['maps/'].str_replace('.tmx','.html',$map).'" title="'.$name[$current_lang].'">';
		if(isset($map_to_function[$map]['shop']))
			for ($i = 1; $i <= $map_to_function[$map]['shop']; $i++)
				$map_descriptor.='<div style="float:left;background-position:-32px 0px;" class="flags flags16" title="Shop"></div>';
		if(isset($map_to_function[$map]['fight']))
			for ($i = 1; $i <= $map_to_function[$map]['fight']; $i++)
				$map_descriptor.='<div style="float:left;background-position:-16px -16px;" class="flags flags16" title="Fight"></div>';
		if(isset($map_to_function[$map]['heal']))
			for ($i = 1; $i <= $map_to_function[$map]['heal']; $i++)
				$map_descriptor.='<div style="float:left;background-position:0px 0px;" class="flags flags16" title="Heal"></div>';
		if(isset($map_to_function[$map]['learn']))
			for ($i = 1; $i <= $map_to_function[$map]['learn']; $i++)
				$map_descriptor.='<div style="float:left;background-position:-48px 0px;" class="flags flags16" title="Learn"></div>';
		if(isset($map_to_function[$map]['warehouse']))
			for ($i = 1; $i <= $map_to_function[$map]['warehouse']; $i++)
				$map_descriptor.='<div style="float:left;background-position:0px -16px;" class="flags flags16" title="Warehouse"></div>';
		if(isset($map_to_function[$map]['market']))
			for ($i = 1; $i <= $map_to_function[$map]['market']; $i++)
				$map_descriptor.='<div style="float:left;background-position:0px -16px;" class="flags flags16" title="Market"></div>';
		if(isset($map_to_function[$map]['clan']))
			for ($i = 1; $i <= $map_to_function[$map]['clan']; $i++)
				$map_descriptor.='<div style="float:left;background-position:-48px -16px;" class="flags flags16" title="Clan"></div>';
		if(isset($map_to_function[$map]['sell']))
			for ($i = 1; $i <= $map_to_function[$map]['sell']; $i++)
				$map_descriptor.='<div style="float:left;background-position:-32px 0px;" class="flags flags16" title="Sell"></div>';
		if(isset($map_to_function[$map]['zonecapture']))
			for ($i = 1; $i <= $map_to_function[$map]['zonecapture']; $i++)
				$map_descriptor.='<div style="float:left;background-position:-32px -16px;" class="flags flags16" title="Zone capture"></div>';
        if(isset($map_to_function[$map]['industry']))
            for ($i = 1; $i <= $map_to_function[$map]['industry']; $i++)
                $map_descriptor.='<div style="float:left;background-position:-32px -16px;" class="flags flags16" title="Industry"></div>';
        if(isset($map_to_function[$map]['quests']))
            for ($i = 1; $i <= $map_to_function[$map]['quests']; $i++)
                $map_descriptor.='<div style="float:left;background-position:-16px 0px;" class="flags flags16" title="Quests"></div>';
		$map_descriptor.='</a></td></tr>';
	}
	$map_descriptor.='<tr>
	<td colspan="2" class="item_list_endline item_list_title_type_outdoor"></td>
	</tr></table></center>';
	$content=$template;
	$content=str_replace('${TITLE}',$zone_name,$content);
	$content=str_replace('${CONTENT}',$map_descriptor,$content);
	$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
	$content=clean_html($content);
	filewrite($datapack_explorer_local_path.$translation_list[$current_lang]['zones/'].text_operation_do_for_url($zone_name).'.html',$content);
}
