<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator map'."\n");

if(!is_dir($datapack_explorer_local_path.'zones/'))
	if(!mkdir($datapack_explorer_local_path.'zones/'))
		die('Unable to make: '.$datapack_explorer_local_path.'zone/');

foreach($zone_to_map as $zone=>$map_by_zone)
{
	$map_descriptor='';

	if(isset($zone_meta[$zone]))
		$zone_name=$zone_meta[$zone]['name'];
	elseif($zone=='')
		$zone_name='Unknown zone';
	else
		$zone_name=$zone;

	$map_descriptor.='<div class="map map_type_'.$maps_list[$map]['type'].'">';
	$map_descriptor.='<div class="subblock"><h1>'.$zone_name.'</h1></div>';
	$bot_count=0;
	if(isset($zone_to_bot_count[$zone]))
		$bot_count=$zone_to_bot_count[$zone];
	if($bot_count==0)
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Population</div><div class="value">No bots in this zone!</div></div>';
	elseif($bot_count==1)
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Population</div><div class="value">1 bot</div></div>';
	else
	{
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Population</div><div class="value">'.$zone_to_bot_count[$zone].' bots<br />';

		$map_descriptor.='<center><table class="item_list item_list_type_outdoor"><tr class="item_list_title item_list_title_type_outdoor"><th>Bot list</th></tr>';
		if(isset($zone_to_function[$zone]['shop']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-32px 0px;" title="Shop"></div>'.$zone_to_function[$zone]['shop'].' shop(s)</td></tr>';
		if(isset($zone_to_function[$zone]['fight']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-16px -16px;" title="Fight"></div>'.$zone_to_function[$zone]['fight'].' bot(s) of fight</td></tr>';
		if(isset($zone_to_function[$zone]['heal']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:0px 0px;" title="Heal"></div>'.$zone_to_function[$zone]['heal'].' bot(s) of heal</td></tr>';
		if(isset($zone_to_function[$zone]['learn']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-48px 0px;" title="Learn"></div>'.$zone_to_function[$zone]['learn'].' bot(s) of learn</td></tr>';
		if(isset($zone_to_function[$zone]['warehouse']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:0px -16px;" title="Warehouse"></div>'.$zone_to_function[$zone]['warehouse'].' warehouse(s)</td></tr>';
		if(isset($zone_to_function[$zone]['market']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:0px -16px;" title="Market"></div>'.$zone_to_function[$zone]['market'].' market(s)</td></tr>';
		if(isset($zone_to_function[$zone]['clan']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-48px -16px;" title="Clan"></div>'.$zone_to_function[$zone]['clan'].' bot(s) to create clan</td></tr>';
		if(isset($zone_to_function[$zone]['sell']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-32px 0px;" title="Sell"></div>'.$zone_to_function[$zone]['sell'].' bot(s) to sell your objects</td></tr>';
		if(isset($zone_to_function[$zone]['zonecapture']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-32px -16px;" title="Zone capture"></div>'.$zone_to_function[$zone]['zonecapture'].' bot(s) to capture the zone</td></tr>';
		if(isset($zone_to_function[$zone]['industry']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-32px -16px;" title="Industry"></div>'.$zone_to_function[$zone]['industry'].' industries</td></tr>';
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
		$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-32px 0px;" title="Shop"></div>';
	if(isset($zone_to_function[$zone]['fight']))
		$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-16px -16px;" title="Fight"></div>';
	if(isset($zone_to_function[$zone]['heal']))
		$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:0px 0px;" title="Heal"></div>';
	if(isset($zone_to_function[$zone]['learn']))
		$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-48px 0px;" title="Learn"></div>';
	if(isset($zone_to_function[$zone]['warehouse']))
		$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:0px -16px;" title="Warehouse"></div>';
	if(isset($zone_to_function[$zone]['market']))
		$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:0px -16px;" title="Market"></div>';
	if(isset($zone_to_function[$zone]['clan']))
		$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-48px -16px;" title="Clan"></div>';
	if(isset($zone_to_function[$zone]['sell']))
		$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-32px 0px;" title="Sell"></div>';
	if(isset($zone_to_function[$zone]['zonecapture']))
		$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-32px -16px;" title="Zone capture"></div>';
	if(isset($zone_to_function[$zone]['industry']))
		$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-32px -16px;" title="Industry"></div>';
	$map_descriptor.='</th></tr>';
	asort($map_by_zone);
	foreach($map_by_zone as $map=>$name)
	{
		$map_descriptor.='<tr class="value"><td><a href="'.$base_datapack_explorer_site_path.'maps/'.str_replace('.tmx','.html',$map).'" title="'.$name.'">'.$name.'</a></td><td><a href="'.$base_datapack_explorer_site_path.'maps/'.str_replace('.tmx','.html',$map).'" title="'.$name.'">';
		if(isset($map_to_function[$map]['shop']))
			for ($i = 1; $i <= $map_to_function[$map]['shop']; $i++)
				$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-32px 0px;" title="Shop"></div>';
		if(isset($map_to_function[$map]['fight']))
			for ($i = 1; $i <= $map_to_function[$map]['fight']; $i++)
				$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-16px -16px;" title="Fight"></div>';
		if(isset($map_to_function[$map]['heal']))
			for ($i = 1; $i <= $map_to_function[$map]['heal']; $i++)
				$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:0px 0px;" title="Heal"></div>';
		if(isset($map_to_function[$map]['learn']))
			for ($i = 1; $i <= $map_to_function[$map]['learn']; $i++)
				$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-48px 0px;" title="Learn"></div>';
		if(isset($map_to_function[$map]['warehouse']))
			for ($i = 1; $i <= $map_to_function[$map]['warehouse']; $i++)
				$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:0px -16px;" title="Warehouse"></div>';
		if(isset($map_to_function[$map]['market']))
			for ($i = 1; $i <= $map_to_function[$map]['market']; $i++)
				$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:0px -16px;" title="Market"></div>';
		if(isset($map_to_function[$map]['clan']))
			for ($i = 1; $i <= $map_to_function[$map]['clan']; $i++)
				$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-48px -16px;" title="Clan"></div>';
		if(isset($map_to_function[$map]['sell']))
			for ($i = 1; $i <= $map_to_function[$map]['sell']; $i++)
				$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-32px 0px;" title="Sell"></div>';
		if(isset($map_to_function[$map]['zonecapture']))
			for ($i = 1; $i <= $map_to_function[$map]['zonecapture']; $i++)
				$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-32px -16px;" title="Zone capture"></div>';
		if(isset($map_to_function[$map]['industry']))
			for ($i = 1; $i <= $map_to_function[$map]['industry']; $i++)
				$map_descriptor.='<div style="float:left;width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-32px -16px;" title="Industry"></div>';
		$map_descriptor.='</a></td></tr>';
	}
	$map_descriptor.='<tr>
	<td colspan="2" class="item_list_endline item_list_title_type_outdoor"></td>
	</tr></table></center>';
	$content=$template;
	$content=str_replace('${TITLE}',$zone_name,$content);
	$content=str_replace('${CONTENT}',$map_descriptor,$content);
	$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
	$content=preg_replace("#[\r\n\t]+#isU",'',$content);
	filewrite($datapack_explorer_local_path.'zones/'.text_operation_do_for_url($zone_name).'.html',$content);
}
