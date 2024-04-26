<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into load map'."\n");

$maps_list=array();
$maps_name_to_file=array();
$zone_to_map=array();
$monster_to_map=array();
$monster_to_map_count=array();
$bots_file=array();
$bot_id_to_skin=array();
$bot_id_to_map=array();
$map_name_to_path=array();
$map_short_path_to_name=array();
$map_short_path_to_path=array();
$duplicate_map_file_name=false;
$duplicate_map_file_name_list=array();
$maps_name_to_map=array();
$item_to_map=array();
$duplicate_detection_name=array();
$duplicate_detection_name_and_zone=array();
$temp_maps=array();
$monster_to_rarity=array();
$mapsgroup_meta=array();
$bot_toconvert=array();
$bots_meta=array();
$bots_found_in=array();
$fight_to_bot=array();
$bots_name_count=array();
$industry_to_bot=array();
$team_to_bot=array();
$item_to_bot_shop=array();
$highest_bot_id=0;
$shop_toconvert=array();
$shop_meta=array();
$item_to_shop=array();
$fight_toconvert=array();
$fight_meta=array();
$monster_to_fight=array();
$item_to_fight=array();
$industrie_meta=array();
$industrie_link_meta=array();
$item_produced_by=array();
$item_consumed_by=array();

$dir = $datapack_path.'map/main/';
$dh  = opendir($dir);
while ($dh!==FALSE && false !== ($maindatapackcode = readdir($dh)))
{
    if(is_dir($datapack_path.'map/main/'.$maindatapackcode) && preg_match('#^[a-z0-9]+$#isU',$maindatapackcode))
    {
        $temp_maps[$maindatapackcode]=getTmxList($datapack_path.'map/main/'.$maindatapackcode.'/');
        foreach($temp_maps[$maindatapackcode] as $map)
        {
            $averagelevel=(float)0.0;
            $averagelevelType='';
            $mapgroup=0;
            $width=0;
            $height=0;
            $pixelwidth=0;
            $pixelheight=0;
            $pos=strrpos($map,'/');
            if ($pos === false)
            {
                $map_folder='';
                $map_file=$map;
            }
            else
            {
                $map_folder=substr($map,0,$pos+1);
                $map_file=substr($map,$pos+1);
            }
            $map_xml_meta=str_replace('.tmx','.xml',$map);
            $map_xml_pref=str_replace('.tmx','/',$map);
            $borders=array();
            $tp=array();
            $doors=array();
            $bots=array();
            $items=array();
            $content=file_get_contents($datapack_path.'map/main/'.$maindatapackcode.'/'.$map);
            if(
                preg_match('# orientation="orthogonal"#isU',$content) &&
                preg_match('# width="([0-9]+)"#isU',$content) &&
                preg_match('# height="([0-9]+)"#isU',$content) &&
                preg_match('# tilewidth="([0-9]+)"#isU',$content) &&
                preg_match('# tileheight="([0-9]+)"#isU',$content)
            )
            {
                $width=(int)preg_replace('#^.* width="([0-9]+)".*$#isU','$1',$content);
                $height=(int)preg_replace('#^.* height="([0-9]+)".*$#isU','$2',$content);
                $tilewidth=(int)preg_replace('#^.* tilewidth="([0-9]+)".*$#isU','$3',$content);
                $tileheight=(int)preg_replace('#^.* tileheight="([0-9]+)".*$#isU','$4',$content);
                $pixelwidth=$width*$tilewidth;
                $pixelheight=$height*$tileheight;
            }
            $name_in_other_lang=array('en'=>'Unknown name ('.$map.')');
            $description_in_other_lang=array('en'=>'');
            preg_match_all('#<object[^>]+(type|class)="border-(left|right|top|bottom)".*</object>#isU',$content,$temp_text_list);
            foreach($temp_text_list[0] as $border_text)
            {
                if(preg_match('#(type|class)="border-(left|right|top|bottom)"#isU',$border_text))
                {
                    $border_orientation=preg_replace('#^.*(type|class)="border-(left|right|top|bottom).*$#isU','$2',$border_text);
                    $border_orientation=preg_replace("#[\n\r\t]+#is",'',$border_orientation);
                    $propertyList=textToProperty($border_text);
                    if(isset($propertyList['map']))
                    {
                        if(!isset($borders[$border_orientation]))
                        {
                            $border_map=$propertyList['map'];
                            if($border_map=='')
                            {
                                echo '$border_map can\'t be empty for '.$datapack_path.'map/main/'.$maindatapackcode.'/'.$map."\n";
                                exit;
                            }
                            $border_map=$map_folder.$border_map;
                            if(!preg_match('#\\.tmx$#',$border_map))
                                $border_map.='.tmx';
                            $border_map=preg_replace('#/[^/]+/\\.\\./#isU','/',$border_map);
                            $border_map=preg_replace('#^[^/]+/\\.\\./#isU','',$border_map);
                            $border_map=preg_replace("#[\n\r\t]+#is",'',$border_map);
                            $borders[$border_orientation]=$border_map;
                            if(isset($maps_list[$maindatapackcode][$border_map]))
                            {
                                if($mapgroup==0)//just add to current group
                                {
                                    $mapgroup=$maps_list[$maindatapackcode][$border_map]['mapgroup'];
                                    $mapsgroup_meta[$mapgroup][]=$map;
                                }
                                else//merge 2 group
                                {
                                    $tempmapgroup=$maps_list[$maindatapackcode][$border_map]['mapgroup'];
                                    foreach($mapsgroup_meta[$mapgroup] as $maptomigrate)
                                    {
                                        $maps_list[$maindatapackcode][$maptomigrate]['mapgroup']=$tempmapgroup;
                                        $mapsgroup_meta[$tempmapgroup][]=$maptomigrate;
                                    }
                                    $mapsgroup_meta[$mapgroup]=array();
                                    $mapgroup=$tempmapgroup;
                                }
                            }
                        }
                        else
                            echo 'Dual same border detected '.$map."\n";
                    }
                    else
                        echo 'No border property on '.$map."\n";
                }
            }
            preg_match_all('#<object[^>]+type="teleport( on [a-z]+)?".*</object>#isU',$content,$temp_text_list);
            foreach($temp_text_list[0] as $border_text)
            {
                $propertyList=textToProperty($border_text);
                if(!isset($propertyList['map']))
                    $propertyList['map']=$map_file;
                $border_map=$propertyList['map'];
                if($border_map=='')
                {
                    echo '$border_map can\'t be empty for '.$datapack_path.'map/main/'.$maindatapackcode.'/'.$map."\n";
                    exit;
                }
                $border_map=$map_folder.$border_map;
                if(!preg_match('#\\.tmx$#',$border_map))
                    $border_map.='.tmx';
                $border_map=preg_replace('#/[^/]+/\\.\\./#isU','/',$border_map);
                $border_map=preg_replace('#^[^/]+/\\.\\./#isU','',$border_map);
                $border_map=preg_replace("#[\n\r\t]+#is",'',$border_map);
                $tp[]=$border_map;
            }
            preg_match_all('#<object[^>]+type="door".*</object>#isU',$content,$temp_text_list);
            foreach($temp_text_list[0] as $door_text)
            {
                if(preg_match('#type="door"#isU',$door_text))
                {
                    $propertyList=textToProperty($door_text);
                    if(!isset($propertyList['map']))
                        $propertyList['map']=$map_file;
                    $door_map=$propertyList['map'];
                    $door_map=$map_folder.$door_map;
                    if(!preg_match('#\\.tmx$#',$door_map))
                        $door_map.='.tmx';
                    $door_map=preg_replace('#/[^/]+/\\.\\./#isU','/',$door_map);
                    $door_map=preg_replace('#^[^/]+/\\.\\./#isU','',$door_map);
                    $door_map=preg_replace("#[\n\r\t]+#is",'',$door_map);
                    $doors[]=array('map'=>$door_map);
                }
            }
            preg_match_all('#<object[^>]+type="bot".*</object>#isU',$content,$temp_text_list);
            foreach($temp_text_list[0] as $bot_text)
            {
                if(preg_match('#type="bot"#isU',$bot_text))
                {
                    $propertyList=textToProperty($bot_text);
                    if(isset($propertyList['id']) && isset($propertyList['file']) && preg_match('#^[0-9]+$#isU',$propertyList['id']))
                    {
                        $bot_id=$map_xml_pref.$propertyList['id'];
                        $bot_id_to_map[$bot_id][$maindatapackcode]=array('map'=>$map);
                        $bot_file=$propertyList['file'];
                        $bot_file=$map_folder.$bot_file;
                        if(!preg_match('#\\.xml$#',$bot_file))
                            $bot_file.='.xml';
                        do
                        {
                            $old_bot_file=$bot_file;
                            $bot_file=preg_replace('#/[^/]+/\\.\\./#isU','/',$bot_file);
                            $bot_file=preg_replace('#^[^/]+/\\.\\./#isU','',$bot_file);
                            $bot_file=preg_replace("#[\n\r\t]+#is",'',$bot_file);
                        } while($old_bot_file!=$bot_file);
                        if(!isset($bots))
                            $bots=array();
                        if(isset($propertyList['lookAt']) && preg_match('#^([a-z])$#isU',$propertyList['lookAt']) && isset($propertyList['skin']))
                        {
                            if(preg_match('#^(bottom|top|left|right)$#isU',$propertyList['lookAt']))
                                $lookAt=$propertyList['lookAt'];
                            else
                                $lookAt='bottom';
                            $skin=$propertyList['skin'];
                            $bots[]=array('file'=>$bot_file,'id'=>$bot_id,'lookAt'=>$lookAt,'skin'=>$skin);
                            $bot_id_to_skin[$bot_id][$maindatapackcode]=$skin;
                        }
                        else
                        {
                            if(isset($propertyList['skin']))
                            {
                                $skin=$propertyList['skin'];
                                $bots[]=array('file'=>$bot_file,'id'=>$bot_id,'skin'=>$skin);
                                $bot_id_to_skin[$bot_id][$maindatapackcode]=$skin;
                            }
                            else
                                $bots[]=array('file'=>$bot_file,'id'=>$bot_id);
                        }
                        $bots_file[$map_xml_pref][$maindatapackcode]=array('map'=>$map);
                    }
                }
            }
            preg_match_all('#<object[^>]+type="object".*</object>#isU',$content,$temp_text_list);
            foreach($temp_text_list[0] as $bot_text)
            {
                if(preg_match('#type="object"#isU',$bot_text))
                {
                    $visible=true;
                    $propertyList=textToProperty($bot_text);
                    if(isset($propertyList['item']) && preg_match('#^[0-9]+$#isU',$propertyList['item']))
                    {
                        if(isset($propertyList['visible']) && $propertyList['visible']=='false')
                            $visible=false;
                        $item_id=$propertyList['item'];
                        if(!isset($item_to_map[$item_id]))
                            $item_to_map[$item_id]=array();
                        if(!in_array($map,$item_to_map[$item_id]))
                            $item_to_map[$item_id][]=array('map'=>$map,'maindatapackcode'=>$maindatapackcode);
                        $items[]=array('item'=>$item_id,'visible'=>$visible);
                    }
                }
            }
            $monsters_list=array();
            $monsters=array();
            $type='outdoor';
            $name='Unknown name ('.$map.')';
            $shortdescription='';
            $description='';
            $zone='';
            $dropcount=0;
            if(file_exists($datapack_path.'map/main/'.$maindatapackcode.'/'.$map_xml_meta))
            {
                $content_meta_map=file_get_contents($datapack_path.'map/main/'.$maindatapackcode.'/'.$map_xml_meta);
                preg_match_all('#<bot [^>]+>(.*)</bot>#isU',$content_meta_map,$temp_text_list);
                foreach($temp_text_list[0] as $bot_text)
                {
                    $botbal=preg_replace('#^.*(<bot [^>]+>).*$#isU','$1',$bot_text);
                    $id=$map_xml_pref.preg_replace('#^.*<bot [^>]*id="([0-9]+)"[^>]*>.*$#isU','$1',$botbal);
                    $bot_toconvert[$id]=$bot_text;
                    if(isset($bots_meta[$maindatapackcode][$id]))
                        echo $maindatapackcode.'/'.$file.': map bot with id '.$id.' is already found into: '.$bots_found_in[$maindatapackcode][$id].' map '.$map_xml_meta."\n";
                    else
                    {
                        $name='';
                        if(preg_match('#<name( lang="en")?>.*</name>#isU',$bot_text))
                        {
                            $name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$bot_text);
                            $name=str_replace('<![CDATA[','',str_replace(']]>','',$name));
                            if(isset($bots_name_count[$maindatapackcode]['en'][$map_xml_pref.text_operation_do_for_url($name)]))
                                $bots_name_count[$maindatapackcode]['en'][$map_xml_pref.text_operation_do_for_url($name)]++;
                            else
                                $bots_name_count[$maindatapackcode]['en'][$map_xml_pref.text_operation_do_for_url($name)]=1;
                        }
                        $name_in_other_lang=array('en'=>$name);
                        foreach($lang_to_load as $lang)
                        {
                            if($lang=='en')
                                continue;
                            if(preg_match('#<name lang="'.$lang.'">([^<]+)</name>#isU',$bot_text))
                            {
                                $temp_name=preg_replace('#^.*<name lang="'.$lang.'">([^<]+)</name>.*$#isU','$1',$bot_text);
                                $temp_name=str_replace('<![CDATA[','',str_replace(']]>','',$temp_name));
                                $temp_name=preg_replace("#[\n\r\t]+#is",'',$temp_name);
                                $name_in_other_lang[$lang]=$temp_name;
                            }
                            else
                                $name_in_other_lang[$lang]=$name;
                            if(isset($bots_name_count[$maindatapackcode][$lang][$map_xml_pref.text_operation_do_for_url($name_in_other_lang[$lang])]))
                                $bots_name_count[$maindatapackcode][$lang][$map_xml_pref.text_operation_do_for_url($name_in_other_lang[$lang])]++;
                            else
                                $bots_name_count[$maindatapackcode][$lang][$map_xml_pref.text_operation_do_for_url($name_in_other_lang[$lang])]=1;
                        }
                        $team='';
                        if(preg_match('#<bot [^>]*team="[^"]+"[^>]*>#isU',$botbal))
                        {
                            $team=preg_replace('#<bot [^>]*team="([^"]+)"[^>]*>#isU','$1',$botbal);
                            if(!isset($team_to_bot[$team]))
                                $team_to_bot[$team]=array();
                            $team_to_bot[$team][]=$id;
                        }
                        if($highest_bot_id<$id)
                            $highest_bot_id=$id;
                        $bots_meta[$maindatapackcode][$id]=array('name'=>$name_in_other_lang,'team'=>$team,'onlytext'=>true,'step'=>array());
                        $bots_found_in[$maindatapackcode][$id]=$maindatapackcode.'/'.$file;
                        $temp_step_list=explode('<step',$bot_text);
                        foreach($temp_step_list as $step_text)
                        {
                            if(preg_match('#^[^>]* id="([0-9]+)".*$#isU',$step_text))
                                $step_id=preg_replace('#^[^>]* id="([0-9]+)".*$#isU','$1',$step_text);
                            else
                                $step_id='1';
                            if(isset($bots_meta[$maindatapackcode][$id]['step'][$step_id]))
                                echo 'step with id '.$step_id.' for bot '.$id.' is already found for maindatapackcode: '.$maindatapackcode."\n";
                            else
                            {
                                if(preg_match('#^[^>]* type="([a-z]+)".*$#isU',$step_text))
                                {
                                    $step_type=preg_replace('#^[^>]* type="([a-z]+)".*$#isU','$1',$step_text);
                                    if($step_type=='text')
                                    {
                                        preg_match_all('# lang="([a-z]+)"#isU',$step_text,$langlist);
                                        $step_text_en=preg_replace('#^.*<text( lang="en")?>('.preg_quote('<![CDATA[').')?(.*)('.preg_quote(']]>').')?</text>.*$#isU','$3',$step_text);
                                        $step_text_en=str_replace(']]>','',str_replace('<![CDATA[','',$step_text_en));
                                        $step_text_in_other_lang=array('en'=>$step_text_en);
                                        preg_match_all('# href="([^"]+)"#isU',$step_text_en,$linkslist);
                                        $linkslisten=$linkslist[1];
                                        //foreach($lang_to_load as $lang) -> disable to detect text href mismatch
                                        foreach($langlist[1] as $lang)
                                        {
                                            if($lang=='en')
                                                continue;
                                            if(preg_match('#<text lang="'.$lang.'">(.+)</text>#isU',$step_text))
                                            {
                                                $temp_step_text=preg_replace('#^.*<text lang="'.$lang.'">('.preg_quote('<![CDATA[').')?(.*)('.preg_quote(']]>').')?</text>.*$#isU','$2',$step_text);
                                                $temp_step_text=str_replace(']]>','',str_replace('<![CDATA[','',$temp_step_text));

                                                preg_match_all('# href="([^"]+)"#isU',$temp_step_text,$linkslistsublang);
                                                if($linkslisten!=$linkslistsublang[1])
                                                    echo 'step with id '.$step_id.' for bot '.$id.' mismatch links into file '.$file.' for maindatapackcode: '.$maindatapackcode."\n";
                                        
                                                $step_text_in_other_lang[$lang]=$temp_step_text;
                                            }
                                            else
                                                $step_text_in_other_lang[$lang]=$step_text;
                                        }
                                        $bots_meta[$maindatapackcode][$id]['step'][$step_id]=array('type'=>$step_type,'text'=>$step_text_in_other_lang);
                                    }
                                    else if($step_type=='fight')
                                    {
                                        /*if(preg_match('#^.*fightid="([0-9]+)".*$#isU',$step_text))
                                        {
                                            $fightid=preg_replace('#^.*fightid="([0-9]+)".*$#isU','$1',$step_text);
                                            if(isset($fight_meta[$maindatapackcode][$fightid]))
                                            {
                                                $leader=false;
                                                if(preg_match('#leader="true"#isU',$step_text))
                                                    $leader=true;
                                                $bots_meta[$maindatapackcode][$id]['onlytext']=false;
                                                $bots_meta[$maindatapackcode][$id]['step'][$step_id]=array('type'=>$step_type,'fightid'=>$fightid,'leader'=>$leader);
                                                if(!isset($fight_to_bot[$maindatapackcode][$fightid]))
                                                    $fight_to_bot[$maindatapackcode][$fightid]=array();
                                                $fight_to_bot[$maindatapackcode][$fightid][]=$id;
                                            }
                                            else
                                                echo 'fightid not found: '.$fightid.' for step with id '.$step_id.' for bot '.$id.' in file: '.$file.' for maindatapackcode: '.$maindatapackcode."\n";
                                        }
                                        else
                                            echo 'fightid attribute not found for step with id '.$step_id.' for bot '.$id.' in file: '.$file.' for maindatapackcode: '.$maindatapackcode."\n";*/
                                        preg_match_all('#<fight.*</fight>#isU',$step_text,$entry_list);
                                        foreach($entry_list[0] as $entry)
                                        {
                                            $start='';
                                            $win='';
                                            $cash=0;
                                            $items=array();
                                            if(!preg_match('#<fight.*</fight>#isU',$entry))
                                                continue;
                                            $idfight=$map_xml_pref.preg_replace('#^.*<fight.*</fight>.*$#isU','$1',$entry);
                                            $fight_toconvert[$idfight]=$entry;
                                            if(isset($fight_meta[$idfight]))
                                            {
                                                echo 'duplicate id '.$idfight.' for the fight'."\n";
                                                continue;
                                            }
                                            if(preg_match('#<gain cash="([0-9]+)"#isU',$entry))
                                                $cash=preg_replace('#^.*<gain cash="([0-9]+)".*$#isU','$1',$entry);
                                            preg_match_all('#<gain item="([^"]+)"#isU',$entry,$items_list);
                                            foreach($items_list[1] as $entry_item)
                                            {
                                                $item=preg_replace('#^.*<gain item="([^"]+)".*$#isU','$1',$entry_item);
                                                if(isset($itemname_to_id[$item]))
                                                    $item=$itemname_to_id[$item];
                                                $items[]=array('item'=>$item,'quantity'=>1);
                                                if(!isset($item_to_fight[$item]))
                                                    $item_to_fight[$item]=array();
                                                $item_to_fight[$item][$maindatapackcode][]=$idfight;
                                                ksort($item_to_fight[$item]);
                                            }
                                            if(preg_match('#<start( lang="en")?>(<!\\[CDATA\\[)?(.*)(]]>)?</start>#isU',$entry))
                                                $start=preg_replace('#^.*<start( lang="en")?>(<!\\[CDATA\\[)?(.*)(]]>)?</start>.*$#isU','$3',$entry);
                                            if(preg_match('#<win( lang="en")?>(<!\\[CDATA\\[)?(.*)(]]>)?</win>#isU',$entry))
                                                $win=preg_replace('#^.*<win( lang="en")?>(<!\\[CDATA\\[)?(.*)(]]>)?</win>.*$#isU','$3',$entry);
                                            $start=str_replace('<![CDATA[','',$start);
                                            $win=str_replace('<![CDATA[','',$win);
                                            $monsters=array();
                                            preg_match_all('#<monster .*/>#isU',$entry,$monster_text_list);
                                            foreach($monster_text_list[0] as $monster_text)
                                            {
                                                $monster=preg_replace('#^.*id="([^"]+)".*$#isU','$1',$monster_text);
                                                if(isset($monstername_to_id[$monster]))
                                                    $monster=$monstername_to_id[$monster];
                                                $level=preg_replace('#^.*level="([0-9]+)".*$#isU','$1',$monster_text);
                                                $monsters[]=array('monster'=>$monster,'level'=>$level);
                                                $monster_to_fight[$monster][$maindatapackcode][]=$idfight;
                                            }
                                            $fight_meta[$maindatapackcode][$idfight]=array('start'=>$start,'win'=>$win,'cash'=>$cash,'monsters'=>$monsters,'items'=>$items);
                                        }
                                    }
                                    else if($step_type=='heal')
                                    {
                                        $bots_meta[$maindatapackcode][$id]['onlytext']=false;
                                        $bots_meta[$maindatapackcode][$id]['step'][$step_id]=array('type'=>$step_type);
                                    }
                                    else if($step_type=='learn')
                                    {
                                        $bots_meta[$maindatapackcode][$id]['onlytext']=false;
                                        $bots_meta[$maindatapackcode][$id]['step'][$step_id]=array('type'=>$step_type);
                                    }
                                    else if($step_type=='warehouse')
                                    {
                                        $bots_meta[$maindatapackcode][$id]['onlytext']=false;
                                        $bots_meta[$maindatapackcode][$id]['step'][$step_id]=array('type'=>$step_type);
                                    }
                                    else if($step_type=='clan')
                                    {
                                        $bots_meta[$maindatapackcode][$id]['onlytext']=false;
                                        $bots_meta[$maindatapackcode][$id]['step'][$step_id]=array('type'=>$step_type);
                                    }
                                    else if($step_type=='shop')
                                    {
                                        /*if(preg_match('#^.*shop="([0-9]+)".*$#isU',$step_text))
                                        {
                                            $shop=preg_replace('#^.*shop="([0-9]+)".*$#isU','$1',$step_text);
                                            if(isset($shop_meta[$maindatapackcode][$shop]))
                                            {
                                                $bots_meta[$maindatapackcode][$id]['onlytext']=false;
                                                $bots_meta[$maindatapackcode][$id]['step'][$step_id]=array('type'=>$step_type,'shop'=>$shop);
                                                if(!isset($shop_to_bot[$shop]))
                                                    $shop_to_bot[$shop][$maindatapackcode]=array();
                                                $shop_to_bot[$shop][$maindatapackcode][]=$id;
                                            }
                                            else
                                                echo 'shop: '.$shop.' not found for step with id '.$step_id.' for bot '.$id.' in file: '.$file.' for maindatapackcode: '.$maindatapackcode."\n";
                                        }
                                        else
                                            echo 'shop attribute not found for step with id '.$step_id.' for bot '.$id.', $step_text: '.$step_text."\n";*/
                                        $products=array();
                                        preg_match_all('#<product[^>]* itemId="([^"]+)"[^>]*>#isU',$step_text,$monster_text_list);
                                        foreach($monster_text_list[0] as $monster_text)
                                        {
                                            $item=preg_replace('#^.* itemId="([^"]+)".*$#isU','$1',$monster_text);
                                            if(isset($itemname_to_id[$item]))
                                                $item=$itemname_to_id[$item];
                                            if(isset($item_meta[$item]))
                                            {
                                                if(!preg_match('#^.* overridePrice="([0-9]+)".*$#isU',$monster_text))
                                                    $price=$item_meta[$item]['price'];
                                                else
                                                    $price=preg_replace('#^.* overridePrice="([0-9]+)".*$#isU','$1',$monster_text);
                                                if($price!=0)
                                                {
                                                    $products[$item]=$price;
                                                    if(!isset($item_to_shop[$item][$maindatapackcode]))
                                                        $item_to_shop[$item][$maindatapackcode]=array();
                                                    $item_to_shop[$item][$maindatapackcode][]=$map_xml_pref.$id;
                                                    ksort($item_to_shop[$item][$maindatapackcode]);
                                                    ksort($item_to_shop[$item]);
                                                }
                                                else
                                                {
                                                    echo 'item with price 0 found '.$item.' for the shop'.$map_xml_pref.$id."\n";
                                                    continue;
                                                }
                                            
                                            }
                                            else
                                            {
                                                echo 'item not found '.$item.' for the shop'.$map_xml_pref.$id."\n";
                                                print_r($itemname_to_id);exit;
                                                continue;
                                            }
                                        }
                                        $shop_meta[$maindatapackcode][$map_xml_pref.$id]=array('products'=>$products);
                                    }
                                    else if($step_type=='sell')
                                    {
                                        $bots_meta[$maindatapackcode][$id]['onlytext']=false;
                                        $bots_meta[$maindatapackcode][$id]['step'][$step_id]=array('type'=>$step_type);
                                    }
                                    else if($step_type=='zonecapture')
                                    {
                                        if(preg_match('#^.*zone="([^"]+)".*$#isU',$step_text))
                                        {
                                            $zone=preg_replace('#^.*zone="([^"]+)".*$#isU','$1',$step_text);
                                            if(isset($zone_meta[$maindatapackcode][$zone]))
                                            {
                                                $bots_meta[$maindatapackcode][$id]['onlytext']=false;
                                                $bots_meta[$maindatapackcode][$id]['step'][$step_id]=array('type'=>$step_type,'zone'=>$zone);
                                            }
                                            else
                                                echo 'zone: '.$zone.' not found for step with id '.$step_id.' for bot '.$id.' in file: '.$file.' for maindatapackcode: '.$maindatapackcode."\n";
                                        }
                                        else
                                            echo 'zone attribute not found for step with id '.$step_id.' for bot '.$id.' for maindatapackcode: '.$maindatapackcode."\n";
                                    }
                                    else if($step_type=='industry')
                                    {
                                        if(preg_match('#^.*industry="([0-9]+)".*$#isU',$step_text))
                                        {
                                            $industry=preg_replace('#^.*industry="([0-9]+)".*$#isU','$1',$step_text);
                                            if(isset($industrie_link_meta[$maindatapackcode][$industry]))
                                            {
                                                $bots_meta[$maindatapackcode][$id]['onlytext']=false;
                                                $bots_meta[$maindatapackcode][$id]['step'][$step_id]=array('type'=>$step_type,'industry'=>$industry);
                                                $link=$industrie_link_meta[$maindatapackcode][$industry];
                                                if(!isset($industry_to_bot[$link['industry_id']]))
                                                    $industry_to_bot[$link['industry_id']]=array();
                                                $industry_to_bot[$link['industry_id']][$maindatapackcode][$maindatapackcode]=$id;
                                            }
                                            else if(isset($industrie_link_meta[''][$industry]))
                                            {
                                                $bots_meta[$maindatapackcode][$id]['onlytext']=false;
                                                $bots_meta[$maindatapackcode][$id]['step'][$step_id]=array('type'=>$step_type,'industry'=>$industry);
                                                $link=$industrie_link_meta[''][$industry];
                                                if(!isset($industry_to_bot[$link['industry_id']]))
                                                    $industry_to_bot[$link['industry_id']]=array();
                                                $industry_to_bot[$link['industry_id']][''][$maindatapackcode]=$id;
                                            }
                                            else
                                                echo 'industrie_link_meta: '.$industry.' not found for step with id '.$step_id.' for bot '.$id.' for maindatapackcode: '.$maindatapackcode."\n";
                                        }
                                        else
                                            echo 'industry attribute not found for step with id '.$step_id.' for bot '.$id.': '.$step_text."\n";
                                    }
                                    else if($step_type=='quests')
                                    {}
                                    else
                                        echo 'step with id '.$step_id.' for bot '.$id.' have unknown type: '.$step_type."\n";
                                }
                            }
                        }
                    }
                }
                if(preg_match('#type="(outdoor|city|cave|indoor)"#isU',$content_meta_map))
                    $type=preg_replace('#^.*type="(outdoor|city|cave|indoor)".*$#isU','$1',$content_meta_map);
                if(preg_match('#zone="([^"]+)"#isU',$content_meta_map))
                    $zone=preg_replace('#^.*zone="([^"]+)".*$#isU','$1',$content_meta_map);
                if(preg_match('#<name( lang="en")?>[^<]+</name>#isU',$content_meta_map))
                {
                    $name=preg_replace('#^.*<name( lang="en")?>([^<]+)</name>.*$#isU','$2',$content_meta_map);
                    $simplified_name=str_replace($map_folder,'',str_replace('.tmx','',$map));
                    if(preg_match('#-?[0-9]+\.-?[0-9]+#isU',$simplified_name))
                    {
                        $name_for_url=text_operation_do_for_url($name);
                        $name_for_url=preg_replace('#^.*((last-)floor)#isU','$1',$name_for_url);
                        if(isset($duplicate_map_file_name_list[$maindatapackcode][$simplified_name]))
                            $duplicate_map_file_name=true;
                        else
                            $duplicate_map_file_name_list[$maindatapackcode][$simplified_name]=1;
                        $map_short_path_to_path[str_replace($map_folder,'',$map)]=$map_folder.$simplified_name;
                        $map_path_without_ext=$map_folder.$simplified_name;
                        if(!isset($map_name_to_path[$map_folder.$name_for_url]))
                        {
                            $map_name_to_path[$map_folder.$name_for_url]=$map_path_without_ext;
                            $map_short_path_to_name[$simplified_name]=$name_for_url;
                        }
                        else
                        {
                            $index=2;
                            while(isset($map_name_to_path[$map_folder.$name_for_url.'-'.$index]))
                                $index++;
                            $map_name_to_path[$map_folder.$name_for_url.'-'.$index]=$map_path_without_ext;
                            $map_short_path_to_name[$simplified_name]=$name_for_url.'-'.$index;
                        }
                    }
                }
                if(preg_match('#<shortdescription lang="en">[^<]+</shortdescription>#isU',$content_meta_map))
                    $shortdescription=preg_replace('#^.*<shortdescription lang="en">([^<]+)</shortdescription>.*$#isU','$1',$content_meta_map);
                elseif(preg_match('#<shortdescription>[^<]+</shortdescription>#isU',$content_meta_map))
                    $shortdescription=preg_replace('#^.*<shortdescription>([^<]+)</shortdescription>.*$#isU','$1',$content_meta_map);
                if(preg_match('#<description lang="en">[^<]+</description>#isU',$content_meta_map))
                    $description=text_operation_first_letter_upper(preg_replace('#^.*<description lang="en">([^<]+)</description>.*$#isU','$1',$content_meta_map));
                elseif(preg_match('#<description>[^<]+</description>#isU',$content_meta_map))
                    $description=text_operation_first_letter_upper(preg_replace('#^.*<description>([^<]+)</description>.*$#isU','$1',$content_meta_map));
                $type=preg_replace("#[\n\r\t]+#is",'',$type);
                $name=preg_replace("#[\n\r\t]+#is",'',$name);
                $zone=preg_replace("#[\n\r\t]+#is",'',$zone);
                if(!isset($duplicate_detection_name[$maindatapackcode]['en'][$name]))
                    $duplicate_detection_name[$maindatapackcode]['en'][$name]=1;
                else
                    $duplicate_detection_name[$maindatapackcode]['en'][$name]++;
                if($zone!='' && isset($zone_meta[$maindatapackcode][$zone]))
                {
                    if(!isset($duplicate_detection_name_and_zone[$maindatapackcode]['en'][$zone_meta[$maindatapackcode][$zone]['name']['en'].' '.$name]))
                        $duplicate_detection_name_and_zone[$maindatapackcode]['en'][$zone_meta[$maindatapackcode][$zone]['name']['en'].' '.$name]=1;
                    else
                        $duplicate_detection_name_and_zone[$maindatapackcode]['en'][$zone_meta[$maindatapackcode][$zone]['name']['en'].' '.$name]++;
                }
                else
                {
                    if(!isset($duplicate_detection_name_and_zone[$maindatapackcode]['en'][$name]))
                        $duplicate_detection_name_and_zone[$maindatapackcode]['en'][$name]=1;
                    else
                        $duplicate_detection_name_and_zone[$maindatapackcode]['en'][$name]++;
                }
                $shortdescription=preg_replace("#[\n\r\t]+#is",'',$shortdescription);
                $description=preg_replace("#[\n\r\t]+#is",'',$description);
                $name_in_other_lang=array('en'=>$name);
                foreach($lang_to_load as $lang)
                {
                    if($lang=='en')
                        continue;
                    if(preg_match('#<name lang="'.$lang.'">([^<]+)</name>#isU',$content_meta_map))
                    {
                        $temp_name=preg_replace('#^.*<name lang="'.$lang.'">([^<]+)</name>.*$#isU','$1',$content_meta_map);
                        $temp_name=str_replace('<![CDATA[','',str_replace(']]>','',$temp_name));
                        $temp_name=preg_replace("#[\n\r\t]+#is",'',$temp_name);
                        $name_in_other_lang[$lang]=$temp_name;
                    }
                    else
                    {
                        $temp_name=$name;
                        $name_in_other_lang[$lang]=$name;
                    }
                    if(!isset($duplicate_detection_name[$maindatapackcode][$lang][$temp_name]))
                        $duplicate_detection_name[$maindatapackcode][$lang][$temp_name]=1;
                    else
                        $duplicate_detection_name[$maindatapackcode][$lang][$temp_name]++;
                    if($zone!='' && isset($zone_meta[$maindatapackcode][$zone]))
                    {
                        if(!isset($duplicate_detection_name_and_zone[$maindatapackcode][$lang][$zone_meta[$maindatapackcode][$zone]['name'][$lang].' '.$temp_name]))
                            $duplicate_detection_name_and_zone[$maindatapackcode][$lang][$zone_meta[$maindatapackcode][$zone]['name'][$lang].' '.$temp_name]=1;
                        else
                            $duplicate_detection_name_and_zone[$maindatapackcode][$lang][$zone_meta[$maindatapackcode][$zone]['name'][$lang].' '.$temp_name]++;
                    }
                    else
                    {
                        if(!isset($duplicate_detection_name_and_zone[$maindatapackcode][$lang][$temp_name]))
                            $duplicate_detection_name_and_zone[$maindatapackcode][$lang][$temp_name]=1;
                        else
                            $duplicate_detection_name_and_zone[$maindatapackcode][$lang][$temp_name]++;
                    }
                }
                $description_in_other_lang=array('en'=>$description);
                foreach($lang_to_load as $lang)
                {
                    if($lang=='en')
                        continue;
                    if(preg_match('#<description lang="'.$lang.'">([^<]+)</description>#isU',$content_meta_map))
                    {
                        $temp_description=preg_replace('#^.*<description lang="'.$lang.'">([^<]+)</description>.*$#isU','$1',$content_meta_map);
                        $temp_description=str_replace('<![CDATA[','',str_replace(']]>','',$temp_description));
                        $temp_description=preg_replace("#[\n\r\t]+#is",'',$temp_description);
                        $description_in_other_lang[$lang]=$temp_description;
                    }
                    else
                        $description_in_other_lang[$lang]=$description;
                }
                $shortdescription_in_other_lang=array('en'=>$shortdescription);
                foreach($lang_to_load as $lang)
                {
                    if($lang=='en')
                        continue;
                    if(preg_match('#<shortdescription lang="'.$lang.'">([^<]+)</shortdescription>#isU',$content_meta_map))
                    {
                        $temp_shortdescription=preg_replace('#^.*<shortdescription lang="'.$lang.'">([^<]+)</shortdescription>.*$#isU','$1',$content_meta_map);
                        $temp_shortdescription=str_replace('<![CDATA[','',str_replace(']]>','',$temp_shortdescription));
                        $temp_shortdescription=preg_replace("#[\n\r\t]+#is",'',$temp_shortdescription);
                        $shortdescription_in_other_lang[$lang]=$temp_shortdescription;
                    }
                    else
                        $shortdescription_in_other_lang[$lang]=$shortdescription;
                }
                foreach($layer_toSearch as $toSearch)
                {
                    if(preg_match('#<'.preg_quote($toSearch).'>(.*)</'.preg_quote($toSearch).'>#isU',$content_meta_map))
                    {
                        $search=false;
                        if(isset($layer_meta[$toSearch]))
                            $search=($layer_meta[$toSearch]['layer']=='' /*Cave*/ || preg_match('#<layer( [^>]+)? name="'.preg_quote($layer_meta[$toSearch]['layer']).'"#isU',$content)/*Have layer into the tmx*/);
                        else if(isset($layer_event[$toSearch]))
                            $search=($layer_event[$toSearch]['layer']=='' /*Cave*/ || preg_match('#<layer( [^>]+)? name="'.preg_quote($layer_event[$toSearch]['layer']).'"#isU',$content)/*Have layer into the tmx*/);
                        if($search)
                        {
                            $text=preg_replace('#^.*<'.preg_quote($toSearch).'>(.*)</'.preg_quote($toSearch).'>.*$#isU','$1',$content_meta_map);
                            preg_match_all('#<monster[^>]+/>#isU',$text,$temp_text_list);
                            
                            $averagelevelTemp=(float)0.0;
                            $monsterCount=0;
                            foreach($temp_text_list[0] as $text_entry)
                            {
                                if(preg_match('# level="([0-9]+)"#isU',$text_entry))
                                {
                                    $minLevel=preg_replace('#^.* level="([0-9]+)".*$#isU','$1',$text_entry);
                                    $maxLevel=preg_replace('#^.* level="([0-9]+)".*$#isU','$1',$text_entry);
                                }
                                elseif(preg_match('# minLevel="([0-9]+)"#isU',$text_entry) && preg_match('# maxLevel="([0-9]+)"#isU',$text_entry))
                                {
                                    $minLevel=preg_replace('#^.* minLevel="([0-9]+)".*$#isU','$1',$text_entry);
                                    $maxLevel=preg_replace('#^.* maxLevel="([0-9]+)".*$#isU','$1',$text_entry);
                                }
                                else
                                    continue;
                                if(preg_match('#luck="([0-9]+)"#isU',$text_entry))
                                    $luck=preg_replace('#^.*luck="([0-9]+)".*$#isU','$1',$text_entry);
                                else
                                    $luck=100;
                                if(preg_match('#id="([0-9]+)"#isU',$text_entry))
                                    $id=preg_replace('#^.*id="([0-9]+)".*$#isU','$1',$text_entry);
                                else
                                    continue;
                                if(isset($monster_meta[$id]))
                                {
                                    if(!isset($monsters[$toSearch]))
                                        $monsters[$toSearch]=array();
                                    if(!isset($monsters[$toSearch]['']))
                                        $monsters[$toSearch]['']=array();
                                    $monsters[$toSearch][''][]=array('id'=>$id,'minLevel'=>$minLevel,'maxLevel'=>$maxLevel,'luck'=>$luck);
                                    if(!isset($monster_to_map[$id]))
                                        $monster_to_map[$id]=array();
                                    if(!isset($monster_to_map[$id][$toSearch]))
                                        $monster_to_map[$id][$toSearch]=array();
                                    if(!isset($monster_to_map[$id][$toSearch][$maindatapackcode]))
                                        $monster_to_map[$id][$toSearch][$maindatapackcode]=array();
                                    $monster_to_map[$id][$toSearch][$maindatapackcode][$map]['']=array('minLevel'=>$minLevel,'maxLevel'=>$maxLevel,'luck'=>$luck);
                                    if(!in_array($id,$monsters_list))
                                        $monsters_list[]=$id;
                                    if(!isset($monster_meta[$id]['game'][$maindatapackcode]))
                                        $monster_meta[$id]['game'][$maindatapackcode]=array();
                                    if(!in_array('',$monster_meta[$id]['game'][$maindatapackcode]))
                                        $monster_meta[$id]['game'][$maindatapackcode][]='';
                                    $monster_to_map_count[$id][$maindatapackcode.'/'.$map]['']=0;
                                    $monster_meta[$id]['rarity']+=$luck;
                                    $dropcount+=count($monster_meta[$id]['drops']);
                                    $averagelevelTemp+=$minLevel+$maxLevel;
                                    $monsterCount++;
                                }
                                else
                                    echo 'Monster: '.$id.' not found on the map: '.$map."\n";
                            }
                            if(
                            ($toSearch=='cave' || $toSearch=='grass') && 
                            ($averagelevelType=='' || ($averagelevelType=='cave' && $toSearch=='grass')) && 
                            $monsterCount>0
                            )
                            {
                                $averagelevel=(float)$averagelevelTemp/(2*$monsterCount);
                                $averagelevelType=$toSearch;
                            }
                        }
                        else
                        {
                            if(isset($layer_meta[$toSearch]))
                                echo '2 Not search because no layer '.$toSearch.' detected, layer meta: '.$layer_meta[$toSearch]['layer'].', with name: '.
                                (preg_match('#<layer name="'.preg_quote($layer_meta[$toSearch]['layer']).'"#isU',$content)).', regex used: '.
                                '#<layer name="'.preg_quote($layer_meta[$toSearch]['layer']).'"#isU'
                                .' for map: '.$map."\n";
                            else if(isset($layer_event[$toSearch]))
                                echo '2 Not search because no layer '.$toSearch.' detected, layer meta: '.$layer_event[$toSearch]['layer'].', with name: '.
                                (preg_match('#<layer name="'.preg_quote($layer_event[$toSearch]['layer']).'"#isU',$content)).', regex used: '.
                                '#<layer name="'.preg_quote($layer_event[$toSearch]['layer']).'"#isU'
                                .' for map: '.$map."\n";
                            else
                                echo '2 Not search because no layer '.$toSearch.' detected for map: '.$map."\n";
                            echo 'Mostly due '.$toSearch.' is into '.$map_xml_meta.' but no layer into: '.$map."\n";
                        }
                    }
                }
            }
            $dir2 = $datapack_path.'map/main/'.$maindatapackcode.'/sub/';
            if(is_dir($dir2))
                $dh2  = opendir($dir2);
            else
                $dh2  = FALSE;
            if($dh2!==FALSE)
            while($dh2!==FALSE && false !== ($subdatapackcode = readdir($dh2)))
            {
                if(is_dir($datapack_path.'map/main/'.$maindatapackcode.'/sub/'.$subdatapackcode) && preg_match('#^[a-z0-9]+$#isU',$subdatapackcode))
                {
                    if(file_exists($datapack_path.'map/main/'.$maindatapackcode.'/sub/'.$subdatapackcode.'/'.$map_xml_meta))
                    {
                        $content_meta_map=file_get_contents($datapack_path.'map/main/'.$maindatapackcode.'/sub/'.$subdatapackcode.'/'.$map_xml_meta);
                        foreach($layer_toSearch as $toSearch)
                        {
                            if(preg_match('#<'.preg_quote($toSearch).'>(.*)</'.preg_quote($toSearch).'>#isU',$content_meta_map))
                            {
                                $search=false;
                                if(isset($layer_meta[$toSearch]))
                                    $search=($layer_meta[$toSearch]['layer']=='' /*Cave*/ || preg_match('#<layer( [^>]+)? name="'.preg_quote($layer_meta[$toSearch]['layer']).'"#isU',$content)/*Have layer into the tmx*/);
                                else if(isset($layer_event[$toSearch]))
                                    $search=($layer_event[$toSearch]['layer']=='' /*Cave*/ || preg_match('#<layer( [^>]+)? name="'.preg_quote($layer_event[$toSearch]['layer']).'"#isU',$content)/*Have layer into the tmx*/);
                                if($search)
                                {
                                    $averagelevelTemp=(float)0.0;
                                    $monsterCount=0;
                                    $text=preg_replace('#^.*<'.preg_quote($toSearch).'>(.*)</'.preg_quote($toSearch).'>.*$#isU','$1',$content_meta_map);
                                    preg_match_all('#<monster[^>]+/>#isU',$text,$temp_text_list);
                                    foreach($temp_text_list[0] as $text_entry)
                                    {
                                        if(preg_match('# level="([0-9]+)"#isU',$text_entry))
                                        {
                                            $minLevel=preg_replace('#^.* level="([0-9]+)".*$#isU','$1',$text_entry);
                                            $maxLevel=preg_replace('#^.* level="([0-9]+)".*$#isU','$1',$text_entry);
                                        }
                                        elseif(preg_match('# minLevel="([0-9]+)"#isU',$text_entry) && preg_match('# maxLevel="([0-9]+)"#isU',$text_entry))
                                        {
                                            $minLevel=preg_replace('#^.* minLevel="([0-9]+)".*$#isU','$1',$text_entry);
                                            $maxLevel=preg_replace('#^.* maxLevel="([0-9]+)".*$#isU','$1',$text_entry);
                                        }
                                        else
                                            continue;
                                        if(preg_match('#luck="([0-9]+)"#isU',$text_entry))
                                            $luck=preg_replace('#^.*luck="([0-9]+)".*$#isU','$1',$text_entry);
                                        else
                                            continue;
                                        if(preg_match('#id="([0-9]+)"#isU',$text_entry))
                                            $id=preg_replace('#^.*id="([0-9]+)".*$#isU','$1',$text_entry);
                                        else
                                            continue;
                                        if(isset($monster_meta[$id]))
                                        {
                                            if(!isset($monsters[$toSearch]))
                                                $monsters[$toSearch]=array();
                                            if(!isset($monsters[$toSearch][$subdatapackcode]))
                                                $monsters[$toSearch][$subdatapackcode]=array();
                                            $monsters[$toSearch][$subdatapackcode][]=array('id'=>$id,'minLevel'=>$minLevel,'maxLevel'=>$maxLevel,'luck'=>$luck);
                                            if(!isset($monster_to_map[$id]))
                                                $monster_to_map[$id]=array();
                                            if(!isset($monster_to_map[$id][$toSearch]))
                                                $monster_to_map[$id][$toSearch]=array();
                                            if(!isset($monster_to_map[$id][$toSearch][$maindatapackcode]))
                                                $monster_to_map[$id][$toSearch][$maindatapackcode]=array();
                                            $monster_to_map[$id][$toSearch][$maindatapackcode][$map][$subdatapackcode]=array('minLevel'=>$minLevel,'maxLevel'=>$maxLevel,'luck'=>$luck);
                                            if(!in_array($id,$monsters_list))
                                                $monsters_list[]=$id;
                                            if(!isset($monster_meta[$id]['game'][$maindatapackcode]))
                                                $monster_meta[$id]['game'][$maindatapackcode]=array();
                                            if(!in_array($subdatapackcode,$monster_meta[$id]['game'][$maindatapackcode]))
                                                $monster_meta[$id]['game'][$maindatapackcode][]=$subdatapackcode;
                                            $monster_to_map_count[$id][$maindatapackcode.'/'.$map][$subdatapackcode]=0;
                                            $monster_meta[$id]['rarity']+=$luck;
                                            $dropcount+=count($monster_meta[$id]['drops']);
                                            $averagelevelTemp+=$minLevel+$maxLevel;
                                            $monsterCount++;
                                        }
                                        else
                                            echo 'Monster: '.$id.' not found on the map: '.$map."\n";
                                    }
                                    if(
                                    ($toSearch=='cave' || $toSearch=='grass') && 
                                    ($averagelevelType=='' || ($averagelevelType=='cave' && $toSearch=='grass')) && 
                                    $monsterCount>0
                                    )
                                    {
                                        $averagelevel=(float)$averagelevelTemp/(2*$monsterCount);
                                        $averagelevelType=$toSearch;
                                    }
                                }
                                else
                                {
                                    if(isset($layer_meta[$toSearch]))
                                        echo '[Meta] Not search because no layer '.$toSearch.' detected, layer meta: '.$layer_meta[$toSearch]['layer'].', with name: '.
                                        (preg_match('#<layer name="'.preg_quote($layer_meta[$toSearch]['layer']).'"#isU',$content)).', regex used: '.
                                        '#<layer name="'.preg_quote($layer_meta[$toSearch]['layer']).'"#isU'
                                        .' for map: '.$map."\n";
                                    else if(isset($layer_event[$toSearch]))
                                        echo '[Event] Not search because no layer '.$toSearch.' detected, layer meta: '.$layer_event[$toSearch]['layer'].', with name: '.
                                        (preg_match('#<layer name="'.preg_quote($layer_event[$toSearch]['layer']).'"#isU',$content)).', regex used: '.
                                        '#<layer name="'.preg_quote($layer_event[$toSearch]['layer']).'"#isU'
                                        .' for map: '.$map."\n";
                                    else
                                        echo 'Not search because no layer '.$toSearch.' detected for map: '.$map."\n";
                                    echo 'Mostly due '.$toSearch.' is into '.$map_xml_meta.' but no layer into: '.$map."\n";
                                }
                            }
                            /*else
                                echo 'No layer '.$toSearch.' detected for map: '.$map."\n";*/
                            if(isset($monsters[$toSearch]['']) && !isset($monsters[$toSearch][$subdatapackcode]))
                            {
                                if(isset($monsters[$toSearch]['']))
                                {
                                    $averagelevelTemp=(float)0.0;
                                    $monsterCount=0;
                                    $monsters[$toSearch][$subdatapackcode]=$monsters[$toSearch][''];
                                    foreach($monsters[$toSearch][$subdatapackcode] as $tempMonsterList)
                                    {
                                        $id=$tempMonsterList['id'];
                                        $minLevel=$tempMonsterList['minLevel'];
                                        $maxLevel=$tempMonsterList['maxLevel'];
                                        $luck=$tempMonsterList['luck'];
                                        if(isset($monster_meta[$id]))
                                        {
                                            if(!isset($monster_to_map[$id]))
                                                $monster_to_map[$id]=array();
                                            if(!isset($monster_to_map[$id][$toSearch]))
                                                $monster_to_map[$id][$toSearch]=array();
                                            if(!isset($monster_to_map[$id][$toSearch][$maindatapackcode]))
                                                $monster_to_map[$id][$toSearch][$maindatapackcode]=array();
                                            $monster_to_map[$id][$toSearch][$maindatapackcode][$map][$subdatapackcode]=array('minLevel'=>$minLevel,'maxLevel'=>$maxLevel,'luck'=>$luck);
                                            if(!in_array($id,$monsters_list))
                                                $monsters_list[]=$id;
                                            if(!isset($monster_meta[$id]['game'][$maindatapackcode]))
                                                $monster_meta[$id]['game'][$maindatapackcode]=array();
                                            if(!in_array($subdatapackcode,$monster_meta[$id]['game'][$maindatapackcode]))
                                                $monster_meta[$id]['game'][$maindatapackcode][]=$subdatapackcode;
                                            $monster_to_map_count[$id][$maindatapackcode.'/'.$map][$subdatapackcode]=0;
                                            $dropcount+=count($monster_meta[$id]['drops']);
                                            $monster_meta[$id]['rarity']+=$tempMonsterList['luck'];
                                            $averagelevelTemp+=$minLevel+$maxLevel;
                                            $monsterCount++;
                                        }
                                    }
                                    if(
                                    ($toSearch=='cave' || $toSearch=='grass') && 
                                    ($averagelevelType=='' || ($averagelevelType=='cave' && $toSearch=='grass')) && 
                                    $monsterCount>0
                                    )
                                    {
                                        $averagelevel=(float)$averagelevelTemp/(2*$monsterCount);
                                        $averagelevelType=$toSearch;
                                    }
                                }
                            }
                        }
                    }
                    else
                    {
                        foreach($layer_toSearch as $toSearch)
                        {
                            if(isset($monsters[$toSearch]['']))
                            {
                                $averagelevelTemp=(float)0.0;
                                $monsterCount=0;
                                $monsters[$toSearch][$subdatapackcode]=$monsters[$toSearch][''];
                                foreach($monsters[$toSearch][$subdatapackcode] as $tempMonsterList)
                                {
                                    $id=$tempMonsterList['id'];
                                    $minLevel=$tempMonsterList['minLevel'];
                                    $maxLevel=$tempMonsterList['maxLevel'];
                                    $luck=$tempMonsterList['luck'];
                                    if(isset($monster_meta[$id]))
                                    {
                                        if(!isset($monster_to_map[$id]))
                                            $monster_to_map[$id]=array();
                                        if(!isset($monster_to_map[$id][$toSearch]))
                                            $monster_to_map[$id][$toSearch]=array();
                                        if(!isset($monster_to_map[$id][$toSearch][$maindatapackcode]))
                                            $monster_to_map[$id][$toSearch][$maindatapackcode]=array();
                                        $monster_to_map[$id][$toSearch][$maindatapackcode][$map][$subdatapackcode]=array('minLevel'=>$minLevel,'maxLevel'=>$maxLevel,'luck'=>$luck);
                                        if(!in_array($id,$monsters_list))
                                            $monsters_list[]=$id;
                                        if(!isset($monster_meta[$id]['game'][$maindatapackcode]))
                                            $monster_meta[$id]['game'][$maindatapackcode]=array();
                                        if(!in_array($subdatapackcode,$monster_meta[$id]['game'][$maindatapackcode]))
                                            $monster_meta[$id]['game'][$maindatapackcode][]=$subdatapackcode;
                                        $monster_to_map_count[$id][$maindatapackcode.'/'.$map][$subdatapackcode]=0;
                                        $dropcount+=count($monster_meta[$id]['drops']);
                                        $monster_meta[$tempMonsterList['id']]['rarity']+=$tempMonsterList['luck'];
                                        $averagelevelTemp+=$minLevel+$maxLevel;
                                        $monsterCount++;
                                    }
                                }
                                if(
                                ($toSearch=='cave' || $toSearch=='grass') && 
                                ($averagelevelType=='' || ($averagelevelType=='cave' && $toSearch=='grass')) && 
                                $monsterCount>0
                                )
                                {
                                    $averagelevel=(float)$averagelevelTemp/(2*$monsterCount);
                                    $averagelevelType=$toSearch;
                                }
                            }
                        }
                    }
                }
            }
            if($mapgroup==0)//create a new group
            {
                $mapgroup=count($mapsgroup_meta)+1;
                $mapsgroup_meta[$mapgroup][]=$map;
            }
            $maps_list[$maindatapackcode][$map]=array('folder'=>$map_folder,'borders'=>$borders,'tp'=>$tp,'doors'=>$doors,'bots'=>$bots,'type'=>$type,'monsters'=>$monsters,'monsters_list'=>$monsters_list,
            'width'=>$width,'height'=>$height,'pixelwidth'=>$pixelwidth,'pixelheight'=>$pixelheight,'dropcount'=>$dropcount,'zone'=>$zone,'items'=>$items,'name'=>$name_in_other_lang,'shortdescription'=>$description_in_other_lang,'description'=>$name_in_other_lang,
            'mapgroup'=>$mapgroup,'averagelevel'=>$averagelevel,'maxfightlevel'=>0.0);
            ksort($maps_list[$maindatapackcode]);
            if(!isset($zone_to_map[$maindatapackcode]))
                $zone_to_map[$maindatapackcode]=array();
            if(!isset($zone_to_map[$maindatapackcode][$zone]))
                $zone_to_map[$maindatapackcode][$zone]=array();
            $zone_to_map[$maindatapackcode][$zone][$map]=$name_in_other_lang;
            ksort($zone_to_map[$maindatapackcode][$zone]);
            ksort($zone_to_map[$maindatapackcode]);
            $maps_name_to_map[$maindatapackcode][$name]=$map;
            ksort($maps_name_to_map[$maindatapackcode]);
        }
        ksort($map_short_path_to_name);
        ksort($zone_to_map);
        foreach($duplicate_detection_name as $maindatapackcode=>$duplicate_detection_name_value)
        {
            ksort($duplicate_detection_name[$maindatapackcode]);
            foreach($duplicate_detection_name_value as $index=>$value)
                ksort($duplicate_detection_name[$maindatapackcode][$index]);
        }
        foreach($duplicate_detection_name_and_zone as $maindatapackcode=>$duplicate_detection_name_value)
        {
            ksort($duplicate_detection_name_and_zone[$maindatapackcode]);
            foreach($duplicate_detection_name_value as $index=>$value)
                ksort($duplicate_detection_name_and_zone[$maindatapackcode][$index]);
        }
    }
}
closedir($dh);

foreach($mapsgroup_meta as $groupid=>$maplist)
    if(count($maplist)==0)
        unset($mapsgroup_meta[$groupid]);
foreach($monster_meta as $id=>$monster)
{
    if($monster['rarity']>0)
    {
        $monster_to_rarity[$id]=$monster['rarity'];
        if(count($monster['game'])==1)
        foreach($monster['game'] as $maingame=>$subgame_list)
            if(count($subgame_list)==1)
            {
                foreach($subgame_list as $subgame)
                if($subgame=='')
                    $informations_meta['main'][$maingame]['monsters'][]=$id;
                else
                    $informations_meta['main'][$maingame]['sub'][$subgame]['monsters'][]=$id;
            }
    }
}
asort($monster_to_rarity);
$index=1;
foreach($monster_to_rarity as $id=>$rarity)
{
    $monster_to_rarity[$id]=array('rarity'=>$rarity,'position'=>$index);
    if(!isset($monster_to_map_count[$id]))
        unset($monster_to_rarity[$id]);
    else if(count($monster_to_map_count[$id])<2)
        unset($monster_to_rarity[$id]);
    else if(count($monster_to_map_count[$id])==1)
    {

        unset($monster_to_rarity[$id]);
    }
    $index++;
}

ksort($maps_list);
ksort($maps_name_to_file);
ksort($zone_to_map);
ksort($monster_to_map);
ksort($monster_to_map_count);
ksort($bots_file);
ksort($bot_id_to_skin);
ksort($bot_id_to_map);
ksort($map_name_to_path);
ksort($map_short_path_to_name);
ksort($map_short_path_to_path);
ksort($duplicate_map_file_name_list);
ksort($maps_name_to_map);
ksort($item_to_map);
ksort($duplicate_detection_name);
ksort($duplicate_detection_name_and_zone);
ksort($temp_maps);
ksort($monster_to_rarity);
ksort($mapsgroup_meta);

$exclusive_monster=array();
$exclusive_monster_reverse=array();
foreach($monster_meta as $id=>$monster)
{
    if(count($monster['game'])==1)
    {
        foreach($monster['game'] as $maindatapackcode=>$values)
        {
            if(count($values)==1)
            {
                $exclusive_monster[$maindatapackcode][$values[0]][]=$id;
                $exclusive_monster_reverse[$id]=array('maindatapackcode'=>$maindatapackcode,'subdatapackcode'=>$values[0]);
                ksort($exclusive_monster[$maindatapackcode][$values[0]]);
                ksort($exclusive_monster[$maindatapackcode]);
            }
        }
    }
}
ksort($exclusive_monster_reverse);
ksort($exclusive_monster);
ksort($bots_meta);
ksort($bots_found_in);
ksort($fight_to_bot);
ksort($bots_name_count);
ksort($industry_to_bot);
ksort($team_to_bot);
ksort($item_to_bot_shop);

ksort($bots_meta);
ksort($bots_found_in);
ksort($fight_to_bot);
ksort($bots_name_count);
ksort($industry_to_bot);
ksort($team_to_bot);
ksort($item_to_bot_shop);

//scan each map to set average fight level
foreach($maps_list as $maindatapackcode=>$map_list)
foreach($map_list as $mapTempId=>$map)
{
    $maxlevel=0;
    if(isset($map['bots']) && count($map['bots'])>0)
		foreach($map['bots'] as $bot_on_map)
			if(isset($bots_meta[$maindatapackcode][$bot_on_map['id']]))
			{
                $bot=$bots_meta[$maindatapackcode][$bot_on_map['id']];
                foreach($bot['step'] as $step_id=>$step)
                    if($step['type']=='fight')
                        if(isset($fight_meta[$maindatapackcode][$step['fightid']]))
                            foreach($fight_meta[$maindatapackcode][$step['fightid']]['monsters'] as $monster)
                                if($maxlevel<$monster['level'])
                                    $maxlevel=$monster['level'];
            }
    $maps_list[$maindatapackcode][$mapTempId]['maxfightlevel']=$maxlevel;
}

