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
                if(!isset($bots_by_zone[$zone_meta[$maps_list[$bot_id_to_map[$bot_id]]['zone']]['name'][$current_lang]]))
                    $bots_by_zone[$zone_meta[$maps_list[$bot_id_to_map[$bot_id]]['zone']]['name'][$current_lang]]=array();
                $bots_by_zone[$zone_meta[$maps_list[$bot_id_to_map[$bot_id]]['zone']]['name'][$current_lang]][]=$bot_id;
            }
            else
            {
                if(!isset($bots_by_zone[$maps_list[$bot_id_to_map[$bot_id]]['name'][$current_lang]]))
                    $bots_by_zone[$maps_list[$bot_id_to_map[$bot_id]]['name'][$current_lang]]=array();
                $bots_by_zone[$maps_list[$bot_id_to_map[$bot_id]]['name'][$current_lang]][]=$bot_id;
            }
        }
        else
            $bots_by_zone[''][]=$bot_id;
        }
        else
        $bots_by_zone[''][]=$bot_id;
    }
	$map_descriptor='';

	$map_descriptor.='<div class="map item_details">'."\n";
        if($bot['name'][$current_lang]=='')
            $final_url_name='bot-'.$bot_id;
        else if($bots_name_count[$current_lang][$bot['name'][$current_lang]]==1)
            $final_url_name=$bot['name'][$current_lang];
        else
            $final_url_name=$bot_id.'-'.$bot['name'][$current_lang];
		if($bot['name'][$current_lang]=='')
			$map_descriptor.='<div class="subblock"><h1>Bot #'.$bot_id.'</h1>'."\n";
		else
		{
			$map_descriptor.='<div class="subblock"><h1>'.$bot['name'][$current_lang].'</h1>'."\n";
			$map_descriptor.='<h2>Bot #'.$bot_id.'</h2>'."\n";
		}
		if(isset($bot_id_to_skin[$bot_id]))
		{
			if(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.png'))
				$map_descriptor.='<center><h2><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_http.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;" title="Skin: '.$bot_id_to_skin[$bot_id].'"></div></h2></center>'."\n";
			elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.png'))
				$map_descriptor.='<center><h2><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_http.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;" title="Skin: '.$bot_id_to_skin[$bot_id].'"></div></h2></center>'."\n";
			elseif(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.gif'))
				$map_descriptor.='<center><h2><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_http.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;" title="Skin: '.$bot_id_to_skin[$bot_id].'"></div></h2></center>'."\n";
			elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.gif'))
				$map_descriptor.='<center><h2><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_http.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;" title="Skin: '.$bot_id_to_skin[$bot_id].'"></div></h2></center>'."\n";
		}
		$map_descriptor.='</div>'."\n";
		if(isset($bot_id_to_skin[$bot_id]))
		{
			if(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/front.png'))
			{
				$map_descriptor.='<div class="value datapackscreenshot"><center>'."\n";
				$map_descriptor.='<img src="'.$base_datapack_site_http.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/front.png" width="80" height="80" alt="" />'."\n";
				$map_descriptor.='</center></div>'."\n";
			}
			else if(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/front.png'))
			{
				$map_descriptor.='<div class="value datapackscreenshot"><center>'."\n";
				$map_descriptor.='<img src="'.$base_datapack_site_http.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/front.png" width="80" height="80" alt="" />'."\n";
				$map_descriptor.='</center></div>'."\n";
			}
			elseif(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/front.gif'))
			{
				$map_descriptor.='<div class="value datapackscreenshot"><center>'."\n";
				$map_descriptor.='<img src="'.$base_datapack_site_http.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/front.gif" width="80" height="80" alt="" />'."\n";
				$map_descriptor.='</center></div>'."\n";
			}
			else if(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/front.gif'))
			{
				$map_descriptor.='<div class="value datapackscreenshot"><center>'."\n";
				$map_descriptor.='<img src="'.$base_datapack_site_http.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/front.gif" width="80" height="80" alt="" />'."\n";
				$map_descriptor.='</center></div>'."\n";
			}
		}
		if(isset($bot_id_to_map[$bot_id]))
		{
			if(isset($maps_list[$bot_id_to_map[$bot_id]]))
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Map'].'</div><div class="value">'."\n";
				if(isset($zone_meta[$maps_list[$bot_id_to_map[$bot_id]]['zone']]))
				{
					$map_descriptor.='[['.$translation_list[$current_lang]['Maps:'].map_to_wiki_name($bot_id_to_map[$bot_id]).'|'.$maps_list[$bot_id_to_map[$bot_id]]['name'][$current_lang].']]'."\n";
					$zone_name=$zone_meta[$maps_list[$bot_id_to_map[$bot_id]]['zone']]['name'][$current_lang];
					$map_descriptor.='(Zone: [['.$translation_list[$current_lang]['Zones:'].$zone_name.'|'.$zone_name.']])'."\n";
				}
				else
					$map_descriptor.='[['.$translation_list[$current_lang]['Maps:'].map_to_wiki_name($bot_id_to_map[$bot_id]).'|'.$maps_list[$bot_id_to_map[$bot_id]]['name'][$current_lang].']]'."\n";
				$map_descriptor.='</div></div>'."\n";
			}
		}
        if(isset($bot_start_to_quests[$bot_id]))
        {
            $map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Quest start'].'</div><div class="value">'."\n";
            $map_descriptor.=questList($bot_start_to_quests[$bot_id],false);
            $map_descriptor.='</div></div>'."\n";
        }
		foreach($bot['step'] as $step_id=>$step)
		{
			if($step['type']=='text')
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle" id="step'.$step_id.'">'.$translation_list[$current_lang]['Text'].'</div><div class="value">'."\n";
				$map_descriptor.=preg_replace('#a href="([0-9]+)#isU','a href="#step$1',$step['text'][$current_lang]);
				$map_descriptor.='</div></div>'."\n";
			}
			else if($step['type']=='shop')
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle" id="step'.$step_id.'">'.$translation_list[$current_lang]['Shop'].'</div>
				<center><div style="background-position:-32px 0px;float:none;" class="flags flags16"></div></center>
				<div class="value">'."\n";
				$map_descriptor.='<center><table class="item_list item_list_type_normal">
				<tr class="item_list_title item_list_title_type_normal">
					<th colspan="2">'.$translation_list[$current_lang]['Item'].'</th>
					<th>'.$translation_list[$current_lang]['Price'].'</th>
				</tr>'."\n";
				foreach($shop_meta[$step['shop']]['products'] as $item=>$price)
				{
					if(isset($item_meta[$item]))
					{
						$map_descriptor.='<tr class="value">'."\n";
						if(isset($item_meta[$item]))
						{
							$link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$item]['name'][$current_lang]).'.html'."\n";
							$name=$item_meta[$item]['name'][$current_lang];
							if($item_meta[$item]['image']!='')
								$image=$base_datapack_site_http.$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
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
						<td>'."\n";
						if($image!='')
						{
							if($link!='')
								$map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$name.'|';
							$map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
							if($link!='')
								$map_descriptor.=']]'."\n";
						}
						$map_descriptor.='</td>
						<td>'."\n";
						if($link!='')
							$map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$name.'|';
						if($name!='')
							$map_descriptor.=$name;
						else
							$map_descriptor.='Unknown item';
						if($link!='')
							$map_descriptor.=']]'."\n";
						$map_descriptor.='</td>'."\n";
						$map_descriptor.='<td>'.$price.'$</td>'."\n";
						$map_descriptor.='</tr>'."\n";
					}
				}
				$map_descriptor.='<tr>
					<td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
				</tr>
				</table>'."\n";
				$map_descriptor.='</center>'."\n";
				$map_descriptor.='</div></div>'."\n";
			}
			else if($step['type']=='fight')
			{
				if(isset($fight_meta[$step['fightid']]))
				{
					$map_descriptor.='<div class="subblock"><div class="valuetitle" id="step'.$step_id.'">'.$translation_list[$current_lang]['Fight'].'</div>
					<div style="background-position:-16px -16px;float:none;" class="flags flags16">
					<div class="value">'."\n";
					if($fight_meta[$step['fightid']]['cash']>0)
						$map_descriptor.=$translation_list[$current_lang]['Rewards'].': <b>'.$fight_meta[$step['fightid']]['cash'].'$</b><br />'."\n";

                    if(count($fight_meta[$step['fightid']]['items'])>0)
                    {
                        $map_descriptor.='<center><table class="item_list item_list_type_normal">
                        <tr class="item_list_title item_list_title_type_normal">
                            <th colspan="2">'.$translation_list[$current_lang]['Item'].'</th>
                        </tr>'."\n";
                        foreach($fight_meta[$step['fightid']]['items'] as $item)
                        {
                            if(isset($item_meta[$item['item']]))
                            {
                                $link_item=$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$item['item']]['name'][$current_lang]).'.html'."\n";
                                $name=$item_meta[$item['item']]['name'][$current_lang];
                                if($item_meta[$item['item']]['image']!='')
                                    $image=$base_datapack_site_http.$base_datapack_site_path.'/items/'.$item_meta[$item['item']]['image'];
                                else
                                    $image='';
                            }
                            else
                            {
                                $link_item='';
                                $name='';
                                $image='';
                            }
                            $quantity_text='';
                            if($item['quantity']>1)
                                $quantity_text=$item['quantity'].' '."\n";
                            $map_descriptor.='<tr class="value">
                                <td>'."\n";
                                if($image!='')
                                {
                                    if($link_item!='')
                                        $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$name.'|';
                                    $map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
                                    if($link_item!='')
                                        $map_descriptor.=']]';
                                }
                                $map_descriptor.='</td>
                                <td>'."\n";
                                if($link_item!='')
                                    $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$name.'|';
                                if($name!='')
                                    $map_descriptor.=$quantity_text.$name;
                                else
                                    $map_descriptor.=$quantity_text.$translation_list[$current_lang]['Unknown item'];
                                if($link_item!='')
                                    $map_descriptor.=']]'."\n";
                                $map_descriptor.='</td>'."\n";
                                $map_descriptor.='</tr>'."\n";
                        }
                        $map_descriptor.='<tr>
                            <td colspan="2" class="item_list_endline item_list_title_type_normal"></td>
                        </tr>
                        </table></center>'."\n";
                    }
                    $map_descriptor.='<br style="clear:both;" />'."\n";

					foreach($fight_meta[$step['fightid']]['monsters'] as $monster)
					{
						if(isset($monster_meta[$monster['monster']]))
						{
							$monster_full=$monster_meta[$monster['monster']];
							$map_descriptor.='<table class="item_list item_list_type_'.$monster_full['type'][0].' map_list">
							<tr class="item_list_title item_list_title_type_'.$monster_full['type'][0].'">
								<th>'."\n";
							$map_descriptor.='</th>
							</tr>'."\n";
							$map_descriptor.='<tr class="value">'."\n";
							$map_descriptor.='<td>'."\n";
							$map_descriptor.='<table class="monsterforevolution">'."\n";
							if(file_exists($datapack_path.'monsters/'.$monster['monster'].'/front.png'))
								$map_descriptor.='<tr><td><center>[['.$translation_list[$current_lang]['Monsters:'].$monster_full['name'][$current_lang].'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$monster['monster'].'/front.png" width="80" height="80" alt="'.$monster_full['name'][$current_lang].'" title="'.$monster_full['name'][$current_lang].'" />]]</center></td></tr>'."\n";
							else if(file_exists($datapack_path.'monsters/'.$monster['monster'].'/front.gif'))
								$map_descriptor.='<tr><td><center>[['.$translation_list[$current_lang]['Monsters:'].$monster_full['name'][$current_lang].'|<img src="'.$base_datapack_site_http.$base_datapack_site_path.'monsters/'.$monster['monster'].'/front.gif" width="80" height="80" alt="'.$monster_full['name'][$current_lang].'" title="'.$monster_full['name'][$current_lang].'" />]]</center></td></tr>'."\n";
							$map_descriptor.='<tr><td class="evolution_name">[['.$translation_list[$current_lang]['Monsters:'].$monster_full['name'][$current_lang].'|'.$monster_full['name'][$current_lang].']]</td></tr>'."\n";
                            $map_descriptor.='<tr><td>'."\n";
                            $type_list=array();
                            foreach($monster_meta[$monster['monster']]['type'] as $type)
                                if(isset($type_meta[$type]))
                                    $type_list[]='<span class="type_label type_label_'.$type.'">[['.$translation_list[$current_lang]['Monsters type:'].$type_meta[$type]['name'][$current_lang].'|'.$type_meta[$type]['name'][$current_lang].']]</span>'."\n";
                            $map_descriptor.='<div class="type_label_list">'.implode(' ',$type_list).'</div></td></tr>'."\n";
							$map_descriptor.='<tr><td>'.$translation_list[$current_lang]['Level'].' '.$monster['level'].'</td></tr>'."\n";
							$map_descriptor.='</table>'."\n";
							$map_descriptor.='</td>'."\n";
							$map_descriptor.='</tr>'."\n";
							$map_descriptor.='<tr>
								<th class="item_list_endline item_list_title item_list_title_type_'.$monster_full['type'][0].'">'."\n";
							$map_descriptor.='</th>
							</tr>
							</table>'."\n";
						}
					}
					$map_descriptor.='<br style="clear:both;" />'."\n";

					$map_descriptor.='</div></div>'."\n";
                    $map_descriptor.='<br style="clear:both" />'."\n";
				}
			}
			else if($step['type']=='heal')
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle" id="step'.$step_id.'">'.$translation_list[$current_lang]['Heal'].'</div>
				<div class="value">
					<center><div style="background-position:0px 0px;float:none;" class="flags flags128"></div></center>
				</div>'."\n";
			}
			else if($step['type']=='learn')
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle" id="step'.$step_id.'">'.$translation_list[$current_lang]['Learn'].'</div>
				<div class="value">
					<center><div style="background-position:-384px 0px;float:none;" class="flags flags128"></div></center>
				</div>'."\n";
			}
			else if($step['type']=='warehouse')
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle" id="step'.$step_id.'">'.$translation_list[$current_lang]['Warehouse'].'</div>
				<div class="value">
					<center><div style="background-position:0px -128px;float:none;" class="flags flags128"></div></center>
				</div>'."\n";
			}
			else if($step['type']=='market')
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle" id="step'.$step_id.'">'.$translation_list[$current_lang]['Market'].'</div>
				<div class="value">
					<center><div style="background-position:0px -128px;float:none;" class="flags flags128"></div></center>
				</div>'."\n";
			}
			else if($step['type']=='clan')
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle" id="step'.$step_id.'">'.$translation_list[$current_lang]['Clan'].'</div>
				<div class="value">
					<center><div style="background-position:-384px -128px;float:none;" class="flags flags128"></div></center>
				</div>'."\n";
			}
			else if($step['type']=='sell')
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle" id="step'.$step_id.'">'.$translation_list[$current_lang]['Sell'].'</div>
				<div class="value">
					<center><div style="background-position:-256px 0px;float:none;" class="flags flags128"></div></center>
				</div>'."\n";
			}
			else if($step['type']=='zonecapture')
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle" id="step'.$step_id.'">'.$translation_list[$current_lang]['Zone capture'].'</div>
				<div class="value">
					Zone: '."\n";
				if(isset($zone_meta[$step['zone']]))
					$map_descriptor.=$zone_meta[$step['zone']]['name'][$current_lang];
				else
					$map_descriptor.='Unknown zone'."\n";
				$map_descriptor.='<center><div style="background-position:-256px -128px;float:none;" class="flags flags128"></div></center>
				</div>'."\n";
			}
			else if($step['type']=='industry')
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle" id="step'.$step_id.'">'.$translation_list[$current_lang]['Industry'].'</div>
				<center><div style="background-position:0px -32px;float:none;" class="flags flags16"></div></center>
				<div class="value">'."\n";
                if(!isset($industrie_meta[$step['industry']]))
                {
                    $map_descriptor.='Industry '.$step['industry'].' not found for bot '.$bot_id.'!</td>'."\n";
                    echo 'Industry '.$step['industry'].' not found for bot '.$bot_id.'!'."\n";
                }
                else
                {
                    $map_descriptor.='<center><table class="item_list item_list_type_normal">
                    <tr class="item_list_title item_list_title_type_normal">
                        <th>'.$translation_list[$current_lang]['Industry'].'</th>
                        <th>'.$translation_list[$current_lang]['Resources'].'</th>
                        <th>'.$translation_list[$current_lang]['Products'].'</th>
                    </tr>'."\n";
                    $industry=$industrie_meta[$step['industry']];
                    $map_descriptor.='<tr class="value">'."\n";
                    $map_descriptor.='<td>'."\n";
                    $map_descriptor.='[['.$translation_list[$current_lang]['Industries:'].str_replace('[id]',$step['industry'],$translation_list[$current_lang]['Industry [id]']).'|'.str_replace('[id]',$step['industry'],$translation_list[$current_lang]['Industry [id]']).']]'."\n";
                    $map_descriptor.='</td>'."\n";
                    $map_descriptor.='<td>'."\n";
                    foreach($industry['resources'] as $resources)
                    {
                        $item=$resources['item'];
                        $quantity=$resources['quantity'];
                        if(isset($item_meta[$item]))
                        {
                            $link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$item]['name'][$current_lang]).'.html'."\n";
                            $name=$item_meta[$item]['name'][$current_lang];
                            if($item_meta[$item]['image']!='')
                                $image=$base_datapack_site_http.$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
                            else
                                $image='';
                            $map_descriptor.='<div style="float:left;text-align:center;">'."\n";
                            if($image!='')
                            {
                                if($link!='')
                                    $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$name.'|';
                                $map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
                                if($link!='')
                                    $map_descriptor.=']]';
                            }
                            if($link!='')
                                $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$name.'|';
                            if($name!='')
                                $map_descriptor.=$name;
                            else
                                $map_descriptor.=$translation_list[$current_lang]['Unknown item'];
                            if($link!='')
                                $map_descriptor.=']]</div>'."\n";
                        }
                        else
                            $map_descriptor.='Unknown item'."\n";
                    }
                    $map_descriptor.='</td>'."\n";
                    $map_descriptor.='<td>'."\n";
                    foreach($industry['products'] as $products)
                    {
                        $item=$products['item'];
                        $quantity=$products['quantity'];
                        if(isset($item_meta[$item]))
                        {
                            $link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['items/'].text_operation_do_for_url($item_meta[$item]['name'][$current_lang]).'.html'."\n";
                            $name=$item_meta[$item]['name'][$current_lang];
                            if($item_meta[$item]['image']!='')
                                $image=$base_datapack_site_http.$base_datapack_site_path.'/items/'.$item_meta[$item]['image'];
                            else
                                $image='';
                            $map_descriptor.='<div style="float:left;text-align:middle;">'."\n";
                            if($image!='')
                            {
                                if($link!='')
                                    $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$name.'|';
                                $map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
                                if($link!='')
                                    $map_descriptor.=']]';
                            }
                            if($link!='')
                                $map_descriptor.='[['.$translation_list[$current_lang]['Items:'].$name.'|';
                            if($name!='')
                                $map_descriptor.=$name;
                            else
                                $map_descriptor.=$translation_list[$current_lang]['Unknown item'];
                            if($link!='')
                                $map_descriptor.=']]</div>'."\n";
                        }
                        else
                            $map_descriptor.=$translation_list[$current_lang]['Unknown item'];
                    }
                    $map_descriptor.='</td>'."\n";
                    $map_descriptor.='</tr>'."\n";
                    $map_descriptor.='<tr>
                        <td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
                    </tr>
                    </table></center>'."\n";
                }
                $map_descriptor.='
                </div>'."\n";
			}
			else
				$map_descriptor.='<div class="subblock"><div class="valuetitle" id="step'.$step_id.'">'.$translation_list[$current_lang]['Unknown type'].' ('.$step['type'].')</div></div>'."\n";
		}
	$map_descriptor.='</div>'."\n";

    savewikipage('Template:bot'.$bot_id,$map_descriptor,false);$map_descriptor='';

    $lang_template='';
    if(count($wikivarsapp)>1)
    {
        $temp_current_lang=$current_lang;
        foreach($wikivarsapp as $wikivars2)
            if($wikivars2['lang']!=$temp_current_lang)
            {
                $current_lang=$wikivars2['lang'];
                $link=bot_to_wiki_name($bot_id);
                $lang_template.='[['.$current_lang.':'.$translation_list[$current_lang]['Bots:'].$link.']]'."\n";
            }
        savewikipage('Template:bot'.$bot_id.'_LANG',$lang_template,false);$lang_template='';
        $current_lang=$temp_current_lang;
        $map_descriptor.='{{Template:bot'.$bot_id.'_LANG}}'."\n";
    }

    $link=bot_to_wiki_name($bot_id);
    $map_descriptor.='{{Template:bot'.$bot_id.'}}'."\n";
    savewikipage($translation_list[$current_lang]['Bots:'].$link,$map_descriptor,!$wikivars['generatefullpage']);
}



$map_descriptor='';

$map_descriptor.='<!-- highest bot id: '.$highest_bot_id.' -->'."\n";
foreach($bots_by_zone as $zone=>$bot_id_list)
{
    if(count($bot_id_list))
    {
        $map_descriptor.='<table class="item_list item_list_type_normal map_list">
        <tr class="item_list_title item_list_title_type_normal">
            <th colspan="2">'."\n";
        if($zone!='')
        {
            if(isset($zone_name_to_code[$current_lang][$zone]))
                $map_descriptor.='[['.$translation_list[$current_lang]['Zones:'].$zone.'|'.$zone.']]'."\n";
            else
            {
                if(isset($maps_name_to_map[$zone]))
                    $map_descriptor.='[['.$translation_list[$current_lang]['Maps:'].map_to_wiki_name($maps_name_to_map[$zone]).'|'.$maps_list[$maps_name_to_map[$zone]]['name'][$current_lang].']]'."\n";
                else
                    $map_descriptor.=$zone;
            }
        }
        else
            $map_descriptor.=$translation_list[$current_lang]['Unknown zone'];
        $map_descriptor.='</th>
        </tr>'."\n";
        $name_count_for_zone=array();
        foreach($bot_id_list as $bot_id)
        {
            $bot=$bots_meta[$bot_id];
            if($bot['name'][$current_lang]!='')
            {
                if(isset($bot_id_to_map[$bot_id]))
                {
                    if(isset($maps_list[$bot_id_to_map[$bot_id]]))
                        $final_name=$name_count_for_zone[$bot['name'][$current_lang]][$maps_list[$bot_id_to_map[$bot_id]]['name'][$current_lang]]=true;
                    else
                        $final_name=$name_count_for_zone[$bot['name'][$current_lang]]['']=true;
                }
                else
                    $final_name=$name_count_for_zone[$bot['name'][$current_lang]]['']=true;
            }
        }
        foreach($bot_id_list as $bot_id)
        {
            $bot=$bots_meta[$bot_id];
            if($bot['name'][$current_lang]=='')
                $final_name='Bot #'.$bot_id;
            elseif(count($name_count_for_zone[$bot['name'][$current_lang]])==1)
                $final_name=$bot['name'][$current_lang];
            else
            {
                if(isset($bot_id_to_map[$bot_id]))
                {
                    if(isset($maps_list[$bot_id_to_map[$bot_id]]))
                        $final_name=$bot['name'][$current_lang].' ('.$maps_list[$bot_id_to_map[$bot_id]]['name'][$current_lang].')'."\n";
                    else
                        $final_name=$bot['name'][$current_lang];
                }
                else
                    $final_name=$bot['name'][$current_lang];
            }
            if($bot['name'][$current_lang]=='')
                $final_url_name='bot-'.$bot_id;
            else if($bots_name_count[$current_lang][$bot['name'][$current_lang]]==1)
                $final_url_name=$bot['name'][$current_lang];
            else
                $final_url_name=$bot_id.'-'.$bot['name'][$current_lang];
            $map_descriptor.='<tr class="value">'."\n";
                $skin_found=true;
                if(isset($bot_id_to_skin[$bot_id]))
                {
                    if(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.png'))
                        $map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_http.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></div></center></td>'."\n";
                    elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.png'))
                        $map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_http.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;"></div></center></td>'."\n";
                    elseif(file_exists($datapack_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.gif'))
                        $map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_http.$base_datapack_site_path.'skin/bot/'.$bot_id_to_skin[$bot_id].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></div></center></td>'."\n";
                    elseif(file_exists($datapack_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.gif'))
                        $map_descriptor.='<td><center><div style="width:16px;height:24px;background-image:url(\''.$base_datapack_site_http.$base_datapack_site_path.'skin/fighter/'.$bot_id_to_skin[$bot_id].'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;"></div></center></td>'."\n";
                    else
                        $skin_found=false;
                }
                else
                    $skin_found=false;
            $map_descriptor.='<td'."\n";
            if(!$skin_found)
                $map_descriptor.=' colspan="2"'."\n";
    
            $link=bot_to_wiki_name($bot_id);
            if($bot['name'][$current_lang]=='')
                $map_descriptor.='>[['.$translation_list[$current_lang]['Bots:'].$link.'|Bot #'.$bot_id.']]</td>'."\n";
            else
                $map_descriptor.='>[['.$translation_list[$current_lang]['Bots:'].$link.'|'.$bot['name'][$current_lang].']]</td>'."\n";

            $map_descriptor.='</tr>'."\n";
        }
        $map_descriptor.='<tr>
            <td colspan="2" class="item_list_endline item_list_title_type_normal"></td>
        </tr>
        </table>'."\n";
    }
}
savewikipage('Template:bots_list',$map_descriptor,false);$map_descriptor='';

$lang_template='';
if(count($wikivarsapp)>1)
{
    foreach($wikivarsapp as $wikivars2)
        if($wikivars2['lang']!=$current_lang)
            $lang_template.='[['.$wikivars2['lang'].':'.$translation_list[$wikivars2['lang']]['Bots list'].']]'."\n";
    savewikipage('Template:bots_list_LANG',$lang_template,false);$lang_template='';
    $map_descriptor.='{{Template:bots_list_LANG}}'."\n";
}

$map_descriptor.='{{Template:bots_list}}'."\n";
savewikipage($translation_list[$current_lang]['Bots list'],$map_descriptor,!$wikivars['generatefullpage']);
