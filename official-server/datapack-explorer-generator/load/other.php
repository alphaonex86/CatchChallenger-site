<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into load other'."\n");

$zone_meta=array();
$zone_name_to_code=array();
$dir = $datapack_path.'map/main/';
$dh  = opendir($dir);
while (false !== ($maindatapackcode = readdir($dh)))
{
    if($maindatapackcode!='.' && $maindatapackcode!='..')
    {
        if(is_dir($datapack_path.'map/main/'.$maindatapackcode) && preg_match('#^[a-z0-9]+$#isU',$maindatapackcode))
        {
            $xmlZoneList=getXmlList($datapack_path.'map/main/'.$maindatapackcode.'/zone/');
            if(!isset($zone_meta[$maindatapackcode]))
                $zone_meta[$maindatapackcode]=array();

            if(!isset($zone_name_to_code[$maindatapackcode]))
                $zone_name_to_code[$maindatapackcode]=array();
            foreach($xmlZoneList as $file)
            {
                $content=file_get_contents($datapack_path.'map/main/'.$maindatapackcode.'/zone/'.$file);
                if(!preg_match('#^([^"\\.]+).xml$#isU',$file))
                {
                    echo 'Into '.$datapack_path.'map/main/ the entry: '.$maindatapackcode.' file: '.$file.' the file name is wrong'."\n";
                    continue;
                }
                $code=preg_replace('#^([^"\\.]+).xml$#isU','$1',$file);
                if(!preg_match('#<name( lang="en")?>([^<]+)</name>#isU',$content))
                {
                    echo 'Into '.$datapack_path.'map/main/ the entry: '.$maindatapackcode.' file: '.$file.' the zone name is wrong'."\n";
                    continue;
                }
                $name=preg_replace('#^.*<name( lang="en")?>([^<]+)</name>.*$#isU','$2',$content);
                $name=str_replace('<![CDATA[','',str_replace(']]>','',$name));
                $name=preg_replace("#[\n\t\r]+#is",'',$name);
                $name_in_other_lang=array('en'=>$name);
                $zone_name_to_code[$maindatapackcode]['en'][$name]=$code;
                foreach($lang_to_load as $lang)
                {
                    if(!isset($name_in_other_lang[$lang]))
                    {
                        if(preg_match('#<name lang="'.$lang.'">([^<]+)</name>#isU',$content))
                        {
                            $temp_name=preg_replace('#^.*<name lang="'.$lang.'">([^<]+)</name>.*$#isU','$1',$content);
                            $temp_name=str_replace('<![CDATA[','',str_replace(']]>','',$temp_name));
                            $temp_name=preg_replace("#[\n\r\t]+#is",'',$temp_name);
                            $name_in_other_lang[$lang]=$temp_name;
                        }
                        else
                        {
                            $name_in_other_lang[$lang]=$name;
                            $temp_name=$name;
                        }
                        $zone_name_to_code[$maindatapackcode][$lang][$temp_name]=$code;
                    }
                }
                $zone_meta[$maindatapackcode][$code]=array('name'=>$name_in_other_lang);
                $zone_name_to_code[$maindatapackcode]['en'][$name]=$code;
            }
            ksort($zone_meta[$maindatapackcode]);
        }
        else
            echo 'Into '.$datapack_path.'map/main/ the entry: '.$maindatapackcode.' is not correct!'."\n";
    }
}
closedir($dh);
ksort($zone_meta);

$fight_meta=array();
$xmlFightList=getXmlList($datapack_path.'fight/');
foreach($xmlFightList as $file)
{
	$content=file_get_contents($datapack_path.'fight/'.$file);
	preg_match_all('#<fight id="[0-9]+".*</fight>#isU',$content,$entry_list);
	foreach($entry_list[0] as $entry)
	{
		$start='';
		$win='';
		$cash=0;
		if(!preg_match('#<fight id="[0-9]+".*</fight>#isU',$entry))
			continue;
		$id=preg_replace('#^.*<fight id="([0-9]+)".*</fight>.*$#isU','$1',$entry);
		if(isset($fight_meta[$id]))
		{
			echo 'duplicate id '.$id.' for the fight'."\n";
			continue;
		}
		if(preg_match('#<gain cash="([0-9]+)"#isU',$entry))
			$cash=preg_replace('#^.*<gain cash="([0-9]+)".*$#isU','$1',$entry);
		if(preg_match('#<start( lang="en")?>(<!\\[CDATA\\[)?(.*)(]]>)?</start>#isU',$entry))
			$start=preg_replace('#^.*<start( lang="en")?>(<!\\[CDATA\\[)?(.*)(]]>)?</start>.*$#isU','$3',$entry);
		if(preg_match('#<win( lang="en")?>(<!\\[CDATA\\[)?(.*)(]]>)?</win>#isU',$entry))
			$win=preg_replace('#^.*<win( lang="en")?>(<!\\[CDATA\\[)?(.*)(]]>)?</win>.*$#isU','$3',$entry);
		$start=str_replace('<![CDATA[','',$start);
		$win=str_replace('<![CDATA[','',$win);
		$monsters=array();
        preg_match_all('#<monster .* />#isU',$entry,$monster_text_list);
        foreach($monster_text_list[0] as $monster_text)
        {
            $monster=preg_replace('#^.* id="([0-9]+)".*$#isU','$1',$monster_text);
            $level=preg_replace('#^.* level="([0-9]+)".*$#isU','$1',$monster_text);
            $monsters[]=array('monster'=>$monster,'level'=>$level);
        }
		$fight_meta[$id]=array('start'=>$start,'win'=>$win,'cash'=>$cash,'monsters'=>$monsters);
	}
}
ksort($fight_meta);

$start_meta=array();

if(file_exists($datapack_path.'player/start.xml'))
{
    $content=file_get_contents($datapack_path.'player/start.xml');
    preg_match_all('#<start id="([^"]+)">.*</start>#isU',$content,$entry_list);
    foreach($entry_list[0] as $entry_list_index=>$entry)
    {
        $profile_id=$entry_list[1][$entry_list_index];
        if(!preg_match('#<name( lang="en")?>.*</name>#isU',$entry))
            continue;
        $name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$entry);
        $name=str_replace('<![CDATA[','',str_replace(']]>','',$name));
        $name_in_other_lang=array('en'=>$name);
        foreach($lang_to_load as $lang)
        {
            if($lang=='en')
                continue;
            if(preg_match('#<name lang="'.$lang.'">([^<]+)</name>#isU',$entry))
            {
                $temp_name=preg_replace('#^.*<name lang="'.$lang.'">([^<]+)</name>.*$#isU','$1',$entry);
                $temp_name=str_replace('<![CDATA[','',str_replace(']]>','',$temp_name));
                $temp_name=preg_replace("#[\n\r\t]+#is",'',$temp_name);
                $name_in_other_lang[$lang]=$temp_name;
            }
            else
                $name_in_other_lang[$lang]=$name;
        }
        if(!preg_match('#<description( lang="en")?>.*</description>#isU',$entry))
            continue;
        $description=text_operation_first_letter_upper(preg_replace('#^.*<description( lang="en")?>(.*)</description>.*$#isU','$2',$entry));
        $description=str_replace('<![CDATA[','',str_replace(']]>','',$description));
        $description_in_other_lang=array('en'=>$description);
        foreach($lang_to_load as $lang)
        {
            if($lang=='en')
                continue;
            if(preg_match('#<description lang="'.$lang.'">([^<]+)</description>#isU',$entry))
            {
                $temp_description=preg_replace('#^.*<description lang="'.$lang.'">([^<]+)</description>.*$#isU','$1',$entry);
                $temp_description=str_replace('<![CDATA[','',str_replace(']]>','',$temp_description));
                $temp_description=preg_replace("#[\n\r\t]+#is",'',$temp_description);
                $description_in_other_lang[$lang]=$temp_description;
            }
            else
                $description_in_other_lang[$lang]=$description;
        }
        $forcedskin=array();
        if(preg_match('#<forcedskin.*value="([^"]+)".*/>#isU',$entry))
            $forcedskin=explode(';',preg_replace('#^.*<forcedskin.*value="([^"]+)".*/>.*$#isU','$1',$entry));
        $cash=0;
        if(preg_match('#<cash.*value="([^"]+)".*/>#isU',$entry))
            $cash=preg_replace('#^.*<cash.*value="([^"]+)".*/>.*$#isU','$1',$entry);
        
        $monstergroup=array();
        preg_match_all('#<monstergroup>.*</monstergroup>#isU',$entry,$monstergroup_text_list);
        foreach($monstergroup_text_list[0] as $monstergroup_text)
        {
            $monsters=array();
            preg_match_all('#<monster .*/>#isU',$monstergroup_text,$monster_list);

            foreach($monster_list[0] as $monster)
            {
                if(!preg_match('#<monster.*id="([0-9]+)".*/>#isU',$monster))
                    continue;
                if(!preg_match('#<monster.*level="([0-9]+)".*/>#isU',$monster))
                    continue;
                if(!preg_match('#<monster.*captured_with="([0-9]+)".*/>#isU',$monster))
                    continue;
                $id=preg_replace('#^.*<monster.*id="([0-9]+)".*/>.*$#isU','$1',$monster);
                $level=preg_replace('#^.*<monster.*level="([0-9]+)".*/>.*$#isU','$1',$monster);
                $captured_with=preg_replace('#^.*<monster.*captured_with="([0-9]+)".*/>.*$#isU','$1',$monster);
                $skill_added=0;
                $attack_list=array();
                if(isset($monster_meta[$id]['attack_list']))
                    foreach($monster_meta[$id]['attack_list'] as $learn_at_level=>$skill_list)
                    {
                        foreach($skill_list as $skill)
                        {
                            if($learn_at_level<=$level)
                            {
                                $attack_list[]=$skill;
                                $skill_added++;
                            }
                            if(count($attack_list)>=4)
                                break;
                        }
                        if(count($attack_list)>=4)
                            break;
                    }
                $monsters[]=array('id'=>$id,'level'=>$level,'captured_with'=>$captured_with,'attack_list'=>$attack_list);
            }

            $monstergroup[]=$monsters;
        }

        if(count($monsters)<=0)
            continue;

        preg_match_all('#<reputation type="[a-z]+" level="[0-9]+" />#isU',$entry,$reputation_list);
        $reputations=array();
        foreach($reputation_list as $reputation)
        {
            if(!preg_match('#<reputation.*type="([a-z]+)".*/>#isU',$entry))
                continue;
            if(!preg_match('#<reputation.*level="([0-9]+)".*/>#isU',$entry))
                continue;
            $type=preg_replace('#^.*<reputation.*type="([a-z]+)".*/>.*$#isU','$1',$entry);
            $level=preg_replace('#^.*<reputation.*level="([0-9]+)".*/>.*$#isU','$1',$entry);
            $reputations[]=array('type'=>$type,'level'=>$level);
        }

        preg_match_all('#<item id="[0-9]+" quantity="[0-9]+" />#isU',$entry,$item_list);
        $items=array();
        foreach($item_list as $item)
        {
            if(!preg_match('#<item.*id="([0-9]+)".*/>#isU',$entry))
                continue;
            if(!preg_match('#<item.*quantity="([0-9]+)".*/>#isU',$entry))
                continue;
            $id=preg_replace('#^.*<item.*id="([^"]+)".*/>.*$#isU','$1',$entry);
            $quantity=preg_replace('#^.*<item.*quantity="([0-9]+)".*/>.*$#isU','$1',$entry);
            $items[]=array('id'=>$id,'quantity'=>$quantity);
        }
        
        $start_meta[$profile_id]=array('description'=>$description_in_other_lang,'forcedskin'=>$forcedskin,'cash'=>$cash,'monstergroup'=>$monstergroup,'reputations'=>$reputations,'items'=>$items,'name'=>$name_in_other_lang);
    }
}

$start_map_meta=array();
$dir = $datapack_path.'map/main/';
$dh  = opendir($dir);
while (false !== ($maindatapackcode = readdir($dh)))
{
    if($maindatapackcode!='.' && $maindatapackcode!='..')
    {
        if(is_dir($datapack_path.'map/main/'.$maindatapackcode) && preg_match('#^[a-z0-9]+$#isU',$maindatapackcode))
        {
            if(file_exists($datapack_path.'map/main/'.$maindatapackcode.'/start.xml'))
            {
                $content=file_get_contents($datapack_path.'map/main/'.$maindatapackcode.'/start.xml');
                preg_match_all('#map.*file="([^"]+)"#isU',$content,$map_list);
                foreach($map_list[1] as $mapcontent)
                {
                    $map=preg_replace('#^.*<map.*file="([^"]+)".* />.*$#isU','$1',$mapcontent);
                    $map=preg_replace("#[\n\r\t]+#is",'',$map);
                    if(!preg_match('#\\.tmx$#',$map))
                        $map.='.tmx';
                    if(!isset($start_map_meta[$maindatapackcode]))
                        $start_map_meta[$maindatapackcode]=array();
                    if(!in_array($map,$start_map_meta[$maindatapackcode]))
                        $start_map_meta[$maindatapackcode][]=$map;
                }
            }
        }
    }
}

$dir = $datapack_path.'map/main/';
$dh  = opendir($dir);
while (false !== ($maindatapackcode = readdir($dh)))
{
    if($maindatapackcode!='.' && $maindatapackcode!='..')
    {
        if(is_dir($datapack_path.'map/main/'.$maindatapackcode) && preg_match('#^[a-z0-9]+$#isU',$maindatapackcode))
        {
            if(file_exists($datapack_path.'map/main/'.$maindatapackcode.'/start.xml'))
            {
                $content=file_get_contents($datapack_path.'map/main/'.$maindatapackcode.'/start.xml');
                preg_match_all('#<start id="([^"]+)">.*</start>#isU',$content,$entry_list);
                foreach($entry_list[0] as $entry_list_index=>$entry)
                {
                    $profile_id=$entry_list[1][$entry_list_index];
                    $map_list=array();
                    preg_match_all('#<map .*/>#isU',$entry,$map_entry_list);
                    foreach($map_entry_list[0] as $map_entry)
                    {
                        if(!preg_match('#<map.*file="([^"]+)".*/>#isU',$entry))
                            continue;
                        if(!preg_match('#<map.*x="([0-9]+)".*/>#isU',$entry))
                            continue;
                        if(!preg_match('#<map.*y="([0-9]+)".*/>#isU',$entry))
                            continue;
                        $map=preg_replace('#^.*<map.*file="([^"]+)".*/>.*$#isU','$1',$entry);
                        if(!preg_match('#\.tmx$#',$map))
                            $map=$map.'.tmx';
                        $x=preg_replace('#^.*<map.*x="([0-9]+)".*/>.*$#isU','$1',$entry);
                        $y=preg_replace('#^.*<map.*y="([0-9]+)".*/>.*$#isU','$1',$entry);
                        $map_list[]=array('map'=>$map,'x'=>$x,'y'=>$y);
                    }

                    if(isset($start_meta[$profile_id]))
                        $start_meta[$profile_id]['map_list'][$maindatapackcode]=$map_list;
                    else
                        echo 'Profile to put the main code map not found: '.$profile_id."\n";
                }
            }
        }
    }
}
closedir($dh);

$quests_meta=array();
$monster_to_quests=array();
$items_to_quests=array();
$items_to_quests_for_step=array();
$bot_start_to_quests=array();
$dir = $datapack_path.'map/main/';
$dh  = opendir($dir);
while (false !== ($maindatapackcode = readdir($dh)))
{
    if($maindatapackcode!='.' && $maindatapackcode!='..')
    {
        if(is_dir($datapack_path.'map/main/'.$maindatapackcode) && preg_match('#^[a-z0-9]+$#isU',$maindatapackcode))
        {
            $xmlFightList=getDefinitionXmlList($datapack_path.'map/main/'.$maindatapackcode.'/quests/');
            foreach($xmlFightList as $file)
            {
                $file_temp=preg_replace('#^([0-9]+)([^0-9].*)?$#isU','$1',$file);
                if(!preg_match('#^([0-9]+)$#is',$file_temp))
                    continue;
                $id=preg_replace('#^([0-9]+)$#is','$1',$file_temp);
                if(isset($quests_meta[$maindatapackcode][$id]))
                {
                    echo 'duplicate id '.$id.' for the quests'."\n";
                    continue;
                }
                $content=file_get_contents($datapack_path.'map/main/'.$maindatapackcode.'/quests/'.$file);
                $repeatable=false;
                if(preg_match('#repeatable="(yes|true)"#isU',$content))
                    $repeatable=true;
                if(!preg_match('#bot="([0-9]+)"#isU',$content))
                    continue;
                $bot=preg_replace('#^.*bot="([0-9]+)".*$#isU','$1',$content);
                if(!preg_match('#<name( lang="en")?>.*</name>#isU',$content))
                    continue;
                $bot=preg_replace("#[\n\r\t]+#is",'',$bot);
                $name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$content);
                $name=str_replace('<![CDATA[','',str_replace(']]>','',$name));
                $name=preg_replace("#[\n\r\t]+#is",'',$name);
                $name_in_other_lang=array('en'=>$name);
                foreach($lang_to_load as $lang)
                {
                    if($lang=='en')
                        continue;
                    if(preg_match('#<name lang="'.$lang.'">([^<]+)</name>#isU',$entry))
                    {
                        $temp_name=preg_replace('#^.*<name lang="'.$lang.'">([^<]+)</name>.*$#isU','$1',$entry);
                        $temp_name=str_replace('<![CDATA[','',str_replace(']]>','',$temp_name));
                        $temp_name=preg_replace("#[\n\r\t]+#is",'',$temp_name);
                        $name_in_other_lang[$lang]=$temp_name;
                    }
                    else
                        $name_in_other_lang[$lang]=$name;
                }

                $requirements=array();
                preg_match_all('#<requirements.*</requirements>#isU',$content,$entry_list);
                foreach($entry_list[0] as $entry)
                {
                    preg_match_all('#<quest id="([0-9]+)"[^>]+/>#isU',$entry,$item_text_list);
                    foreach($item_text_list[0] as $item_text)
                    {
                        if(!isset($requirements['quests']))
                            $requirements['quests']=array();
                        $quest_id=preg_replace('#^.*<quest id="([0-9]+)"[^>]+/>.*$#isU','$1',$item_text);
                        $requirements['quests'][]=$quest_id;
                    }
                }

                $steps=array();
                preg_match_all('#<step id="[0-9]+".*</step>#isU',$content,$entry_list);
                foreach($entry_list[0] as $entry)
                {
                    $tempbot=$bot;
                    if(preg_match('#bot="([0-9]+)"#isU',$entry))
                        $tempbot=preg_replace('#^.*bot="([0-9]+)".*</step>.*$#is','$1',$entry);
                    $id_step=preg_replace('#^.*<step id="([0-9]+)".*</step>.*$#is','$1',$entry);
                    $text=preg_replace('#^.*<text( lang="en")?>(.*)</text>.*$#isU','$2',$entry);
                    $text=str_replace('<![CDATA[','',$text);
                    $text=str_replace(']]>','',$text);
                    $items=array();
                    preg_match_all('#<item ([^>]+)/>#isU',$entry,$item_text_list);
                    foreach($item_text_list[0] as $item_text)
                    {
                        if(!preg_match('# id="([0-9]+)"#isU',$item_text))
                            continue;
                        $item=preg_replace('#^.* id="([0-9]+)".*$#isU','$1',$item_text);
                        if(preg_match('#quantity="([0-9]+)"#isU',$item_text))
                            $quantity=preg_replace('#^.*quantity="([0-9]+)".*$#isU','$1',$item_text);
                        else
                            $quantity=1;
                        if(preg_match('#monster="([0-9]+)"#isU',$item_text))
                        {
                            $monster=preg_replace('#^.*monster="([0-9]+)".*$#isU','$1',$item_text);
                            if(preg_match('#rate="([0-9]+)%?"#isU',$item_text))
                                $rate=preg_replace('#^.*rate="([0-9]+)%?".*$#isU','$1',$item_text);
                            else
                                $rate=100;
                        }
                        else
                            $monster=0;
                        if(!isset($items_to_quests_for_step[$item]))
                            $items_to_quests_for_step[$item]=array();
                        if($monster!=0)
                        {
                            $items[]=array('item'=>$item,'quantity'=>$quantity,'monster'=>$monster,'rate'=>$rate);
                            if(!isset($monster_to_quests[$monster]))
                                $monster_to_quests[$monster]=array();
                            if(!isset($monster_to_quests[$monster][$maindatapackcode]))
                                $monster_to_quests[$monster][$maindatapackcode]=array();
                            $monster_to_quests[$monster][$maindatapackcode][]=array('quest'=>$id,'item'=>$item,'quantity'=>$quantity,'rate'=>$rate);
                            $items_to_quests_for_step[$item][$maindatapackcode][]=array('quest'=>$id,'quantity'=>$quantity,'monster'=>$monster,'rate'=>$rate);
                        }
                        else
                        {
                            $items[]=array('item'=>$item,'quantity'=>$quantity);
                            $items_to_quests_for_step[$item][$maindatapackcode][]=array('quest'=>$id,'quantity'=>$quantity);
                        }
                    }
                    $tempbot=preg_replace("#[\n\r\t]+#is",'',$tempbot);
                    $steps[$id_step]=array('text'=>$text,'bot'=>$tempbot,'items'=>$items);
                    if($id_step==1)
                    {
                        if(!isset($bot_start_to_quests[$tempbot]))
                            $bot_start_to_quests[$tempbot]=array();
                        if(!in_array($id,$bot_start_to_quests[$tempbot]))
                            $bot_start_to_quests[$tempbot][]=$id;
                    }
                }

                $rewards=array();
                preg_match_all('#<rewards.*</rewards>#isU',$content,$entry_list);
                foreach($entry_list[0] as $entry)
                {
                    preg_match_all('#<item ([^>]+)/>#isU',$entry,$item_text_list);
                    foreach($item_text_list[0] as $item_text)
                    {
                        if(!preg_match('# id="([0-9]+)"#isU',$item_text))
                            continue;
                        if(!isset($rewards['items']))
                            $rewards['items']=array();
                        $item=preg_replace('#^.* id="([0-9]+)".*$#isU','$1',$item_text);
                        if(preg_match('#quantity="([0-9]+)"#isU',$item_text))
                            $quantity=preg_replace('#^.*quantity="([0-9]+)".*$#isU','$1',$item_text);
                        else
                            $quantity=1;
                        if(!isset($items_to_quests[$item]))
                            $items_to_quests[$item]=array();
                        $items_to_quests[$item][$maindatapackcode][$id]=$quantity;
                        $rewards['items'][]=array('item'=>$item,'quantity'=>$quantity);
                    }
                    preg_match_all('#<allow ([^>]+)/>#isU',$entry,$item_text_list);
                    foreach($item_text_list[0] as $item_text)
                    {
                        if(!preg_match('# type="([a-z]+)"#isU',$item_text))
                            continue;
                        if(!isset($rewards['allow']))
                            $rewards['allow']=array();
                        $allow=preg_replace('#^.* type="([a-z]+)".*$#isU','$1',$item_text);
                        if(!in_array($allow,$rewards['allow']))
                            $rewards['allow'][]=$allow;
                    }
                    preg_match_all('#<reputation type="([^"]+)" point="(-?[0-9]+)"[^>]+/>#isU',$entry,$item_text_list);
                    foreach($item_text_list[0] as $item_text)
                    {
                        if(!isset($rewards['reputation']))
                            $rewards['reputation']=array();
                        $type=preg_replace('#^.*<reputation type="([^"]+)" point="(-?[0-9]+)"[^>]+/>.*$#isU','$1',$item_text);
                        $point=preg_replace('#^.*<reputation type="([^"]+)" point="(-?[0-9]+)"[^>]+/>.*$#isU','$2',$item_text);
                        $rewards['reputation'][]=array('type'=>$type,'point'=>$point);
                    }
                }
                $quests_meta[$maindatapackcode][$id]=array('repeatable'=>$repeatable,'steps'=>$steps,'rewards'=>$rewards,'requirements'=>$requirements,'bot'=>$bot,'name'=>$name_in_other_lang);
            }
            if(isset($quests_meta[$maindatapackcode]))
                ksort($quests_meta[$maindatapackcode]);
        }
    }
}
closedir($dh);
ksort($quests_meta);

$visualcategory_meta=array();
if(!file_exists($datapack_path.'/map/visualcategory.xml'))
{
    echo 'visualcategory.xml not found (abort)';
    exit;
}
$content=file_get_contents($datapack_path.'/map/visualcategory.xml');
$entry_list=preg_split('#<category #isU',$content);
foreach($entry_list as $entry)
{
    $color='#000000';
    $alpha='255';
    if(!preg_match('#id="[0-9a-zA-Z]+".*#isU',$entry))
        continue;
    $id=preg_replace('#^.*id="([0-9a-zA-Z]+)".*$#isU','$1',$entry);
    if(isset($visualcategory_meta[$id]))
    {
        echo 'duplicate id '.$id.' for the visualcategory'."\n";
        continue;
    }
    if(preg_match('#^[^>]* color="(.[0-9]+)"#isU',$entry))
        $color=preg_replace('#^[^>]* color="(.[0-9]+)".*$#isU','$1',$entry);
    if(preg_match('#^[^>]* alpha="([0-9]+)"#isU',$entry))
        $alpha=preg_replace('#^[^>]* alpha="([0-9]+)".*$#isU','$1',$entry);
    $visualcategory_meta[$id]=array('color'=>$color,'alpha'=>$alpha);
}
ksort($visualcategory_meta);
