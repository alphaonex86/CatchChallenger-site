<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator skills'."\n");

$map_descriptor='';

$map_descriptor.='<div class="map map_type_city">';
    $map_descriptor.='<div class="subblock"><h1>';
    if(isset($informations_meta['name'][$current_lang]))
        $map_descriptor.=htmlspecialchars($informations_meta['name'][$current_lang]);
    else if(isset($informations_meta['name']['en']))
        $map_descriptor.=htmlspecialchars($informations_meta['name']['en']);
    else
        $map_descriptor.='Informations';
    $map_descriptor.='</h1></div>';
    if(isset($informations_meta['description'][$current_lang]))
        $map_descriptor.='<div class="type_label_list">'.htmlspecialchars($informations_meta['description'][$current_lang]).'</div>';
    else if(isset($informations_meta['description']['en']))
        $map_descriptor.='<div class="type_label_list">'.htmlspecialchars($informations_meta['description']['en']).'</div>';

$map_descriptor.='<div class="subblock"><div class="valuetitle">Main part(s)</div><div class="value">';
foreach($informations_meta['main'] as $maindatapackcode=>$mainContent)
{
    $map_descriptor.='<div class="map map_type_city">';
        $map_descriptor.='<div class="subblock"><h1';
        if($mainContent['initial']=='' && $mainContent['color']!='')
            $map_descriptor.=' style="color:'.$mainContent['color'].'"';
        $map_descriptor.='>';
        if(isset($mainContent['name'][$current_lang]))
            $map_descriptor.=htmlspecialchars($mainContent['name'][$current_lang]);
        else if(isset($mainContent['name']['en']))
            $map_descriptor.=htmlspecialchars($mainContent['name']['en']);
        else
            $map_descriptor.='Informations';
        if($mainContent['initial']!='')
        {
            $color_temp_sub='';
            if($mainContent['color']!='')
                $map_descriptor.='&nbsp;<span style="background-color:'.$mainContent['color'].';" class="datapackinital">'.$mainContent['initial'].'</span>';
            else
                $map_descriptor.='&nbsp;<span class="datapackinital">'.$mainContent['initial'].'</span>';
        }
        $map_descriptor.='</h1></div>';
        if(isset($mainContent['description'][$current_lang]))
            $map_descriptor.='<div class="type_label_list">'.htmlspecialchars($mainContent['description'][$current_lang]).'</div>';
        else if(isset($mainContent['description']['en']))
            $map_descriptor.='<div class="type_label_list">'.htmlspecialchars($mainContent['description']['en']).'</div>';

        if(count($mainContent['monsters']))
        {
            $map_descriptor.='<div class="subblock"><div class="valuetitle">Monster specific</div><div class="value">';
            $map_descriptor.='<table class="item_list item_list_type_normal">
            <tr class="item_list_title item_list_title_type_normal">
                <th colspan="2">'.$translation_list[$current_lang]['Monster'].'</th>
            </tr>';
            foreach($mainContent['monsters'] as $item_to_monster_list)
            {
                $monsterId=$monsterId;
                if(isset($monster_meta[$monsterId]))
                {
                    if($item_to_monster_list['quantity_min']!=$item_to_monster_list['quantity_max'])
                        $quantity_text=$item_to_monster_list['quantity_min'].' to '.$item_to_monster_list['quantity_max'];
                    else
                        $quantity_text=$item_to_monster_list['quantity_min'];
                    $name=$monster_meta[$monsterId]['name'][$current_lang];
                    $link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($name).'.html';
                    $map_descriptor.='<tr class="value">';
                    $map_descriptor.='<td>';
                    if(file_exists($datapack_path.'monsters/'.$monsterId.'/small.png'))
                        $map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$monsterId.'/small.png" width="32" height="32" alt="'.$monster_meta[$monsterId]['name'][$current_lang].'" title="'.$monster_meta[$monsterId]['name'][$current_lang].'" /></a></div>';
                    else if(file_exists($datapack_path.'monsters/'.$monsterId.'/small.gif'))
                        $map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$monsterId.'/small.gif" width="32" height="32" alt="'.$monster_meta[$monsterId]['name'][$current_lang].'" title="'.$monster_meta[$monsterId]['name'][$current_lang].'" /></a></div>';
                    $map_descriptor.='</td>
                    <td><a href="'.$link.'">'.$name.'</a></td>';
                    $map_descriptor.='</tr>';
                }
            }
            $map_descriptor.='<tr>';
                $map_descriptor.='<td colspan="2" class="item_list_endline item_list_title_type_normal"></td>';
            $map_descriptor.='</tr>
            </table>';
            $map_descriptor.='</div></div>';
        }

        $map_descriptor.='<div class="subblock"><div class="valuetitle">Sub part(s)</div><div class="value">';
        foreach($mainContent['sub'] as $subdatapackcode=>$subContent)
        {
            $map_descriptor.='<div class="map map_type_city">';
                $map_descriptor.='<div class="subblock"><h1';
                if($subContent['initial']=='' && $subContent['color']!='')
                    $map_descriptor.=' style="color:'.$subContent['color'].'"';
                $map_descriptor.='>';
                if(isset($subContent['name'][$current_lang]))
                    $map_descriptor.=htmlspecialchars($subContent['name'][$current_lang]);
                else if(isset($subContent['name']['en']))
                    $map_descriptor.=htmlspecialchars($subContent['name']['en']);
                else
                    $map_descriptor.='Informations';
                if($subContent['initial']!='')
                {
                    $color_temp_sub='';
                    if($subContent['color']!='')
                        $map_descriptor.='&nbsp;<span style="background-color:'.$subContent['color'].';" class="datapackinital">'.$subContent['initial'].'</span>';
                    else
                        $map_descriptor.='&nbsp;<span class="datapackinital">'.$subContent['initial'].'</span>';
                }
                $map_descriptor.='</h1></div>';
                if(isset($subContent['description'][$current_lang]))
                    $map_descriptor.='<div class="type_label_list">'.htmlspecialchars($subContent['description'][$current_lang]).'</div>';
                else if(isset($subContent['description']['en']))
                    $map_descriptor.='<div class="type_label_list">'.htmlspecialchars($subContent['description']['en']).'</div>';

                if(count($subContent['monsters']))
                {
                    $map_descriptor.='<div class="subblock"><div class="valuetitle">Monster specific</div><div class="value">';
                    $map_descriptor.='<table class="item_list item_list_type_normal">
                    <tr class="item_list_title item_list_title_type_normal">
                        <th colspan="2">'.$translation_list[$current_lang]['Monster'].'</th>
                    </tr>';
                    foreach($mainContent['monsters'] as $item_to_monster_list)
                    {
                        $monsterId=$monsterId;
                        if(isset($monster_meta[$monsterId]))
                        {
                            if($item_to_monster_list['quantity_min']!=$item_to_monster_list['quantity_max'])
                                $quantity_text=$item_to_monster_list['quantity_min'].' to '.$item_to_monster_list['quantity_max'];
                            else
                                $quantity_text=$item_to_monster_list['quantity_min'];
                            $name=$monster_meta[$monsterId]['name'][$current_lang];
                            $link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($name).'.html';
                            $map_descriptor.='<tr class="value">';
                            $map_descriptor.='<td>';
                            if(file_exists($datapack_path.'monsters/'.$monsterId.'/small.png'))
                                $map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$monsterId.'/small.png" width="32" height="32" alt="'.$monster_meta[$monsterId]['name'][$current_lang].'" title="'.$monster_meta[$monsterId]['name'][$current_lang].'" /></a></div>';
                            else if(file_exists($datapack_path.'monsters/'.$monsterId.'/small.gif'))
                                $map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$monsterId.'/small.gif" width="32" height="32" alt="'.$monster_meta[$monsterId]['name'][$current_lang].'" title="'.$monster_meta[$monsterId]['name'][$current_lang].'" /></a></div>';
                            $map_descriptor.='</td>
                            <td><a href="'.$link.'">'.$name.'</a></td>';
                            $map_descriptor.='</tr>';
                        }
                    }
                    $map_descriptor.='<tr>';
                        $map_descriptor.='<td colspan="2" class="item_list_endline item_list_title_type_normal"></td>';
                    $map_descriptor.='</tr>
                    </table>';
                    $map_descriptor.='</div></div>';
                }

            $map_descriptor.='</div>';
        }
        $map_descriptor.='</div></div>';

    $map_descriptor.='</div>';
}
$map_descriptor.='</div></div>';

$map_descriptor.='</div>';

$content=$template;
if(isset($informations_meta['name'][$current_lang]))
    $content=str_replace('${TITLE}',htmlspecialchars($informations_meta['name'][$current_lang]),$content);
else if(isset($informations_meta['name']['en']))
    $content=str_replace('${TITLE}',htmlspecialchars($informations_meta['name']['en']),$content);
else
    $content=str_replace('${TITLE}','Informations',$content);
$content=str_replace('${CONTENT}',$map_descriptor,$content);
$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
$content=clean_html($content);
filewrite($datapack_explorer_local_path.'tree.html',$content);
