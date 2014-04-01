<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into load plans');

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
			echo 'duplicate id '.$id.' for plant';
			continue;
		}
		$itemUsed=preg_replace('#^.*<plant[^>]+itemUsed="([0-9]+)".*</plant>.*$#isU','$1',$entry);
		$fruits=preg_replace('#^.*<fruits>([0-9]+)</fruits>.*$#isU','$1',$entry)*60;
		$quantity=preg_replace('#^.*<quantity>([0-9]+(\\.[0-9]+)?)</quantity>.*$#isU','$1',$entry);
		$item_to_plant[$itemUsed]=$id;
		$plant_meta[$id]=array('itemUsed'=>$itemUsed,'fruits'=>$fruits,'quantity'=>$quantity);
	}
}