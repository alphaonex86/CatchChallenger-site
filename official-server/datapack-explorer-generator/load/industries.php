<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into load industries'."\n");

$industrie_meta=array();
$industrie_link_meta=array();
$item_produced_by=array();
$item_consumed_by=array();

function industrie_parse($industrie_path,$content)
{
    global $industrie_meta,$industrie_link_meta,$item_produced_by,$item_consumed_by;

    $new_content=array();
    preg_match_all('#<link[^>]+/>#isU',$content,$entry_list);
    $new_content=array_merge($new_content,$entry_list[0]);
    preg_match_all('#<link[^>]+[^/]>.+</link>#isU',$content,$entry_list);
    $new_content=array_merge($new_content,$entry_list[0]);
    foreach($new_content as $entry)
    {
        if(!preg_match('# industrialrecipe="([0-9]+)"#isU',$entry))
            continue;
        if(!preg_match('# industry="([0-9]+)"#isU',$entry))
            continue;
        $industry_id=preg_replace('#^.* industrialrecipe="([0-9]+)".*$#isU','$1',$entry);
        $factory_id=preg_replace('#^.* industry="([0-9]+)".*$#isU','$1',$entry);
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
                $requirements[]=array('type'=>$type,'level'=>$level);
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
                if(!preg_match('# level="([0-9]+)"#isU',$entry))
                    continue;
                $type=preg_replace('#^.* type="([^"]+)".*$#isU','$1',$entry);
                $level=preg_replace('#^.* level="([0-9]+)".*$#isU','$1',$entry);
                $rewards[]=array('type'=>$type,'level'=>$level);
            }
        }
        if(!isset($industrie_link_meta[$industrie_path][$factory_id]))
            $industrie_link_meta[$industrie_path][$factory_id]=array('industry_id'=>$industry_id,'requirements'=>$requirements,'rewards'=>$rewards);
        else
            echo 'industries link with industry="'.$factory_id.'" already found'."\n";
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
            if(preg_match('#<resource[^>]*quantity="([0-9]+)"#isU',$resource))
                $quantity=preg_replace('#^.*<resource[^>]*quantity="([0-9]+)".*$#isU','$1',$resource);
            $item_consumed_by[$item][$industrie_path][$id]=$quantity;
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
            if(preg_match('#<product[^>]*quantity="([0-9]+)"#isU',$product))
                $quantity=preg_replace('#^.*<product[^>]*quantity="([0-9]+)".*$#isU','$1',$product);
            $item_produced_by[$item][$industrie_path][$id]=$quantity;
            $products[]=array('item'=>$item,'quantity'=>$quantity);
        }
        $industrie_meta[$industrie_path][$id]=array('time'=>$time,'cycletobefull'=>$cycletobefull,'resources'=>$resources,'products'=>$products);
    }
}

if(is_dir($datapack_path.'industries/'))
{
	if($handle = opendir($datapack_path.'industries/'))
    {
		while(false !== ($entry = readdir($handle)))
        {
            if($entry != '.' && $entry != '..')
            {
                $content=file_get_contents($datapack_path.'industries/'.$entry);
                industrie_parse('',$content);
            }
        }
		closedir($handle);
	}
}

$dir = $datapack_path.'map/main/';
$dh  = opendir($dir);
while (false !== ($maindatapackcode = readdir($dh)))
{
    if(is_dir($datapack_path.'map/main/'.$maindatapackcode) && preg_match('#^[a-z0-9]+$#isU',$maindatapackcode))
    {
        if(is_dir($datapack_path.'map/main/'.$maindatapackcode.'/industries/'))
        {
            if($handle = opendir($datapack_path.'map/main/'.$maindatapackcode.'/industries/'))
            {
                while(false !== ($entry = readdir($handle)))
                {
                    if($entry != '.' && $entry != '..')
                    {
                        $content=file_get_contents($datapack_path.'map/main/'.$maindatapackcode.'/industries/'.$entry);
                        industrie_parse($maindatapackcode,$content);
                    }
                }
                closedir($handle);
            }
        }
    }
}
closedir($dh);

ksort($industrie_meta);
ksort($industrie_link_meta);
ksort($item_produced_by);
ksort($item_consumed_by);
