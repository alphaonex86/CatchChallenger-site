<?php
$is_up=true;
require '../config.php';
$mysql_link=@mysql_connect($mysql_host,$mysql_login,$mysql_pass,true);
if($mysql_link===NULL)
	$is_up=false;
else if(!@mysql_select_db($mysql_db))
	$is_up=false;
if(!$is_up)
	exit;

if(!isset($datapack_explorer_local_path))
	die('$datapack_explorer_local_path not set');

if(!is_dir($datapack_explorer_local_path))
	if(!mkdir($datapack_explorer_local_path))
		exit;

$automaticallygen='<div id="automaticallygen">Automatically generated from ';
if(isset($datapack_source_url) && $datapack_source_url!='')
	$automaticallygen.='<a href="'.$datapack_source_url.'">';
$automaticallygen.='the datapack';
if(isset($datapack_source_url) && $datapack_source_url!='')
	$automaticallygen.='</a>';
$automaticallygen.='</div>';

function filewrite($file,$content)
{
	if($filecurs=fopen($file, 'w'))
	{
		if(fwrite($filecurs,$content) === false)
			die('Unable to write the file: '.$file);
		fclose($filecurs);
	}
	else
		die('Unable to write or create the file: '.$file);
}

function text_operation_lower_case($text)
{
	$text=strtolower($text);
	$text=str_replace('Â','â',$text);
	$text=str_replace('À','à',$text);
	$text=str_replace('Ä','ä',$text);
	$text=str_replace('Ç','ç',$text);
	$text=str_replace('Ê','ê',$text);
	$text=str_replace('È','è',$text);
	$text=str_replace('Ë','ë',$text);
	$text=str_replace('É','é',$text);
	$text=str_replace('Ï','ï',$text);
	$text=str_replace('Ö','ö',$text);
	$text=str_replace('Ô','ô',$text);
	$text=str_replace('Û','û',$text);
	$text=str_replace('Ù','ù',$text);
	$text=str_replace('Ü','ü',$text);
	return $text;
}

function text_operation_clean_text($text,$minimum_word_length=4,$minimum_string_length=15,$maximum_string_length=64)
{
	$text=text_operation_lower_case($text);

	$text=str_replace('â','a',str_replace('à','a',$text));
	$text=str_replace('ã','a',str_replace('á','a',str_replace('ã','a',str_replace('ä','a',$text))));

	$text=str_replace('ç','c',$text);

	$text=str_replace('é','e',str_replace('è','e',str_replace('ê','e',str_replace('ë','e',$text))));

	$text=str_replace('ì','i',str_replace('í','i',str_replace('î','i',str_replace('ï','i',$text))));

	$text=str_replace('ñ','n',$text);
	
	$text=str_replace('õ','o',str_replace('ö','o',str_replace('ó','o',str_replace('ô','o',$text))));
	$text=str_replace('ô','o',str_replace('ò','o',$text));
	
	$text=str_replace('û','u',str_replace('ü','u',str_replace('ú','u',str_replace('ù','u',$text))));
	
	$text=str_replace('ý','y',str_replace('ÿ','y',$text));
	
	if(strlen($text)>$maximum_string_length)
		$text=substr($text,0,$maximum_string_length);
	$text=preg_replace('#([0-9]+)(\.|-| )+#','$1 ',$text);
	$text=preg_replace('#[^a-zA-Z0-9_-]+#',' ',$text);
	$text=preg_replace('# +#',' ',$text);
	if($minimum_word_length>2)
	{
		$a=$minimum_word_length-1;
		do
		{
			$text_temp=preg_replace('#\b[a-zA-Z_-]{1,'.$a.'}\b#',' ',$text);
			$text_temp=preg_replace('# +#',' ',$text_temp);
			$text_temp=preg_replace('# +$#','',$text_temp);
			$text_temp=preg_replace('#^ +#','',$text_temp);
			$a--;
		}
		while(strlen($text_temp)<=$minimum_string_length && $a>1);
		if(strlen($text_temp)>$minimum_string_length && $a>1)
			$text=$text_temp;
	}
	$text=preg_replace('# +#',' ',$text);
	$text=preg_replace('# +$#','',$text);
	$text=preg_replace('#^ +#','',$text);
	return $text;
}

function text_operation_do_for_url($text,$minimum_word_length=4,$minimum_string_length=15,$maximum_string_length=64)
{
	$text=text_operation_clean_text($text,$minimum_word_length,$minimum_string_length,$maximum_string_length);
	$text=str_replace(' ','-',$text);
	$text=preg_replace('#-+#','-',$text);
	$text=preg_replace('#^-+#','',$text);
	$text=preg_replace('#-+$#','',$text);
	return $text;
}

function text_operation_lower_case_first_letter_upper($text)
{
	if(strlen($text)<=0)
		return $text;
	else if(strlen($text)==1)
		return strtoupper($text);
	else
		return strtoupper(substr($text,0,1)).text_operation_lower_case(substr($text,1,strlen($text)-1));
}

function text_operation_first_letter_upper($text)
{
	if(strlen($text)<=0)
		return $text;
	else if(strlen($text)==1)
		return strtoupper($text);
	else
		return strtoupper(substr($text,0,1)).substr($text,1,strlen($text)-1);
}

$template=file_get_contents('template.html');

$item_meta=array();
if(file_exists($datapack_path.'items/items.xml'))
{
	$content=file_get_contents($datapack_path.'items/items.xml');
	preg_match_all('#<item[^>]*>.*</item>#isU',$content,$temp_text_list);
	foreach($temp_text_list[0] as $entry)
	{
		if(!preg_match('#<item[^>]*id="[0-9]+".*</item>#isU',$entry))
			continue;
		$id=preg_replace('#^.*<item[^>]*id="([0-9]+)".*</item>.*$#isU','$1',$entry);
		$price=0;
		if(preg_match('#<item[^>]*price="[0-9]+".*</item>#isU',$entry))
			$price=preg_replace('#^.*<item[^>]*price="([0-9]+)".*</item>.*$#isU','$1',$entry);
		if(preg_match('#<item[^>]*image="[^"]+".*</item>#isU',$entry))
			$image=preg_replace('#^.*<item[^>]*image="([^"]+)".*</item>.*$#isU','$1',$entry);
		else
			$image='';
		if(!preg_match('#<name( lang="en")?>.*</name>#isU',$entry))
			continue;
		$name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$entry);
		if(!preg_match('#<description( lang="en")?>.*</description>#isU',$entry))
			continue;
		$description=text_operation_first_letter_upper(preg_replace('#^.*<description( lang="en")?>(.*)</description>.*$#isU','$2',$entry));
		if(preg_match('#<trap[^>]+/>#isU',$entry))
		{
			$temp_text=preg_replace('#^.*(<trap[^>]+/>).*$#isU','$1',$entry);
			if(preg_match('#bonus_rate="([0-9]+(\.[0-9]+)?)"#isU',$temp_text))
			{
				$bonus_rate=preg_replace('#^.*bonus_rate="([0-9]+(\.[0-9]+)?)".*$#isU','$1',$temp_text);
				$item_meta[$id]=array('price'=>$price,'image'=>$image,'name'=>$name,'description'=>$description,'trap'=>$bonus_rate);
			}
			else
				$item_meta[$id]=array('price'=>$price,'image'=>$image,'name'=>$name,'description'=>$description);
		}
		else if(preg_match('#<repel[^>]+/>#isU',$entry))
		{
			$temp_text=preg_replace('#^.*(<repel[^>]+/>).*$#isU','$1',$entry);
			if(preg_match('#step="([0-9]+(\.[0-9]+)?)"#isU',$temp_text))
			{
				$step=preg_replace('#^.*step="([0-9]+(\.[0-9]+)?)".*$#isU','$1',$temp_text);
				$item_meta[$id]=array('price'=>$price,'image'=>$image,'name'=>$name,'description'=>$description,'repel'=>$step);
			}
			else
				$item_meta[$id]=array('price'=>$price,'image'=>$image,'name'=>$name,'description'=>$description);
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
			if(count($effect)>0)
				$item_meta[$id]=array('price'=>$price,'image'=>$image,'name'=>$name,'description'=>$description,'effect'=>$effect);
			else
				$item_meta[$id]=array('price'=>$price,'image'=>$image,'name'=>$name,'description'=>$description);
		}
	}
}

$type_meta=array();
if(file_exists($datapack_path.'monsters/type.xml'))
{
	$content=file_get_contents($datapack_path.'monsters/type.xml');
	preg_match_all('#<type[^>]+>.*</type>#isU',$content,$entry_list);
	foreach($entry_list[0] as $entry)
	{
		$multiplicator=array();
		if(!preg_match('#name="([^"]+)"#isU',$entry))
			continue;
		$name=preg_replace('#^.*name="([^"]+)".*$#isU','$1',$entry);
		$english_name='Unknown name';
		if(preg_match('#<name lang="en">([^<]+)</name>#isU',$entry))
			$english_name=preg_replace('#^.*<name lang="en">([^<]+)</name>.*$#isU','$1',$entry);
		elseif(preg_match('#<name>([^<]+)</name>#isU',$entry))
			$english_name=preg_replace('#^.*<name>([^<]+)</name>.*$#isU','$1',$entry);
		preg_match_all('#<multiplicator number="([^"]+)" to="([^"]+)" />#isU',$entry,$multiplicator_list);
		foreach($multiplicator_list[0] as $tempmultiplicator)
		{
			$number=(float)preg_replace('#^.*<multiplicator number="([^"]+)" to="([^"]+)" />.*$#isU','$1',$tempmultiplicator);
			$to=preg_replace('#^.*<multiplicator number="([^"]+)" to="([^"]+)" />.*$#isU','$2',$tempmultiplicator);
			$to_list=explode(';',$to);;
			foreach($to_list as $to)
				$multiplicator[$to]=$number;
		}
		$type_meta[$name]=array('english_name'=>$english_name,'multiplicator'=>$multiplicator);
	}
}

$reputation_meta=array();
if(file_exists($datapack_path.'player/reputation.xml'))
{
	$content=file_get_contents($datapack_path.'player/reputation.xml');
	preg_match_all('#<reputation type="[a-z]+".*</reputation>#isU',$content,$entry_list);
	foreach($entry_list[0] as $entry)
	{
		if(!preg_match('#<reputation type="[a-z]+".*</reputation>#isU',$entry))
			continue;
		$type=preg_replace('#^.*<reputation type="([a-z]+)".*</reputation>.*$#isU','$1',$entry);
		preg_match_all('#<level point="-?[0-9]+".*</level>#isU',$entry,$level_list);
		$reputation_meta_list=array();
		foreach($level_list[0] as $level)
		{
			if(!preg_match('#<level point="-?[0-9]+".*</level>#isU',$level))
				continue;
			$point=preg_replace('#^.*<level point="(-?[0-9]+)".*</level>.*$#isU','$1',$level);
			if(!preg_match('#<text( lang="en")?>.*</text>#isU',$level))
				continue;
			$text=preg_replace('#^.*<text( lang="en")?>(.*)</text>.*$#isU','$2',$level);
			$reputation_meta_list[(int)$point]=$text;
		}
		if(count($reputation_meta_list)>0)
		{
			ksort($reputation_meta_list);
			$level_offset=0;
			foreach($reputation_meta_list as $point=>$text)
			{
				if($point>=0)
					break;
				$level_offset++;
			}
			$reputation_meta_list_by_level=array();
			foreach($reputation_meta_list as $point=>$text)
			{
				$reputation_meta_list_by_level[-$level_offset]=$text;
				$level_offset--;
			}
			unset($reputation_meta_list);
			$reputation_meta[$type]=$reputation_meta_list_by_level;
			unset($reputation_meta_list_by_level);
		}
	}
}

$crafting_meta=array();
$item_to_crafting=array();
if(file_exists($datapack_path.'crafting/recipes.xml'))
{
	$content=file_get_contents($datapack_path.'crafting/recipes.xml');
	preg_match_all('#<recipe[^>]*>.*</recipe>#isU',$content,$temp_text_list);
	foreach($temp_text_list[0] as $entry)
	{
		if(!preg_match('#<recipe[^>]*id="[0-9]+".*</recipe>#isU',$entry))
			continue;
		$id=preg_replace('#^.*<recipe[^>]*id="([0-9]+)".*</recipe>.*$#isU','$1',$entry);
		if(!preg_match('#<recipe[^>]*itemToLearn="[0-9]+".*</recipe>#isU',$entry))
			continue;
		$itemToLearn=preg_replace('#^.*<recipe[^>]*itemToLearn="([0-9]+)".*</recipe>.*$#isU','$1',$entry);
		if(!preg_match('#<recipe[^>]*doItemId="[0-9]+".*</recipe>#isU',$entry))
			continue;
		$doItemId=preg_replace('#^.*<recipe[^>]*doItemId="([0-9]+)".*</recipe>.*$#isU','$1',$entry);
		$material=array();
		preg_match_all('#<material itemId="([0-9]+)" quantity="([0-9]+)" />#isU',$entry,$temp_material_list);
		foreach($temp_material_list[0] as $material_text)
		{
			$itemId=preg_replace('#^.*<material itemId="([0-9]+)".*$#isU','$1',$material_text);
			$quantity=1;
			if(preg_match('#<material[^>]+quantity="([0-9]+)"#isU',$material_text))
				$quantity=preg_replace('#^.*<material[^>]+quantity="([0-9]+)".*$#isU','$1',$material_text);
			$material[$itemId]=$quantity;
		}
		$crafting_meta[$id]=array('itemToLearn'=>$itemToLearn,'doItemId'=>$doItemId,'material'=>$material);
		$item_to_crafting[$itemToLearn]=array('doItemId'=>$doItemId,'material'=>$material);
	}
}

$skill_meta=array();
if(file_exists($datapack_path.'monsters/skill.xml'))
{
	$content=file_get_contents($datapack_path.'monsters/skill.xml');
	preg_match_all('#<skill.*</skill>#isU',$content,$temp_text_list);
	foreach($temp_text_list[0] as $entry)
	{
		if(!preg_match('#id="[0-9]+"#isU',$entry))
			continue;
		$id=preg_replace('#^.*id="([0-9]+)".*$#isU','$1',$entry);
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
		$level_list=array();
		preg_match_all('#<level.*</level>#isU',$entry,$temp_level_list);
		foreach($temp_level_list[0] as $level_text)
		{
			if(!preg_match('#number="[0-9]+"#isU',$level_text))
				continue;
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
			$level_list[$number]=array('endurance'=>$endurance,'sp'=>$sp,'life_quantity'=>$life_quantity,'base_level_luck'=>$base_level_luck);
		}
		$skill_meta[$id]=array('type'=>$type,'name'=>$name,'level_list'=>$level_list);
	}
}

$industrie_meta=array();
$industrie_link_meta=array();
$item_produced_by=array();
$item_consumed_by=array();
if(is_dir($datapack_path.'industries/'))
{
	if($handle = opendir($datapack_path.'industries/')) {
		while(false !== ($entry = readdir($handle))) {
		if($entry != '.' && $entry != '..') {
				$content=file_get_contents($datapack_path.'industries/'.$entry);
				preg_match_all('#<link[^>]+/>#isU',$content,$entry_list);
				foreach($entry_list[0] as $entry)
				{
					if(!preg_match('#<link[^>]*industrialrecipe="([0-9]+)"[^>]*/>#isU',$entry))
						continue;
					if(!preg_match('#<link[^>]*industry="([0-9]+)"[^>]*/>#isU',$entry))
						continue;
					$industry_id=preg_replace('#^.*<link[^>]*industrialrecipe="([0-9]+)"[^>]*/>.*$#isU','$1',$entry);
					$factory_id=preg_replace('#^.*<link[^>]*industry="([0-9]+)"[^>]*/>.*$#isU','$1',$entry);
					$industrie_link_meta[$factory_id]=$industry_id;
				}
				preg_match_all('#<industrialrecipe[^>]+>.*</industrialrecipe>#isU',$content,$entry_list);
				foreach($entry_list[0] as $entry)
				{
					if(!preg_match('#<industrialrecipe[^>]*id="([0-9]+)"#isU',$entry))
						continue;
					if(!preg_match('#<industrialrecipe[^>]*time="([0-9]+)"#isU',$entry))
						continue;
					if(!preg_match('#<industrialrecipe[^>]*cycletobefull="([0-9]+)"#isU',$entry))
						continue;
					$id=preg_replace('#^.*<industrialrecipe[^>]*id="([0-9]+)".*$#isU','$1',$entry);
					$time=preg_replace('#^.*<industrialrecipe[^>]*time="([0-9]+)".*$#isU','$1',$entry);
					$cycletobefull=preg_replace('#^.*<industrialrecipe[^>]*cycletobefull="([0-9]+)".*$#isU','$1',$entry);
					//resource
					$resources=array();
					preg_match_all('#<resource[^>]+/>#isU',$entry,$temp_text_list);
					foreach($temp_text_list[0] as $resource)
					{
						if(!preg_match('#<resource[^>]*id="([0-9]+)"#isU',$resource))
							continue;
						$quantity=1;
						$item=preg_replace('#^.*<resource[^>]*id="([0-9]+)".*$#isU','$1',$resource);
						if(!preg_match('#<resource[^>]*quantity="([0-9]+)"#isU',$resource))
							$quantity=preg_replace('#^.*<resource[^>]*quantity="([0-9]+)".*$#isU','$1',$resource);
						$item_consumed_by[$item][$id]=$quantity;
						$resources[]=array('item'=>$item,'quantity'=>$quantity);
					}
					//product
					$products=array();
					preg_match_all('#<product[^>]+/>#isU',$entry,$temp_text_list);
					foreach($temp_text_list[0] as $product)
					{
						if(!preg_match('#<product[^>]*id="([0-9]+)"#isU',$product))
							continue;
						$quantity=1;
						$item=preg_replace('#^.*<product[^>]*id="([0-9]+)".*$#isU','$1',$product);
						if(!preg_match('#<product[^>]*quantity="([0-9]+)"#isU',$product))
							$quantity=preg_replace('#^.*<product[^>]*quantity="([0-9]+)".*$#isU','$1',$product);
						$item_produced_by[$item][$id]=$quantity;
						$products[]=array('item'=>$item,'quantity'=>$quantity);
					}
					$industrie_meta[$id]=array('time'=>$time,'cycletobefull'=>$cycletobefull,'resources'=>$resources,'products'=>$products);
				}
			}
		}
		closedir($handle);
	}
}

$plant_meta=array();
$item_to_plant=array();
if(file_exists($datapack_path.'plants/plants.xml'))
{
	$content=file_get_contents($datapack_path.'plants/plants.xml');
	preg_match_all('#<plant[^>]+>.*</plant>#isU',$content,$entry_list);
	foreach($entry_list[0] as $entry)
	{
		if(!preg_match('#<plant[^>]+id="[0-9]+"#isU',$entry))
			continue;
		if(!preg_match('#<plant[^>]+itemUsed="[0-9]+"#isU',$entry))
			continue;
		if(!preg_match('#<fruits>([0-9]+)</fruits>#isU',$entry))
			continue;
		if(!preg_match('#<quantity>([0-9]+(\\.[0-9]+)?)</quantity>#isU',$entry))
			continue;
		$id=preg_replace('#^.*<plant[^>]+id="([0-9]+)".*</plant>.*$#isU','$1',$entry);
		$itemUsed=preg_replace('#^.*<plant[^>]+itemUsed="([0-9]+)".*</plant>.*$#isU','$1',$entry);
		$fruits=preg_replace('#^.*<fruits>([0-9]+)</fruits>.*$#isU','$1',$entry)*60;
		$quantity=preg_replace('#^.*<quantity>([0-9]+(\\.[0-9]+)?)</quantity>.*$#isU','$1',$entry);
		$item_to_plant[$itemUsed]=$id;
		$plant_meta[$id]=array('itemUsed'=>$itemUsed,'fruits'=>$fruits,'quantity'=>$quantity);
	}
}

$monster_meta=array();
$item_to_monster=array();
$item_to_evolution=array();
$reverse_evolution=array();
$type_to_monster=array();
$skill_to_monster=array();
if(file_exists($datapack_path.'monsters/monster.xml'))
{
	$content=file_get_contents($datapack_path.'monsters/monster.xml');
	preg_match_all('#<monster.*</monster>#isU',$content,$entry_list);
	foreach($entry_list[0] as $entry)
	{
		$first=preg_replace('#attack_list.*$#isU','',$entry);
		if(!preg_match('#.*id="[0-9]+".*#isU',$first))
			continue;
		$id=preg_replace('#^.*id="([0-9]+)".*$#isU','$1',$first);
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
		if(!preg_match('#<description( lang="en")?>.*</description>#isU',$entry))
			continue;
		$description=text_operation_first_letter_upper(preg_replace('#^.*<description( lang="en")?>(.*)</description>.*$#isU','$2',$entry));
		$kind='';
		if(preg_match('#<kind( lang="en")?>(.*)</kind>#isU',$entry))
			$kind=preg_replace('#^.*<kind( lang="en")?>(.*)</kind>.*$#isU','$2',$entry);
		$habitat='';
		if(preg_match('#<habitat( lang="en")?>(.*)</habitat>#isU',$entry))
			$habitat=preg_replace('#^.*<habitat( lang="en")?>(.*)</habitat>.*$#isU','$2',$entry);
		$attack_list=array();
		preg_match_all('#<attack[^>]+/>#isU',$entry,$temp_text_list);
		foreach($temp_text_list[0] as $attack_text)
		{
			if(!preg_match('#<attack[^>]*id="[0-9]+"[^>]*>#isU',$attack_text))
				continue;
			$skill_id=preg_replace('#^.*<attack[^>]*id="([0-9]+)"[^>]*>.*$#isU','$1',$attack_text);
			if(!preg_match('#<attack[^>]*level="[0-9]+"[^>]*>#isU',$attack_text))
				continue;
			$level=preg_replace('#^.*<attack[^>]*level="([0-9]+)"[^>]*>.*$#isU','$1',$attack_text);
			if(preg_match('#<attack[^>]*attack_level="[0-9]+"[^>]*>#isU',$attack_text))
				$attack_level=preg_replace('#^.*<attack[^>]*attack_level="([0-9]+)"[^>]*>.*$#isU','$1',$attack_text);
			else
				$attack_level='1';
			if(!isset($attack_list[$level]))
				$attack_list[$level]=array();
			$attack_list[$level][]=array('id'=>$skill_id,'attack_level'=>$attack_level);
			if(!isset($skill_to_monster[$skill_id]))
				$skill_to_monster[$skill_id]=array();
			if(!isset($skill_to_monster[$skill_id][$attack_level]))
				$skill_to_monster[$skill_id][$attack_level]=array();
			$skill_to_monster[$skill_id][$attack_level][]=$id;
		}
		$evolution_list=array();
		preg_match_all('#<evolution [^>]+/>#isU',$entry,$temp_text_list);
		foreach($temp_text_list[0] as $attack_text)
		{
			if(!preg_match('#level="([0-9]+)" type="([^"]+)" evolveTo="([0-9]+)"#isU',$attack_text))
				continue;
			$level=preg_replace('#^.*level="([0-9]+)" type="([^"]+)" evolveTo="([0-9]+)".*$#isU','$1',$attack_text);
			$type_evolution=preg_replace('#^.*level="([0-9]+)" type="([^"]+)" evolveTo="([0-9]+)".*$#isU','$2',$attack_text);
			$evolveTo=preg_replace('#^.*level="([0-9]+)" type="([^"]+)" evolveTo="([0-9]+)".*$#isU','$3',$attack_text);
			if(isset($reverse_evolution[$evolveTo]))
				$reverse_evolution[$evolveTo]=array();
			if($type_evolution=='item')
			{
				if(isset($item_to_evolution[$level]))
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
		$monster_meta[$id]=array('name'=>$name,'type'=>$type,'description'=>$description,'kind'=>$kind,'habitat'=>$habitat,'attack_list'=>$attack_list,'drops'=>$drops_list,'evolution_list'=>$evolution_list,'ratio_gender'=>$ratio_gender,'catch_rate'=>$catch_rate,
		'height'=>$height,'weight'=>$weight,'egg_step'=>$egg_step,'hp'=>$hp,'attack'=>$attack,'defense'=>$defense,'special_attack'=>$special_attack,'special_defense'=>$special_defense,'speed'=>$speed,
		);
	}
}

function getTmxList($dir,$sub_dir='')
{
	$files_list=array();
	if(is_dir($dir.$sub_dir))
		if($handle = opendir($dir.$sub_dir)) {
			while(false !== ($entry = readdir($handle))) {
			if($entry != '.' && $entry != '..') {
					if(is_dir($dir.$sub_dir.$entry))
						$files_list=array_merge($files_list,getTmxList($dir,$sub_dir.$entry.'/'));
					else if(preg_match('#\\.tmx$#',$entry))
						$files_list[]=$sub_dir.$entry;
				}
			}
			closedir($handle);
		}
	return $files_list;
}

function getXmlList($dir,$sub_dir='')
{
	$files_list=array();
	if(is_dir($dir.$sub_dir))
		if($handle = opendir($dir.$sub_dir)) {
			while(false !== ($entry = readdir($handle))) {
			if($entry != '.' && $entry != '..') {
					if(is_dir($dir.$sub_dir.$entry))
						$files_list=array_merge($files_list,getXmlList($dir,$sub_dir.$entry.'/'));
					else if(preg_match('#\\.xml$#',$entry))
						$files_list[]=$sub_dir.$entry;
				}
			}
			closedir($handle);
		}
	return $files_list;
}

function getDefinitionXmlList($dir,$sub_dir='')
{
	$files_list=array();
	if(is_dir($dir.$sub_dir))
		if($handle = opendir($dir.$sub_dir)) {
			while(false !== ($entry = readdir($handle))) {
			if($entry != '.' && $entry != '..') {
					if(is_dir($dir.$sub_dir.$entry))
						$files_list=array_merge($files_list,getDefinitionXmlList($dir,$sub_dir.$entry.'/'));
					else if(preg_match('#definition\\.xml$#',$entry))
						$files_list[]=$sub_dir.$entry;
				}
			}
			closedir($handle);
		}
	return $files_list;
}

$xmlZoneList=getXmlList($datapack_path.'map/zone/');
$zone_meta=array();
foreach($xmlZoneList as $file)
{
	$content=file_get_contents($datapack_path.'map/zone/'.$file);
	if(!preg_match('#^([^"\\.]+).xml$#isU',$file))
		continue;
	$code=preg_replace('#^([^"\\.]+).xml$#isU','$1',$file);
	if(!preg_match('#<name( lang="en")?>([^<]+)</name>#isU',$content))
		continue;
	$name=preg_replace('#^.*<name( lang="en")?>([^<]+)</name>.*$#isU','$2',$content);
	$name=preg_replace("#[\n\t\r]+#is",'',$name);
	$zone_meta[$code]=array('name'=>$name);
}

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
		if(preg_match('#<gain cash="([0-9]+)"#isU',$entry))
			$cash=preg_replace('#^.*<gain cash="([0-9]+)".*$#isU','$1',$entry);
		if(preg_match('#<start( lang="en")?>(<!\\[CDATA\\[)?(.*)(]]>)?</start>#isU',$entry))
			$start=preg_replace('#^.*<start( lang="en")?>(<!\\[CDATA\\[)?(.*)(]]>)?</start>.*$#isU','$3',$entry);
		if(preg_match('#<win( lang="en")?>(<!\\[CDATA\\[)?(.*)(]]>)?</win>#isU',$entry))
			$win=preg_replace('#^.*<win( lang="en")?>(<!\\[CDATA\\[)?(.*)(]]>)?</win>.*$#isU','$3',$entry);
		$start=str_replace('<![CDATA[','',$start);
		$win=str_replace('<![CDATA[','',$win);
		$monsters=array();
		preg_match_all('#<monster id="([0-9]+)" level="([0-9]+)" />#isU',$entry,$monster_text_list);
		foreach($monster_text_list[0] as $monster_text)
		{
			$monster=preg_replace('#^.*<monster id="([0-9]+)" level="([0-9]+)" />.*$#isU','$1',$monster_text);
			$level=preg_replace('#^.*<monster id="([0-9]+)" level="([0-9]+)" />.*$#isU','$2',$monster_text);
			$monsters[]=array('monster'=>$monster,'level'=>$level);
		}
		$fight_meta[$id]=array('start'=>$start,'win'=>$win,'cash'=>$cash,'monsters'=>$monsters);
	}
}

$industries_meta=array();
$xmlFightList=getXmlList($datapack_path.'industries/');
foreach($xmlFightList as $file)
{
	$content=file_get_contents($datapack_path.'industries/'.$file);
	preg_match_all('#<industrialrecipe id="[0-9]+".*</industrialrecipe>#isU',$content,$entry_list);
	foreach($entry_list[0] as $entry)
	{
		if(!preg_match('#<industrialrecipe id="([0-9]+)".*</industrialrecipe>#isU',$entry))
			continue;
		$id=preg_replace('#^.*<industrialrecipe id="([0-9]+)".*</industrialrecipe>.*$#isU','$1',$entry);
		if(!preg_match('#time="([0-9]+)"#isU',$entry))
			continue;
		$time=preg_replace('#^.*time="([0-9]+)".*$#isU','$1',$entry);
		if(!preg_match('#cycletobefull="([0-9]+)"#isU',$entry))
			continue;
		$cycletobefull=preg_replace('#^.*cycletobefull="([0-9]+)".*$#isU','$1',$entry);
		$resources=array();
		preg_match_all('#<resource id="([0-9]+)" quantity="([0-9]+)" />#isU',$entry,$monster_text_list);
		foreach($monster_text_list[0] as $monster_text)
		{
			$item=preg_replace('#^.*<resource id="([0-9]+)" quantity="([0-9]+)" />.*$#isU','$1',$monster_text);
			$quantity=preg_replace('#^.*<resource id="([0-9]+)" quantity="([0-9]+)" />.*$#isU','$2',$monster_text);
			$resources[$item]=$quantity;
		}
		$products=array();
		preg_match_all('#<product id="([0-9]+)" quantity="([0-9]+)" />#isU',$entry,$monster_text_list);
		foreach($monster_text_list[0] as $monster_text)
		{
			$item=preg_replace('#^.*<product id="([0-9]+)" quantity="([0-9]+)" />.*$#isU','$1',$monster_text);
			$quantity=preg_replace('#^.*<product id="([0-9]+)" quantity="([0-9]+)" />.*$#isU','$2',$monster_text);
			$products[$item]=$quantity;
		}
		$industries_meta[$id]=array('time'=>$time,'cycletobefull'=>$cycletobefull,'resources'=>$resources,'products'=>$products);
	}
}

$shop_meta=array();
$xmlFightList=getXmlList($datapack_path.'shop/');
foreach($xmlFightList as $file)
{
	$content=file_get_contents($datapack_path.'shop/'.$file);
	preg_match_all('#<shop id="[0-9]+".*</shop>#isU',$content,$entry_list);
	foreach($entry_list[0] as $entry)
	{
		if(!preg_match('#<shop id="([0-9]+)".*</shop>#isU',$entry))
			continue;
		$id=preg_replace('#^.*<shop id="([0-9]+)".*</shop>.*$#isU','$1',$entry);
		$products=array();
		preg_match_all('#<product itemId="([0-9]+)" />#isU',$entry,$monster_text_list);
		foreach($monster_text_list[0] as $monster_text)
		{
			$item=preg_replace('#^.*<product itemId="([0-9]+)" />.*$#isU','$1',$monster_text);
			$products[$item]=true;
		}
		$shop_meta[$id]=array('products'=>$products);
	}
}

$start=array();
if(file_exists($datapack_path.'player/start.xml'))
{
	$content=file_get_contents($datapack_path.'player/start.xml');
	preg_match_all('#<start>.*</start>#isU',$content,$entry_list);
	foreach($entry_list[0] as $entry)
	{
		if(!preg_match('#<name( lang="en")?>.*</name>#isU',$entry))
			continue;
		$name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$entry);
		if(!preg_match('#<description( lang="en")?>.*</description>#isU',$entry))
			continue;
		$description=text_operation_first_letter_upper(preg_replace('#^.*<description( lang="en")?>(.*)</description>.*$#isU','$2',$entry));
		if(!preg_match('#<map.*file="([^"]+)".*/>#isU',$entry))
			continue;
		if(!preg_match('#<map.*x="([0-9]+)".*/>#isU',$entry))
			continue;
		if(!preg_match('#<map.*y="([0-9]+)".*/>#isU',$entry))
			continue;
		$map=preg_replace('#^.*<map.*file="([^"]+)".*/>.*$#isU','$1',$entry);
		$x=preg_replace('#^.*<map.*x="([0-9]+)".*/>.*$#isU','$1',$entry);
		$y=preg_replace('#^.*<map.*y="([0-9]+)".*/>.*$#isU','$1',$entry);
		$forcedskin=array();
		if(preg_match('#<forcedskin.*value="([^"]+)".*/>#isU',$entry))
			$forcedskin=explode(';',preg_replace('#^.*<forcedskin.*value="([^"]+)".*/>.*$#isU','$1',$entry));
		$cash=0;
		if(preg_match('#<cash.*value="([^"]+)".*/>#isU',$entry))
			$cash=preg_replace('#^.*<cash.*value="([^"]+)".*/>.*$#isU','$1',$entry);
		
		preg_match_all('#<monster id="[0-9]+" level="[0-9]+" captured_with="[0-9]+" />#isU',$entry,$monster_list);
		$monsters=array();
		foreach($monster_list as $monster)
		{
			if(!preg_match('#<monster.*id="([0-9]+)".*/>#isU',$entry))
				continue;
			if(!preg_match('#<monster.*level="([0-9]+)".*/>#isU',$entry))
				continue;
			if(!preg_match('#<monster.*captured_with="([0-9]+)".*/>#isU',$entry))
				continue;
			$id=preg_replace('#^.*<monster.*id="([0-9]+)".*/>.*$#isU','$1',$entry);
			$level=preg_replace('#^.*<monster.*level="([0-9]+)".*/>.*$#isU','$1',$entry);
			$captured_with=preg_replace('#^.*<monster.*captured_with="([0-9]+)".*/>.*$#isU','$1',$entry);
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
		
		if(!preg_match('#\.tmx$#',$map))
			$map=$map.'.tmx';
		$start[]=array('name'=>$name,'description'=>$description,'map'=>$map,'x'=>$x,'y'=>$y,'forcedskin'=>$forcedskin,'cash'=>$cash,'monsters'=>$monsters,'reputations'=>$reputations,'items'=>$items);
	}
}

$quests_meta=array();
$monster_to_quests=array();
$items_to_quests=array();
$items_to_quests_for_step=array();
$xmlFightList=getDefinitionXmlList($datapack_path.'quests/');
foreach($xmlFightList as $file)
{
	if(!preg_match('#([0-9]+)#is',$file))
		continue;
	$id=preg_replace('#^.*([0-9]+).*$#is','$1',$file);
	$content=file_get_contents($datapack_path.'quests/'.$file);
	$repeatable=false;
	if(preg_match('#repeatable="(yes|true)"#isU',$content))
		$repeatable=true;
	if(!preg_match('#bot="([0-9]+)"#isU',$content))
		continue;
	$bot=preg_replace('#^.*bot="([0-9]+)".*$#isU','$1',$content);
	if(!preg_match('#<name( lang="en")?>.*</name>#isU',$content))
		continue;
	$name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$content);
	$name=str_replace('<![CDATA[','',$name);
	$name=str_replace(']]>','',$name);

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
		preg_match_all('#<item id="([0-9]+)"[^>]+/>#isU',$entry,$item_text_list);
		foreach($item_text_list[0] as $item_text)
		{
			$item=preg_replace('#^.*<item id="([0-9]+)"[^>]+/>.*$#isU','$1',$item_text);
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
				$monster_to_quests[$monster][]=array('quest'=>$id,'item'=>$item,'quantity'=>$quantity,'rate'=>$rate);
				$items_to_quests_for_step[$item][]=array('quest'=>$id,'quantity'=>$quantity,'monster'=>$monster,'rate'=>$rate);
			}
			else
			{
				$items[]=array('item'=>$item,'quantity'=>$quantity);
				$items_to_quests_for_step[$item][]=array('quest'=>$id,'quantity'=>$quantity);
			}
		}
		$steps[$id_step]=array('text'=>$text,'bot'=>$tempbot,'items'=>$items);
	}

	$rewards=array();
	preg_match_all('#<rewards.*</rewards>#isU',$content,$entry_list);
	foreach($entry_list[0] as $entry)
	{
		preg_match_all('#<item id="([0-9]+)"[^>]+/>#isU',$entry,$item_text_list);
		foreach($item_text_list[0] as $item_text)
		{
			if(!isset($rewards['items']))
				$rewards['items']=array();
			$item=preg_replace('#^.*<item id="([0-9]+)"[^>]+/>.*$#isU','$1',$item_text);
			if(!preg_match('#quantity="([0-9]+)"#isU',$content))
				$quantity=preg_replace('#^.*quantity="([0-9]+)".*$#isU','$1',$item_text);
			else
				$quantity=1;
			if(!isset($items_to_quests[$item]))
				$items_to_quests[$item]=array();
			$items_to_quests[$item][$id]=$quantity;
			$rewards['items'][]=array('item'=>$item,'quantity'=>$quantity);
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
	$quests_meta[$id]=array('name'=>$name,'repeatable'=>$repeatable,'steps'=>$steps,'rewards'=>$rewards,'requirements'=>$requirements);
}

$maps_list=array();
$maps_name_to_file=array();
$zone_to_map=array();
$monster_to_map=array();
$temp_maps=getTmxList($datapack_path.'map/');
foreach($temp_maps as $map)
{
	$width=0;
	$height=0;
	$pixelwidth=0;
	$pixelheight=0;
	if(preg_match('#/[^/]+$#',$map))
		$map_folder=preg_replace('#/[^/]+$#','',$map).'/';
	else
		$map_folder='';
	$map_xml_meta=str_replace('.tmx','.xml',$map);
	$borders=array();
	$tp=array();
	$doors=array();
	$content=file_get_contents($datapack_path.'map/'.$map);
	if(preg_match('#orientation="orthogonal" width="([0-9]+)" height="([0-9]+)" tilewidth="([0-9]+)" tileheight="([0-9]+)"#isU',$content))
	{
		$width=(int)preg_replace('#^.*orientation="orthogonal" width="([0-9]+)" height="([0-9]+)" tilewidth="([0-9]+)" tileheight="([0-9]+)".*$#isU','$1',$content);
		$height=(int)preg_replace('#^.*orientation="orthogonal" width="([0-9]+)" height="([0-9]+)" tilewidth="([0-9]+)" tileheight="([0-9]+)".*$#isU','$2',$content);
		$tilewidth=(int)preg_replace('#^.*orientation="orthogonal" width="([0-9]+)" height="([0-9]+)" tilewidth="([0-9]+)" tileheight="([0-9]+)".*$#isU','$3',$content);
		$tileheight=(int)preg_replace('#^.*orientation="orthogonal" width="([0-9]+)" height="([0-9]+)" tilewidth="([0-9]+)" tileheight="([0-9]+)".*$#isU','$4',$content);
		$pixelwidth=$width*$tilewidth;
		$pixelheight=$height*$tileheight;
	}
	preg_match_all('#<object[^>]+type="border-(left|right|top|bottom)".*</object>#isU',$content,$temp_text_list);
	foreach($temp_text_list[0] as $border_text)
	{
		if(preg_match('#type="border-(left|right|top|bottom)"#isU',$border_text))
		{
			$border_orientation=preg_replace('#^.*type="border-(left|right|top|bottom).*$#isU','$1',$border_text);
			$border_orientation=preg_replace("#[\n\r\t]+#is",'',$border_orientation);
			if(preg_match('#<property name="map" value="([^"]+)"/>#isU',$border_text))
			{
				$border_map=preg_replace('#^.*<property name="map" value="([^"]+)"/>.*$#isU','$1',$border_text);
				$border_map=$map_folder.$border_map;
				if(!preg_match('#\\.tmx$#',$border_map))
					$border_map.='.tmx';
				$border_map=preg_replace('#/[^/]+/\\.\\./#isU','/',$border_map);
				$border_map=preg_replace('#^[^/]+/\\.\\./#isU','',$border_map);
				$border_map=preg_replace("#[\n\r\t]+#is",'',$border_map);
				$borders[$border_orientation]=$border_map;
			}
		}
	}
	preg_match_all('#<object[^>]+type="teleport( on [a-z]+)?".*</object>#isU',$content,$temp_text_list);
	foreach($temp_text_list[0] as $border_text)
	{
		if(preg_match('#<property name="map" value="([^"]+)"/>#isU',$border_text))
		{
			$border_map=preg_replace('#^.*<property name="map" value="([^"]+)"/>.*$#isU','$1',$border_text);
			$border_map=$map_folder.$border_map;
			if(!preg_match('#\\.tmx$#',$border_map))
				$border_map.='.tmx';
			$border_map=preg_replace('#/[^/]+/\\.\\./#isU','/',$border_map);
			$border_map=preg_replace('#^[^/]+/\\.\\./#isU','',$border_map);
			$border_map=preg_replace("#[\n\r\t]+#is",'',$border_map);
			$tp[]=$border_map;
		}
	}
	preg_match_all('#<object[^>]+type="door".*</object>#isU',$content,$temp_text_list);
	foreach($temp_text_list[0] as $door_text)
	{
		if(preg_match('#type="door"#isU',$door_text))
		{
			if(preg_match('#<property name="map" value="([^"]+)"/>#isU',$door_text))
			{
				$door_map=preg_replace('#^.*<property name="map" value="([^"]+)"/>.*$#isU','$1',$door_text);
				$door_map=$map_folder.$door_map;
				if(!preg_match('#\\.tmx$#',$door_map))
					$door_map.='.tmx';
				$door_map=preg_replace('#/[^/]+/\\.\\./#isU','/',$door_map);
				$door_map=preg_replace('#^[^/]+/\\.\\./#isU','',$door_map);
				$door_map=preg_replace("#[\n\r\t]+#is",'',$door_map);
				$doors[]=$door_map;
			}
		}
	}
	$grass=array();
	$water=array();
	$cave=array();
	$type='outdoor';
	$name='Unknown name ('.$map.')';
	$shortdescription='';
	$description='';
	$zone='';
	$dropcount=0;
	if(file_exists($datapack_path.'map/'.$map_xml_meta))
	{
		$content_meta_map=file_get_contents($datapack_path.'map/'.$map_xml_meta);
		if(preg_match('#type="(outdoor|city|cave|indoor)"#isU',$content_meta_map))
			$type=preg_replace('#^.*type="(outdoor|city|cave|indoor)".*$#isU','$1',$content_meta_map);
		if(preg_match('#zone="([^"]+)"#isU',$content_meta_map))
			$zone=preg_replace('#^.*zone="([^"]+)".*$#isU','$1',$content_meta_map);
		if(preg_match('#<name lang="en">[^<]+</name>#isU',$content_meta_map))
			$name=preg_replace('#^.*<name lang="en">([^<]+)</name>.*$#isU','$1',$content_meta_map);
		elseif(preg_match('#<name>[^<]+</name>#isU',$content_meta_map))
			$name=preg_replace('#^.*<name>([^<]+)</name>.*$#isU','$1',$content_meta_map);
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
		$shortdescription=preg_replace("#[\n\r\t]+#is",'',$shortdescription);
		$description=preg_replace("#[\n\r\t]+#is",'',$description);
		//grass
		if(preg_match('#<grass>(.*)</grass>#isU',$content_meta_map) && preg_match('#<layer name="Grass"#isU',$content))
		{
			$grass_text=preg_replace('#^.*<grass>(.*)</grass>.*$#isU','$1',$content_meta_map);
			preg_match_all('#<monster[^>]+/>#isU',$grass_text,$temp_text_list);
			foreach($temp_text_list[0] as $grass_text_entry)
			{
				if(preg_match('#level="([0-9]+)"#isU',$grass_text_entry))
				{
					$minLevel=preg_replace('#^.*level="([0-9]+)".*$#isU','$1',$grass_text_entry);
					$maxLevel=preg_replace('#^.*level="([0-9]+)".*$#isU','$1',$grass_text_entry);
				}
				elseif(preg_match('#minLevel="([0-9]+)"#isU',$grass_text_entry) && preg_match('maxLevel="([0-9]+)"#isU',$grass_text_entry))
				{
					$minLevel=preg_replace('#^.*minLevel="([0-9]+)".*$#isU','$1',$grass_text_entry);
					$maxLevel=preg_replace('#^.*maxLevel="([0-9]+)".*$#isU','$1',$grass_text_entry);
				}
				else
					continue;
				if(preg_match('#luck="([0-9]+)"#isU',$grass_text_entry))
					$luck=preg_replace('#^.*luck="([0-9]+)".*$#isU','$1',$grass_text_entry);
				else
					continue;
				if(preg_match('#id="([0-9]+)"#isU',$grass_text_entry))
					$id=preg_replace('#^.*id="([0-9]+)".*$#isU','$1',$grass_text_entry);
				else
					continue;
				if(isset($monster_meta[$id]))
				{
					$grass[]=array('id'=>$id,'minLevel'=>$minLevel,'maxLevel'=>$maxLevel,'luck'=>$luck);
					if(!isset($monster_to_map[$id]))
						$monster_to_map[$id]=array();
					if(!isset($monster_to_map[$id]['grass']))
						$monster_to_map[$id]['grass']=array();
					$monster_to_map[$id]['grass'][]=array('map'=>$map,'minLevel'=>$minLevel,'maxLevel'=>$maxLevel,'luck'=>$luck);
					$dropcount+=count($monster_meta[$id]['drops']);
				}
			}
		}
		//water
		if(preg_match('#<water>(.*)</water>#isU',$content_meta_map) && preg_match('#<layer name="Water"#isU',$content))
		{
			$grass_text=preg_replace('#^.*<water>(.*)</water>.*$#isU','$1',$content_meta_map);
			preg_match_all('#<monster[^>]+/>#isU',$grass_text,$temp_text_list);
			foreach($temp_text_list[0] as $grass_text_entry)
			{
				if(preg_match('#level="([0-9]+)"#isU',$grass_text_entry))
				{
					$minLevel=preg_replace('#^.*level="([0-9]+)".*$#isU','$1',$grass_text_entry);
					$maxLevel=preg_replace('#^.*level="([0-9]+)".*$#isU','$1',$grass_text_entry);
				}
				elseif(preg_match('#minLevel="([0-9]+)"#isU',$grass_text_entry) && preg_match('maxLevel="([0-9]+)"#isU',$grass_text_entry))
				{
					$minLevel=preg_replace('#^.*minLevel="([0-9]+)".*$#isU','$1',$grass_text_entry);
					$maxLevel=preg_replace('#^.*maxLevel="([0-9]+)".*$#isU','$1',$grass_text_entry);
				}
				else
					continue;
				if(preg_match('#luck="([0-9]+)"#isU',$grass_text_entry))
					$luck=preg_replace('#^.*luck="([0-9]+)".*$#isU','$1',$grass_text_entry);
				else
					continue;
				if(preg_match('#id="([0-9]+)"#isU',$grass_text_entry))
					$id=preg_replace('#^.*id="([0-9]+)".*$#isU','$1',$grass_text_entry);
				else
					continue;
				if(isset($monster_meta[$id]))
				{
					$water[]=array('id'=>$id,'minLevel'=>$minLevel,'maxLevel'=>$maxLevel,'luck'=>$luck);
					if(!isset($monster_to_map[$id]))
						$monster_to_map[$id]=array();
					if(!isset($monster_to_map[$id]['water']))
						$monster_to_map[$id]['water']=array();
					$monster_to_map[$id]['water'][]=array('map'=>$map,'minLevel'=>$minLevel,'maxLevel'=>$maxLevel,'luck'=>$luck);
					$dropcount+=count($monster_meta[$id]['drops']);
				}
			}
		}
		//cave
		if(preg_match('#<cave>(.*)</cave>#isU',$content_meta_map))
		{
			$grass_text=preg_replace('#^.*<cave>(.*)</cave>.*$#isU','$1',$content_meta_map);
			preg_match_all('#<monster[^>]+/>#isU',$grass_text,$temp_text_list);
			foreach($temp_text_list[0] as $grass_text_entry)
			{
				if(preg_match('#level="([0-9]+)"#isU',$grass_text_entry))
				{
					$minLevel=preg_replace('#^.*level="([0-9]+)".*$#isU','$1',$grass_text_entry);
					$maxLevel=preg_replace('#^.*level="([0-9]+)".*$#isU','$1',$grass_text_entry);
				}
				elseif(preg_match('#minLevel="([0-9]+)"#isU',$grass_text_entry) && preg_match('maxLevel="([0-9]+)"#isU',$grass_text_entry))
				{
					$minLevel=preg_replace('#^.*minLevel="([0-9]+)".*$#isU','$1',$grass_text_entry);
					$maxLevel=preg_replace('#^.*maxLevel="([0-9]+)".*$#isU','$1',$grass_text_entry);
				}
				else
					continue;
				if(preg_match('#luck="([0-9]+)"#isU',$grass_text_entry))
					$luck=preg_replace('#^.*luck="([0-9]+)".*$#isU','$1',$grass_text_entry);
				else
					continue;
				if(preg_match('#id="([0-9]+)"#isU',$grass_text_entry))
					$id=preg_replace('#^.*id="([0-9]+)".*$#isU','$1',$grass_text_entry);
				else
					continue;
				if(isset($monster_meta[$id]))
				{
					$cave[]=array('id'=>$id,'minLevel'=>$minLevel,'maxLevel'=>$maxLevel,'luck'=>$luck);
					if(!isset($monster_to_map[$id]))
						$monster_to_map[$id]=array();
					if(!isset($monster_to_map[$id]['cave']))
						$monster_to_map[$id]['cave']=array();
					$monster_to_map[$id]['cave'][]=array('map'=>$map,'minLevel'=>$minLevel,'maxLevel'=>$maxLevel,'luck'=>$luck);
					$dropcount+=count($monster_meta[$id]['drops']);
				}
			}
		}
	}
	$maps_list[$map]=array('borders'=>$borders,'tp'=>$tp,'doors'=>$doors,'name'=>$name,'shortdescription'=>$shortdescription,'description'=>$description,'type'=>$type,'grass'=>$grass,'water'=>$water,'cave'=>$cave,
	'width'=>$width,'height'=>$height,'pixelwidth'=>$pixelwidth,'pixelheight'=>$pixelheight,'dropcount'=>$dropcount,'zone'=>$zone
	);
	if(!isset($zone_to_map[$zone]))
		$zone_to_map[$zone]=array();
	$zone_to_map[$zone][$map]=$name;
}
if(!is_dir($datapack_explorer_local_path.'maps/'))
	mkdir($datapack_explorer_local_path.'maps/');

foreach($temp_maps as $map)
{
	$map_html=str_replace('.tmx','.html',$map);
	$map_image=str_replace('.tmx','.png',$map);
	if(preg_match('#/#isU',$map))
	{
		$map_folder=preg_replace('#/[^/]+$#','',$map).'/';
		if(!is_dir($datapack_explorer_local_path.'maps/'.$map_folder))
			mkdir($datapack_explorer_local_path.'maps/'.$map_folder);
	}
}

$temprand=rand(10000,99999);
if(isset($map_generator) && $map_generator!='')
{
	$pwd=getcwd();
	$return_var=0;
	echo 'cd '.$datapack_explorer_local_path.'maps/ && '.$map_generator.' -platform offscreen '.$pwd.'/'.$datapack_path.'map/';
	chdir($datapack_explorer_local_path.'maps/');
	exec($map_generator.' -platform offscreen '.$pwd.'/'.$datapack_path.'map/',$output,$return_var);
	if(isset($png_compress) && $png_compress!='')
	{
		$before = microtime(true);
		exec($png_compress);
		$after = microtime(true);
		echo 'Png compressed into '.(int)($after-$before)."s\n";
	}
	chdir($pwd);
}

foreach($temp_maps as $map)
{
	$map_html=str_replace('.tmx','.html',$map);
	$map_image=str_replace('.tmx','.png',$map);
	$map_folder='';
	if(preg_match('#/#isU',$map))
	{
		$map_folder=preg_replace('#/[^/]+$#','',$map).'/';
		if(!is_dir($datapack_explorer_local_path.'maps/'.$map_folder))
			mkdir($datapack_explorer_local_path.'maps/'.$map_folder);
	}
	$map_descriptor='';

	$map_descriptor.='<div class="map map_type_'.$maps_list[$map]['type'].'">';
		$map_descriptor.='<div class="subblock"><h1>'.$maps_list[$map]['name'].'</h1>';
		if($maps_list[$map]['type']!='')
			$map_descriptor.='<h3>('.$maps_list[$map]['type'].')</h3>';
		if($maps_list[$map]['shortdescription']!='')
			$map_descriptor.='<h2>'.$maps_list[$map]['shortdescription'].'</h2>';
		$map_descriptor.='</div>';
		if(file_exists($datapack_explorer_local_path.'maps/'.$map_image))
			$map_descriptor.='<div class="value mapscreenshot datapackscreenshot"><a href="'.$base_datapack_explorer_site_path.'maps/'.$map_image.'"><img src="'.$base_datapack_explorer_site_path.'maps/'.$map_image.'" alt="Screenshot of '.$maps_list[$map]['name'].'" title="Screenshot of '.$maps_list[$map]['name'].'" width="'.($maps_list[$map]['pixelwidth']/2).'" height="'.($maps_list[$map]['pixelheight']/2).'" /></a></div>';
		if($maps_list[$map]['description']!='')
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Map description</div><div class="value">'.$maps_list[$map]['description'].'</div></div>';
		if(count($maps_list[$map]['borders'])>0 || count($maps_list[$map]['doors'])>0 || count($maps_list[$map]['tp'])>0)
		{
			$duplicate=array();
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Linked locations</div><div class="value"><ul>';
			foreach($maps_list[$map]['borders'] as $bordertype=>$border)
			{
				if(!isset($duplicate[$border]))
				{
					$duplicate[$border]='';
					if(isset($maps_list[$border]))
						$map_descriptor.='<li>Border '.$bordertype.': <a href="'.$base_datapack_explorer_site_path.'maps/'.str_replace('.tmx','.html',$border).'">'.$maps_list[$border]['name'].'</a></li>';
					else
						$map_descriptor.='<li>Border '.$bordertype.': <span class="mapnotfound">'.$border.'</span></li>';
				}
			}
			foreach($maps_list[$map]['doors'] as $door)
			{
				if(!isset($duplicate[$door]))
				{
					$duplicate[$door]='';
					if(isset($maps_list[$door]))
						$map_descriptor.='<li>Door: <a href="'.$base_datapack_explorer_site_path.'maps/'.str_replace('.tmx','.html',$door).'">'.$maps_list[$door]['name'].'</a></li>';
					else
						$map_descriptor.='<li>Door: <span class="mapnotfound">'.$door.'</span></li>';
				}
			}
			foreach($maps_list[$map]['tp'] as $tp)
			{
				if(!isset($duplicate[$tp]))
				{
					$duplicate[$tp]='';
					if(isset($maps_list[$tp]))
						$map_descriptor.='<li>Teleporter: <a href="'.$base_datapack_explorer_site_path.'maps/'.str_replace('.tmx','.html',$tp).'">'.$maps_list[$tp]['name'].'</a></li>';
					else
						$map_descriptor.='<li>Teleporter: <span class="mapnotfound">'.$tp.'</span></li>';
				}
			}
			$map_descriptor.='</ul></div></div>';
		}
	$map_descriptor.='</div>';
	
	if($maps_list[$map]['dropcount']>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$maps_list[$map]['type'].'">
		<tr class="item_list_title item_list_title_type_'.$maps_list[$map]['type'].'">
			<th colspan="2">Item</th>
			<th>Location</th>
		</tr>';
		$monster_list=array_merge($maps_list[$map]['grass'],$maps_list[$map]['water'],$maps_list[$map]['cave']);
		foreach($monster_list as $monster)
		{
			if(isset($monster_meta[$monster['id']]))
			{
				$drops=$monster_meta[$monster['id']]['drops'];
				foreach($drops as $drop)
				{
					if(isset($item_meta[$drop['item']]))
					{
						$link=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$drop['item']]['name']).'.html';
						$name=$item_meta[$drop['item']]['name'];
						if($item_meta[$drop['item']]['image']!='')
							$image=$base_datapack_site_path.'/items/'.$item_meta[$drop['item']]['image'];
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
						$map_descriptor.='<td>Drop on <a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_meta[$monster['id']]['name']).'.html" title="'.$monster_meta[$monster['id']]['name'].'">'.$monster_meta[$monster['id']]['name'].'</a> with luck of '.$drop['luck'].'%</td>
					</tr>';
				}
			}
		}
		$map_descriptor.='<tr>
			<td colspan="3" class="item_list_endline item_list_title_type_'.$maps_list[$map]['type'].'"></td>
		</tr>
		</table>';
	}

	if(count($maps_list[$map]['grass'])>0 || count($maps_list[$map]['water'])>0 || count($maps_list[$map]['cave'])>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$maps_list[$map]['type'].'">
		<tr class="item_list_title item_list_title_type_'.$maps_list[$map]['type'].'">
			<th colspan="2">Monster</th>
			<th>Location</th>
			<th>Levels</th>
			<th colspan="3">Rate</th>
		</tr>';
		if(count($maps_list[$map]['grass'])>0)
		{
			$map_descriptor.='<tr class="item_list_title_type_'.$maps_list[$map]['type'].'">
					<th colspan="7">Grass</th>
				</tr>';
			foreach($maps_list[$map]['grass'] as $monster)
			{
				if(isset($monster_meta[$monster['id']]))
				{
					$name=$monster_meta[$monster['id']]['name'];
					$link=$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($name).'.html';
					$map_descriptor.='<tr class="value">
						<td>';
						if(file_exists($datapack_path.'monsters/'.$monster['id'].'/small.png'))
							$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$monster['id'].'/small.png" width="32" height="32" alt="'.$monster_meta[$monster['id']]['name'].'" title="'.$monster_meta[$monster['id']]['name'].'" /></a></div>';
						else if(file_exists($datapack_path.'monsters/'.$monster['id'].'/small.gif'))
							$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$monster['id'].'/small.gif" width="32" height="32" alt="'.$monster_meta[$monster['id']]['name'].'" title="'.$monster_meta[$monster['id']]['name'].'" /></a></div>';
						$map_descriptor.='</td>
						<td><a href="'.$link.'">'.$name.'</a></td>
						<td><img src="/images/datapack-explorer/grass.png" alt="" class="locationimg">Grass</td>
						<td>';
						if($monster['minLevel']==$monster['maxLevel'])
							$map_descriptor.=$monster['minLevel'];
						else
							$map_descriptor.=$monster['minLevel'].'-'.$monster['maxLevel'];
						$map_descriptor.='</td>';
						$map_descriptor.='<td colspan="3">'.$monster['luck'].'%</td>
					</tr>';
				}
			}
		}
		if(count($maps_list[$map]['water'])>0)
		{
			$map_descriptor.='<tr class="item_list_title_type_'.$maps_list[$map]['type'].'">
					<th colspan="7">Water</th>
				</tr>';
			foreach($maps_list[$map]['water'] as $monster)
			{
				if(isset($monster_meta[$monster['id']]))
				{
					$name=$monster_meta[$monster['id']]['name'];
					$link=$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($name).'.html';
					$map_descriptor.='<tr class="value">
						<td>';
						if(file_exists($datapack_path.'monsters/'.$monster['id'].'/small.png'))
							$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$monster['id'].'/small.png" width="32" height="32" alt="'.$monster_meta[$monster['id']]['name'].'" title="'.$monster_meta[$monster['id']]['name'].'" /></a></div>';
						else if(file_exists($datapack_path.'monsters/'.$monster['id'].'/small.gif'))
							$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$monster['id'].'/small.gif" width="32" height="32" alt="'.$monster_meta[$monster['id']]['name'].'" title="'.$monster_meta[$monster['id']]['name'].'" /></a></div>';
						$map_descriptor.='</td>
						<td><a href="'.$link.'">'.$name.'</a></td>
						<td><img src="/images/datapack-explorer/water.png" alt="" class="locationimg">Water</td>
						<td>';
						if($monster['minLevel']==$monster['maxLevel'])
							$map_descriptor.=$monster['minLevel'];
						else
							$map_descriptor.=$monster['minLevel'].'-'.$monster['maxLevel'];
						$map_descriptor.='</td>';
						$map_descriptor.='<td colspan="3">'.$monster['luck'].'%</td>
					</tr>';
				}
			}
		}
		if(count($maps_list[$map]['cave'])>0)
		{
			$map_descriptor.='<tr class="item_list_title_type_'.$maps_list[$map]['type'].'">
					<th colspan="7">Cave</th>
				</tr>';
			foreach($maps_list[$map]['cave'] as $monster)
			{
				if(isset($monster_meta[$monster['id']]))
				{
					$name=$monster_meta[$monster['id']]['name'];
					$link=$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($name).'.html';
					$map_descriptor.='<tr class="value">
						<td>';
						if(file_exists($datapack_path.'monsters/'.$monster['id'].'/small.png'))
							$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$monster['id'].'/small.png" width="32" height="32" alt="'.$monster_meta[$monster['id']]['name'].'" title="'.$monster_meta[$monster['id']]['name'].'" /></a></div>';
						else if(file_exists($datapack_path.'monsters/'.$monster['id'].'/small.gif'))
							$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$monster['id'].'/small.gif" width="32" height="32" alt="'.$monster_meta[$monster['id']]['name'].'" title="'.$monster_meta[$monster['id']]['name'].'" /></a></div>';
						$map_descriptor.='</td>
						<td><a href="'.$link.'">'.$name.'</a></td>
						<td><img src="/images/datapack-explorer/cave.png" alt="" class="locationimg">Cave</td>
						<td>';
						if($monster['minLevel']==$monster['maxLevel'])
							$map_descriptor.=$monster['minLevel'];
						else
							$map_descriptor.=$monster['minLevel'].'-'.$monster['maxLevel'];
						$map_descriptor.='</td>';
						$map_descriptor.='<td colspan="3">'.$monster['luck'].'%</td>
					</tr>';
				}
			}
		}
		$map_descriptor.='<tr>
			<td colspan="7" class="item_list_endline item_list_title_type_'.$maps_list[$map]['type'].'"></td>
		</tr>
		</table>';
	}
	
	$content=$template;
	$content=str_replace('${TITLE}',$maps_list[$map]['name'],$content);
	$content=str_replace('${CONTENT}',$map_descriptor,$content);
	$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
	$content=preg_replace("#[\r\n\t]+#isU",'',$content);
	filewrite($datapack_explorer_local_path.'maps/'.$map_html,$content);
}

$map_descriptor='';
foreach($zone_to_map as $zone=>$map_by_zone)
{
	$map_descriptor.='<table class="item_list item_list_type_outdoor map_list"><tr class="item_list_title item_list_title_type_outdoor">
	<th>';
	if(isset($zone_meta[$zone]))
		$map_descriptor.=$zone_meta[$zone]['name'];
	elseif($zone=='')
		$map_descriptor.='Unknown zone';
	else
		$map_descriptor.=$zone;
	$map_descriptor.='</th></tr>';
	asort($map_by_zone);
	foreach($map_by_zone as $map=>$name)
		$map_descriptor.='<tr class="value"><td><a href="'.$base_datapack_explorer_site_path.'maps/'.str_replace('.tmx','.html',$map).'" title="'.$name.'">'.$name.'</a></td></tr>';
	$map_descriptor.='<tr>
	<td colspan="1" class="item_list_endline item_list_title_type_outdoor"></td>
	</tr></table>';
}
$content=$template;
$content=str_replace('${TITLE}','Map list',$content);
$content=str_replace('${CONTENT}',$map_descriptor,$content);
$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
$content=preg_replace("#[\r\n\t]+#isU",'',$content);
filewrite($datapack_explorer_local_path.'maps.html',$content);

foreach($monster_meta as $id=>$monster)
{
	$resolved_type=$monster['type'][0];
	if(!isset($type_meta[$resolved_type]))
	{
		if(!isset($type_meta['normal']) || count($type_meta)<=0)
			$resolved_type='normal';
		else
		{
			foreach($type_meta as $type=>$type_content)
			{
				$resolved_type=$type;
				break;
			}
		}
	}
	if(!is_dir($datapack_explorer_local_path.'monsters/'))
		mkdir($datapack_explorer_local_path.'monsters/');
	$map_descriptor='';

	$effectiveness_list=array();
	foreach($type_meta as $realtypeindex=>$typecontent)
	{
		$effectiveness=(float)1.0;
		foreach($monster['type'] as $type)
			if(isset($typecontent['multiplicator'][$type]))
				$effectiveness*=$typecontent['multiplicator'][$type];
		if($effectiveness!=1.0)
		{
			if(!isset($effectiveness_list[(string)$effectiveness]))
				$effectiveness_list[(string)$effectiveness]=array();
			$effectiveness_list[(string)$effectiveness][]=$realtypeindex;
		}
	}
	$map_descriptor.='<div class="map monster_type_'.$resolved_type.'">';
		$map_descriptor.='<div class="subblock"><h1>'.$monster['name'].'</h1>';
		$map_descriptor.='<h2>#'.$id.'</h2>';
		$map_descriptor.='</div>';
		$map_descriptor.='<div class="value datapackscreenshot">';
		if(file_exists($datapack_path.'monsters/'.$id.'/front.png'))
			$map_descriptor.='<img src="'.$base_datapack_site_path.'monsters/'.$id.'/front.png" width="80" height="80" alt="'.$monster['name'].'" title="'.$monster['name'].'" />';
		else if(file_exists($datapack_path.'monsters/'.$id.'/front.gif'))
			$map_descriptor.='<img src="'.$base_datapack_site_path.'monsters/'.$id.'/front.gif" width="80" height="80" alt="'.$monster['name'].'" title="'.$monster['name'].'" />';
		$map_descriptor.='</div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Type</div><div class="value">';
		$type_list=array();
		foreach($monster['type'] as $type)
			if(isset($type_meta[$type]))
				$type_list[]='<span class="type_label type_label_'.$type.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type.'.html">'.$type_meta[$type]['english_name'].'</a></span>';
		$map_descriptor.='<div class="type_label_list">'.implode(' ',$type_list).'</div></div></div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Gender ratio</div><div class="value">'.$monster['ratio_gender'].'% male, '.(100-$monster['ratio_gender']).'% female</div></div>';
		if($monster['description']!='')
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Description</div><div class="value">'.$monster['description'].'</div></div>';
		if($monster['kind']!='')
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Kind</div><div class="value">'.$monster['kind'].'</div></div>';
		if($monster['habitat']!='')
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Habitat</div><div class="value">'.$monster['habitat'].'</div></div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Catch rate</div><div class="value">'.$monster['catch_rate'].'</div></div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Egg step</div><div class="value">'.$monster['egg_step'].'</div></div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Body</div><div class="value">Height: '.$monster['height'].'m, width: '.$monster['weight'].'kg</div></div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Stat</div><div class="value">Hp: <i>'.$monster['hp'].'</i>, Attack: <i>'.$monster['attack'].'</i>, Defense: <i>'.$monster['defense'].'</i>, Special attack: <i>'.$monster['special_attack'].'</i>, Special defense: <i>'.$monster['special_defense'].'</i>, Speed: <i>'.$monster['speed'].'</i></div></div>';
		if(isset($effectiveness_list['4']) || isset($effectiveness_list['2']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Weak to</div><div class="value">';
			$type_list=array();
			if(isset($effectiveness_list['2']))
				foreach($effectiveness_list['2'] as $type)
					if(isset($type_meta[$type]))
						$type_list[]='<span class="type_label type_label_'.$type.'">2x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type.'.html">'.$type_meta[$type]['english_name'].'</a></span>';
			if(isset($effectiveness_list['4']))
				foreach($effectiveness_list['4'] as $type)
					if(isset($type_meta[$type]))
						$type_list[]='<span class="type_label type_label_'.$type.'">4x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type.'.html">'.$type_meta[$type]['english_name'].'</a></span>';
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>';
		}
		if(isset($effectiveness_list['0.25']) || isset($effectiveness_list['0.5']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Resistant to</div><div class="value">';
			$type_list=array();
			if(isset($effectiveness_list['0.25']))
				foreach($effectiveness_list['0.25'] as $type)
					if(isset($type_meta[$type]))
						$type_list[]='<span class="type_label type_label_'.$type.'">0.25x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type.'.html">'.$type_meta[$type]['english_name'].'</a></span>';
			if(isset($effectiveness_list['0.5']))
				foreach($effectiveness_list['0.5'] as $type)
					if(isset($type_meta[$type]))
						$type_list[]='<span class="type_label type_label_'.$type.'">0.5x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type.'.html">'.$type_meta[$type]['english_name'].'</a></span>';
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>';
		}
		if(isset($effectiveness_list['0']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Immune to</div><div class="value">';
			$type_list=array();
			foreach($effectiveness_list['0'] as $type)
				if(isset($type_meta[$type]))
					$type_list[]='<span class="type_label type_label_'.$type.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type.'.html">'.$type_meta[$type]['english_name'].'</a></span>';
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>';
		}
	$map_descriptor.='</div>';
	
	if(count($monster['drops'])>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$resolved_type.'">
		<tr class="item_list_title item_list_title_type_'.$resolved_type.'">
			<th colspan="2">Item</th>
			<th>Location</th>
		</tr>';
		$drops=$monster['drops'];
		foreach($drops as $drop)
		{
			if(isset($item_meta[$drop['item']]))
			{
				$link=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$drop['item']]['name']).'.html';
				$name=$item_meta[$drop['item']]['name'];
				if($item_meta[$drop['item']]['image']!='')
					$image=$base_datapack_site_path.'/items/'.$item_meta[$drop['item']]['image'];
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
				$map_descriptor.='<td>Drop luck of '.$drop['luck'].'%</td>
			</tr>';
		}
		$map_descriptor.='<tr>
			<td colspan="3" class="item_list_endline item_list_title_type_'.$resolved_type.'"></td>
		</tr>
		</table>';
	}

	if(count($monster['attack_list'])>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$resolved_type.'">
		<tr class="item_list_title item_list_title_type_'.$resolved_type.'">
			<th>Level</th>
			<th>Skill</th>
			<th>Type</th>
			<th>Endurance</th>
		</tr>';
		$attack_list=$monster['attack_list'];
		foreach($attack_list as $level=>$attack_at_level)
		{
			foreach($attack_at_level as $attack)
			{
				if(isset($skill_meta[$attack['id']]))
				{
					$map_descriptor.='<tr class="value">';
					$map_descriptor.='<td>';
					if($level==0)
						$map_descriptor.='Start';
					else
						$map_descriptor.=$level;
					$map_descriptor.='</td>';
					$map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.'monsters/skills/'.text_operation_do_for_url($skill_meta[$attack['id']]['name']).'.html">'.$skill_meta[$attack['id']]['name'];
					if($attack['attack_level']>1)
						$map_descriptor.=' at level '.$attack['attack_level'];
					$map_descriptor.='</a></td>';
					if(isset($type_meta[$skill_meta[$attack['id']]['type']]))
						$map_descriptor.='<td><span class="type_label type_label_'.$skill_meta[$attack['id']]['type'].'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$skill_meta[$attack['id']]['type'].'.html">'.$type_meta[$skill_meta[$attack['id']]['type']]['english_name'].'</a></span></td>';
					else
						$map_descriptor.='<td>&nbsp;</td>';
					if(isset($skill_meta[$attack['id']]['level_list'][$attack['attack_level']]))
						$map_descriptor.='<td>'.$skill_meta[$attack['id']]['level_list'][$attack['attack_level']]['endurance'].'</td>';
					else
						$map_descriptor.='<td>&nbsp;</td>';
					$map_descriptor.='</tr>';
				}
			}
		}
		$map_descriptor.='<tr>
			<td colspan="4" class="item_list_endline item_list_title_type_'.$resolved_type.'"></td>
		</tr>
		</table>';
	}

	if(isset($monster_to_quests[$id]))
	{
		$map_descriptor.='<table class="item_list item_list_type_outdoor"><tr class="item_list_title item_list_title_type_outdoor">';
		$map_descriptor.='<th colspan="2">Item</th><th>Quests</th><th>Luck</th></tr>';
		foreach($monster_to_quests[$id] as $quests_monsters_details)
		{
			$map_descriptor.='<tr class="value"><td>';
			if(isset($item_meta[$quests_monsters_details['item']]))
			{
				$link=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$quests_monsters_details['item']]['name']).'.html';
				$name=$item_meta[$quests_monsters_details['item']]['name'];
				if($item_meta[$quests_monsters_details['item']]['image']!='')
					$image=$base_datapack_site_path.'/items/'.$item_meta[$quests_monsters_details['item']]['image'];
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
			if($quests_monsters_details['quantity']>1)
				$quantity_text=$quests_monsters_details['quantity'].' ';
			if($image!='')
			{
				if($link!='')
					$map_descriptor.='<a href="'.$link.'">';
				$map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
				if($link!='')
					$map_descriptor.='</a>';
			}
			$map_descriptor.='</td><td>';
			if($link!='')
				$map_descriptor.='<a href="'.$link.'">';
			if($name!='')
				$map_descriptor.=$quantity_text.$name;
			else
				$map_descriptor.=$quantity_text.'Unknown item';
			if($link!='')
				$map_descriptor.='</a>';
			$map_descriptor.='</td>';

			$map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.'quests/'.$quests_monsters_details['quest'].'-'.text_operation_do_for_url($quests_meta[$quests_monsters_details['quest']]['name']).'.html" title="'.$quests_meta[$quests_monsters_details['quest']]['name'].'">';
			$map_descriptor.=$quests_meta[$quests_monsters_details['quest']]['name'];
			$map_descriptor.='</a></td>';
			$map_descriptor.='<td>'.$quests_monsters_details['rate'].'%</td>';
			$map_descriptor.='</tr>';
		}
		$map_descriptor.='<tr>
		<td colspan="4" class="item_list_endline item_list_title_type_outdoor"></td>
		</tr></table>';
	}

	if(count($monster['evolution_list'])>0 || isset($reverse_evolution[$id]))
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$resolved_type.'">
		<tr class="item_list_title item_list_title_type_'.$resolved_type.'">
			<th>';
		if(isset($reverse_evolution[$id]))
			$map_descriptor.='Evolve from';
		$map_descriptor.='</th>
		</tr>';

		if(isset($reverse_evolution[$id]))
		{
			$map_descriptor.='<tr class="value">';
			$map_descriptor.='<td>';
			$map_descriptor.='<table class="monsterforevolution">';
			foreach($reverse_evolution[$id] as $evolution)
			{
				if(file_exists($datapack_path.'monsters/'.$evolution['evolveFrom'].'/front.png'))
					$map_descriptor.='<tr><td><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_meta[$evolution['evolveFrom']]['name']).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$evolution['evolveFrom'].'/front.png" width="80" height="80" alt="'.$monster_meta[$evolution['evolveFrom']]['name'].'" title="'.$monster_meta[$evolution['evolveFrom']]['name'].'" /></a></td></tr>';
				else if(file_exists($datapack_path.'monsters/'.$evolution['evolveFrom'].'/front.gif'))
					$map_descriptor.='<tr><td><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_meta[$evolution['evolveFrom']]['name']).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$evolution['evolveFrom'].'/front.gif" width="80" height="80" alt="'.$monster_meta[$evolution['evolveFrom']]['name'].'" title="'.$monster_meta[$evolution['evolveFrom']]['name'].'" /></a></td></tr>';
				$map_descriptor.='<tr><td class="evolution_name"><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_meta[$evolution['evolveFrom']]['name']).'.html">'.$monster_meta[$evolution['evolveFrom']]['name'].'</a></td></tr>';
				if($evolution['type']=='level')
					$map_descriptor.='<tr><td class="evolution_type">At level '.$evolution['level'].'</td></tr>';
				elseif($evolution['type']=='item')
				{
					if(isset($item_meta[$evolution['level']]))
					{
						$link=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$evolution['level']]['name']).'.html';
						$name=$item_meta[$evolution['level']]['name'];
						$map_descriptor.='<tr><td class="evolution_type">Evolve with<br /><a href="'.$link.'" title="'.$name.'">';
						if($item_meta[$evolution['level']]['image']!='')
							$map_descriptor.='<img src="'.$base_datapack_site_path.'items/'.$item_meta[$evolution['level']]['image'].'" alt="'.$name.'" title="'.$name.'" style="float:left;" />';
						$map_descriptor.=$name.'</a></td></tr>';
					}
					else
						$map_descriptor.='<tr><td class="evolution_type">With unknown item</td></tr>';
				}
				elseif($evolution['type']=='trade')
					$map_descriptor.='<tr><td class="evolution_type">After trade</td></tr>';
				else
					$map_descriptor.='<tr><td class="evolution_type">&nbsp;</td></tr>';
			}
			$map_descriptor.='</table>';
			$map_descriptor.='</td>';
			$map_descriptor.='</tr>';
		}

		$map_descriptor.='<tr class="value">';
		$map_descriptor.='<td>';
		$map_descriptor.='<table class="monsterforevolution">';
		if(file_exists($datapack_path.'monsters/'.$id.'/front.png'))
			$map_descriptor.='<tr><td><img src="'.$base_datapack_site_path.'monsters/'.$id.'/front.png" width="80" height="80" alt="'.$monster['name'].'" title="'.$monster['name'].'" /></td></tr>';
		else if(file_exists($datapack_path.'monsters/'.$id.'/front.gif'))
			$map_descriptor.='<tr><td><img src="'.$base_datapack_site_path.'monsters/'.$id.'/front.gif" width="80" height="80" alt="'.$monster['name'].'" title="'.$monster['name'].'" /></td></tr>';
		$map_descriptor.='<tr><td class="evolution_name">'.$monster['name'].'</td></tr>';
		$map_descriptor.='</table>';
		$map_descriptor.='</td>';
		$map_descriptor.='</tr>';

		if(count($monster['evolution_list'])>0)
		{
			$map_descriptor.='<tr class="value">';
			$map_descriptor.='<td>';
			$map_descriptor.='<table class="monsterforevolution">';
			foreach($monster['evolution_list'] as $evolution)
			{
				if(file_exists($datapack_path.'monsters/'.$evolution['evolveTo'].'/front.png'))
					$map_descriptor.='<tr><td><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_meta[$evolution['evolveTo']]['name']).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$evolution['evolveTo'].'/front.png" width="80" height="80" alt="'.$monster_meta[$evolution['evolveTo']]['name'].'" title="'.$monster_meta[$evolution['evolveTo']]['name'].'" /></a></td></tr>';
				else if(file_exists($datapack_path.'monsters/'.$evolution['evolveTo'].'/front.gif'))
					$map_descriptor.='<tr><td><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_meta[$evolution['evolveTo']]['name']).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$evolution['evolveTo'].'/front.gif" width="80" height="80" alt="'.$monster_meta[$evolution['evolveTo']]['name'].'" title="'.$monster_meta[$evolution['evolveTo']]['name'].'" /></a></td></tr>';
				$map_descriptor.='<tr><td class="evolution_name"><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_meta[$evolution['evolveTo']]['name']).'.html">'.$monster_meta[$evolution['evolveTo']]['name'].'</a></td></tr>';
				if($evolution['type']=='level')
					$map_descriptor.='<tr><td class="evolution_type">At level '.$evolution['level'].'</td></tr>';
				elseif($evolution['type']=='item')
				{
					if(isset($item_meta[$evolution['level']]))
					{
						$link=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$evolution['level']]['name']).'.html';
						$name=$item_meta[$evolution['level']]['name'];
						$map_descriptor.='<tr><td class="evolution_type">Evolve with<br /><a href="'.$link.'" title="'.$name.'">';
						if($item_meta[$evolution['level']]['image']!='')
							$map_descriptor.='<img src="'.$base_datapack_site_path.'items/'.$item_meta[$evolution['level']]['image'].'" alt="'.$name.'" title="'.$name.'" style="float:left;" />';
						$map_descriptor.=$name.'</a></td></tr>';
					}
					else
						$map_descriptor.='<tr><td class="evolution_type">With unknown item</td></tr>';
				}
				elseif($evolution['type']=='trade')
					$map_descriptor.='<tr><td class="evolution_type">After trade</td></tr>';
				else
					$map_descriptor.='<tr><td class="evolution_type">&nbsp;</td></tr>';
			}
			$map_descriptor.='</table>';
			$map_descriptor.='</td>';
			$map_descriptor.='</tr>';
		}

		$map_descriptor.='<tr>
			<th class="item_list_endline item_list_title item_list_title_type_'.$resolved_type.'">';
		if(count($monster['evolution_list'])>0)
			$map_descriptor.='Evolve to';
		$map_descriptor.='</th>
		</tr>
		</table>';
	}

	if(isset($monster_to_map[$id]))
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$resolved_type.'">
		<tr class="item_list_title item_list_title_type_'.$resolved_type.'">
			<th colspan="2">Map</th>
			<th>Location</th>
			<th>Levels</th>
			<th colspan="3">Rate</th>
		</tr>';
		if(isset($monster_to_map[$id]['grass']))
		{
			$map_descriptor.='<tr class="item_list_title_type_'.$resolved_type.'">
					<th colspan="7">Grass</th>
				</tr>';
			foreach($monster_to_map[$id]['grass'] as $monster_on_map)
			{
				$map_descriptor.='<tr class="value">';
					if(isset($maps_list[$monster_on_map['map']]))
					{
						if(isset($zone_meta[$maps_list[$monster_on_map['map']]['zone']]))
						{
							$map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.'maps/'.str_replace('.tmx','.html',$monster_on_map['map']).'" title="'.$maps_list[$monster_on_map['map']]['name'].'">'.$maps_list[$monster_on_map['map']]['name'].'</a></td>';
							$map_descriptor.='<td>'.$zone_meta[$maps_list[$monster_on_map['map']]['zone']]['name'].'</td>';
						}
						else
							$map_descriptor.='<td colspan="2"><a href="'.$base_datapack_explorer_site_path.'maps/'.str_replace('.tmx','.html',$monster_on_map['map']).'" title="'.$maps_list[$monster_on_map['map']]['name'].'">'.$maps_list[$monster_on_map['map']]['name'].'</a></td>';
					}
					else
						$map_descriptor.='<td>Unknown map</td><td>&nbsp;</td>';
					$map_descriptor.='<td><img src="/images/datapack-explorer/grass.png" alt="" class="locationimg">Grass</td>
					<td>';
					if($monster_on_map['minLevel']==$monster_on_map['maxLevel'])
						$map_descriptor.=$monster_on_map['minLevel'];
					else
						$map_descriptor.=$monster_on_map['minLevel'].'-'.$monster_on_map['maxLevel'];
					$map_descriptor.='</td>';
					$map_descriptor.='<td colspan="3">'.$monster_on_map['luck'].'%</td>
				</tr>';
			}
		}
		if(isset($monster_to_map[$id]['water']))
		{
			$map_descriptor.='<tr class="item_list_title_type_'.$resolved_type.'">
					<th colspan="7">Water</th>
				</tr>';
			foreach($monster_to_map[$id]['water'] as $monster_on_map)
			{
				$map_descriptor.='<tr class="value">';
					if(isset($maps_list[$monster_on_map['map']]))
						$map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.'maps/'.str_replace('.tmx','.html',$monster_on_map['map']).'" title="'.$maps_list[$monster_on_map['map']]['name'].'">'.$maps_list[$monster_on_map['map']]['name'].'</a></td>';
					else
						$map_descriptor.='<td>Unknown map</td><td>&nbsp;</td>';
					$map_descriptor.='<td><img src="/images/datapack-explorer/water.png" alt="" class="locationimg">Water</td>
					<td>';
					if($monster_on_map['minLevel']==$monster_on_map['maxLevel'])
						$map_descriptor.=$monster_on_map['minLevel'];
					else
						$map_descriptor.=$monster_on_map['minLevel'].'-'.$monster_on_map['maxLevel'];
					$map_descriptor.='</td>';
					$map_descriptor.='<td colspan="3">'.$monster_on_map['luck'].'%</td>
				</tr>';
			}
		}
		if(isset($monster_to_map[$id]['cave']))
		{
			$map_descriptor.='<tr class="item_list_title_type_'.$resolved_type.'">
					<th colspan="7">Cave</th>
				</tr>';
			foreach($monster_to_map[$id]['cave'] as $monster_on_map)
			{
				$map_descriptor.='<tr class="value">';
					if(isset($maps_list[$monster_on_map['map']]))
						$map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.'maps/'.str_replace('.tmx','.html',$monster_on_map['map']).'" title="'.$maps_list[$monster_on_map['map']]['name'].'">'.$maps_list[$monster_on_map['map']]['name'].'</a></td>';
					else
						$map_descriptor.='<td>Unknown map</td><td>&nbsp;</td>';
					$map_descriptor.='<td><img src="/images/datapack-explorer/cave.png" alt="" class="locationimg">Cave</td>
					<td>';
					if($monster_on_map['minLevel']==$monster_on_map['maxLevel'])
						$map_descriptor.=$monster_on_map['minLevel'];
					else
						$map_descriptor.=$monster_on_map['minLevel'].'-'.$monster_on_map['maxLevel'];
					$map_descriptor.='</td>';
					$map_descriptor.='<td colspan="3">'.$monster_on_map['luck'].'%</td>
				</tr>';
			}
		}
		$map_descriptor.='<tr>
			<td colspan="7" class="item_list_endline item_list_title_type_'.$resolved_type.'"></td>
		</tr>
		</table>';
	}

	$content=$template;
	$content=str_replace('${TITLE}',$monster['name'],$content);
	$content=str_replace('${CONTENT}',$map_descriptor,$content);
	$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
	$content=preg_replace("#[\r\n\t]+#isU",'',$content);
	filewrite($datapack_explorer_local_path.'monsters/'.text_operation_do_for_url($monster['name']).'.html',$content);
}

$map_descriptor='';
$map_descriptor.='<table class="item_list item_list_type_normal">
<tr class="item_list_title item_list_title_type_normal">
	<th colspan="3">Monster</th>
</tr>';
foreach($monster_meta as $id=>$monster)
{
	$name=$monster['name'];
	$link=$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($name).'.html';
	$map_descriptor.='<tr class="value">';
	$map_descriptor.='<td>';
	if(file_exists($datapack_path.'monsters/'.$id.'/small.png'))
		$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$id.'/small.png" width="32" height="32" alt="'.$monster['name'].'" title="'.$monster['name'].'" /></a></div>';
	else if(file_exists($datapack_path.'monsters/'.$id.'/small.gif'))
		$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$id.'/small.gif" width="32" height="32" alt="'.$monster['name'].'" title="'.$monster['name'].'" /></a></div>';
	$map_descriptor.='</td>
	<td><a href="'.$link.'">'.$name.'</a></td>';
	$map_descriptor.='<td>';
	$type_list=array();
	foreach($monster['type'] as $type)
		if(isset($type_meta[$type]))
			$type_list[]='<span class="type_label type_label_'.$type.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type.'.html">'.$type_meta[$type]['english_name'].'</a></span>';
	$map_descriptor.='<div class="type_label_list">'.implode(' ',$type_list).'</div>';
	$map_descriptor.='</td>';
	$map_descriptor.='</tr>';
}
$map_descriptor.='<tr>
	<td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
</tr>
</table>';
$content=$template;
$content=str_replace('${TITLE}','Monster list',$content);
$content=str_replace('${CONTENT}',$map_descriptor,$content);
$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
$content=preg_replace("#[\r\n\t]+#isU",'',$content);
filewrite($datapack_explorer_local_path.'monsters.html',$content);

foreach($item_meta as $id=>$item)
{
	if(!is_dir($datapack_explorer_local_path.'items/'))
		mkdir($datapack_explorer_local_path.'items/');
	$map_descriptor='';

	$map_descriptor.='<div class="map item_details">';
		$map_descriptor.='<div class="subblock"><h1>'.$item['name'].'</h1>';
		$map_descriptor.='<h2>#'.$id.'</h2>';
		$map_descriptor.='</div>';
		$map_descriptor.='<div class="value datapackscreenshot">';
		if($item['image']!='' && file_exists($datapack_path.'items/'.$item['image']))
			$map_descriptor.='<img src="'.$base_datapack_site_path.'items/'.$item['image'].'" width="24" height="24" alt="'.$item['name'].'" title="'.$item['name'].'" />';
		$map_descriptor.='</div>';
		if($item['price']>0)
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Price</div><div class="value">'.$item['price'].'$</div></div>';
		else
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Price</div><div class="value">Can\'t be sold</div></div>';
		if($item['description']!='')
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Description</div><div class="value">'.$item['description'].'</div></div>';
		if(isset($item['trap']))
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Trap</div><div class="value">Bonus rate: '.$item['trap'].'x</div></div>';
		if(isset($item['repel']))
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Repel</div><div class="value">Repel the monsters during '.$item['repel'].' steps</div></div>';
		if(isset($item_to_plant[$id]) && isset($plant_meta[$item_to_plant[$id]]))
		{
			$image='';
			if(file_exists($datapack_path.'plants/'.$item_to_plant[$id].'.png'))
				$image.=$base_datapack_site_path.'plants/'.htmlspecialchars($item_to_plant[$id]).'.png';
			elseif(file_exists($datapack_path.'plants/'.$item_to_plant[$id].'.gif'))
				$image.=$base_datapack_site_path.'plants/'.htmlspecialchars($item_to_plant[$id]).'.gif';
			if($image!='')
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle">Plant</div><div class="value">';
				$map_descriptor.='After <b>'.($plant_meta[$item_to_plant[$id]]['fruits']/60).'</b> minutes you will have <b>'.$plant_meta[$item_to_plant[$id]]['quantity'].'</b> fruits';
				$map_descriptor.='<table class="item_list item_list_type_normal">
				<tr class="item_list_title item_list_title_type_normal">
					<th>Seed</th>
					<th>Sprouted</th>
					<th>Taller</th>
					<th>Flowering</th>
					<th>Fruits</th>
				</tr><tr class="value">';
				$map_descriptor.='<td><center><div style="width:16px;height:32px;background-image:url(\''.$image.'\');background-repeat:no-repeat;background-position:0px 0px;"></div></center></td>';
				$map_descriptor.='<td><center><div style="width:16px;height:32px;background-image:url(\''.$image.'\');background-repeat:no-repeat;background-position:-16px 0px;"></div></center></td>';
				$map_descriptor.='<td><center><div style="width:16px;height:32px;background-image:url(\''.$image.'\');background-repeat:no-repeat;background-position:-32px 0px;"></div></center></td>';
				$map_descriptor.='<td><center><div style="width:16px;height:32px;background-image:url(\''.$image.'\');background-repeat:no-repeat;background-position:-48px 0px;"></div></center></td>';
				$map_descriptor.='<td><center><div style="width:16px;height:32px;background-image:url(\''.$image.'\');background-repeat:no-repeat;background-position:-64px 0px;"></div></center></td>';
				$map_descriptor.='</tr><tr>
				<td colspan="5" class="item_list_endline item_list_title_type_normal"></td>
				</tr>
				</table>';
				$map_descriptor.='</div></div>';
			}
		}
		if(isset($item['effect']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Effect</div><div class="value"><ul>';
			if(isset($item['effect']['regeneration']))
			{
				if($item['effect']['regeneration']=='all')
					$map_descriptor.='<li>Regenerate all the hp</li>';
				else
					$map_descriptor.='<li>Regenerate '.$item['effect']['regeneration'].' hp</li>';
			}
			$map_descriptor.='</ul></div></div>';
		}

		if(isset($item_to_crafting[$id]))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Do the item</div><div class="value">';
			$map_descriptor.='<a href="'.$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$item_to_crafting[$id]['doItemId']]['name']).'.html" title="'.$item_meta[$item_to_crafting[$id]['doItemId']]['name'].'">';
				$map_descriptor.='<table><tr><td>';
				if($item_meta[$item_to_crafting[$id]['doItemId']]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$item_to_crafting[$id]['doItemId']]['image']))
					$map_descriptor.='<img src="'.$base_datapack_site_path.'items/'.$item_meta[$item_to_crafting[$id]['doItemId']]['image'].'" width="24" height="24" alt="'.$item_meta[$item_to_crafting[$id]['doItemId']]['name'].'" title="'.$item_meta[$item_to_crafting[$id]['doItemId']]['name'].'" />';
				$map_descriptor.='</td><td>'.$item_meta[$item_to_crafting[$id]['doItemId']]['name'].'</td></tr></table>';
			$map_descriptor.='</a>';
			$map_descriptor.='</div></div>';

			$map_descriptor.='<div class="subblock"><div class="valuetitle">Material</div><div class="value">';
			foreach($item_to_crafting[$id]['material'] as $material=>$quantity)
			{
				$map_descriptor.='<a href="'.$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$material]['name']).'.html" title="'.$item_meta[$material]['name'].'">';
					$map_descriptor.='<table><tr><td>';
					if($item_meta[$material]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$material]['image']))
						$map_descriptor.='<img src="'.$base_datapack_site_path.'items/'.$item_meta[$material]['image'].'" width="24" height="24" alt="'.$item_meta[$material]['name'].'" title="'.$item_meta[$material]['name'].'" />';
					$map_descriptor.='</td><td>';
				if($quantity>1)
					$map_descriptor.=$quantity.'x ';
				$map_descriptor.=$item_meta[$material]['name'].'</td></tr></table>';
				$map_descriptor.='</a>';
			}
			$map_descriptor.='</div></div>';
		}

		if(isset($item_to_evolution[$id]) && count($item_to_evolution[$id])>0)
		{
			$count_evol=0;
			foreach($item_to_evolution[$id] as $evolution)
			{
				if(isset($monster_meta[$evolution['from']]) && isset($monster_meta[$evolution['to']]))
					$count_evol++;
			}
			foreach($item_to_evolution[$id] as $evolution)
			{
				if(isset($monster_meta[$evolution['from']]) && isset($monster_meta[$evolution['to']]))
				{
					$map_descriptor.='<table class="item_list item_list_type_normal">
					<tr class="item_list_title item_list_title_type_normal">
						<th colspan="'.$count_evol.'">Evolve from</th>
					</tr>';
					$map_descriptor.='<tr class="value">';
					$map_descriptor.='<td>';
					$map_descriptor.='<table class="monsterforevolution">';
					if(file_exists($datapack_path.'monsters/'.$evolution['from'].'/front.png'))
						$map_descriptor.='<tr><td><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_meta[$evolution['from']]['name']).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$evolution['from'].'/front.png" width="80" height="80" alt="'.$monster_meta[$evolution['from']]['name'].'" title="'.$monster_meta[$evolution['from']]['name'].'" /></a></td></tr>';
					else if(file_exists($datapack_path.'monsters/'.$evolution['from'].'/front.gif'))
						$map_descriptor.='<tr><td><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_meta[$evolution['from']]['name']).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$evolution['from'].'/front.gif" width="80" height="80" alt="'.$monster_meta[$evolution['from']]['name'].'" title="'.$monster_meta[$evolution['from']]['name'].'" /></a></td></tr>';
					$map_descriptor.='<tr><td class="evolution_name"><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_meta[$evolution['from']]['name']).'.html">'.$monster_meta[$evolution['from']]['name'].'</a></td></tr>';
					$map_descriptor.='</table>';
					$map_descriptor.='</td>';
					$map_descriptor.='</tr>';

					$map_descriptor.='<tr><td class="evolution_type">Evolve with<br /><a href="'.$link.'" title="'.$item_meta[$id]['name'].'">';
					if($item_meta[$id]['image']!='')
						$map_descriptor.='<img src="'.$base_datapack_site_path.'items/'.$item_meta[$id]['image'].'" alt="'.$item_meta[$id]['name'].'" title="'.$item_meta[$id]['name'].'" style="float:left;" />';
					$map_descriptor.=$item_meta[$id]['name'].'</a></td></tr>';

					$map_descriptor.='<tr class="value">';
					$map_descriptor.='<td>';
					$map_descriptor.='<table class="monsterforevolution">';
					if(file_exists($datapack_path.'monsters/'.$evolution['to'].'/front.png'))
						$map_descriptor.='<tr><td><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_meta[$evolution['to']]['name']).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$evolution['to'].'/front.png" width="80" height="80" alt="'.$monster_meta[$evolution['to']]['name'].'" title="'.$monster_meta[$evolution['to']]['name'].'" /></a></td></tr>';
					else if(file_exists($datapack_path.'monsters/'.$evolution['to'].'/front.gif'))
						$map_descriptor.='<tr><td><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_meta[$evolution['to']]['name']).'.html"><img src="'.$base_datapack_site_path.'monsters/'.$evolution['to'].'/front.gif" width="80" height="80" alt="'.$monster_meta[$evolution['to']]['name'].'" title="'.$monster_meta[$evolution['to']]['name'].'" /></a></td></tr>';
					$map_descriptor.='<tr><td class="evolution_name"><a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_meta[$evolution['to']]['name']).'.html">'.$monster_meta[$evolution['to']]['name'].'</a></td></tr>';
					$map_descriptor.='</table>';
					$map_descriptor.='</td>';
					$map_descriptor.='</tr>';

					$map_descriptor.='<tr>
						<th colspan="'.$count_evol.'" class="item_list_endline item_list_title item_list_title_type_normal">Evolve to</th>
					</tr>
					</table>';
				}
			}
		}

	$map_descriptor.='</div>';

	if(isset($item_to_monster[$id]))
	{
		$map_descriptor.='<table class="item_list item_list_type_normal">
		<tr class="item_list_title item_list_title_type_normal">
			<th colspan="2">Monster</th>
			<th>Quantity</th>
			<th>Luck</th>
		</tr>';
		foreach($item_to_monster[$id] as $item_to_monster_list)
		{
			if(isset($monster_meta[$item_to_monster_list['monster']]))
			{
				if($item_to_monster_list['quantity_min']!=$item_to_monster_list['quantity_max'])
					$quantity_text=$item_to_monster_list['quantity_min'].' to '.$item_to_monster_list['quantity_max'];
				else
					$quantity_text=$item_to_monster_list['quantity_min'];
				$name=$monster_meta[$item_to_monster_list['monster']]['name'];
				$link=$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($name).'.html';
				$map_descriptor.='<tr class="value">';
				$map_descriptor.='<td>';
				if(file_exists($datapack_path.'monsters/'.$item_to_monster_list['monster'].'/small.png'))
					$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$item_to_monster_list['monster'].'/small.png" width="32" height="32" alt="'.$monster_meta[$item_to_monster_list['monster']]['name'].'" title="'.$monster_meta[$item_to_monster_list['monster']]['name'].'" /></a></div>';
				else if(file_exists($datapack_path.'monsters/'.$item_to_monster_list['monster'].'/small.gif'))
					$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$item_to_monster_list['monster'].'/small.gif" width="32" height="32" alt="'.$monster_meta[$item_to_monster_list['monster']]['name'].'" title="'.$monster_meta[$item_to_monster_list['monster']]['name'].'" /></a></div>';
				$map_descriptor.='</td>
				<td><a href="'.$link.'">'.$name.'</a></td>';
				$map_descriptor.='<td>'.$quantity_text.'</td>';
				$map_descriptor.='<td>'.$item_to_monster_list['luck'].'%</td>';
				$map_descriptor.='</tr>';
			}
		}
		$map_descriptor.='<tr>
			<td colspan="4" class="item_list_endline item_list_title_type_normal"></td>
		</tr>
		</table>';
	}

	if(isset($items_to_quests[$id]))
	{
		$map_descriptor.='<table class="item_list item_list_type_normal">
		<tr class="item_list_title item_list_title_type_normal">
			<th>Quests</th>
			<th>Quantity rewarded</th>
		</tr>';
		foreach($items_to_quests[$id] as $quest_id=>$quantity)
		{
			if(isset($quests_meta[$quest_id]))
			{
				$map_descriptor.='<tr class="value">';
				$map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.'quests/'.$quest_id.'-'.text_operation_do_for_url($quests_meta[$quest_id]['name']).'.html" title="'.$quests_meta[$quest_id]['name'].'">';
				$map_descriptor.=$quests_meta[$quest_id]['name'];
				$map_descriptor.='</a></td>';
				$map_descriptor.='<td>'.$quantity.'</td>';
				$map_descriptor.='</tr>';
			}
		}
		$map_descriptor.='<tr>
			<td colspan="2" class="item_list_endline item_list_title_type_normal"></td>
		</tr>
		</table>';
	}
	if(isset($items_to_quests_for_step[$id]))
	{
		$full_details=false;
		foreach($items_to_quests_for_step[$id] as $items_to_quests_for_step_details)
		{
			if(isset($quests_meta[$items_to_quests_for_step_details['quest']]))
				if(isset($items_to_quests_for_step_details['monster']) && isset($monster_meta[$items_to_quests_for_step_details['monster']]))
					$full_details=true;
		}
		if($full_details)
			$map_descriptor.='<table class="item_list item_list_type_normal">
			<tr class="item_list_title item_list_title_type_normal">
				<th>Quests</th>
				<th>Quantity needed</th>
				<th colspan="2">Monster</th>
				<th>Luck</th>
			</tr>';
		else
			$map_descriptor.='<table class="item_list item_list_type_normal">
			<tr class="item_list_title item_list_title_type_normal">
				<th>Quests</th>
				<th>Quantity needed</th>
			</tr>';
		foreach($items_to_quests_for_step[$id] as $items_to_quests_for_step_details)
		{
			if(isset($quests_meta[$items_to_quests_for_step_details['quest']]))
			{
				$map_descriptor.='<tr class="value">';
				$map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.'quests/'.$items_to_quests_for_step_details['quest'].'-'.text_operation_do_for_url($quests_meta[$items_to_quests_for_step_details['quest']]['name']).'.html" title="'.$quests_meta[$items_to_quests_for_step_details['quest']]['name'].'">';
				$map_descriptor.=$quests_meta[$items_to_quests_for_step_details['quest']]['name'];
				$map_descriptor.='</a></td>';
				$map_descriptor.='<td>'.$items_to_quests_for_step_details['quantity'].'</td>';
				if(isset($items_to_quests_for_step_details['monster']) && isset($monster_meta[$items_to_quests_for_step_details['monster']]))
				{
					$name=$monster_meta[$items_to_quests_for_step_details['monster']]['name'];
					$link=$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($name).'.html';
					$map_descriptor.='<td>';
					if(file_exists($datapack_path.'monsters/'.$items_to_quests_for_step_details['monster'].'/small.png'))
						$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$items_to_quests_for_step_details['monster'].'/small.png" width="32" height="32" alt="'.$monster_meta[$items_to_quests_for_step_details['monster']]['name'].'" title="'.$monster_meta[$items_to_quests_for_step_details['monster']]['name'].'" /></a></div>';
					else if(file_exists($datapack_path.'monsters/'.$items_to_quests_for_step_details['monster'].'/small.gif'))
						$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$items_to_quests_for_step_details['monster'].'/small.gif" width="32" height="32" alt="'.$monster_meta[$items_to_quests_for_step_details['monster']]['name'].'" title="'.$monster_meta[$items_to_quests_for_step_details['monster']]['name'].'" /></a></div>';
					$map_descriptor.='</td>
					<td><a href="'.$link.'">'.$name.'</a></td>';
					$map_descriptor.='<td>'.$items_to_quests_for_step_details['rate'].'%</td>';
				}
				else if($full_details)
					$map_descriptor.='<td></td><td></td><td></td>';
				$map_descriptor.='</tr>';
			}
		}
		if($full_details)
			$map_descriptor.='<tr>
				<td colspan="5" class="item_list_endline item_list_title_type_normal"></td>
			</tr>
			</table>';
		else
			$map_descriptor.='<tr>
				<td colspan="2" class="item_list_endline item_list_title_type_normal"></td>
			</tr>
			</table>';
	}

	if(isset($item_consumed_by[$id]))
	{
		$map_descriptor.='<table class="item_list item_list_type_normal">
		<tr class="item_list_title item_list_title_type_normal">
			<th>Resource of industry</th>
			<th>Quantity</th>
		</tr>';
		foreach($item_consumed_by[$id] as $industry_id=>$quantity)
		{
			if(isset($industries_meta[$industry_id]))
			{
				$map_descriptor.='<tr class="value">';
				$map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.'industries/'.$industry_id.'.html">';
				$map_descriptor.='Industry #'.$industry_id;
				$map_descriptor.='</a></td>';
				$map_descriptor.='<td>'.$quantity.'</td>';
				$map_descriptor.='</tr>';
			}
		}
		$map_descriptor.='<tr>
			<td colspan="2" class="item_list_endline item_list_title_type_normal"></td>
		</tr>
		</table>';
	}

	if(isset($item_produced_by[$id]))
	{
		$map_descriptor.='<table class="item_list item_list_type_normal">
		<tr class="item_list_title item_list_title_type_normal">
			<th>Product of industry</th>
			<th>Quantity</th>
		</tr>';
		foreach($item_produced_by[$id] as $industry_id=>$quantity)
		{
			if(isset($industries_meta[$industry_id]))
			{
				$map_descriptor.='<tr class="value">';
				$map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.'industries/'.$industry_id.'.html">';
				$map_descriptor.='Industry #'.$industry_id;
				$map_descriptor.='</a></td>';
				$map_descriptor.='<td>'.$quantity.'</td>';
				$map_descriptor.='</tr>';
			}
		}
		$map_descriptor.='<tr>
			<td colspan="2" class="item_list_endline item_list_title_type_normal"></td>
		</tr>
		</table>';
	}

	$content=$template;
	$content=str_replace('${TITLE}',$item['name'],$content);
	$content=str_replace('${CONTENT}',$map_descriptor,$content);
	$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
	$content=preg_replace("#[\r\n\t]+#isU",'',$content);
	filewrite($datapack_explorer_local_path.'items/'.text_operation_do_for_url($item['name']).'.html',$content);
}

$map_descriptor='';

$map_descriptor.='<table class="item_list item_list_type_normal">
<tr class="item_list_title item_list_title_type_normal">
	<th colspan="2">Item</th>
	<th>Price</th>
</tr>';
foreach($item_meta as $id=>$item)
{
	$link=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item['name']).'.html';
	$name=$item['name'];
	if($item['image']!='' && file_exists($datapack_path.'items/'.$item['image']))
		$image=$base_datapack_site_path.'/items/'.$item['image'];
	else
		$image='';
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
	if($item['price']>0)
		$map_descriptor.='<td>'.$item['price'].'$</td>';
	else
		$map_descriptor.='<td>&nbsp;</td>';
	$map_descriptor.='</tr>';
}
$map_descriptor.='<tr>
	<td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
</tr>
</table>';

$content=$template;
$content=str_replace('${TITLE}','Item list',$content);
$content=str_replace('${CONTENT}',$map_descriptor,$content);
$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
$content=preg_replace("#[\r\n\t]+#isU",'',$content);
filewrite($datapack_explorer_local_path.'items.html',$content);

/*foreach($crafting_meta as $id=>$crafting)
{
	if(!is_dir($datapack_explorer_local_path.'crafting/'))
		mkdir($datapack_explorer_local_path.'crafting/');
	$map_descriptor='';

	$map_descriptor.='<div class="map item_details">';
		$map_descriptor.='<div class="subblock"><h1>'.$item_meta[$crafting['itemToLearn']]['name'].'</h1>';
		$map_descriptor.='<h2>#'.$id.'</h2>';
		$map_descriptor.='</div>';
		$map_descriptor.='<div class="value datapackscreenshot">';
		$map_descriptor.='<a href="'.$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$crafting['itemToLearn']]['name']).'.html" title="'.$item_meta[$crafting['itemToLearn']]['name'].'">';
		if($item_meta[$crafting['itemToLearn']]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$crafting['itemToLearn']]['image']))
			$map_descriptor.='<img src="'.$base_datapack_site_path.'items/'.$item_meta[$crafting['itemToLearn']]['image'].'" width="24" height="24" alt="'.$item_meta[$crafting['itemToLearn']]['name'].'" title="'.$item_meta[$crafting['itemToLearn']]['name'].'" />';
		$map_descriptor.='</a>';
		$map_descriptor.='</div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Price</div><div class="value">'.$item_meta[$crafting['itemToLearn']]['price'].'</div></div>';
		//if($crafting['success']!='100')
		//	$map_descriptor.='<div class="subblock"><div class="valuetitle">Success</div><div class="value">'.$crafting['success'].'%</div></div>';

		$map_descriptor.='<div class="subblock"><div class="valuetitle">Do the item</div><div class="value">';
		$map_descriptor.='<a href="'.$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$crafting['doItemId']]['name']).'.html" title="'.$item_meta[$crafting['doItemId']]['name'].'">';
			$map_descriptor.='<table><tr><td>';
			if($item_meta[$crafting['doItemId']]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$crafting['doItemId']]['image']))
				$map_descriptor.='<img src="'.$base_datapack_site_path.'items/'.$item_meta[$crafting['doItemId']]['image'].'" width="24" height="24" alt="'.$item_meta[$crafting['doItemId']]['name'].'" title="'.$item_meta[$crafting['doItemId']]['name'].'" />';
			$map_descriptor.='</td><td>'.$item_meta[$crafting['doItemId']]['name'].'</td></tr></table>';
		$map_descriptor.='</a>';
		$map_descriptor.='</div></div>';

		$map_descriptor.='<div class="subblock"><div class="valuetitle">Material</div><div class="value">';
		foreach($crafting['material'] as $material=>$quantity)
		{
			$map_descriptor.='<a href="'.$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$material]['name']).'.html" title="'.$item_meta[$material]['name'].'">';
				$map_descriptor.='<table><tr><td>';
				if($item_meta[$material]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$material]['image']))
					$map_descriptor.='<img src="'.$base_datapack_site_path.'items/'.$item_meta[$material]['image'].'" width="24" height="24" alt="'.$item_meta[$material]['name'].'" title="'.$item_meta[$material]['name'].'" />';
				$map_descriptor.='</td><td>';
			if($quantity>1)
				$map_descriptor.=$quantity.'x ';
			$map_descriptor.=$item_meta[$material]['name'].'</td></tr></table>';
			$map_descriptor.='</a>';
		}
		$map_descriptor.='</div></div>';
	$map_descriptor.='</div>';
	
	if(isset($item_to_monster[$crafting['itemToLearn']]))
	{
		$map_descriptor.='<table class="item_list item_list_type_normal">
		<tr class="item_list_title item_list_title_type_normal">
			<th colspan="2">Monster</th>
			<th>Quantity</th>
			<th>Luck</th>
		</tr>';
		foreach($item_to_monster[$crafting['itemToLearn']] as $item_to_monster_list)
		{
			if(isset($monster_meta[$item_to_monster_list['monster']]))
			{
				if($item_to_monster_list['quantity_min']!=$item_to_monster_list['quantity_max'])
					$quantity_text=$item_to_monster_list['quantity_min'].' to '.$item_to_monster_list['quantity_max'];
				else
					$quantity_text=$item_to_monster_list['quantity_min'];
				$name=$monster_meta[$item_to_monster_list['monster']]['name'];
				$link=$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($name).'.html';
				$map_descriptor.='<tr class="value">';
				$map_descriptor.='<td>';
				if(file_exists($datapack_path.'monsters/'.$item_to_monster_list['monster'].'/small.png'))
					$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$item_to_monster_list['monster'].'/small.png" width="32" height="32" alt="'.$monster_meta[$item_to_monster_list['monster']]['name'].'" title="'.$monster_meta[$item_to_monster_list['monster']]['name'].'" /></a></div>';
				else if(file_exists($datapack_path.'monsters/'.$item_to_monster_list['monster'].'/small.gif'))
					$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$item_to_monster_list['monster'].'/small.gif" width="32" height="32" alt="'.$monster_meta[$item_to_monster_list['monster']]['name'].'" title="'.$monster_meta[$item_to_monster_list['monster']]['name'].'" /></a></div>';
				$map_descriptor.='</td>
				<td><a href="'.$link.'">'.$name.'</a></td>';
				$map_descriptor.='<td>'.$quantity_text.'</td>';
				$map_descriptor.='<td>'.$item_to_monster_list['luck'].'%</td>';
				$map_descriptor.='</tr>';
			}
		}
		$map_descriptor.='<tr>
			<td colspan="4" class="item_list_endline item_list_title_type_normal"></td>
		</tr>
		</table>';
	}

	$content=$template;
	$content=str_replace('${TITLE}',$item_meta[$crafting['itemToLearn']]['name'],$content);
	$content=str_replace('${CONTENT}',$map_descriptor,$content);
	$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
	$content=preg_replace("#[\r\n\t]+#isU",'',$content);
	filewrite($datapack_explorer_local_path.'crafting/'.text_operation_do_for_url($item_meta[$crafting['itemToLearn']]['name']).'.html',$content);
}*/

$map_descriptor='';

$map_descriptor.='<table class="item_list item_list_type_normal">
<tr class="item_list_title item_list_title_type_normal">
	<th colspan="2">Item</th>
	<th>Price</th>
</tr>';
foreach($crafting_meta as $id=>$crafting)
{
	$link=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$crafting['itemToLearn']]['name']).'.html';
	//$link=$base_datapack_explorer_site_path.'crafting/'.text_operation_do_for_url($item_meta[$crafting['itemToLearn']]['name']).'.html';
	$name=$item_meta[$crafting['itemToLearn']]['name'];
	if($item_meta[$crafting['itemToLearn']]['image']!='')
		$image=$base_datapack_site_path.'/items/'.$item_meta[$crafting['itemToLearn']]['image'];
	else
		$image='';
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
	$map_descriptor.='<td>'.$item_meta[$crafting['itemToLearn']]['price'].'$</td>
	</tr>';
}
$map_descriptor.='<tr>
	<td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
</tr>
</table>';
$content=$template;
$content=str_replace('${TITLE}','Crafting list',$content);
$content=str_replace('${CONTENT}',$map_descriptor,$content);
$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
$content=preg_replace("#[\r\n\t]+#isU",'',$content);
filewrite($datapack_explorer_local_path.'crafting.html',$content);


foreach($industries_meta as $id=>$industry)
{
	if(!is_dir($datapack_explorer_local_path.'industries/'))
		mkdir($datapack_explorer_local_path.'industries/');
	$map_descriptor='';

	$map_descriptor.='<div class="map item_details">';
		$map_descriptor.='<div class="subblock"><h1>Industry #'.$id.'</h1>';
		$map_descriptor.='</div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Time to complet a cycle</div><div class="value">';
		if($industry['time']<(60*2))
			$map_descriptor.=$industry['time'].'s';
		elseif($industry['time']<(60*60*2))
			$map_descriptor.=($industry['time']/60).'mins';
		elseif($industry['time']<(60*60*24*2))
			$map_descriptor.=($industry['time']/(60*60)).'hours';
		else
			$map_descriptor.=($industry['time']/(60*60*24)).'days';
		$map_descriptor.='</div></div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Cycle to be full</div><div class="value">'.$industry['cycletobefull'].'</div></div>';

		$map_descriptor.='<div class="subblock"><div class="valuetitle">Resources</div><div class="value">';
		foreach($industry['resources'] as $material=>$quantity)
		{
			$map_descriptor.='<a href="'.$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$material]['name']).'.html" title="'.$item_meta[$material]['name'].'">';
			$map_descriptor.='<table><tr><td>';
			if($item_meta[$material]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$material]['image']))
				$map_descriptor.='<img src="'.$base_datapack_site_path.'items/'.$item_meta[$material]['image'].'" width="24" height="24" alt="'.$item_meta[$material]['name'].'" title="'.$item_meta[$material]['name'].'" />';
			$map_descriptor.='</td><td>';
			if($quantity>1)
				$map_descriptor.=$quantity.'x ';
			$map_descriptor.=$item_meta[$material]['name'].'</td></tr></table>';
			$map_descriptor.='</a>';
		}
		$map_descriptor.='</div></div>';

		$map_descriptor.='<div class="subblock"><div class="valuetitle">Products</div><div class="value">';
		foreach($industry['products'] as $material=>$quantity)
		{
			$map_descriptor.='<a href="'.$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$material]['name']).'.html" title="'.$item_meta[$material]['name'].'">';
			if($item_meta[$material]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$material]['image']))
				$map_descriptor.='<img src="'.$base_datapack_site_path.'items/'.$item_meta[$material]['image'].'" width="24" height="24" alt="'.$item_meta[$material]['name'].'" title="'.$item_meta[$material]['name'].'" />';
			$map_descriptor.='</td><td>';
			if($quantity>1)
				$map_descriptor.=$quantity.'x ';
			$map_descriptor.=$item_meta[$material]['name'].'</td></tr></table>';
			$map_descriptor.='</a>';
		}
		$map_descriptor.='</div></div>';
	$map_descriptor.='</div>';

	$content=$template;
	$content=str_replace('${TITLE}','Industry #'.$id,$content);
	$content=str_replace('${CONTENT}',$map_descriptor,$content);
	$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
	$content=preg_replace("#[\r\n\t]+#isU",'',$content);
	filewrite($datapack_explorer_local_path.'industries/'.$id.'.html',$content);
}

$map_descriptor='';

$map_descriptor.='<table class="item_list item_list_type_normal">
<tr class="item_list_title item_list_title_type_normal">
	<th>Industry</th>
	<th>Resources</th>
	<th>Products</th>
</tr>';
foreach($industries_meta as $id=>$industry)
{
	$map_descriptor.='<tr class="value">';
	$map_descriptor.='<td>';
	$map_descriptor.='<a href="'.$base_datapack_explorer_site_path.'industries/'.$id.'.html">#'.$id.'</a>';
	$map_descriptor.='</td>';
	$map_descriptor.='<td>';
	foreach($industry['resources'] as $item=>$quantity)
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
	$map_descriptor.='</td>';
	$map_descriptor.='<td>';
	foreach($industry['products'] as $item=>$quantity)
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
	$map_descriptor.='</td>';
	$map_descriptor.='</tr>';
}
$map_descriptor.='<tr>
	<td colspan="3" class="item_list_endline item_list_title_type_normal"></td>
</tr>
</table>';
$content=$template;
$content=str_replace('${TITLE}','Industries list',$content);
$content=str_replace('${CONTENT}',$map_descriptor,$content);
$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
$content=preg_replace("#[\r\n\t]+#isU",'',$content);
filewrite($datapack_explorer_local_path.'industries.html',$content);

$map_descriptor='';
$index=1;
$loadSkinPreview=array();
foreach($start as $entry)
{
	$map_descriptor.='
	<fieldset>
	<legend><h2><strong>'.htmlspecialchars($entry['name']).'</strong></h2></legend>
	<b>'.htmlspecialchars($entry['description']).'</b><br />';
	$map_name='';
	$zone_code='';
	$map_meta='datapack/map/'.str_replace('.tmx','.xml',$entry['map']);
	if(file_exists($map_meta))
	{
		$content=file_get_contents($map_meta);
		if(preg_match('#<name( lang="en")?>.*</name>#isU',$content))
			$map_name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$content);
		else if(preg_match('#<map[^>]*zone="[^"]+"#isU',$content))
		{
			$zone_code=preg_replace('#<map[^>]*zone="([^"]+)"#isU','$1',$content);
			$zone_meta='datapack/map/zone/'.$zone_code.'.xml';
			if(file_exists($zone_meta))
			{
				$content=file_get_contents($zone_meta);
				if(preg_match('#<name( lang="en")?>.*</name>#isU',$content))
					$map_name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$content);
			}
		}
	}
	if($map_name!='')
		$map_descriptor.='Map: <i>'.htmlspecialchars($map_name).'</i><br />';
	$skin_count=0;
	if ($handle = opendir($datapack_path.'skin/fighter/')) {
		while (false !== ($inode = readdir($handle)))
		{
			if(file_exists($datapack_path.'skin/fighter/'.$inode.'/front.png') || file_exists($datapack_path.'skin/fighter/'.$inode.'/front.gif'))
				if(count($entry['forcedskin'])==0 || in_array($inode,$entry['forcedskin']))
					$skin_count++;
		}
		closedir($handle);
	}
	if($skin_count>0)
	{
		$map_descriptor.='Skin: <div id="skin_preview_'.$index.'">';
		if ($handle = opendir($datapack_path.'skin/fighter/')) {
			while (false !== ($inode = readdir($handle)))
			{
				if(file_exists($datapack_path.'skin/fighter/'.$inode.'/front.png') || file_exists($datapack_path.'skin/fighter/'.$inode.'/front.gif'))
					if(count($entry['forcedskin'])==0 || in_array($inode,$entry['forcedskin']))
					{
						if(file_exists($datapack_path.'skin/fighter/'.$inode.'/front.png'))
							$map_descriptor.='<img src="'.$base_datapack_site_path.'skin/fighter/'.$inode.'/front.png" width="80" height="80" alt="Front" style="float:left" />';
						else
							$map_descriptor.='<img src="'.$base_datapack_site_path.'skin/fighter/'.$inode.'/front.gif" width="80" height="80" alt="Front" style="float:left" />';
					}
			}
			closedir($handle);
		}
		$map_descriptor.='</div><br style="clear:both" />';
	}
	else
		$map_descriptor.='Skin: No skin found<br />';
	if($entry['cash']>0)
		$map_descriptor.='Cash: <i>'.htmlspecialchars($entry['cash']).'$</i><br />';
	$map_descriptor.='Monster: <ul style="margin:0px;">';
	foreach($entry['monsters'] as $monster)
		if(array_key_exists($monster['id'],$monster_meta))
		{
			$map_descriptor.='<li>';
			$map_descriptor.='<a href="'.$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($monster_meta[$monster['id']]['name']).'.html">';
			if(file_exists($datapack_path.'monsters/'.$monster['id'].'/front.png'))
				$map_descriptor.='<img src="'.$base_datapack_site_path.'monsters/'.$monster['id'].'/front.png" width="80" height="80" alt="'.htmlspecialchars($monster_meta[$monster['id']]['name']).'" title="'.htmlspecialchars($monster_meta[$monster['id']]['description']).'" /><br />';
			elseif(file_exists($datapack_path.'monsters/'.$monster['id'].'/front.gif'))
				$map_descriptor.='<img src="'.$base_datapack_site_path.'monsters/'.$monster['id'].'/front.gif" width="80" height="80" alt="'.htmlspecialchars($monster_meta[$monster['id']]['name']).'" title="'.htmlspecialchars($monster_meta[$monster['id']]['description']).'" /><br />';
			else
				$map_descriptor.='No skin found!';
			$map_descriptor.='<b>'.htmlspecialchars($monster_meta[$monster['id']]['name']).'</b> level <i>'.htmlspecialchars($monster['level']).'</i>';
			$map_descriptor.='</a>';
			$map_descriptor.='</li>';
		}
		else
			$map_descriptor.='<li>No monster information!</li>';
	$map_descriptor.='</ul>';
	if(count($entry['reputations'])>0)
	{
		$map_descriptor.='Reputations: <ul style="margin:0px;">';
		foreach($entry['reputations'] as $reputation)
		{
			if(array_key_exists($reputation['type'],$reputation_meta))
			{
				if(array_key_exists($reputation['level'],$reputation_meta[$reputation['type']]))
					$map_descriptor.='<li>'.htmlspecialchars($reputation_meta[$reputation['type']][$reputation['level']]).'</li>';
				else
					$map_descriptor.='<li>Unknown reputation '.htmlspecialchars($reputation['type']).' level: '.htmlspecialchars($reputation['level']).'</li>';
			}
			else
				$map_descriptor.='<li>Unknown reputation type: '.htmlspecialchars($reputation['type']).'</li>';
		}
		$map_descriptor.='</ul>';
	}
	if(count($entry['items'])>0)
	{
		$map_descriptor.='Items: <ul style="margin:0px;">';
		foreach($entry['items'] as $item)
		{
			if($item['quantity']<=1)
				$quantity='';
			else
				$quantity=htmlspecialchars($item['quantity']).' ';
			if(array_key_exists($item['id'],$item_meta))
			{
				$map_descriptor.='<li>';
				$map_descriptor.='<a href="'.$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$item['id']]['name']).'.html" title="'.$item_meta[$item['id']]['name'].'">';
				if($item_meta[$item['id']]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$item['id']]['image']))
					$map_descriptor.='<li><img src="'.$base_datapack_site_path.'items/'.htmlspecialchars($item_meta[$item['id']]['image']).'" width="24" height="24" alt="'.htmlspecialchars($item_meta[$item['id']]['description']).'" title="'.htmlspecialchars($item_meta[$item['id']]['description']).'" />'.$quantity.htmlspecialchars($item_meta[$item['id']]['name']).'</li>';
				else
					$map_descriptor.='<li>'.$quantity.htmlspecialchars($item_meta[$item['id']]['name']).'</li>';
				$map_descriptor.='</a>';
				$map_descriptor.='</li>';
			}
			else
				$map_descriptor.='<li>'.$quantity.'unknown item ('.htmlspecialchars($item['id']).')</li>';
		}
		$map_descriptor.='</ul>';
	}
	$map_descriptor.='</fieldset>';
	$index++;
}
$content=$template;
$content=str_replace('${TITLE}','Starter characters',$content);
$content=str_replace('${CONTENT}',$map_descriptor,$content);
$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
$content=preg_replace("#[\r\n\t]+#isU",'',$content);
filewrite($datapack_explorer_local_path.'start.html',$content);

foreach($quests_meta as $id=>$quest)
{
	if(!is_dir($datapack_explorer_local_path.'quests/'))
		mkdir($datapack_explorer_local_path.'quests/');
	$map_descriptor='';

	$map_descriptor.='<div class="map item_details">';
		$map_descriptor.='<div class="subblock"><h1>'.$quest['name'];
		if($quest['repeatable'])
			$map_descriptor.=' (repeatable)';
		else
			$map_descriptor.=' (one time)';
		$map_descriptor.='</h1>';
		$map_descriptor.='<h2>#'.$id.'</h2>';
		$map_descriptor.='</div>';

		if(count($quest['requirements'])>0)
		{
			if(isset($quest['requirements']['quests']))
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle">Requirements</div><div class="value">';
				foreach($quest['requirements']['quests'] as $quest_id)
				{
					$map_descriptor.='Quest: <a href="'.$base_datapack_explorer_site_path.'quests/'.$quest_id.'-'.text_operation_do_for_url($quests_meta[$quest_id]['name']).'.html" title="'.$quests_meta[$quest_id]['name'].'">';
					$map_descriptor.=$quests_meta[$quest_id]['name'];
					$map_descriptor.='</a><br />';
				}
				$map_descriptor.='</div></div>';
			}
		}
		if(count($quest['steps'])>0)
		{
			foreach($quest['steps'] as $id_step=>$step)
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle">Step #'.$id_step.'</div><div class="value">';
				$map_descriptor.=$step['text'];
				if(count($step['items']))
				{
					$show_full=false;
					foreach($step['items'] as $item)
					{
						if(isset($item['monster']))
						{
							if(isset($monster_meta[$item['monster']]))
								$show_full=true;
						}
					}
					$map_descriptor.='<table class="item_list item_list_type_outdoor"><tr class="item_list_title item_list_title_type_outdoor">';
					if($show_full)
						$map_descriptor.='<th colspan="2">Item</th><th colspan="2">Monster</th><th>Luck</th></tr>';
					else
						$map_descriptor.='<th colspan="2">Item</th></tr>';
					foreach($step['items'] as $item)
					{
						$map_descriptor.='<tr class="value"><td>';
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
						if($item['quantity']>1)
							$quantity_text=$item['quantity'].' ';
						if($image!='')
						{
							if($link!='')
								$map_descriptor.='<a href="'.$link.'">';
							$map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
							if($link!='')
								$map_descriptor.='</a>';
						}
						$map_descriptor.='</td><td>';
						if($link!='')
							$map_descriptor.='<a href="'.$link.'">';
						if($name!='')
							$map_descriptor.=$quantity_text.$name;
						else
							$map_descriptor.=$quantity_text.'Unknown item';
						if($link!='')
							$map_descriptor.='</a>';
						$map_descriptor.='</td>';
						if(isset($item['monster']))
						{
							if(isset($monster_meta[$item['monster']]))
							{
								$name=$monster_meta[$item['monster']]['name'];
								$link=$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($name).'.html';
								$map_descriptor.='<td>';
								if(file_exists($datapack_path.'monsters/'.$item['monster'].'/small.png'))
									$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$item['monster'].'/small.png" width="32" height="32" alt="'.$monster_meta[$item['monster']]['name'].'" title="'.$monster_meta[$item['monster']]['name'].'" /></a></div>';
								else if(file_exists($datapack_path.'monsters/'.$item['monster'].'/small.gif'))
									$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$item['monster'].'/small.gif" width="32" height="32" alt="'.$monster_meta[$item['monster']]['name'].'" title="'.$monster_meta[$item['monster']]['name'].'" /></a></div>';
								$map_descriptor.='</td>
								<td><a href="'.$link.'">'.$name.'</a></td>';
								$map_descriptor.='<td>'.$item['rate'].'%</td>';
							}
							else if($show_full)
								$map_descriptor.='<td></td><td></td><td></td>';
						}
						else if($show_full)
							$map_descriptor.='<td></td><td></td><td></td>';
						$map_descriptor.='</tr>';
					}
					if($show_full)
						$map_descriptor.='<tr>
						<td colspan="5" class="item_list_endline item_list_title_type_outdoor"></td>
						</tr></table>';
					else
						$map_descriptor.='<tr>
						<td colspan="2" class="item_list_endline item_list_title_type_outdoor"></td>
						</tr></table>';
					$map_descriptor.='<br />';
				}
				$map_descriptor.='</div></div>';
			}
		}
		if(count($quest['rewards'])>0)
		{
			if(isset($quest['rewards']['items']) || isset($quest['rewards']['reputation']))
			{
				$map_descriptor.='<div class="subblock"><div class="valuetitle">Rewards</div><div class="value">';
				if(isset($quest['rewards']['items']))
				{
					$map_descriptor.='<table class="item_list item_list_type_outdoor"><tr class="item_list_title item_list_title_type_outdoor">
					<th colspan="2">Item</th></tr>';
					foreach($quest['rewards']['items'] as $item)
					{
						$map_descriptor.='<tr class="value"><td>';
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
						if($item['quantity']>1)
							$quantity_text=$item['quantity'].' ';
						
						if($image!='')
						{
							if($link!='')
								$map_descriptor.='<a href="'.$link.'">';
							$map_descriptor.='<img src="'.$image.'" width="24" height="24" alt="'.$name.'" title="'.$name.'" />';
							if($link!='')
								$map_descriptor.='</a>';
						}
						$map_descriptor.='</td><td>';
						if($link!='')
							$map_descriptor.='<a href="'.$link.'">';
						if($name!='')
							$map_descriptor.=$quantity_text.$name;
						else
							$map_descriptor.=$quantity_text.'Unknown item';
						if($link!='')
							$map_descriptor.='</a>';
						$map_descriptor.='</td></tr>';
					}
					$map_descriptor.='<tr>
					<td colspan="2" class="item_list_endline item_list_title_type_outdoor"></td>
					</tr></table>';
				}
				if(isset($quest['rewards']['reputation']))
					foreach($quest['rewards']['reputation'] as $reputation)
					{
						if($reputation['point']<0)
							$map_descriptor.='Less reputation in: '.$reputation['type'];
						else
							$map_descriptor.='More reputation in: '.$reputation['type'];
					}
				$map_descriptor.='</div></div>';
			}
		}
	$map_descriptor.='</div>';

	$content=$template;
	$content=str_replace('${TITLE}',$quest['name'],$content);
	$content=str_replace('${CONTENT}',$map_descriptor,$content);
	$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
	$content=preg_replace("#[\r\n\t]+#isU",'',$content);
	filewrite($datapack_explorer_local_path.'quests/'.$id.'-'.text_operation_do_for_url($quest['name']).'.html',$content);
}

$map_descriptor='';

$map_descriptor.='<table class="item_list item_list_type_normal">
<tr class="item_list_title item_list_title_type_normal">
	<th>Quests</th>
</tr>';
foreach($quests_meta as $id=>$quest)
{
	$map_descriptor.='<tr class="value">
	<td><a href="'.$base_datapack_explorer_site_path.'quests/'.$id.'-'.text_operation_do_for_url($quest['name']).'.html" title="'.$quest['name'].'">'.$quest['name'].'</a></td>';
	$map_descriptor.='</tr>';
}
$map_descriptor.='<tr>
	<td colspan="1" class="item_list_endline item_list_title_type_normal"></td>
</tr>
</table>';

$content=$template;
$content=str_replace('${TITLE}','Quests list',$content);
$content=str_replace('${CONTENT}',$map_descriptor,$content);
$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
$content=preg_replace("#[\r\n\t]+#isU",'',$content);
filewrite($datapack_explorer_local_path.'quests.html',$content);

foreach($type_meta as $type=>$type_content)
{
	if(!is_dir($datapack_explorer_local_path.'monsters/'))
		mkdir($datapack_explorer_local_path.'monsters/');
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
	$map_descriptor.='<div class="map monster_type_'.$type.'">';
		$map_descriptor.='<div class="subblock"><h1>'.$type_content['english_name'].'</h1></div>';
		$map_descriptor.='<div class="subblock"><div class="valuetitle">Type</div><div class="value">';
		$map_descriptor.='<div class="type_label_list"><span class="type_label type_label_'.$type.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type.'.html">'.$type_meta[$type]['english_name'].'</a></span></div></div></div>';
		if(isset($effectiveness_list['4']) || isset($effectiveness_list['2']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Weak to</div><div class="value">';
			$type_list=array();
			if(isset($effectiveness_list['2']))
				foreach($effectiveness_list['2'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">2x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.$type_meta[$type_effectiveness]['english_name'].'</a></span>';
			if(isset($effectiveness_list['4']))
				foreach($effectiveness_list['4'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">4x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.$type_meta[$type_effectiveness]['english_name'].'</a></span>';
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>';
		}
		if(isset($effectiveness_list['0.25']) || isset($effectiveness_list['0.5']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Resistant to</div><div class="value">';
			$type_list=array();
			if(isset($effectiveness_list['0.25']))
				foreach($effectiveness_list['0.25'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">0.25x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.$type_meta[$type_effectiveness]['english_name'].'</a></span>';
			if(isset($effectiveness_list['0.5']))
				foreach($effectiveness_list['0.5'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">0.5x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.$type_meta[$type_effectiveness]['english_name'].'</a></span>';
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>';
		}
		if(isset($effectiveness_list['0']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Immune to</div><div class="value">';
			$type_list=array();
			foreach($effectiveness_list['0'] as $type_effectiveness)
				if(isset($type_meta[$type_effectiveness]))
					$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.$type_meta[$type_effectiveness]['english_name'].'</a></span>';
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>';
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
			
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Effective against</div><div class="value">';
			$type_list=array();
			if(isset($effectiveness_list['2']))
				foreach($effectiveness_list['2'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">2x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.$type_meta[$type_effectiveness]['english_name'].'</a></span>';
			if(isset($effectiveness_list['4']))
				foreach($effectiveness_list['4'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">4x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.$type_meta[$type_effectiveness]['english_name'].'</a></span>';
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>';
		}
		if(isset($effectiveness_list['0.25']) || isset($effectiveness_list['0.5']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Not effective against</div><div class="value">';
			$type_list=array();
			if(isset($effectiveness_list['0.25']))
				foreach($effectiveness_list['0.25'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">0.25x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.$type_meta[$type_effectiveness]['english_name'].'</a></span>';
			if(isset($effectiveness_list['0.5']))
				foreach($effectiveness_list['0.5'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">0.5x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.$type_meta[$type_effectiveness]['english_name'].'</a></span>';
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>';
		}
		if(isset($effectiveness_list['0']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Useless against</div><div class="value">';
			$type_list=array();
			foreach($effectiveness_list['0'] as $type_effectiveness)
				if(isset($type_meta[$type_effectiveness]))
					$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.$type_meta[$type_effectiveness]['english_name'].'</a></span>';
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>';
		}
	$map_descriptor.='</div>';
	$second_type_displayed='';
	if(isset($type_to_monster[$type]) && count($type_to_monster[$type])>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$type.'">
		<tr class="item_list_title item_list_title_type_'.$type.'">
			<th colspan="2">Monster</th>
			<th>Type</th>
		</tr>';
		foreach($type_to_monster[$type] as $second_type=>$second_type_content)
		{
			if($second_type_displayed!=$second_type)
			{
				if($second_type==$type)
					$map_descriptor.='<tr class="item_list_title_type_'.$second_type.'"><th colspan="3">'.$type_meta[$second_type]['english_name'].'</th></tr>';
				else
					$map_descriptor.='<tr class="item_list_title_type_'.$second_type.'"><th colspan="3">'.$type_meta[$type]['english_name'].' - '.$type_meta[$second_type]['english_name'].'</th></tr>';
				$second_type_displayed=$second_type;
			}
			foreach($second_type_content as $monster)
			{
				if(isset($monster_meta[$monster]))
				{
					$name=$monster_meta[$monster]['name'];
					$link=$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($name).'.html';
					$map_descriptor.='<tr class="value">
						<td>';
						if(file_exists($datapack_path.'monsters/'.$monster.'/small.png'))
							$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$monster.'/small.png" width="32" height="32" alt="'.$monster_meta[$monster]['name'].'" title="'.$monster_meta[$monster]['name'].'" /></a></div>';
						else if(file_exists($datapack_path.'monsters/'.$monster.'/small.gif'))
							$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$monster.'/small.gif" width="32" height="32" alt="'.$monster_meta[$monster]['name'].'" title="'.$monster_meta[$monster]['name'].'" /></a></div>';
						$map_descriptor.='</td>
						<td><a href="'.$link.'">'.$name.'</a></td>';
						$type_list=array();
						foreach($monster_meta[$monster]['type'] as $type_monster)
							if(isset($type_meta[$type_monster]))
								$type_list[]='<span class="type_label type_label_'.$type_monster.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_monster.'.html">'.$type_meta[$type_monster]['english_name'].'</a></span>';
						$map_descriptor.='<td><div class="type_label_list">'.implode(' ',$type_list).'</div></td>';
					$map_descriptor.='</tr>';
				}
			}
		}
		$map_descriptor.='<tr>
			<td colspan="3" class="item_list_endline item_list_title_type_'.$type.'"></td>
		</tr>
		</table>';
	}

	$content=$template;
	$content=str_replace('${TITLE}',$type_content['english_name'],$content);
	$content=str_replace('${CONTENT}',$map_descriptor,$content);
	$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
	$content=preg_replace("#[\r\n\t]+#isU",'',$content);
	filewrite($datapack_explorer_local_path.'monsters/type-'.$type.'.html',$content);
}

$map_descriptor='';
$map_descriptor.='<table class="item_list item_list_type_normal">
<tr class="item_list_title item_list_title_type_normal">
	<th>Type</th>
	<th>Monster with this type</th>
</tr>';
foreach($type_meta as $type=>$type_content)
{
	$map_descriptor.='<tr class="value">';
	$map_descriptor.='<td><div class="type_label_list"><span class="type_label type_label_'.$type.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type.'.html">'.$type_content['english_name'].'</a></span></div></td>';
	$count=0;
	if(isset($type_to_monster[$type]))
		foreach($type_to_monster[$type] as $second_type=>$second_type_content)
			$count+=count($second_type_content);
	//foreach($type_to_monster as $first_type=>$first_type_content)
		//foreach($first_type_content as $second_type=>$second_type_content)
	$map_descriptor.='<td>'.$count.'</td>';
	$map_descriptor.='</tr>';
}
$map_descriptor.='<tr>
	<td colspan="2" class="item_list_endline item_list_title_type_normal"></td>
</tr>
</table>';
$content=$template;
$content=str_replace('${TITLE}','Type list',$content);
$content=str_replace('${CONTENT}',$map_descriptor,$content);
$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
$content=preg_replace("#[\r\n\t]+#isU",'',$content);
filewrite($datapack_explorer_local_path.'types.html',$content);

foreach($skill_meta as $skill_id=>$skill)
{
	if(!is_dir($datapack_explorer_local_path.'monsters/'))
		mkdir($datapack_explorer_local_path.'monsters/');
	if(!is_dir($datapack_explorer_local_path.'monsters/skills/'))
		mkdir($datapack_explorer_local_path.'monsters/skills/');
	$map_descriptor='';

	$type=$skill['type'];
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
	$map_descriptor.='<div class="map monster_type_'.$type.'">';
		$map_descriptor.='<div class="subblock"><h1>'.$skill['name'].'</h1></div>';
		$map_descriptor.='<div class="type_label_list"><span class="type_label type_label_'.$type.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type.'.html">'.$type_meta[$type]['english_name'].'</a></span></div>';
		if(isset($effectiveness_list['2']) || isset($effectiveness_list['4']))
		{
			
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Effective against</div><div class="value">';
			$type_list=array();
			if(isset($effectiveness_list['2']))
				foreach($effectiveness_list['2'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">2x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.$type_meta[$type_effectiveness]['english_name'].'</a></span>';
			if(isset($effectiveness_list['4']))
				foreach($effectiveness_list['4'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">4x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.$type_meta[$type_effectiveness]['english_name'].'</a></span>';
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>';
		}
		if(isset($effectiveness_list['0.25']) || isset($effectiveness_list['0.5']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Not effective against</div><div class="value">';
			$type_list=array();
			if(isset($effectiveness_list['0.25']))
				foreach($effectiveness_list['0.25'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">0.25x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.$type_meta[$type_effectiveness]['english_name'].'</a></span>';
			if(isset($effectiveness_list['0.5']))
				foreach($effectiveness_list['0.5'] as $type_effectiveness)
					if(isset($type_meta[$type_effectiveness]))
						$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'">0.5x: <a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.$type_meta[$type_effectiveness]['english_name'].'</a></span>';
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>';
		}
		if(isset($effectiveness_list['0']))
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Useless against</div><div class="value">';
			$type_list=array();
			foreach($effectiveness_list['0'] as $type_effectiveness)
				if(isset($type_meta[$type_effectiveness]))
					$type_list[]='<span class="type_label type_label_'.$type_effectiveness.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_effectiveness.'.html">'.$type_meta[$type_effectiveness]['english_name'].'</a></span>';
			$map_descriptor.=implode(' ',$type_list);
			$map_descriptor.='</div></div>';
		}
		foreach($skill['level_list'] as $level=>$effect)
		{
			$map_descriptor.='<div class="subblock"><div class="valuetitle">Level '.$level.'</div><div class="value">';
			$map_descriptor.='Endurance: '.$effect['endurance'].'<br />';
			if($effect['sp']!='0')
				$map_descriptor.='Skill point/SP (to learn): '.$effect['sp'].'<br />';
			else
				$map_descriptor.='You can\'t learn this skill<br />';
			if($effect['life_quantity']!='0' || $effect['life_quantity']!='0%')
				$map_descriptor.='Life quantity: '.$effect['life_quantity'].'<br />';
			if($effect['base_level_luck']!='100')
				$map_descriptor.='Luck: '.$effect['base_level_luck'].'%<br />';
			$map_descriptor.='</div></div>';
		}
	$map_descriptor.='</div>';
	$skill_level_displayed=0;
	if(isset($skill_to_monster[$skill_id]) && count($skill_to_monster[$skill_id])>0)
	{
		$map_descriptor.='<table class="item_list item_list_type_'.$type.'">
		<tr class="item_list_title item_list_title_type_'.$type.'">
			<th colspan="2">Monster</th>
			<th>Type</th>';
			if(count($skill_to_monster[$skill_id])>1)
				$map_descriptor.='<th>Skill level</th>';
		$map_descriptor.='</tr>';
		foreach($skill_to_monster[$skill_id] as $skill_level=>$monster_list_content)
		{
			if($skill_level_displayed!=$skill_level && count($skill_to_monster[$skill_id])>1)
			{
				$map_descriptor.='<tr class="item_list_title_type_'.$type.'"><th colspan="4">Level '.$skill_level.'</th></tr>';
				$skill_level_displayed=$skill_level;
			}
			foreach($monster_list_content as $monster)
			{
				if(isset($monster_meta[$monster]))
				{
					$name=$monster_meta[$monster]['name'];
					$link=$base_datapack_explorer_site_path.'monsters/'.text_operation_do_for_url($name).'.html';
					$map_descriptor.='<tr class="value">
						<td>';
						if(file_exists($datapack_path.'monsters/'.$monster.'/small.png'))
							$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$monster.'/small.png" width="32" height="32" alt="'.$monster_meta[$monster]['name'].'" title="'.$monster_meta[$monster]['name'].'" /></a></div>';
						else if(file_exists($datapack_path.'monsters/'.$monster.'/small.gif'))
							$map_descriptor.='<div class="monstericon"><a href="'.$link.'"><img src="'.$base_datapack_site_path.'monsters/'.$monster.'/small.gif" width="32" height="32" alt="'.$monster_meta[$monster]['name'].'" title="'.$monster_meta[$monster]['name'].'" /></a></div>';
						$map_descriptor.='</td>
						<td><a href="'.$link.'">'.$name.'</a></td>';
						$type_list=array();
						foreach($monster_meta[$monster]['type'] as $type_monster)
							if(isset($type_meta[$type_monster]))
								$type_list[]='<span class="type_label type_label_'.$type_monster.'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$type_monster.'.html">'.$type_meta[$type_monster]['english_name'].'</a></span>';
						$map_descriptor.='<td><div class="type_label_list">'.implode(' ',$type_list).'</div></td>';
						if(count($skill_to_monster[$skill_id])>1)
							$map_descriptor.='<td>'.$skill_level.'</td>';
					$map_descriptor.='</tr>';
				}
			}
		}
		$map_descriptor.='<tr>
			<td colspan="';
			if(count($skill_to_monster[$skill_id])>1)
				$map_descriptor.='4';
			else
				$map_descriptor.='3';
			$map_descriptor.='" class="item_list_endline item_list_title_type_'.$type.'"></td>
		</tr>
		</table>';
	}

	$content=$template;
	$content=str_replace('${TITLE}',$skill['name'],$content);
	$content=str_replace('${CONTENT}',$map_descriptor,$content);
	$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
	$content=preg_replace("#[\r\n\t]+#isU",'',$content);
	filewrite($datapack_explorer_local_path.'monsters/skills/'.text_operation_do_for_url($skill['name']).'.html',$content);
}

$map_descriptor='';
$map_descriptor.='<table class="item_list item_list_type_normal">
<tr class="item_list_title item_list_title_type_normal">
	<th>Skill</th>
	<th>Type</th>
	<th>Endurance</th>
	<th>Number of level</th>
</tr>';
foreach($skill_meta as $skill_id=>$skill)
{
	$map_descriptor.='<tr class="value">';
	$map_descriptor.='<td><a href="'.$base_datapack_explorer_site_path.'monsters/skills/'.text_operation_do_for_url($skill['name']).'.html">'.$skill['name'].'</a></td>';
	if(isset($type_meta[$skill['type']]))
		$map_descriptor.='<td><span class="type_label type_label_'.$skill['type'].'"><a href="'.$base_datapack_explorer_site_path.'monsters/type-'.$skill['type'].'.html">'.$type_meta[$skill['type']]['english_name'].'</a></span></td>';
	else
		$map_descriptor.='<td>&nbsp;</td>';
	if(isset($skill['level_list'][1]))
		$map_descriptor.='<td>'.$skill['level_list'][1]['endurance'].'</td>';
	else
		$map_descriptor.='<td>&nbsp;</td>';
	$map_descriptor.='<td>'.count($skill['level_list']).'</td>';
	$map_descriptor.='</tr>';
}
$map_descriptor.='<tr>
	<td colspan="4" class="item_list_endline item_list_title_type_normal"></td>
</tr>
</table>';
$content=$template;
$content=str_replace('${TITLE}','Skills list',$content);
$content=str_replace('${CONTENT}',$map_descriptor,$content);
$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
$content=preg_replace("#[\r\n\t]+#isU",'',$content);
filewrite($datapack_explorer_local_path.'skills.html',$content);


$map_descriptor='';
$map_descriptor.='<table class="item_list item_list_type_normal">
<tr class="item_list_title item_list_title_type_normal">
	<th colspan="2">Plant</th>
	<th colspan="2">Time to grow</th>
	<th>Fruits produced</th>
</tr>';
foreach($plant_meta as $id=>$plant)
{
	$link=$base_datapack_explorer_site_path.'items/'.text_operation_do_for_url($item_meta[$plant['itemUsed']]['name']).'.html';
	$name=$item_meta[$plant['itemUsed']]['name'];
	if($item_meta[$plant['itemUsed']]['image']!='' && file_exists($datapack_path.'items/'.$item_meta[$plant['itemUsed']]['image']))
		$image=$base_datapack_site_path.'/items/'.$item_meta[$plant['itemUsed']]['image'];
	else
		$image='';
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
	$map_descriptor.='<td>';
	if(file_exists($datapack_path.'plants/'.$id.'.png'))
		$map_descriptor.='<img src="'.$base_datapack_site_path.'plants/'.$id.'.png" width="80" height="32" alt="'.$name.'" title="'.$name.'" />';
	$map_descriptor.='</td>';
	$map_descriptor.='<td><b>'.($plant['fruits']/60).'</b> minutes</td>';
	$map_descriptor.='<td>'.$plant['quantity'].'</td>';
	$map_descriptor.='</tr>';
}
$map_descriptor.='<tr>
	<td colspan="5" class="item_list_endline item_list_title_type_normal"></td>
</tr>
</table>';

$content=$template;
$content=str_replace('${TITLE}','Plants list',$content);
$content=str_replace('${CONTENT}',$map_descriptor,$content);
$content=str_replace('${AUTOGEN}',$automaticallygen,$content);
$content=preg_replace("#[\r\n\t]+#isU",'',$content);
filewrite($datapack_explorer_local_path.'plants.html',$content);