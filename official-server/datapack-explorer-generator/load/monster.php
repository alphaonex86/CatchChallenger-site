<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into load monster'."\n");

$monster_meta=array();
$item_to_monster=array();
$item_to_evolution=array();
$reverse_evolution=array();
$type_to_monster=array();
$skill_to_monster=array();
$item_to_skill_of_monster=array();
$temp_monsters=getXmlList($datapack_path.'monsters/');
foreach($temp_monsters as $monster_file)
{
	$content=file_get_contents($datapack_path.'monsters/'.$monster_file);
	preg_match_all('#<monster.*</monster>#isU',$content,$entry_list);
	foreach($entry_list[0] as $entry)
	{
		$first=preg_replace('#attack_list.*$#isU','',$entry);
		if(!preg_match('#.*id="[0-9]+".*#isU',$first))
			continue;
		$id=preg_replace('#^.*id="([0-9]+)".*$#isU','$1',$first);
		if(isset($monster_meta[$id]))
		{
			echo 'duplicate id '.$id.' for monster'."\n";
			continue;
		}
		$ratio_gender="50";
		if(preg_match('#ratio_gender="([0-9]+)%?"#isU',$first))
			$ratio_gender=preg_replace('#^.*ratio_gender="([0-9]+)%?".*$#isU','$1',$first);

		$height="0";
		if(preg_match('#height="([0-9]+(\.[0-9]+)?)m?"#isU',$first))
			$height=preg_replace('#^.*height="([0-9]+(\.[0-9]+)?)m?".*$#isU','$1',$first);
		$weight="0";
		if(preg_match('#weight="([0-9]+(\.[0-9]+)?)(kg)?"#isU',$first))
			$weight=preg_replace('#^.*weight="([0-9]+(\.[0-9]+)?)(kg)?".*$#isU','$1',$first);
		$egg_step="0";
		if(preg_match('#egg_step="([0-9]+)"#isU',$first))
			$egg_step=preg_replace('#^.*egg_step="([0-9]+)".*$#isU','$1',$first);

		$hp="0";
		if(preg_match('#hp="([0-9]+)"#isU',$first))
			$hp=preg_replace('#^.*hp="([0-9]+)".*$#isU','$1',$first);
		$attack="0";
		if(preg_match('#attack="([0-9]+)"#isU',$first))
			$attack=preg_replace('#^.*attack="([0-9]+)".*$#isU','$1',$first);
		$defense="0";
		if(preg_match('#defense="([0-9]+)"#isU',$first))
			$defense=preg_replace('#^.*defense="([0-9]+)".*$#isU','$1',$first);
		$special_attack="0";
		if(preg_match('#special_attack="([0-9]+)"#isU',$first))
			$special_attack=preg_replace('#^.*special_attack="([0-9]+)".*$#isU','$1',$first);
		$special_defense="0";
		if(preg_match('#special_defense="([0-9]+)"#isU',$first))
			$special_defense=preg_replace('#^.*special_defense="([0-9]+)".*$#isU','$1',$first);
		$speed="0";
		if(preg_match('#speed="([0-9]+)"#isU',$first))
			$speed=preg_replace('#^.*speed="([0-9]+)".*$#isU','$1',$first);

		$catch_rate="100";
		if(preg_match('#catch_rate="([0-9]+)"#isU',$first))
			$catch_rate=preg_replace('#^.*catch_rate="([0-9]+)".*$#isU','$1',$first);
		$type=array('normal');
		if(preg_match('#type="([^"]+)"#isU',$first))
			$type=explode(';',preg_replace('#^.*type="([^"]+)".*$#isU','$1',$first));
		if(preg_match('#type2="([^"]+)"#isU',$first))
			$type=array_merge($type,explode(';',preg_replace('#^.*type2="([^"]+)".*$#isU','$1',$first)));
		if(count($type)<=0)
			$type=array('normal');
		if(isset($type_meta[$type[0]]))
		{
			if(!isset($type_to_monster[$type[0]]))
				$type_to_monster[$type[0]]=array();
			$temp_type2=$type[0];
			if(isset($type[1]))
				$temp_type2=$type[1];
			if(!isset($type_meta[$temp_type2]))
				$temp_type2=$type[0];
			if(!isset($type_to_monster[$type[0]][$temp_type2]))
				$type_to_monster[$type[0]][$temp_type2]=array();
			$type_to_monster[$type[0]][$temp_type2][]=$id;
			if($type[0]!=$temp_type2)
			{
				if(!isset($type_to_monster[$temp_type2]))
					$type_to_monster[$temp_type2]=array();
				if(!isset($type_to_monster[$temp_type2][$type[0]]))
					$type_to_monster[$temp_type2][$type[0]]=array();
				$type_to_monster[$temp_type2][$type[0]][]=$id;
			}
		}

		if(!preg_match('#<name( lang="en")?>.*</name>#isU',$entry))
			continue;
		$name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$entry);
        $name=str_replace('<![CDATA[','',str_replace(']]>','',$name));
        $name_in_other_lang=array('en'=>$name);
        foreach($lang_to_load as $lang)
        {
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
		$kind='';
		if(preg_match('#<kind( lang="en")?>(.*)</kind>#isU',$entry))
			$kind=preg_replace('#^.*<kind( lang="en")?>(.*)</kind>.*$#isU','$2',$entry);
        $kind=str_replace('<![CDATA[','',str_replace(']]>','',$kind));
        $kind_in_other_lang=array('en'=>$kind);
        foreach($lang_to_load as $lang)
        {
            if(preg_match('#<kind lang="'.$lang.'">([^<]+)</kind>#isU',$entry))
            {
                $temp_kind=preg_replace('#^.*<kind lang="'.$lang.'">([^<]+)</kind>.*$#isU','$1',$entry);
                $temp_kind=str_replace('<![CDATA[','',str_replace(']]>','',$temp_kind));
                $temp_kind=preg_replace("#[\n\r\t]+#is",'',$temp_kind);
                $kind_in_other_lang[$lang]=$temp_kind;
            }
            else
                $kind_in_other_lang[$lang]=$kind;
        }
		$habitat='';
		if(preg_match('#<habitat( lang="en")?>(.*)</habitat>#isU',$entry))
			$habitat=preg_replace('#^.*<habitat( lang="en")?>(.*)</habitat>.*$#isU','$2',$entry);
        $habitat=str_replace('<![CDATA[','',str_replace(']]>','',$habitat));
        $habitat_in_other_lang=array('en'=>$habitat);
        foreach($lang_to_load as $lang)
        {
            if(preg_match('#<habitat lang="'.$lang.'">([^<]+)</habitat>#isU',$entry))
            {
                $temp_habitat=preg_replace('#^.*<habitat lang="'.$lang.'">([^<]+)</habitat>.*$#isU','$1',$entry);
                $temp_habitat=str_replace('<![CDATA[','',str_replace(']]>','',$temp_habitat));
                $temp_habitat=preg_replace("#[\n\r\t]+#is",'',$temp_habitat);
                $habitat_in_other_lang[$lang]=$temp_habitat;
            }
            else
                $habitat_in_other_lang[$lang]=$habitat;
        }
		$attack_list=array();
		$attack_list_byitem=array();
		preg_match_all('#<attack[^>]+/>#isU',$entry,$temp_text_list);
		foreach($temp_text_list[0] as $attack_text)
		{
			if(!preg_match('#<attack[^>]*id="[0-9]+"[^>]*>#isU',$attack_text))
				continue;
			$skill_id=preg_replace('#^.*<attack[^>]*id="([0-9]+)"[^>]*>.*$#isU','$1',$attack_text);
			if(preg_match('#<attack[^>]*attack_level="[0-9]+"[^>]*>#isU',$attack_text))
				$attack_level=preg_replace('#^.*<attack[^>]*attack_level="([0-9]+)"[^>]*>.*$#isU','$1',$attack_text);
			else
				$attack_level='1';
			if(preg_match('#<attack[^>]* level="[0-9]+"[^>]*>#isU',$attack_text))
			{
				$level=preg_replace('#^.*<attack[^>]* level="([0-9]+)"[^>]*>.*$#isU','$1',$attack_text);
				if(!isset($attack_list[$level]))
					$attack_list[$level]=array();
				$attack_list[$level][]=array('id'=>$skill_id,'attack_level'=>$attack_level);
				if(!isset($skill_to_monster[$skill_id]))
					$skill_to_monster[$skill_id]=array();
				if(!isset($skill_to_monster[$skill_id][$attack_level]))
					$skill_to_monster[$skill_id][$attack_level]=array();
				$skill_to_monster[$skill_id][$attack_level][]=$id;
			}
			else if(preg_match('#<attack[^>]* byitem="[0-9]+"[^>]*>#isU',$attack_text))
			{
				$byitem=preg_replace('#^.*<attack[^>]* byitem="([0-9]+)"[^>]*>.*$#isU','$1',$attack_text);
				if(!isset($attack_list_byitem[$byitem]))
					$attack_list_byitem[$byitem]=array();
				$attack_list_byitem[$byitem][]=array('id'=>$skill_id,'attack_level'=>$attack_level);
				if(!isset($skill_to_monster[$skill_id]))
					$skill_to_monster[$skill_id]=array();
				if(!isset($skill_to_monster[$skill_id][$attack_level]))
					$skill_to_monster[$skill_id][$attack_level]=array();
				$skill_to_monster[$skill_id][$attack_level][]=$id;
				if(!isset($item_to_skill_of_monster[$byitem]))
					$item_to_skill_of_monster[$byitem]=array();
				$item_to_skill_of_monster[$byitem][]=array('id'=>$skill_id,'attack_level'=>$attack_level,'monster'=>$id);
			}
		}
		$evolution_list=array();
		preg_match_all('#<evolution [^>]+/>#isU',$entry,$temp_text_list);
		foreach($temp_text_list[0] as $attack_text)
		{
			if(!preg_match('#level="([0-9]+)"#isU',$attack_text) && !preg_match('#item="([0-9]+)"#isU',$attack_text))
				continue;
			if(!preg_match('#type="([^"]+)"#isU',$attack_text))
				continue;
			if(!preg_match('#evolveTo="([0-9]+)"#isU',$attack_text))
				continue;
            if(preg_match('#level="([0-9]+)"#isU',$attack_text))
                $level=preg_replace('#^.*level="([0-9]+)".*$#isU','$1',$attack_text);
            else
                $level=preg_replace('#^.*item="([0-9]+)".*$#isU','$1',$attack_text);
			$type_evolution=preg_replace('#^.*type="([^"]+)".*$#isU','$1',$attack_text);
			$evolveTo=preg_replace('#^.*evolveTo="([0-9]+)".*$#isU','$1',$attack_text);
			if(!isset($reverse_evolution[$evolveTo]))
				$reverse_evolution[$evolveTo]=array();
			if($type_evolution=='item')
			{
				if(!isset($item_to_evolution[$level]))
					$item_to_evolution[$level]=array();
				$item_to_evolution[$level][]=array('from'=>$id,'to'=>$evolveTo);
			}
			$reverse_evolution[$evolveTo][]=array('level'=>$level,'type'=>$type_evolution,'evolveFrom'=>$id);
			$evolution_list[]=array('level'=>$level,'type'=>$type_evolution,'evolveTo'=>$evolveTo);
		}
		$drops_list=array();
		preg_match_all('#<drop[^>]+/>#isU',$entry,$temp_text_list);
		foreach($temp_text_list[0] as $attack_text)
		{
			$quantity_min=1;
			$quantity_max=1;
			$luck=1;
			if(!preg_match('#<drop[^>]*item="[0-9]+"[^>]*>#isU',$attack_text))
				continue;
			$item=preg_replace('#^.*<drop[^>]*item="([0-9]+)"[^>]*>.*$#isU','$1',$attack_text);
			if(preg_match('#<drop[^>]*quantity="[0-9]+"[^>]*>#isU',$attack_text))
			{
				$quantity_min=preg_replace('#^.*<drop[^>]*quantity="([0-9]+)"[^>]*>.*$#isU','$1',$attack_text);
				$quantity_max=$quantity_min;
			}
			if(preg_match('#<drop[^>]*quantity_min="[0-9]+"[^>]*>#isU',$attack_text))
				$quantity_min=preg_replace('#^.*<drop[^>]*quantity_min="([0-9]+)"[^>]*>.*$#isU','$1',$attack_text);
			if(preg_match('#<drop[^>]*quantity_max="[0-9]+"[^>]*>#isU',$attack_text))
				$quantity_max=preg_replace('#^.*<drop[^>]*quantity_max="([0-9]+)"[^>]*>.*$#isU','$1',$attack_text);
			if(preg_match('#<drop[^>]*luck="[0-9]+%?"[^>]*>#isU',$attack_text))
				$luck=preg_replace('#^.*<drop[^>]*luck="([0-9]+)%?"[^>]*>.*$#isU','$1',$attack_text);
			$drops_list[]=array('item'=>$item,'quantity_min'=>$quantity_min,'quantity_max'=>$quantity_max,'luck'=>$luck);
			if(!isset($item_to_monster[$item]))
				$item_to_monster[$item]=array();
			$item_to_monster[$item][]=array('monster'=>$id,'quantity_min'=>$quantity_min,'quantity_max'=>$quantity_max,'luck'=>$luck);
		}
		ksort($attack_list);
		ksort($attack_list_byitem);
		$monster_meta[$id]=array('type'=>$type,'kind'=>$kind_in_other_lang,'habitat'=>$habitat_in_other_lang,'attack_list'=>$attack_list,'attack_list_byitem'=>$attack_list_byitem,'drops'=>$drops_list,'evolution_list'=>$evolution_list,'ratio_gender'=>$ratio_gender,'catch_rate'=>$catch_rate,
		'height'=>$height,'weight'=>$weight,'egg_step'=>$egg_step,'hp'=>$hp,'attack'=>$attack,'defense'=>$defense,'special_attack'=>$special_attack,'special_defense'=>$special_defense,'speed'=>$speed,'name'=>$name_in_other_lang,'description'=>$description_in_other_lang
		);
	}
}
ksort($monster_meta);