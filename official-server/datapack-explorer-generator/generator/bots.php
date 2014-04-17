<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator bots'."\n");

$bots_by_zone=array(''=>array());
foreach($bots_meta as $bot_id=>$bot)
{
    $have_skin=true;
    if(isset($bot_id_to_skin[$bot_id]))
    {
        if(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.png'))
            $have_skin=true;
        elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.png'))
            $have_skin=true;
        elseif(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.gif'))
            $have_skin=true;
        elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.gif'))
            $have_skin=true;
        else
            $have_skin=false;
    }
    else
        $have_skin=false;
    if($have_skin || $bot['onlytext']!=1)
    {
        if(isset($bot_id_to_map[$bot_id]))
        {
        if(isset($maps_list[$bot_id_to_map[$bot_id]]))
        {
            if(isset($zone_meta[$maps_list[$bot_id_to_map[$bot_id]]['zone']]))
            {
            if(!isset($bots_by_zone[$zone_meta[$maps_list[$bot_id_to_map[$bot_id]]['zone']]['name']]))
                $bots_by_zone[$zone_meta[$maps_list[$bot_id_to_map[$bot_id]]['zone']]['name']]=array();
            $bots_by_zone[$zone_meta[$maps_list[$bot_id_to_map[$bot_id]]['zone']]['name']][]=$bot_id;
            }
            else
            {
            if(!isset($bots_by_zone[$maps_list[$bot_id_to_map[$bot_id]]['name']]))
                $bots_by_zone[$maps_list[$bot_id_to_map[$bot_id]]['name']]=array();
            $bots_by_zone[$maps_list[$bot_id_to_map[$bot_id]]['name']][]=$bot_id;
            }
        }
        else
            $bots_by_zone[''][]=$bot_id;
        }
        else
        $bots_by_zone[''][]=$bot_id;
    }
	if(!is_dir($datapack_explorer_local_path.'bots/'))
		mkdir($datapack_explorer_local_path.'bots/');
	$map_descriptor='';

	$map_descriptor.='<div class="map item_details">';
		if($bot['name']=='')
		{
			$final_url_name='bot '.$bot_id;
			$map_descriptor.='<div class="subblock"><h1>Bot #'.$bot_id.'</h1>';
		}
		else
		{
			if($bots_name_count[$bot['name']]==1)
				$final_url_name=$bot['name'];
			else
				$final_url_name=$bot_id.'-'.$bot['name'];
			$map_descriptor.='<div class="subblock"><h1>'.$bot['name'].'</h1>';
			$map_descriptor.='<h2>Bot #'.$bot_id.'</h2>';
		}
		if(isset($bot_id_to_skin[$bot_id]))
		{
			if(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.png'))
				$map_descriptor.='<center><h2><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></div></h2></center>';
			elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.png'))
				$map_descriptor.='<center><h2><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></div></h2></center>';
			elseif(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.gif'))
				$map_descriptor.='<center><h2><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></div></h2></center>';
			elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.gif'))
				$map_descriptor.='<center><h2><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></div></h2></center>';
		}
		$map_descriptor.='</div>';
		if(isset($bot_id_to_skin[$bot_id]))
		{
			if(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/front.png'))
			{
				$map_descriptor.='<div class="value datapackscreenshot"><center>';
				$map_descriptor.='<img src="'.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/front.png" width="80" height="80" alt="" />';
				$map_descriptor.='</center></div>';
			}
			else if(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/front.png'))
			{
				$map_descriptor.='<div class="value datapackscreenshot"><center>';
				$map_descriptor.='<img src="'.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/front.png" width="80" height="80" alt="" />';
				$map_descriptor.='</center></div>';
			}
			elseif(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/front.gif'))
			{
				$map_descriptor.='<div class="value datapackscreenshot"><center>';
				$map_descriptor.='<img src="'.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/front.gif" width="80" height="80" alt="" />';
				$map_descriptor.='</center></div>';
			}
			else if(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/front.gif'))
			{
				$map_descriptor.='<div class="value datapackscreenshot"><center>';
				$map_descriptor.='<img src="'.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/front.gif" width="80" height="80" alt="" />';
				$map_descriptor.='</center></div>';
			}
		}
		if(isset($bot_id_to_map[$bot_id]))
		{
			if(isset($maps_list[$bot_id_to_map[$bot_id]]))
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle">Map</div><div class="value">';
				if(isset($zone_meta[$maps_list[$bot_id_to_map[$bot_id]]['zone']]))
				{
					$map_descriptor.='<a href="'.$base_datapack_explorer_site_path.'maps/'.str_replace('.tmx','.html',$bot_id_to_map[$bot_id]).'" title="'.$maps_list[$bot_id_to_map[$bot_id]]['name'].'">'.$maps_list[$bot_id_to_map[$bot_id]]['name'].'</a>&nbsp;';
					$zone_name=$zone_meta[$maps_list[$bot_id_to_map[$bot_id]]['zone']]['name'];
					$map_descriptor.='(Zone: <a href="'.$base_datapack_explorer_site_path.'zones/'.text_operation_do_for_url($zone_name).'.html" title="'.$zone_name.'">'.$zone_name.'</a>)';
				}
				else
					$map_descriptor.='<a href="'.$base_datapack_explorer_site_path.'maps/'.str_replace('.tmx','.html',$bot_id_to_map[$bot_id]).'" title="'.$maps_list[$bot_id_to_map[$bot_id]]['name'].'">'.$maps_list[$bot_id_to_map[$bot_id]]['name'].'</a>';
				$map_descriptor.='</div></div>';
			}
		}
		foreach($bot['step'] as $step_id=>$step)
		{
			if($step['type']=='text')
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle" id="step'.$step_id.'">Text</div><div class="value">';
				$map_descriptor.=preg_replace('#a href="([0-9]+)#isU','a href="#step$1',$step['text']);
				$map_descriptor.='</div></div>';
			}
			else if($step['type']=='shop')
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle" id="step'.$step_id.'">Shop</div>
				<center><div style="width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-32px 0px;"></center>
				</div><div class="value">';
				$map_descriptor.='<center><table class="item_list item_list_type_normal">
				<tr class="item_list_title item_list_title_type_normal">
					<th colspan="2">Item</th>
					<th>Price</th>
				</tr>';
				foreach($shop_meta[$step['shop']]['products'] as $item=>$price)
				{
					if(isset($item_meta[$item]))
					{
						$map_descriptor.='<tr class="value">';
						if(isset($item_meta[$item]))
						{
							$link=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$item]['name']).'.html';
							$name=$item_meta[$item]['name'];
							if($item_meta[$item]['image']!='')
								$image=$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
							else
								$image='';
						}
						else
						{
							$link='';
							$name='';
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
							$map_descriptor.='Unknown item';
						if($link!='')
							$map_descriptor.='</a>';
						$map_descriptor.='</td>';
						$map_descriptor.='<td>'.$price.'$</td>';
						$map_descriptor.='</tr>';
					}
				}
				$map_descriptor.='<tr>
					<td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
				</tr>
				</table>';
				$map_descriptor.='</center>';
				$map_descriptor.='</div></div>';
			}
			else if($step['type']=='fight')
			{
				if(isset($fight_meta[$step['fightid']]))
				{
					$map_descriptor.='<div class="subblock"><div class="valuetitle" id="step'.$step_id.'">Fight</div>
					<center><div style="width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:-16px -16px;"></center>
					<div class="value">';
					if($fight_meta[$step['fightid']]['cash']>0)
						$map_descriptor.='Rewards: <b>'.$fight_meta[$step['fightid']]['cash'].'$</b><br />';

                    if(count($fight_meta[$step['fightid']]['items'])>0)
                    {
                        $map_descriptor.='<center><table class="item_list item_list_type_normal">
                        <tr class="item_list_title item_list_title_type_normal">
                            <th colspan="2">Item</th>
                        </tr>';
                        foreach($fight_meta[$step['fightid']]['items'] as $item)
                        {
                            if(isset($item_meta[$item['item']]))
                            {
                                $link=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$item['item']]['name']).'.html';
                                $name=$item_meta[$item['item']]['name'];
                                if($item_meta[$item['item']]['image']!='')
                                    $image=$base_datapack_site_path.'/items/'.$item_meta[$item['item']]['image'];
                                else
                                    $image='';
                            }
                            else
                            {
                                $link='';
                                $name='';
                                $image='';
                            }
                            $quantity_text='';
                            if($drop['quantity_min']!=$drop['quantity_max'])
                                $quantity_text=$drop['quantity_min'].' to '.$drop['quantity_max'].' ';
                            elseif($drop['quantity_min']>1)
                                $quantity_text=$drop['quantity_min'].' ';
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
                                    $map_descriptor.=$quantity_text.$name;
                                else
                                    $map_descriptor.=$quantity_text.'Unknown item';
                                if($link!='')
                                    $map_descriptor.='</a>';
                                $map_descriptor.='</td>';
                                $map_descriptor.='</tr>';
                        }
                        $map_descriptor.='<tr>
                            <td colspan="2" class="item_list_endline item_list_title_type_normal"></td>
                        </tr>
                        </table></center>';
                    }

					foreach($fight_meta[$step['fightid']]['monsters'] as $monster)
					{
						if(isset($monster_meta[$monster['monster']]))
						{
							$monster_full=$monster_meta[$monster['monster']];
							$map_descriptor.='<table class="item_list item_list_type_'.$monster_full['type'][0].' map_list">
							<tr class="item_list_title item_list_title_type_'.$monster_full['type'][0].'">
								<th>';
							$map_descriptor.='</th>
							</tr>';
							$map_descriptor.='<tr class="value">';
							$map_descriptor.='<td>';
							$map_descriptor.='<table class="monsterforevolution">';
							if(file_exists($datapack_path.'monsters/'.$monster['monster'].'/front.png'))
								$map_descriptor.='<tr><td><center><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_full['name']).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$monster['monster'].'/front.png" width="80" height="80" alt="'.$monster_full['name'].'" title="'.$monster_full['name'].'" /></center></a></td></tr>';
							else if(file_exists($datapack_path.'monsters/'.$monster['monster'].'/front.gif'))
								$map_descriptor.='<tr><td><center><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_full['name']).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$monster['monster'].'/front.gif" width="80" height="80" alt="'.$monster_full['name'].'" title="'.$monster_full['name'].'" /></center></a></td></tr>';
							$map_descriptor.='<tr><td class="evolution_name"><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_full['name']).'.html">'.$monster_full['name'].'</a></td></tr>';
                            $map_descriptor.='<tr><td>';
                            $type_list=array();
                            foreach($monster_meta[$monster['monster']]['type'] as $type)
                                if(isset($type_meta[$type]))
                                    $type_list[]='<span class="type_label type_label_'.$type.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type.'.html">'.$type_meta[$type]['english_name'].'</a></span>';
                            $map_descriptor.='<div class="type_label_list">'.implode(' ',$type_list).'</div></td></tr>';
							$map_descriptor.='<tr><td>Level '.$monster['level'].'</td></tr>';
							$map_descriptor.='</table>';
							$map_descriptor.='</td>';
							$map_descriptor.='</tr>';
							$map_descriptor.='<tr>
								<th class="item_list_endline item_list_title item_list_title_type_'.$monster_full['type'][0].'">';
							$map_descriptor.='</th>
							</tr>
							</table>';
						}
					}
					$map_descriptor.='<br style="clear:both;" />';

					$map_descriptor.='</div></div>';
				}
			}
			else if($step['type']=='heal')
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle" id="step'.$step_id.'">Heal</div>
				<div class="value">
					<center><div style="width:128px;height:128px;background-image:url(\'/official-server/images/flags-512.png\');background-repeat:no-repeat;background-position:0px 0px;"></center>
				</div>
				</div>';
			}
			else if($step['type']=='learn')
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle" id="step'.$step_id.'">Learn</div>
				<div class="value">
					<center><div style="width:128px;height:128px;background-image:url(\'/official-server/images/flags-512.png\');background-repeat:no-repeat;background-position:-384px 0px;"></center>
				</div>
				</div>';
			}
			else if($step['type']=='warehouse')
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle" id="step'.$step_id.'">Warehouse</div>
				<div class="value">
					<center><div style="width:128px;height:128px;background-image:url(\'/official-server/images/flags-512.png\');background-repeat:no-repeat;background-position:0px -128px;"></center>
				</div>
				</div>';
			}
			else if($step['type']=='market')
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle" id="step'.$step_id.'">Market</div>
				<div class="value">
					<center><div style="width:128px;height:128px;background-image:url(\'/official-server/images/flags-512.png\');background-repeat:no-repeat;background-position:0px -128px;"></center>
				</div>
				</div>';
			}
			else if($step['type']=='clan')
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle" id="step'.$step_id.'">Clan</div>
				<div class="value">
					<center><div style="width:128px;height:128px;background-image:url(\'/official-server/images/flags-512.png\');background-repeat:no-repeat;background-position:-384px -128px;"></center>
				</div>
				</div>';
			}
			else if($step['type']=='sell')
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle" id="step'.$step_id.'">Sell</div>
				<div class="value">
					<center><div style="width:128px;height:128px;background-image:url(\'/official-server/images/flags-512.png\');background-repeat:no-repeat;background-position:-256px 0px;"></center>
				</div>
				</div>';
			}
			else if($step['type']=='zonecapture')
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle" id="step'.$step_id.'">Zone capture</div>
				<div class="value">
					Zone: ';
				if(isset($zone_meta[$step['zone']]))
					$map_descriptor.=$zone_meta[$step['zone']]['name'];
				else
					$map_descriptor.='Unknown zone';
				$map_descriptor.='<center><div style="width:128px;height:128px;background-image:url(\'/official-server/images/flags-512.png\');background-repeat:no-repeat;background-position:-256px -128px;"></center>
				</div>
				</div>';
			}
			else if($step['type']=='industry')
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle" id="step'.$step_id.'">Industry</div>
				<center><div style="width:16px;height:16px;background-image:url(\'/official-server/images/flags.png\');background-repeat:no-repeat;background-position:0px -32px;"></center>
				<div class="value">';

				$map_descriptor.='<center><table class="item_list item_list_type_normal">
				<tr class="item_list_title item_list_title_type_normal">
					<th>Industry</th>
					<th>Resources</th>
					<th>Products</th>
				</tr>';
				$industry=$industries_meta[$step['industry']];
				$map_descriptor.='<tr class="value">';
				$map_descriptor.='<td>';
				$map_descriptor.='<a href="'.$base_datapack_explorer_site_path.'industries/'.$step['industry'].'.html">#'.$step['industry'].'</a>';
				$map_descriptor.='</td>';
				$map_descriptor.='<td>';
				foreach($industry['resources'] as $item=>$quantity)
				{
					if(isset($item_meta[$item]))
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
					else
						$map_descriptor.='Unknown item';
				}
				$map_descriptor.='</td>';
				$map_descriptor.='<td>';
				foreach($industry['products'] as $item=>$quantity)
				{
					if(isset($item_meta[$item]))
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
					else
						$map_descriptor.='Unknown item';
				}
				$map_descriptor.='</td>';
				$map_descriptor.='</tr>';
				$map_descriptor.='<tr>
					<td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
				</tr>
				</table></center>';

				$map_descriptor.='</div>
				</div>';
			}
			else
				$map_descriptor.='<div class="subblock"><div class="valuetitle" id="step'.$step_id.'">Unknown type ('.$step['type'].')</div></div>';
		}
	$map_descriptor.='</div>';

	$content=$template;
	if($bot['name']=='')
		$content=str_replace('${TITLE}','Bot #'.$bot_id,$content);
	else
		$content=str_replace('${TITLE}',$bot['name'],$content);
	$content=str_replace('${CONTENT}',$map_descriptor,$content);
	$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
	$content=preg_replace("#[\r\n\t]+#isU",'',$content);
	filewrite($datapack_explorer_local_path.'bots/'.text_operation_do_for_url($final_url_name).'.html',$content);
}

$map_descriptor='';

foreach($bots_by_zone as $zone=>$bot_id_list)
{
    if(count($bot_id_list))
    {
        $map_descriptor.='<table class="item_list item_list_type_normal map_list">
        <tr class="item_list_title item_list_title_type_normal">
            <th colspan="2">';
        if($zone!='')
            $map_descriptor.=$zone;
        else
            $map_descriptor.='Unknown zone';
        $map_descriptor.='</th>
        </tr>';
            $name_count_for_zone=array();
        foreach($bot_id_list as $bot_id)
        {
            $bot=$bots_meta[$bot_id];
            if($bot['name']!='')
            {
                if(isset($bot_id_to_map[$bot_id]))
                {
                    if(isset($maps_list[$bot_id_to_map[$bot_id]]))
                        $final_name=$name_count_for_zone[$bot['name']][$maps_list[$bot_id_to_map[$bot_id]]['name']]=true;
                    else
                        $final_name=$name_count_for_zone[$bot['name']]['']=true;
                }
                else
                    $final_name=$name_count_for_zone[$bot['name']]['']=true;
            }
        }
        foreach($bot_id_list as $bot_id)
        {
            $bot=$bots_meta[$bot_id];
            if($bot['name']=='')
            {
                $final_name='Bot #'.$bot_id;
                $final_url_name='bot '.$bot_id;
            }
            elseif(count($name_count_for_zone[$bot['name']])==1)
            {
                $final_name=$bot['name'];
                $final_url_name=$bot['name'];
            }
            else
            {
                if(isset($bot_id_to_map[$bot_id]))
                {
                    if(isset($maps_list[$bot_id_to_map[$bot_id]]))
                        $final_name=$bot['name'].' ('.$maps_list[$bot_id_to_map[$bot_id]]['name'].')';
                    else
                        $final_name=$bot['name'];
                }
                else
                    $final_name=$bot['name'];
                $final_url_name=$bot_id.'-'.$bot['name'];
            }
            $map_descriptor.='<tr class="value">';
                $skin_found=true;
                if(isset($bot_id_to_skin[$bot_id]))
                {
                    if(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.png'))
                        $map_descriptor.='<td><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></div></td>';
                    elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.png'))
                        $map_descriptor.='<td><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></div></td>';
                    elseif(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.gif'))
                        $map_descriptor.='<td><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></div></td>';
                    elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.gif'))
                        $map_descriptor.='<td><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></div></td>';
                    else
                        $skin_found=false;
                }
                else
                    $skin_found=false;
            $map_descriptor.='<td';
            if(!$skin_found)
                $map_descriptor.=' colspan="2"';
            $map_descriptor.='><a href="'.$base_datapack_explorer_site_path.'bots/'.text_operation_do_for_url($final_url_name).'.html" title="'.$final_name.'">'.$final_name.'</a></td>';
            $map_descriptor.='</tr>';
        }
        $map_descriptor.='<tr>
            <td colspan="2" class="item_list_endline item_list_title_type_normal"></td>
        </tr>
        </table>';
    }
}

$content=$template;
$content=str_replace('${TITLE}','Bots list',$content);
$content=str_replace('${CONTENT}',$map_descriptor,$content);
$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
$content=preg_replace("#[\r\n\t]+#isU",'',$content);
filewrite($datapack_explorer_local_path.'bots.html',$content);