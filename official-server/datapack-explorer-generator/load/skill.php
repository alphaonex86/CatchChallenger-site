<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into load skill'."\n");

$duplicate_skill_name=array();
$buff_to_skill=array();
$skill_meta=array();
$temp_skills=getXmlList($datapack_path.'monsters/skill/');
$skill_type_to_id=array();
foreach($temp_skills as $skill_file)
{
	$content=file_get_contents($datapack_path.'monsters/skill/'.$skill_file);
	preg_match_all('#<skill.*</skill>#isU',$content,$temp_text_list);
	foreach($temp_text_list[0] as $entry)
	{
		if(!preg_match('#id="[0-9]+"#isU',$entry))
        {
            echo 'id not found';
			continue;
        }
		$id=preg_replace('#^.*id="([0-9]+)".*$#isU','$1',$entry);
		if(isset($skill_meta[$id]))
		{
			echo 'duplicate id '.$id.' for the skill'."\n";
			continue;
		}
		$type='normal';
		if(preg_match('#type="[^"]+"#isU',$entry))
			$type=preg_replace('#^.*type="([^"]+)".*$#isU','$1',$entry);
		if(!isset($type_meta[$type]))
			$type='normal';

		$base_luck=100;
		if(preg_match('#<skill[^>]+luck="([0-9]+)%?"#isU',$entry))
			$base_luck=preg_replace('#^.*<skill[^>]+luck="([0-9]+)%?".*$#isU','$1',$entry);
		if(!preg_match('#<name( lang="en")?>.*</name>#isU',$entry))
			continue;
		$name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$entry);
        $name=str_replace('<![CDATA[','',str_replace(']]>','',$name));
        $name=preg_replace("#[\n\r\t]+#is",'',$name);
        if(isset($duplicate_skill_name['en'][$name]) && $duplicate_skill_name['en'][$name]!=$id)
            echo 'duplicate name '.$name.' for skill ('.$id.' previously on '.$duplicate_skill_name['en'][$name].')'."\n";
        else
            $duplicate_skill_name['en'][$name]=$id;
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
                $name_in_other_lang[$lang]=$name;
                $temp_name=$name;
            }
            if(isset($duplicate_skill_name[$lang][$temp_name]) && $duplicate_skill_name[$lang][$temp_name]!=$id)
                echo 'duplicate name '.$temp_name.' for skill ('.$id.' previously on '.$duplicate_skill_name[$lang][$temp_name].') for lang '.$lang."\n";
            else
                $duplicate_skill_name[$lang][$temp_name]=$id;
        }
		$level_list=array();
		preg_match_all('#<level.*</level>#isU',$entry,$temp_level_list);
		foreach($temp_level_list[0] as $level_text)
		{
			if(!preg_match('#number="[0-9]+"#isU',$level_text))
            {
                echo 'The level for skill '.$id.' don\'t have number'."\n";
				continue;
            }
			$number=preg_replace('#^.*number="([0-9]+)".*$#isU','$1',$level_text);
			$endurance=40;
			if(preg_match('#endurance="[0-9]+"#isU',$level_text))
				$endurance=preg_replace('#^.*endurance="([0-9]+)".*$#isU','$1',$level_text);
			$base_level_luck=$base_luck;
			if(preg_match('#<level[^>]+luck="([0-9]+)%?"#isU',$level_text))
				$base_level_luck=preg_replace('#^.*<level[^>]+luck="([0-9]+)%?".*$#isU','$1',$level_text);
			$sp=0;
			if(preg_match('#sp="[0-9]+"#isU',$level_text))
				$sp=preg_replace('#^.*sp="([0-9]+)".*$#isU','$1',$level_text);
			$life_quantity=0;
			if(preg_match('#life[^>]+quantity="(-?[0-9]+%?)"#isU',$level_text))
				$life_quantity=preg_replace('#^.*life[^>]+quantity="(-?[0-9]+%?)".*$#isU','$1',$level_text);
			$buff_list=array();
			preg_match_all('#(<buff[^>]+>)#isU',$level_text,$temp_buff_list);
			foreach($temp_buff_list[0] as $buff)
			{
				if(preg_match('#id="([0-9]+)"#isU',$buff))
				{
					$buff_id=preg_replace('#^.*id="([0-9]+)".*$#isU','$1',$buff);
					$success=100;
					if(preg_match('#success="([0-9]+)%?"#isU',$buff))
						$success=preg_replace('#^.*success="([0-9]+)%?".*$#isU','$1',$buff);
					$buff_list[]=array('id'=>$buff_id,'success'=>$success);
				}
			}
			$level_list[$number]=array('endurance'=>$endurance,'sp'=>$sp,'life_quantity'=>$life_quantity,'buff'=>$buff_list,'base_level_luck'=>$base_level_luck);
		}
		$skill_meta[$id]=array('type'=>$type,'level_list'=>$level_list,'name'=>$name_in_other_lang);
        if(!isset($skill_type_to_id[$type]))
            $skill_type_to_id[$type]=array();
        $skill_type_to_id[$type][]=$id;
        ksort($skill_type_to_id[$type]);
	}
}
ksort($skill_meta);
ksort($skill_type_to_id);