<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into load plans'."\n");

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
		if(isset($plant_meta[$id]))
		{
			echo 'duplicate id '.$id.' for plant'."\n";
			continue;
		}
		$itemUsed=preg_replace('#^.*<plant[^>]+itemUsed="([0-9]+)".*</plant>.*$#isU','$1',$entry);
		$fruits=preg_replace('#^.*<fruits>([0-9]+)</fruits>.*$#isU','$1',$entry)*60;
		$quantity=preg_replace('#^.*<quantity>([0-9]+(\\.[0-9]+)?)</quantity>.*$#isU','$1',$entry);
		$item_to_plant[$itemUsed]=$id;

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

		$plant_meta[$id]=array('itemUsed'=>$itemUsed,'fruits'=>$fruits,'quantity'=>$quantity,'rewards'=>$rewards,'requirements'=>$requirements);
	}
}
ksort($plant_meta);