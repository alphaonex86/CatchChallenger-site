<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into load bots'."\n");

$bots_meta=array();
$bots_name_count=array();
foreach($bots_file as $file=>$value)
{
	$content=file_get_contents($datapack_path.'map/'.$file);
	preg_match_all('#<bot id="([0-9]+)">(.*)</bot>#isU',$content,$temp_text_list);
	foreach($temp_text_list[0] as $bot_text)
	{
		$id=preg_replace('#^.*<bot id="([0-9]+)">(.*)</bot>.*$#isU','$1',$bot_text);
		$bot_text=preg_replace('#^.*<bot id="([0-9]+)">(.*)</bot>.*$#isU','$2',$bot_text);
		if(isset($bots_meta[$id]))
			echo 'bot with id '.$id.' is already found'."\n";
		else
		{
			$name='';
			if(preg_match('#<name( lang="en")?>.*</name>#isU',$bot_text))
			{
				$name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$bot_text);
                $name=str_replace('<![CDATA[','',str_replace(']]>','',$name));
				if(isset($bots_name_count[$name]))
					$bots_name_count[$name]++;
				else
					$bots_name_count[$name]=1;
			}
			$bots_meta[$id]=array('name'=>$name,'onlytext'=>true,'step'=>array());
			$temp_step_list=explode('<step',$bot_text);
			foreach($temp_step_list as $step_text)
			{
				if(preg_match('#^[^>]* id="([0-9]+)".*$#isU',$step_text))
				{
					$step_id=preg_replace('#^[^>]* id="([0-9]+)".*$#isU','$1',$step_text);
					if(isset($bots_meta[$id]['step'][$step_id]))
						echo 'step with id '.$step_id.' for bot '.$id.' is already found'."\n";
					else
					{
						if(preg_match('#^[^>]* type="([a-z]+)".*$#isU',$step_text))
						{
							$step_type=preg_replace('#^[^>]* type="([a-z]+)".*$#isU','$1',$step_text);
							if($step_type=='text')
							{
								$step_text=preg_replace('#^.*<text( lang="en")?>('.preg_quote('<![CDATA[').')?(.*)('.preg_quote(']]>').')?</text>.*$#isU','$3',$step_text);
								$step_text=str_replace('<![CDATA[','',$step_text);
								$step_text=str_replace(']]>','',$step_text);
								$bots_meta[$id]['step'][$step_id]=array('type'=>$step_type,'text'=>$step_text);
							}
							else if($step_type=='fight')
							{
								if(preg_match('#^.*fightid="([0-9]+)".*$#isU',$step_text))
								{
									$fightid=preg_replace('#^.*fightid="([0-9]+)".*$#isU','$1',$step_text);
									if(isset($fight_meta[$fightid]))
									{
                                        $leader=false;
                                        if(preg_match('#leader="true"#isU',$step_text))
                                            $leader=true;
										$bots_meta[$id]['onlytext']=false;
										$bots_meta[$id]['step'][$step_id]=array('type'=>$step_type,'fightid'=>$fightid,'leader'=>$leader);
									}
									else
										echo 'fightid not found: '.$fightid.' for step with id '.$step_id.' for bot '.$id."\n";
								}
								else
									echo 'fightid attribute not found for step with id '.$step_id.' for bot '.$id."\n";
							}
							else if($step_type=='heal')
							{
								$bots_meta[$id]['onlytext']=false;
								$bots_meta[$id]['step'][$step_id]=array('type'=>$step_type);
							}
							else if($step_type=='learn')
							{
								$bots_meta[$id]['onlytext']=false;
								$bots_meta[$id]['step'][$step_id]=array('type'=>$step_type);
							}
							else if($step_type=='warehouse')
							{
								$bots_meta[$id]['onlytext']=false;
								$bots_meta[$id]['step'][$step_id]=array('type'=>$step_type);
							}
							else if($step_type=='market')
							{
								$bots_meta[$id]['onlytext']=false;
								$bots_meta[$id]['step'][$step_id]=array('type'=>$step_type);
							}
							else if($step_type=='clan')
							{
								$bots_meta[$id]['onlytext']=false;
								$bots_meta[$id]['step'][$step_id]=array('type'=>$step_type);
							}
							else if($step_type=='shop')
							{
								if(preg_match('#^.*shop="([0-9]+)".*$#isU',$step_text))
								{
									$shop=preg_replace('#^.*shop="([0-9]+)".*$#isU','$1',$step_text);
									if(isset($shop_meta[$shop]))
									{
										$bots_meta[$id]['onlytext']=false;
										$bots_meta[$id]['step'][$step_id]=array('type'=>$step_type,'shop'=>$shop);
									}
									else
										echo 'shop: '.$shop.' not found for step with id '.$step_id.' for bot '.$id."\n";
								}
								else
									echo 'shop attribute not found for step with id '.$step_id.' for bot '.$id.', $step_text: '.$step_text."\n";
							}
							else if($step_type=='sell')
							{
								if(preg_match('#^.*shop="([0-9]+)".*$#isU',$step_text))
								{
									$shop=preg_replace('#^.*shop="([0-9]+)".*$#isU','$1',$step_text);
									if(isset($shop_meta[$shop]))
									{
										$bots_meta[$id]['onlytext']=false;
										$bots_meta[$id]['step'][$step_id]=array('type'=>$step_type,'shop'=>$shop);
									}
									else
										echo 'shop: '.$shop.' not found for step with id '.$step_id.' for bot '.$id."\n";
								}
								else
									echo 'sell attribute not found for step with id '.$step_id.' for bot '.$id."\n";
							}
							else if($step_type=='zonecapture')
							{
								if(preg_match('#^.*zone="([^"]+)".*$#isU',$step_text))
								{
									$zone=preg_replace('#^.*zone="([^"]+)".*$#isU','$1',$step_text);
									if(isset($zone_meta[$zone]))
									{
										$bots_meta[$id]['onlytext']=false;
										$bots_meta[$id]['step'][$step_id]=array('type'=>$step_type,'zone'=>$zone);
									}
									else
										echo 'zone: '.$zone.' not found for step with id '.$step_id.' for bot '.$id."\n";
								}
								else
									echo 'zone attribute not found for step with id '.$step_id.' for bot '.$id."\n";
							}
							else if($step_type=='industry')
							{
								if(preg_match('#^.*industry="([0-9]+)".*$#isU',$step_text))
								{
									$industry=preg_replace('#^.*industry="([0-9]+)".*$#isU','$1',$step_text);
									if(isset($industrie_link_meta[$industry]))
									{
										$bots_meta[$id]['onlytext']=false;
										$bots_meta[$id]['step'][$step_id]=array('type'=>$step_type,'industry'=>$industry);
									}
									else
										echo 'industrie_link_meta: '.$industry.' not found for step with id '.$step_id.' for bot '.$id."\n";
								}
								else
									echo 'industry attribute not found for step with id '.$step_id.' for bot '.$id.': '.$step_text."\n";
							}
							else if($step_type=='quests')
							{}
							else
								echo 'step with id '.$step_id.' for bot '.$id.' have unknown type: '.$step_type."\n";
						}
					}
				}
			}
		}
	}
}
ksort($bots_meta);
