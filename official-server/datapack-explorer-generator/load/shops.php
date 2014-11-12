<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into load shop'."\n");

$shop_meta=array();
$item_to_shop=array();
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
		if(isset($shop_meta[$id]))
		{
			echo 'duplicate id '.$id.' for the shop'."\n";
			continue;
		}
		$products=array();
		preg_match_all('#<product[^>]* itemId="([0-9]+)"[^>]*>#isU',$entry,$monster_text_list);
		foreach($monster_text_list[0] as $monster_text)
		{
			$item=preg_replace('#^.* itemId="([0-9]+)".*$#isU','$1',$monster_text);
			if(isset($item_meta[$item]))
			{
				if(!preg_match('#^.* overridePrice="([0-9]+)".*$#isU',$monster_text))
					$price=$item_meta[$item]['price'];
				else
					$price=preg_replace('#^.* overridePrice="([0-9]+)".*$#isU','$1',$monster_text);
                if($price!=0)
                {
                    $products[$item]=$price;
                    if(!isset($item_to_shop[$item]))
                        $item_to_shop[$item]=array();
                    $item_to_shop[$item][]=$id;
                }
                else
                {
                    echo 'item with price 0 found '.$item.' for the shop'.$id."\n";
                    continue;
                }
            
			}
			else
			{
				echo 'item not found '.$item.' for the shop'.$id."\n";
				continue;
			}
		}
		$shop_meta[$id]=array('products'=>$products);
	}
}
ksort($shop_meta);