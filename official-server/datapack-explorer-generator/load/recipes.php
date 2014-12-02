<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into load recipes'."\n");

$crafting_meta=array();
$item_to_crafting=array();
$doItemId_to_crafting=array();
$material_to_crafting=array();
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
		if(isset($crafting_meta[$id]))
		{
			echo 'duplicate id '.$id.' for the crafting recipe'."\n";
			continue;
		}
		$itemToLearn=preg_replace('#^.*<recipe[^>]*itemToLearn="([0-9]+)".*</recipe>.*$#isU','$1',$entry);
		if(!preg_match('#<recipe[^>]*doItemId="[0-9]+".*</recipe>#isU',$entry))
			continue;
		$doItemId=preg_replace('#^.*<recipe[^>]*doItemId="([0-9]+)".*</recipe>.*$#isU','$1',$entry);
		$material=array();
		preg_match_all('#<material[^>]*>#isU',$entry,$temp_material_list);
		foreach($temp_material_list[0] as $material_text)
		{
            if(!preg_match('# itemId="[0-9]+"#isU',$material_text))
                continue;
			$itemId=preg_replace('#^.* itemId="([0-9]+)".*$#isU','$1',$material_text);
			$quantity=1;
			if(preg_match('#<material[^>]+quantity="([0-9]+)"#isU',$material_text))
				$quantity=preg_replace('#^.*<material[^>]+quantity="([0-9]+)".*$#isU','$1',$material_text);
			$material[$itemId]=$quantity;

            if(!isset($material_to_crafting[$itemId]))
                $material_to_crafting[$itemId]=array();
            $material_to_crafting[$itemId][]=$itemToLearn;
		}
        $requirements=array();
        preg_match_all('#<requirements>(.+)</requirements>#isU',$entry,$requirements_list);
        foreach($requirements_list[0] as $requirements_text)
        {
            preg_match_all('#<reputation([^>]+)/>#isU',$requirements_text,$requirements_entry_list);
            foreach($requirements_entry_list[0] as $requirements_entry_text)
            {
                if(!preg_match('# type="([^"]+)"#isU',$entry))
                    continue;
                if(!preg_match('# level="([0-9]+)"#isU',$entry))
                    continue;
                $type=preg_replace('#^.* type="([^"]+)".*$#isU','$1',$entry);
                $level=preg_replace('#^.* level="([0-9]+)".*$#isU','$1',$entry);
                if(!isset($requirements['reputation']))
                    $requirements['reputation']=array();
                $requirements['reputation'][]=array('type'=>$type,'level'=>$level);
            }
        }
        $rewards=array();
        preg_match_all('#<rewards>(.+)</rewards>#isU',$entry,$rewards_list);
        foreach($rewards_list[0] as $rewards_text)
        {
            preg_match_all('#<reputation([^>]+)/>#isU',$rewards_text,$rewards_entry_list);
            foreach($rewards_entry_list[0] as $rewards_entry_text)
            {
                if(!preg_match('# type="([^"]+)"#isU',$entry))
                    continue;
                if(!preg_match('# point="([0-9]+)"#isU',$entry))
                    continue;
                $type=preg_replace('#^.* type="([^"]+)".*$#isU','$1',$entry);
                $point=preg_replace('#^.* point="([0-9]+)".*$#isU','$1',$entry);
                if(!isset($rewards['reputation']))
                    $rewards['reputation']=array();
                $rewards['reputation'][]=array('type'=>$type,'point'=>$point);
            }
        }
        if(count($material)>0)
        {
            $crafting_meta[$id]=array('itemToLearn'=>$itemToLearn,'doItemId'=>$doItemId,'material'=>$material,'requirements'=>$requirements,'rewards'=>$rewards);
            $item_to_crafting[$itemToLearn]=array('doItemId'=>$doItemId,'material'=>$material,'requirements'=>$requirements,'rewards'=>$rewards,'crafting_id'=>$id);
            if(!isset($doItemId_to_crafting[$doItemId]))
                $doItemId_to_crafting[$doItemId]=array();
            $doItemId_to_crafting[$doItemId][]=$itemToLearn;
        }
        else
            echo 'material list is empty for recipe '.$id."\n";
	}
}
ksort($crafting_meta);