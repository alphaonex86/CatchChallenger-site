<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into load industries'."\n");

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
					if(isset($industrie_meta[$id]))
					{
						echo 'duplicate id '.$id.' for the industries'."\n";
						continue;
					}
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