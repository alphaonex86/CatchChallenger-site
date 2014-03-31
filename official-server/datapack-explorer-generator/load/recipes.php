<?php
if(!isset($datapackexplorergeneratorinclude))
	exit;

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
