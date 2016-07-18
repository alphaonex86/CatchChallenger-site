<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into generator types'."\n");

foreach($type_meta as $type=>$type_content)
{
	if(!is_dir($datapack_explorer_local_path.$translation_list[$current_lang]['monsters/']))
		mkdir($datapack_explorer_local_path.$translation_list[$current_lang]['monsters/'],0777,true);
	$map_descriptor='';

	$effectiveness_list=array();
	foreach($type_meta as $realtypeindex=>$typecontent)
	{
		$effectiveness=(float)1.0;
		if(isset($typecontent['multiplicator'][$type]))
			$effectiveness*=$typecontent['multiplicator'][$type];
		if($effectiveness!=1.0)
		{
			if(!isset($effectiveness_list[(string)$effectiveness]))
				$effectiveness_list[(string)$effectiveness]=array();
			$effectiveness_list[(string)$effectiveness][]=$realtypeindex;
		}
	}
	$map_descriptor.='<div class="map monster_type_'.$type.'">'."\n";
		$map_descriptor.='<div class="subblock"><h1>'.ucfirst($type_content['name'][$current_lang]).'</h1></div>'."\n";
		$map_descriptor.='<div class="subblock"><div class="valuetitle">'.ucfirst($translation_list[$current_lang]['Type']).'</div><div class="value">'."\n";
		$map_descriptor.='<div class="type_label_list"><span class="type_label type_label_'.$type.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type.'.html">'.ucfirst($type_meta[$type]['name'][$current_lang]).'</a></span></div></div></div>'."\n";
		if(isset($effectiveness_list['4']) || isset($effectiveness_list['2']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Weak to'].'</div><div class="value">'."\n";
			$type_list=array();
			if(isset($effectiveness_list['2']))
				foreach($effectiveness_list['2'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">2x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.ucfirst($type_meta[$type_effectiveness]['name'][$current_lang]).'</a></span>'."\n";
			if(isset($effectiveness_list['4']))
				foreach($effectiveness_list['4'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">4x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.ucfirst($type_meta[$type_effectiveness]['name'][$current_lang]).'</a></span>'."\n";
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>'."\n";
		}
		if(isset($effectiveness_list['0.25']) || isset($effectiveness_list['0.5']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Resistant to'].'</div><div class="value">'."\n";
			$type_list=array();
			if(isset($effectiveness_list['0.25']))
				foreach($effectiveness_list['0.25'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">0.25x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.ucfirst($type_meta[$type_effectiveness]['name'][$current_lang]).'</a></span>'."\n";
			if(isset($effectiveness_list['0.5']))
				foreach($effectiveness_list['0.5'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">0.5x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.ucfirst($type_meta[$type_effectiveness]['name'][$current_lang]).'</a></span>'."\n";
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>'."\n";
		}
		if(isset($effectiveness_list['0']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Immune to'].'</div><div class="value">'."\n";
			$type_list=array();
			foreach($effectiveness_list['0'] as $type_effectiveness)
				if(isset($type_meta[$type_effectiveness]))
					$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.ucfirst($type_meta[$type_effectiveness]['name'][$current_lang]).'</a></span>'."\n";
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>'."\n";
		}
		$effectiveness_list=array();
		if(isset($type_meta[$type]))
		{
			foreach($type_meta[$type]['multiplicator'] as $temp_type=>$multiplicator)
			{
				if(!isset($effectiveness_list[(string)$multiplicator]))
					$effectiveness_list[(string)$multiplicator]=array();
				$effectiveness_list[(string)$multiplicator][]=$temp_type;
			}
		}
		if(isset($effectiveness_list['2']) || isset($effectiveness_list['4']))
		{
			
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Effective against'].'</div><div class="value">'."\n";
			$type_list=array();
			if(isset($effectiveness_list['2']))
				foreach($effectiveness_list['2'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">2x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.ucfirst($type_meta[$type_effectiveness]['name'][$current_lang]).'</a></span>'."\n";
			if(isset($effectiveness_list['4']))
				foreach($effectiveness_list['4'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">4x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.ucfirst($type_meta[$type_effectiveness]['name'][$current_lang]).'</a></span>'."\n";
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>'."\n";
		}
		if(isset($effectiveness_list['0.25']) || isset($effectiveness_list['0.5']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Not effective against'].'</div><div class="value">'."\n";
			$type_list=array();
			if(isset($effectiveness_list['0.25']))
				foreach($effectiveness_list['0.25'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">0.25x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.ucfirst($type_meta[$type_effectiveness]['name'][$current_lang]).'</a></span>'."\n";
			if(isset($effectiveness_list['0.5']))
				foreach($effectiveness_list['0.5'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">0.5x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.ucfirst($type_meta[$type_effectiveness]['name'][$current_lang]).'</a></span>'."\n";
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>'."\n";
		}
		if(isset($effectiveness_list['0']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">'.$translation_list[$current_lang]['Useless against'].'</div><div class="value">'."\n";
			$type_list=array();
			foreach($effectiveness_list['0'] as $type_effectiveness)
				if(isset($type_meta[$type_effectiveness]))
					$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.ucfirst($type_meta[$type_effectiveness]['name'][$current_lang]).'</a></span>'."\n";
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>'."\n";
		}
	$map_descriptor.='</div>'."\n";

    if($wikimode)
    {
        savewikipage('Template:Monsters_type_'.$type_content['name'][$current_lang].'_HEADER',$map_descriptor,false);$map_descriptor='';
    }

	$second_type_displayed='';
	if(isset($type_to_monster[$type]) && count($type_to_monster[$type])>0)
	{
		$map_descriptor.='<table class="monster_list item_list item_list_type_'.$type.'">
		<tr class="item_list_title item_list_title_type_'.$type.'">
			<th colspan="2">'.$translation_list[$current_lang]['Monster'].'</th>
			<th>'.$translation_list[$current_lang]['Type'].'</th>
		</tr>'."\n";
        $typetomonster_count=0;
		foreach($type_to_monster[$type] as $second_type=>$second_type_content)
		{
			if($second_type_displayed!=$second_type)
			{
                $typetomonster_count++;

                if($typetomonster_count%10==0)
                {
                    $map_descriptor.='<tr>
                        <td colspan="3" class="item_list_endline item_list_title_type_'.$type.'"></td>
                    </tr>
                    </table>'."\n";
                    
                    $map_descriptor.='<table class="monster_list item_list item_list_type_'.$type.'">
                    <tr class="item_list_title item_list_title_type_'.$type.'">
                        <th colspan="2">'.$translation_list[$current_lang]['Monster'].'</th>
                        <th>'.$translation_list[$current_lang]['Type'].'</th>
                    </tr>'."\n";
                }

                if($second_type==$type)
                    $map_descriptor.='<tr class="item_list_title_type_'.$second_type.'"><th colspan="3">'.ucfirst($type_meta[$second_type]['name'][$current_lang]).'</th></tr>'."\n";
                else
                    $map_descriptor.='<tr class="item_list_title_type_'.$second_type.'"><th colspan="3">'.ucfirst($type_meta[$type]['name'][$current_lang]).' - '.ucfirst($type_meta[$second_type]['name'][$current_lang]).'</th></tr>'."\n";
                $second_type_displayed=$second_type;
			}
			foreach($second_type_content as $monster)
			{
				if(isset($monster_meta[$monster]))
				{
                    $typetomonster_count++;
					$name=$monster_meta[$monster]['name'][$current_lang];
					$link=$base_datapack_explorer_site_path.$translation_list[$current_lang]['monsters/'].text_operation_do_for_url($name);
                    if(!$wikimode)
                        $link.='.html';
					$map_descriptor.='<tr class="value">
						<td>'."\n";
						if(file_exists($datapack_path.'monsters/'.$monster.'/small.png'))
							$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$monster.'/small.png" width="32" height="32" alt="'.$name.'" title="'.$name.'" /></a></div>'."\n";
						else if(file_exists($datapack_path.'monsters/'.$monster.'/small.gif'))
							$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$monster.'/small.gif" width="32" height="32" alt="'.$name.'" title="'.$name.'" /></a></div>'."\n";
						$map_descriptor.='</td>
						<td><a href="'.$link.'">'.$name.'</a></td>'."\n";
						$type_list=array();
						foreach($monster_meta[$monster]['type'] as $type_monster)
							if(isset($type_meta[$type_monster]))
								$type_list[]='<span class="type_label type_label_'.$type_monster.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_monster.'.html">'.ucfirst($type_meta[$type_monster]['name'][$current_lang]).'</a></span>'."\n";
						$map_descriptor.='<td><div class="type_label_list">'.implode(' ',$type_list).'</div></td>'."\n";
					$map_descriptor.='</tr>'."\n";
                    
                    if($typetomonster_count%10==0)
                    {
                        $map_descriptor.='<tr>
                            <td colspan="3" class="item_list_endline item_list_title_type_'.$type.'"></td>
                        </tr>
                        </table>'."\n";
                        
                        $map_descriptor.='<table class="monster_list item_list item_list_type_'.$type.'">
                        <tr class="item_list_title item_list_title_type_'.$type.'">
                            <th colspan="2">'.$translation_list[$current_lang]['Monster'].'</th>
                            <th>'.$translation_list[$current_lang]['Type'].'</th>
                        </tr>'."\n";
                        if($second_type_displayed!=$second_type)
                        {
                            if($second_type==$type)
                                $map_descriptor.='<tr class="item_list_title_type_'.$second_type.'"><th colspan="3">'.ucfirst($type_meta[$second_type]['name'][$current_lang]).'</th></tr>'."\n";
                            else
                                $map_descriptor.='<tr class="item_list_title_type_'.$second_type.'"><th colspan="3">'.ucfirst($type_meta[$type]['name'][$current_lang]).' - '.ucfirst($type_meta[$second_type]['name'][$current_lang]).'</th></tr>'."\n";
                            $second_type_displayed=$second_type;
                            $typetomonster_count++;
                        }
                    }
				}
			}
		}
		$map_descriptor.='<tr>
			<td colspan="3" class="item_list_endline item_list_title_type_'.$type.'"></td>
		</tr>
		</table>'."\n";

        $map_descriptor.='<br style="clear:both;" />'."\n";

        if($wikimode)
        {
            savewikipage('Template:Monsters_type_'.$type_content['name'][$current_lang].'_MONSTERS',$map_descriptor,false);$map_descriptor='';
        }
	}

    if(!$wikimode)
    {
        $content=$template;
        $content=str_replace('${TITLE}',ucfirst($type_content['name'][$current_lang]),$content);
        $content=str_replace('${CONTENT}',$map_descriptor,$content);
        $content=str_replace('${AUTOGEN}',$automaticallygen,$content);
        $content=clean_html($content);
        $filedestination=$datapack_explorer_local_path.'monsters/type-'.$type.'.html';
        if(file_exists($filedestination))
            die('The file already exists: '.$filedestination);
        filewrite($filedestination,$content);
    }
    else
    {
        $base_template='Monsters_type_'.$type_content['name'][$current_lang];

        $lang_template='';
        if(count($wikivarsapp)>1)
        {
            $temp_current_lang=$current_lang;
            foreach($wikivarsapp as $wikivars2)
                if($wikivars2['lang']!=$temp_current_lang)
                {
                    $current_lang=$wikivars2['lang'];
                    $lang_template.='[['.$current_lang.':'.$translation_list[$current_lang]['Monsters type:'].$type_content['name'][$current_lang].']]'."\n";
                }
            savewikipage('Template:'.$base_template.'_LANG',$lang_template,false);$lang_template='';
            $current_lang=$temp_current_lang;
            $map_descriptor.='{{Template:'.$base_template.'_LANG}}'."\n";
        }

        $map_descriptor.='{{Template:'.$base_template.'_HEADER}}'."\n";
        if(isset($type_to_monster[$type]) && count($type_to_monster[$type])>0)
            $map_descriptor.='{{Template:'.$base_template.'_MONSTERS}}'."\n";
        savewikipage($translation_list[$current_lang]['Monsters:'].'type-'.$type_content['name'][$current_lang],$map_descriptor,!$wikivars['generatefullpage']);
    }
}

$map_descriptor='';
$map_descriptor.='<table class="item_list item_list_type_normal">
<tr class="item_list_title item_list_title_type_normal">
	<th>'.$translation_list[$current_lang]['Type'].'</th>
	<th>'.$translation_list[$current_lang]['Monster'].'</th>
</tr>'."\n";
foreach($type_meta as $type=>$type_content)
{
	$map_descriptor.='<tr class="value">'."\n";
	$map_descriptor.='<td><div class="type_label_list"><span class="type_label type_label_'.$type.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type.'.html">'.ucfirst($type_content['name'][$current_lang]).'</a></span></div></td>'."\n";
	$count=0;
	if(isset($type_to_monster[$type]))
		foreach($type_to_monster[$type] as $second_type=>$second_type_content)
			$count+=count($second_type_content);
	//foreach($type_to_monster as $first_type=>$first_type_content)
		//foreach($first_type_content as $second_type=>$second_type_content)
	$map_descriptor.='<td>'.$count.'</td>'."\n";
	$map_descriptor.='</tr>'."\n";
}
$map_descriptor.='<tr>
	<td colspan="2" class="item_list_endline item_list_title_type_normal"></td>
</tr>
</table>'."\n";
if($wikimode)
{
    savewikipage('Template:Monsters_types_list',$map_descriptor,false);$map_descriptor='';
}
$map_descriptor.='<table class="item_list item_list_type_normal">
<tr class="item_list_title item_list_title_type_normal">
	<th class="item_list_title_corner">Effective against</th>'."\n";
foreach($type_meta as $type=>$type_content)
	$map_descriptor.='<th><div class="type_label_list"><span class="type_label type_label_'.$type.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type.'.html">'.ucfirst($type_content['name'][$current_lang]).'</a></span></div></th>'."\n";
foreach($type_meta as $type=>$type_content)
{
	$map_descriptor.='<tr class="value"><td class="item_list_title_left item_list_title_type_normal"><div class="type_label_list"><span class="type_label type_label_'.$type.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type.'.html">'.ucfirst($type_content['name'][$current_lang]).'</a></span></div></td>'."\n";
	foreach($type_meta as $type2=>$type_content2)
	{
		$effectiveness=(float)1.0;
		if(isset($type_content['multiplicator'][$type2]))
			$effectiveness*=$type_content['multiplicator'][$type2];
		if($effectiveness>1.0)
			$map_descriptor.='<td class="very_effective">'.$effectiveness.'</td>'."\n";
		elseif($effectiveness==1.0)
			$map_descriptor.='<td class="normal_effective">'.$effectiveness.'</td>'."\n";
		elseif($effectiveness==0.0)
			$map_descriptor.='<td class="no_effective">'.$effectiveness.'</td>'."\n";
		elseif($effectiveness<1.0)
			$map_descriptor.='<td class="not_very_effective">'.$effectiveness.'</td>'."\n";
	}
	$map_descriptor.='</tr>'."\n";
}
$map_descriptor.='</table>'."\n";

if(!$wikimode)
{
    $content=$template;
    $content=str_replace('${TITLE}',$translation_list[$current_lang]['Monsters types'],$content);
    $content=str_replace('${CONTENT}',$map_descriptor,$content);
    $content=str_replace('${AUTOGEN}',$automaticallygen,$content);
    $content=clean_html($content);
    $filedestination=$datapack_explorer_local_path.$translation_list[$current_lang]['types.html'];
    if(file_exists($filedestination))
        die('The file already exists: '.$filedestination);
    filewrite($filedestination,$content);
}
else
{
    savewikipage('Template:Monsters_types_table',$map_descriptor,false);$map_descriptor='';

    $lang_template='';
    if(count($wikivarsapp)>1)
    {
        foreach($wikivarsapp as $wikivars2)
            if($wikivars2['lang']!=$current_lang)
                $lang_template.='[['.$wikivars2['lang'].':'.$translation_list[$wikivars2['lang']]['Monsters types'].']]'."\n";
        savewikipage('Template:Monsters_types_LANG',$lang_template,false);$lang_template='';
        $map_descriptor.='{{Template:Monsters_types_LANG}}'."\n";
    }

    $map_descriptor.='{{Template:Monsters_types_list}}'."\n";
    $map_descriptor.='{{Template:Monsters_types_table}}'."\n";
    savewikipage($translation_list[$current_lang]['Monsters types'],$map_descriptor,!$wikivars['generatefullpage']);
}
