<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator map');

foreach($zone_to_map as $zone=>$map_by_zone)
{
	$map_descriptor='';

	if(isset($zone_meta[$zone]))
		$zone_name=$zone_meta[$zone]['name'];
	elseif($zone=='')
		$zone_name='Unknown zone';
	else
		$zone_name=$zone;

	$map_descriptor.='<div class="map map_type_'.$maps_list[$map]['type'].'">'."\n";
	$map_descriptor.='<div class="subblock"><h1>'.$zone_name.'</h1></div>'."\n";
	$bot_count=0;
	if(isset($zone_to_bot_count[$zone]))
		$bot_count=$zone_to_bot_count[$zone];
	if($bot_count==0)
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Population</div><div class="value">No bots in this zone!</div></div>'."\n";
	elseif($bot_count==1)
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Population</div><div class="value">1 bot</div></div>'."\n";
	else
	{
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Population</div><div class="value">'.$zone_to_bot_count[$zone].' bots<br />'."\n";

		$map_descriptor.='<center><table class="item_list item_list_type_outdoor"><tr class="item_list_title item_list_title_type_outdoor"><th>Bot list</th></tr>'."\n";
		if(isset($zone_to_function[$zone]['shop']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;background-position:-32px 0px;" class="flags flags16" title="Shop"></div>'.$zone_to_function[$zone]['shop'].' shop(s)</td></tr>'."\n";
		if(isset($zone_to_function[$zone]['fight']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;background-position:-16px -16px;" class="flags flags16" title="Fight"></div>'.$zone_to_function[$zone]['fight'].' bot(s) of fight</td></tr>'."\n";
		if(isset($zone_to_function[$zone]['heal']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;background-position:0px 0px;" class="flags flags16" title="Heal"></div>'.$zone_to_function[$zone]['heal'].' bot(s) of heal</td></tr>'."\n";
		if(isset($zone_to_function[$zone]['learn']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;background-position:-48px 0px;" class="flags flags16" title="Learn"></div>'.$zone_to_function[$zone]['learn'].' bot(s) of learn</td></tr>'."\n";
		if(isset($zone_to_function[$zone]['warehouse']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;background-position:0px -16px;" class="flags flags16" title="Warehouse"></div>'.$zone_to_function[$zone]['warehouse'].' warehouse(s)</td></tr>'."\n";
		if(isset($zone_to_function[$zone]['market']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;background-position:0px -16px;" class="flags flags16" title="Market"></div>'.$zone_to_function[$zone]['market'].' market(s)</td></tr>'."\n";
		if(isset($zone_to_function[$zone]['clan']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;background-position:-48px -16px;" class="flags flags16" title="Clan"></div>'.$zone_to_function[$zone]['clan'].' bot(s) to create clan</td></tr>'."\n";
		if(isset($zone_to_function[$zone]['sell']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;background-position:-32px 0px;" class="flags flags16" title="Sell"></div>'.$zone_to_function[$zone]['sell'].' bot(s) to sell your objects</td></tr>'."\n";
		if(isset($zone_to_function[$zone]['zonecapture']))
			$map_descriptor.='<tr class="value"><td><div style="float:left;background-position:-32px -16px;" class="flags flags16" title="Zone capture"></div>'.$zone_to_function[$zone]['zonecapture'].' bot(s) to capture the zone</td></tr>'."\n";
		if(isset($zone_to_function[$zone]['industry']))
            $map_descriptor.='<tr class="value"><td><div style="float:left;background-position:-32px -16px;" class="flags flags16" title="Industry"></div>'.$zone_to_function[$zone]['industry'].' industries</td></tr>'."\n";
        if(isset($zone_to_function[$zone]['quests']))
            $map_descriptor.='<tr class="value"><td><div style="float:left;background-position:-16px 0px;" class="flags flags16" title="Quests"></div>'.$zone_to_function[$zone]['quests'].' quests to start</td></tr>'."\n";
		$map_descriptor.='<tr>
		<td class="item_list_endline item_list_title_type_outdoor"></td>
		</tr></table></center><br style="clear:both;" />'."\n";

		$map_descriptor.='</div></div>'."\n";
	}
	$map_descriptor.='</div>'."\n";

	$map_descriptor.='<center><table class="item_list item_list_type_outdoor"><tr class="item_list_title item_list_title_type_outdoor">
	<th>'."\n";
	$map_descriptor.=$zone_name;
	$map_descriptor.='</th><th>'."\n";
	if(isset($zone_to_function[$zone]['shop']))
		$map_descriptor.='<div style="float:left;background-position:-32px 0px;" class="flags flags16" title="Shop"></div>'."\n";
	if(isset($zone_to_function[$zone]['fight']))
		$map_descriptor.='<div style="float:left;background-position:-16px -16px;" class="flags flags16" title="Fight"></div>'."\n";
	if(isset($zone_to_function[$zone]['heal']))
		$map_descriptor.='<div style="float:left;background-position:0px 0px;" class="flags flags16" title="Heal"></div>'."\n";
	if(isset($zone_to_function[$zone]['learn']))
		$map_descriptor.='<div style="float:left;background-position:-48px 0px;" class="flags flags16" title="Learn"></div>'."\n";
	if(isset($zone_to_function[$zone]['warehouse']))
		$map_descriptor.='<div style="float:left;background-position:0px -16px;" class="flags flags16" title="Warehouse"></div>'."\n";
	if(isset($zone_to_function[$zone]['market']))
		$map_descriptor.='<div style="float:left;background-position:0px -16px;" class="flags flags16" title="Market"></div>'."\n";
	if(isset($zone_to_function[$zone]['clan']))
		$map_descriptor.='<div style="float:left;background-position:-48px -16px;" class="flags flags16" title="Clan"></div>'."\n";
	if(isset($zone_to_function[$zone]['sell']))
		$map_descriptor.='<div style="float:left;background-position:-32px 0px;" class="flags flags16" title="Sell"></div>'."\n";
	if(isset($zone_to_function[$zone]['zonecapture']))
		$map_descriptor.='<div style="float:left;background-position:-32px -16px;" class="flags flags16" title="Zone capture"></div>'."\n";
    if(isset($zone_to_function[$zone]['industry']))
        $map_descriptor.='<div style="float:left;background-position:-32px -16px;" class="flags flags16" title="Industry"></div>'."\n";
    if(isset($zone_to_function[$zone]['quests']))
        $map_descriptor.='<div style="float:left;background-position:-16px 0px;" class="flags flags16" title="Quests"></div>'."\n";
	$map_descriptor.='</th></tr>'."\n";
	asort($map_by_zone);
	foreach($map_by_zone as $map=>$name)
	{
		$map_descriptor.='<tr class="value"><td>[[Maps:'.map_to_wiki_name($map).'|'.$maps_list[$map]['name'].']]</td><td>'."\n";
		if(isset($map_to_function[$map]['shop']))
			for ($i = 1; $i <= $map_to_function[$map]['shop']; $i++)
				$map_descriptor.='<div style="float:left;background-position:-32px 0px;" class="flags flags16" title="Shop"></div>'."\n";
		if(isset($map_to_function[$map]['fight']))
			for ($i = 1; $i <= $map_to_function[$map]['fight']; $i++)
				$map_descriptor.='<div style="float:left;background-position:-16px -16px;" class="flags flags16" title="Fight"></div>'."\n";
		if(isset($map_to_function[$map]['heal']))
			for ($i = 1; $i <= $map_to_function[$map]['heal']; $i++)
				$map_descriptor.='<div style="float:left;background-position:0px 0px;" class="flags flags16" title="Heal"></div>'."\n";
		if(isset($map_to_function[$map]['learn']))
			for ($i = 1; $i <= $map_to_function[$map]['learn']; $i++)
				$map_descriptor.='<div style="float:left;background-position:-48px 0px;" class="flags flags16" title="Learn"></div>'."\n";
		if(isset($map_to_function[$map]['warehouse']))
			for ($i = 1; $i <= $map_to_function[$map]['warehouse']; $i++)
				$map_descriptor.='<div style="float:left;background-position:0px -16px;" class="flags flags16" title="Warehouse"></div>'."\n";
		if(isset($map_to_function[$map]['market']))
			for ($i = 1; $i <= $map_to_function[$map]['market']; $i++)
				$map_descriptor.='<div style="float:left;background-position:0px -16px;" class="flags flags16" title="Market"></div>'."\n";
		if(isset($map_to_function[$map]['clan']))
			for ($i = 1; $i <= $map_to_function[$map]['clan']; $i++)
				$map_descriptor.='<div style="float:left;background-position:-48px -16px;" class="flags flags16" title="Clan"></div>'."\n";
		if(isset($map_to_function[$map]['sell']))
			for ($i = 1; $i <= $map_to_function[$map]['sell']; $i++)
				$map_descriptor.='<div style="float:left;background-position:-32px 0px;" class="flags flags16" title="Sell"></div>'."\n";
		if(isset($map_to_function[$map]['zonecapture']))
			for ($i = 1; $i <= $map_to_function[$map]['zonecapture']; $i++)
				$map_descriptor.='<div style="float:left;background-position:-32px -16px;" class="flags flags16" title="Zone capture"></div>'."\n";
        if(isset($map_to_function[$map]['industry']))
            for ($i = 1; $i <= $map_to_function[$map]['industry']; $i++)
                $map_descriptor.='<div style="float:left;background-position:-32px -16px;" class="flags flags16" title="Industry"></div>'."\n";
        if(isset($map_to_function[$map]['quests']))
            for ($i = 1; $i <= $map_to_function[$map]['quests']; $i++)
                $map_descriptor.='<div style="float:left;background-position:-16px 0px;" class="flags flags16" title="Quests"></div>'."\n";
		$map_descriptor.='</td></tr>'."\n";
	}
	$map_descriptor.='<tr>
	<td colspan="2" class="item_list_endline item_list_title_type_outdoor"></td>
	</tr></table></center>'."\n";

    savewikipage('Template:Zones/'.$zone_name,$map_descriptor);$map_descriptor='';

    if($wikivarsapp['generatefullpage'])
    {
        $map_descriptor.='{{Template:Zones/'.$zone_name.'}}'."\n";
        savewikipage('Zones:'.$zone_name,$map_descriptor);
    }
}
