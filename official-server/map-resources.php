<?php
$player_owned_expire_at=60*60*24;
$factory_price_change=20;

$is_up=true;
require '../config.php';
$datapackexplorergeneratorinclude=true;
require 'datapack-explorer-generator/function.php';

if($postgres_db_base['host']!='localhost')
    $postgres_link_base = pg_connect('dbname='.$postgres_db_base['database'].' user='.$postgres_login.' password='.$postgres_pass.' host='.$postgres_db_base['host']);
else
    $postgres_link_base = pg_connect('dbname='.$postgres_db_base['database'].' user='.$postgres_login.' password='.$postgres_pass);
if($postgres_link_base===FALSE)
    $is_up=false;

if($is_up)
{
    $is_up=false;
    foreach($postgres_db_tree as $common_server_content)
    {
        $is_up=true;
        if($common_server_content['host']!='localhost')
            $postgres_link_common = pg_connect('dbname='.$common_server_content['database'].' user='.$postgres_login.' password='.$postgres_pass.' host='.$common_server_content['host']);
        else
            $postgres_link_common = pg_connect('dbname='.$common_server_content['database'].' user='.$postgres_login.' password='.$postgres_pass);
        if($postgres_link_common===FALSE)
            $is_up=false;
        else
        {
            $is_up=false;
            foreach($common_server_content['servers'] as $server_content)
            {
                $is_up=true;
                if($server_content['host']!='localhost')
                    $postgres_link_server = pg_connect('dbname='.$server_content['database'].' user='.$postgres_login.' password='.$postgres_pass.' host='.$server_content['host']);
                else
                    $postgres_link_server = pg_connect('dbname='.$server_content['database'].' user='.$postgres_login.' password='.$postgres_pass);
                if($postgres_link_server===FALSE)
                    $is_up=false;
                break;
            }
        }
        break;
    }
}

$item_meta=array();
$temp_items=getXmlList($datapack_path.'items/');
foreach($temp_items as $item_file)
{
    $content=file_get_contents('../datapack/items/'.$item_file);
    preg_match_all('#<item[^>]*>.*</item>#isU',$content,$entry_list);
    foreach($entry_list[0] as $entry)
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
            $image=$id.'.png';
        $image=preg_replace('#[^/]+$#isU','',$item_file).$image;
        if(!preg_match('#<name( lang="en")?>.*</name>#isU',$entry))
            continue;
        $name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$entry);
        if(preg_match('#<description( lang="en")?>.*</description>#isU',$entry))
            $description=preg_replace('#^.*<description( lang="en")?>(.*)</description>.*$#isU','$2',$entry);
        else
            $description='';
        $item_meta[$id]=array('price'=>$price,'image'=>$image,'name'=>$name,'description'=>$description);
    }
}
ksort($item_meta);

$industrie_meta=array();
$industrie_link_meta=array();
if($handle = opendir('../datapack/industries/')) {
	while(false !== ($entry = readdir($handle))) {
	if($entry != '.' && $entry != '..') {
			$content=file_get_contents('../datapack/industries/'.$entry);
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
				preg_match_all('#<resource[^>]+/>#isU',$entry,$resource_list);
				foreach($resource_list[0] as $resource)
				{
					if(!preg_match('#<resource[^>]*id="([0-9]+)"#isU',$resource))
						continue;
					$quantity=1;
					$item=preg_replace('#^.*<resource[^>]*id="([0-9]+)".*$#isU','$1',$resource);
					if(preg_match('#<resource[^>]*quantity="([0-9]+)"#isU',$resource))
						$quantity=preg_replace('#^.*<resource[^>]*quantity="([0-9]+)".*$#isU','$1',$resource);
					$resources[]=array('item'=>$item,'quantity'=>$quantity);
				}
				//product
				$products=array();
				preg_match_all('#<product[^>]+/>#isU',$entry,$product_list);
				foreach($product_list[0] as $product)
				{
					if(!preg_match('#<product[^>]*id="([0-9]+)"#isU',$product))
						continue;
					$quantity=1;
					$item=preg_replace('#^.*<product[^>]*id="([0-9]+)".*$#isU','$1',$product);
					if(preg_match('#<product[^>]*quantity="([0-9]+)"#isU',$product))
						$quantity=preg_replace('#^.*<product[^>]*quantity="([0-9]+)".*$#isU','$1',$product);
					$products[]=array('item'=>$item,'quantity'=>$quantity);
				}
				$industrie_meta[$id]=array('time'=>$time,'cycletobefull'=>$cycletobefull,'resources'=>$resources,'products'=>$products);
			}
		}
	}
	closedir($handle);
}
ksort($industrie_meta);
ksort($industrie_link_meta);

$plant_meta=array();
if(file_exists('../datapack/plants/plants.xml'))
{
	$content=file_get_contents('../datapack/plants/plants.xml');
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
		$plant_meta[$id]=array('itemUsed'=>$itemUsed,'fruits'=>$fruits,'quantity'=>$quantity);
	}
}
ksort($plant_meta);

function getFactoryResourcePrice($quantityInStock,$resource,$industry)
{
	global $factory_price_change,$item_meta;
	$max_items=$resource['quantity']*$industry['cycletobefull'];
	$price_temp_change=($max_items-$quantityInStock)*($factory_price_change*2)/$max_items;
	return $item_meta[$resource['item']]['price']*(100-$factory_price_change+$price_temp_change)/100;
}

function getFactoryProductPrice($quantityInStock,$product,$industry,$factory_price_change)
{
	global $factory_price_change,$item_meta;
	$max_items=$resource['quantity']*$industry['cycletobefull'];
	$price_temp_change=($max_items-$quantityInStock)*($factory_price_change*2)/$max_items;
	return $item_meta[$resource['item']]['price']*(100-$factory_price_change+$price_temp_change)/100;
}

function industryStatusWithCurrentTime($industryStatus,$industry)
{
	global $factory_price_change,$item_meta;
	//do the generated item
	$timeIntervalCount=0;
	if($industryStatus['last_update']<time())
	{
		$timeIntervalCount=(time()-$industryStatus['last_update'])/$industry['time'];
		if($timeIntervalCount>$industry['cycletobefull'])
			$timeIntervalCount=$industry['cycletobefull'];
	}
	$index=0;
	$doOneProduct=($timeIntervalCount>0);
	while($doOneProduct)
	{
		$index=0;
		if($doOneProduct)
			while($index<count($industry['resources']))
			{
				$resource=$industry['resources'][$index];
				if(isset($industryStatus['resources'][$resource['item']]))
					$quantityInStock=$industryStatus['resources'][$resource['item']];
				else
					$quantityInStock=0;
				if($resource['quantity']>$quantityInStock)
				{
					$industryStatus['last_update']=time();
					$doOneProduct=false;
					break;
				}
				$index++;
			}
		$index=0;
		if($doOneProduct)
			while($index<count($industry['products']))
			{
				$product=$industry['products'][$index];
				if(isset($industryStatus['products'][$product['item']]))
					$quantityInStock=$industryStatus['products'][$product['item']];
				else
					$quantityInStock=0;
				if($quantityInStock>=$resource['quantity']*$industry['cycletobefull'])
				{
					$industryStatus['last_update']=time();
					$doOneProduct=false;
					break;
				}
				$index++;
			}
		if($timeIntervalCount<=0)
			break;
		if($doOneProduct)
		{
			$industryStatus['last_update']+=$industry['time'];
			$index=0;
			while($index<count($industry['resources']))
			{
				$industryStatus['resources'][$industry['resources'][$index]['item']]-=$industry['resources'][$index]['quantity'];
				$index++;
			}
			$index=0;
			while($index<count($industry['products']))
			{
				$industryStatus['products'][$industry['products'][$index]['item']]+=$industry['products'][$index]['quantity'];
				$index++;
			}
			$timeIntervalCount--;
		}
	}
	return $industryStatus;
}

?>
<?php
$title='Map ressources - CatchChallenger MMORPG';
$description='Map ressources - CatchChallenger, Independent Old school Opensource MMORPG/Single player game';
$keywords='Map ressources,catchchallenger,catch challenger,catch challenger,community';
include '../template/top.php';
include '../template/top2.php';
include '../template/topdatapack.php';
?>
				<div id="title">Map resources</div>
				<br />
				<br />
				<img src="/images/pixel.png" width="96" height="96" style="float:left; margin-right:7px;" class="tiers_img" alt="" />
				<?php
				if(!$is_up)
					echo '<p class="text">The map resources list is <span style="color:red;"><b>closed</b></span>.<br /></p>';
				else
				{
					echo '<p class="text">The plants mature on the map:<br />';
					echo '<table>';
					echo '<tr class="tiers_img">';
					echo '<td></td>';
					echo '<td>Plant</td>';
					echo '<td>Location</td>';
					echo '<td></td>';
					echo '<td>Player</td>';
					echo '</tr>';
                    $skin_list=array();
                    $map_list=array();
					$index=1;
					$reply = pg_query($postgres_link_server,'SELECT * FROM plant ORDER BY plant_timestamps LIMIT 30') or die(pg_last_error());
					while($data = pg_fetch_array($reply))
					{
						if($data['plant_timestamps']>time())
							continue;
						if(!isset($plant_meta[$data['plant']]))
							continue;
						if(!isset($item_meta[$plant_meta[$data['plant']]['itemUsed']]))
							continue;
						if((time()-$data['plant_timestamps'])<($plant_meta[$data['plant']]['fruits']))
							continue;
						echo '<tr>';
						echo '<td><a href="/official-server/datapack-explorer/items/'.str_replace(' ','-',strtolower($item_meta[$plant_meta[$data['plant']]['itemUsed']]['name'])).'.html">';
						if(file_exists('../datapack/plants/'.$data['plant'].'.png'))
							echo '<div style="width:16px;height:32px;background-image:url(\'/datapack/plants/'.htmlspecialchars($data['plant']).'.png\');background-repeat:no-repeat;background-position:-64px 0px;float:left;"></div>';
						elseif(file_exists('../datapack/plants/'.$data['plant'].'.gif'))
							echo '<div style="width:16px;height:32px;background-image:url(\'/datapack/plants/'.htmlspecialchars($data['plant']).'.gif\');background-repeat:no-repeat;background-position:-64px 0px;float:left;"></div>';
						echo '</td></a>';
						echo '<td>'.htmlspecialchars($item_meta[$plant_meta[$data['plant']]['itemUsed']]['name']).'</td>';
                        if(isset($map_list[$data['map']]))
                            $map=$map_list[$data['map']];
                        else
                        {
                            $reply_map = pg_query($postgres_link_server,'SELECT map FROM dictionary_map WHERE id='.$data['map']) or die(pg_last_error());
                            if($data_map = pg_fetch_array($reply_map))
                                $map=$data_map['map'];
                            else
                                $map='default';
                            $map_list[$data['map']]=$map;
                        }
						echo '<td><a href="/official-server/datapack-explorer/maps/'.$map.'.html">';
						$zone_text='';
						$zone_meta='../datapack/map/'.$map.'.xml';
						if(file_exists($zone_meta))
						{
							$content=file_get_contents($zone_meta);
							if(preg_match('#<name( lang="en")?>.*</name>#isU',$content))
								$zone_text=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$content);
						}
						echo $zone_text;
						echo '</a></td>';
						if((time()-$data['plant_timestamps'])>$player_owned_expire_at)
							echo '<td></td><td></td>';
						else
						{
							$reply_player = pg_query($postgres_link_common,'SELECT * FROM character WHERE id='.$data['character']) or die(pg_last_error());
							if($data_player = pg_fetch_array($reply_player))
							{
								echo '<td>';
                                if(isset($skin_list[$data_player['skin']]))
                                    $skin=$skin_list[$data_player['skin']];
                                else
                                {
                                    $reply_skin = pg_query($postgres_link_base,'SELECT skin FROM dictionary_skin WHERE id='.$data_player['skin']) or die(pg_last_error());
                                    if($data_skin = pg_fetch_array($reply_skin))
                                        $skin=$data_skin['skin'];
                                    else
                                        $skin='default';
                                    $skin_list[$data_player['skin']]=$skin;
                                }
								if(file_exists('../datapack/skin/fighter/'.$skin.'/trainer.png'))
									echo '<div style="width:16px;height:24px;background-image:url(\'../datapack/skin/fighter/'.htmlspecialchars($skin).'/trainer.png\');background-repeat:no-repeat;background-position:-16px -48px;float:left;"></div>';
								elseif(file_exists('../datapack/skin/fighter/'.$skin.'/trainer.gif'))
									echo '<div style="width:16px;height:24px;background-image:url(\'../datapack/skin/fighter/'.htmlspecialchars($skin).'/trainer.gif\');background-repeat:no-repeat;background-position:-16px -48px;float:left;"></div>';
								echo '</td><td>'.htmlspecialchars($data_player['pseudo']).'</td>';
							}
							else
								echo '<td></td><td></td>';
						}
						echo '</tr>';
						$index++;
					}
					echo '</table>';
					echo '</p>';
					echo '<p class="text">Industries\'s products on the map:<br />';
					echo '<table>';
					echo '<tr class="tiers_img">';
					echo '<td></td>';
					echo '<td>Product</td>';
					echo '<td>Quantity</td>';
					echo '<td>Average price</td>';
					echo '<td>Best price</td>';
					echo '</tr>';
					$products_tot=array();
					foreach($industrie_link_meta as $factory=>$industry)
					{
						$reply_factory = pg_query($postgres_link_server,'SELECT * FROM factory WHERE id='.$factory) or die(pg_last_error());
						if($data_factory = pg_fetch_array($reply_factory))
						{
							$data_factory_resources=array();
							$data_factory_resources_bin=explode(';',$data_factory['resources']);
							foreach($data_factory_resources_bin as $tempString)
							{
								$tempSplit=explode('=>',$tempString);
								if(count($tempSplit)==2)
								{
									if(preg_match('#^[0-9]+$#',$tempSplit[0]) && preg_match('#^[0-9]+$#',$tempSplit[1]))
										$data_factory_resources[$tempSplit[0]]=$tempSplit[1];
								}
							}
							$data_factory_products=array();
							$data_factory_products_bin=explode(';',$data_factory['products']);
							foreach($data_factory_products_bin as $tempString)
							{
								$tempSplit=explode('=>',$tempString);
								if(count($tempSplit)==2)
								{
									if(preg_match('#^[0-9]+$#',$tempSplit[0]) && preg_match('#^[0-9]+$#',$tempSplit[1]))
										$data_factory_resources[$tempSplit[0]]=$tempSplit[1];
								}
							}
							$industryStatus=industryStatusWithCurrentTime(
								array(
									'last_update'=>$data_factory['last_update'],
									'resources'=>$data_factory_resources,
									'products'=>$data_factory_products,
								),
								$industrie_meta[$industry]);
						}
						else
						{
							$industryStatus=
							array(
								'last_update'=>time(),
								'resources'=>array(),
								'products'=>array(),
							);
						}
						if(isset($industrie_meta[$industry]))
							foreach($industrie_meta[$industry]['products'] as $products)
							{
								if(isset($item_meta[$products['item']]))
								{
									$item=$products['item'];
									if(isset($industryStatus['resources'][$item]))
										$quantityInStock=$industryStatus['resources'][$item];
									else
										$quantityInStock=0;
									$price=getFactoryResourcePrice($quantityInStock,$products,$industrie_meta[$industry]);
									if(!isset($products_tot[$item]))
										$products_tot[$item]=array('quantityInStock'=>$quantityInStock,'price_multiplied_by_quantity'=>($quantityInStock*$price),'best_price'=>$price);
									else
									{
										$products_tot[$item]['quantityInStock']+=$quantityInStock;
										$products_tot[$item]['price_multiplied_by_quantity']+=($quantityInStock*$price);
										if($price<$products_tot[$item]['best_price'])
											$products_tot[$item]['best_price']=$price;
									}
								}
							}
					}
					foreach($products_tot as $item => $infos)
					{
						if(isset($industryStatus['resources'][$item]))
							$quantityInStock=$industryStatus['resources'][$item];
						else
							$quantityInStock=0;
						if($quantityInStock<=0)
							continue;
						echo '<tr>';
						echo '<td>';
						if(array_key_exists($item,$item_meta))
						{
							if($item_meta[$item]['image']!='' && file_exists('../datapack/items/'.$item_meta[$item]['image']))
								echo '<a href="/official-server/datapack-explorer/items/'.str_replace(' ','-',strtolower($item_meta[$item]['name'])).'.html"><img src="../datapack/items/'.htmlspecialchars($item_meta[$item]['image']).'" width="24" height="24" alt="'.htmlspecialchars($item_meta[$item]['description']).'" title="'.htmlspecialchars($item_meta[$item]['description']).'" style="float:left" /></a>';
						}
						echo '</td>';
						echo '<td><a href="/official-server/datapack-explorer/items/'.str_replace(' ','-',strtolower($item_meta[$item]['name'])).'.html">';
						if(array_key_exists($item,$item_meta))
							echo htmlspecialchars($item_meta[$item]['name']);
						else
							echo 'Unknown item ('.htmlspecialchars($item).')';
						echo '</td></a>';
						echo '<td>'.htmlspecialchars($infos['quantity_needed']).'</td>';
						$average_price=(float)$infos['price_multiplied_by_quantity']/(float)$infos['quantity_needed'];
						if($average_price<9)
							$precision=2;
						else if($average_price<999)
							$precision=1;
						else
							$precision=0;
						echo '<td>'.htmlspecialchars(round($average_price,$precision)).'$</td>';
						echo '<td>'.htmlspecialchars($infos['best_price']).'$</td>';
						echo '</tr>';
					}
					echo '</table>';
					echo '</p>';
					echo '<p class="text">Industries\'s resources on the map:<br />';
					echo '<table>';
					echo '<tr class="tiers_img">';
					echo '<td></td>';
					echo '<td>Resource</td>';
					echo '<td>Quantity</td>';
					echo '<td>Average price</td>';
					echo '<td>Best price</td>';
					echo '</tr>';
					$resources_tot=array();
					foreach($industrie_link_meta as $factory=>$industry)
					{
						$reply_factory = pg_query($postgres_link_server,'SELECT * FROM factory WHERE id='.$factory) or die(pg_last_error());
						if($data_factory = pg_fetch_array($reply_factory))
						{
							$data_factory_resources=array();
							$data_factory_resources_bin=explode(';',$data_factory['resources']);
							foreach($data_factory_resources_bin as $tempString)
							{
								$tempSplit=explode('=>',$tempString);
								if(count($tempSplit)==2)
								{
									if(preg_match('#^[0-9]+$#',$tempSplit[0]) && preg_match('#^[0-9]+$#',$tempSplit[1]))
										$data_factory_resources[$tempSplit[0]]=$tempSplit[1];
								}
							}
							$data_factory_products=array();
							$data_factory_products_bin=explode(';',$data_factory['products']);
							foreach($data_factory_products_bin as $tempString)
							{
								$tempSplit=explode('=>',$tempString);
								if(count($tempSplit)==2)
								{
									if(preg_match('#^[0-9]+$#',$tempSplit[0]) && preg_match('#^[0-9]+$#',$tempSplit[1]))
										$data_factory_resources[$tempSplit[0]]=$tempSplit[1];
								}
							}
							$industryStatus=industryStatusWithCurrentTime(
								array(
									'last_update'=>$data_factory['last_update'],
									'resources'=>$data_factory_resources,
									'products'=>$data_factory_products,
								),
								$industrie_meta[$industry]);
						}
						else
						{
							$industryStatus=
							array(
								'last_update'=>time(),
								'resources'=>array(),
								'products'=>array(),
							);
						}
						if(isset($industrie_meta[$industry]))
							foreach($industrie_meta[$industry]['resources'] as $resources)
							{
								if(isset($item_meta[$resources['item']]))
								{
									$item=$resources['item'];
									if(isset($industryStatus['resources'][$item]))
										$quantityInStock=$industryStatus['resources'][$item];
									else
										$quantityInStock=0;
									$quantity_needed=$resources['quantity']*$industrie_meta[$industry]['cycletobefull']-$quantityInStock;
									if($quantity_needed<=0)
										continue;
									$price=getFactoryResourcePrice($quantityInStock,$resources,$industrie_meta[$industry]);
									if(!isset($resources_tot[$item]))
										$resources_tot[$item]=array('quantity_needed'=>$quantity_needed,'price_multiplied_by_quantity'=>($quantity_needed*$price),'best_price'=>$price);
									else
									{
										$resources_tot[$item]['quantity_needed']+=$quantity_needed;
										$resources_tot[$item]['price_multiplied_by_quantity']+=($quantity_needed*$price);
										if($price>$resources_tot[$item]['best_price'])
											$resources_tot[$item]['best_price']=$price;
									}
								}
							}
					}
					foreach($resources_tot as $item => $infos)
					{
						if(isset($industryStatus['resources'][$item]))
							$quantityInStock=$industryStatus['resources'][$item];
						else
							$quantityInStock=0;
						$quantity_needed=$resources['quantity']*$industrie_meta[$industry]['cycletobefull']-$quantityInStock;
						if($quantity_needed<=0)
							continue;
						echo '<tr>';
						echo '<td>';
						if(array_key_exists($item,$item_meta))
						{
							if($item_meta[$item]['image']!='' && file_exists('../datapack/items/'.$item_meta[$item]['image']))
								echo '<a href="/official-server/datapack-explorer/items/'.str_replace(' ','-',strtolower($item_meta[$item]['name'])).'.html"><img src="../datapack/items/'.htmlspecialchars($item_meta[$item]['image']).'" width="24" height="24" alt="'.htmlspecialchars($item_meta[$item]['description']).'" title="'.htmlspecialchars($item_meta[$item]['description']).'" style="float:left" /></a>';
						}
						echo '</td>';
						echo '<td><a href="/official-server/datapack-explorer/items/'.str_replace(' ','-',strtolower($item_meta[$item]['name'])).'.html">';
						if(array_key_exists($item,$item_meta))
							echo htmlspecialchars($item_meta[$item]['name']);
						else
							echo 'unknown item ('.htmlspecialchars($item).')';
						echo '</a></td>';
						echo '<td>'.htmlspecialchars($infos['quantity_needed']).'</td>';
						$average_price=(float)$infos['price_multiplied_by_quantity']/(float)$infos['quantity_needed'];
						if($average_price<9)
							$precision=2;
						else if($average_price<999)
							$precision=1;
						else
							$precision=0;
						echo '<td>'.htmlspecialchars(round($average_price,$precision)).'$</td>';
						echo '<td>'.htmlspecialchars($infos['best_price']).'$</td>';
						echo '</tr>';
					}
					echo '</table>';
					echo '</p>';
				}
				?>
<?php
include '../template/bottom2.php';
include '../template/bottom.php';
