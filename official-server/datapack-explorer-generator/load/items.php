<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into load items'."\n");

$item_group=array();
//load the item group
if(is_file($datapack_path.'items/groups.xml'))
{
    $content=file_get_contents($datapack_path.'items/groups.xml');
    preg_match_all('#<group id="([a-zA-Z0-9\\-]+)">(.*)</group>#isU',$content,$temp_text_list);
    foreach($temp_text_list[1] as $index=>$base_group_name)
    {
        if(!preg_match('#<name( lang="en")?>.*</name>#isU',$temp_text_list[2][$index]))
            continue;
        $entry=$temp_text_list[2][$index];
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
        $item_group[$base_group_name]=array('name'=>$name_in_other_lang);
    }
}

$duplicate_item_name=array();
$item_meta=array();
$item_to_trap=array();
$item_to_regeneration=array();
$temp_items=getXmlList($datapack_path.'items/');
foreach($temp_items as $item_file)
{
	$group='';
	$content=file_get_contents($datapack_path.'items/'.$item_file);
	if(preg_match('#<items group="([a-zA-Z0-9\\-]+)">#isU',$content))
	{
		$group=preg_replace('#^.*<items group="([a-zA-Z0-9\\-]+)">.*</item>.*$#isU','$1',$content);
		$group=preg_replace("#[\n\r\t]#",'',$group);
		if(!isset($item_group[$group]))
		{
			echo 'group "'.$group.'" not found for the file '.$item_file."\n";
			$group='';
		}
	}
	preg_match_all('#<item[^>]*>.*</item>#isU',$content,$temp_text_list);
	foreach($temp_text_list[0] as $entry)
	{
		if(!preg_match('#<item[^>]*id="[0-9]+".*</item>#isU',$entry))
			continue;
		$id=preg_replace('#^.*<item[^>]*id="([0-9]+)".*</item>.*$#isU','$1',$entry);
		if(isset($item_meta[$id]))
		{
			echo 'duplicate id '.$id.' for item'."\n";
			continue;
		}
		$price=0;

		if(preg_match('#<item[^>]*price="[0-9]+".*</item>#isU',$entry))
			$price=preg_replace('#^.*<item[^>]*price="([0-9]+)".*</item>.*$#isU','$1',$entry);
		if(preg_match('#<item[^>]*image="[^"]+".*</item>#isU',$entry))
			$image=preg_replace('#^.*<item[^>]*image="([^"]+)".*</item>.*$#isU','$1',$entry);
		else
			$image='';
		$image=preg_replace('#[^/]+$#isU','',$item_file).$image;
		if(!preg_match('#<name( lang="en")?>.*</name>#isU',$entry))
			continue;
		$name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$entry);
        $name=str_replace('<![CDATA[','',str_replace(']]>','',$name));
        $name=preg_replace("#[\n\r\t]+#is",'',$name);
        if(isset($duplicate_item_name['en'][$name]) && $duplicate_item_name['en'][$name]!=$id)
            echo 'duplicate name '.$name.' for item ('.$id.' previously on '.$duplicate_item_name['en'][$name].')'."\n";
        else
            $duplicate_item_name['en'][$name]=$id;
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
            {
                $temp_name=$name;
                $name_in_other_lang[$lang]=$name;
            }
            if(isset($duplicate_item_name[$lang][$temp_name]) && $duplicate_item_name[$lang][$temp_name]!=$id)
                echo 'duplicate name '.$temp_name.' for item ('.$id.' previously on '.$duplicate_item_name[$lang][$temp_name].') for lang '.$lang."\n";
            else
                $duplicate_item_name[$lang][$temp_name]=$id;
        }
		if(preg_match('#<description( lang="en")?>.*</description>#isU',$entry))
			$description=text_operation_first_letter_upper(preg_replace('#^.*<description( lang="en")?>(.*)</description>.*$#isU','$2',$entry));
		else
			$description='';
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
		if(preg_match('#<trap[^>]+/>#isU',$entry))
		{
			$temp_text=preg_replace('#^.*(<trap[^>]+/>).*$#isU','$1',$entry);
			if(preg_match('#bonus_rate="([0-9]+(\.[0-9]+)?)"#isU',$temp_text))
			{
				$bonus_rate=preg_replace('#^.*bonus_rate="([0-9]+(\.[0-9]+)?)".*$#isU','$1',$temp_text);
				$item_meta[$id]=array('group'=>$group,'price'=>$price,'image'=>$image,'name'=>$name_in_other_lang,'description'=>$description_in_other_lang,'trap'=>$bonus_rate);
				$item_to_trap[$id]=array('price'=>$price,'image'=>$image,'name'=>$name_in_other_lang,'description'=>$description_in_other_lang,'trap'=>$bonus_rate);
			}
			else
				$item_meta[$id]=array('group'=>$group,'price'=>$price,'image'=>$image,'name'=>$name_in_other_lang,'description'=>$description_in_other_lang);
		}
		else if(preg_match('#<repel[^>]+/>#isU',$entry))
		{
			$temp_text=preg_replace('#^.*(<repel[^>]+/>).*$#isU','$1',$entry);
			if(preg_match('#step="([0-9]+(\.[0-9]+)?)"#isU',$temp_text))
			{
				$step=preg_replace('#^.*step="([0-9]+(\.[0-9]+)?)".*$#isU','$1',$temp_text);
				$item_meta[$id]=array('group'=>$group,'price'=>$price,'image'=>$image,'name'=>$name_in_other_lang,'description'=>$description_in_other_lang,'repel'=>$step);
			}
			else
				$item_meta[$id]=array('group'=>$group,'price'=>$price,'image'=>$image,'name'=>$name_in_other_lang,'description'=>$description_in_other_lang);
		}
		else
		{
			$effect=array();
			if(preg_match('#<regeneration[^>]+/>#isU',$entry))
			{
				$temp_text=preg_replace('#^.*(<regeneration[^>]+/>).*$#isU','$1',$entry);
				if(preg_match('#hp="([0-9]+(\.[0-9]+)?|all)"#isU',$temp_text))
					$effect['regeneration']=preg_replace('#^.*hp="([0-9]+(\.[0-9]+)?|all)".*$#isU','$1',$temp_text);
			}
			if(preg_match('#<hp[^>]+/>#isU',$entry))
			{
				$temp_text=preg_replace('#^.*(<hp[^>]+/>).*$#isU','$1',$entry);
				if(preg_match('#add="([0-9]+(\.[0-9]+)?|all)"#isU',$temp_text))
					$effect['regeneration']=preg_replace('#^.*add="([0-9]+(\.[0-9]+)?|all)".*$#isU','$1',$temp_text);
			}
			if(preg_match('#<buff[^>]+/>#isU',$entry))
			{
				$temp_text=preg_replace('#^.*(<buff[^>]+/>).*$#isU','$1',$entry);
				if(preg_match('#remove="([0-9]+(\.[0-9]+)?|all)"#isU',$temp_text))
					$effect['buff']=preg_replace('#^.*remove="([0-9]+(\.[0-9]+)?|all)".*$#isU','$1',$temp_text);
			}
			if(count($effect)>0)
			{
				$item_meta[$id]=array('group'=>$group,'price'=>$price,'image'=>$image,'name'=>$name_in_other_lang,'description'=>$description_in_other_lang,'effect'=>$effect);
				$item_to_regeneration[$id]=$effect;
			}
			else
				$item_meta[$id]=array('group'=>$group,'price'=>$price,'image'=>$image,'name'=>$name_in_other_lang,'description'=>$description_in_other_lang);
		}
        if($id==576)
        {
        }
	}
}
ksort($item_meta);