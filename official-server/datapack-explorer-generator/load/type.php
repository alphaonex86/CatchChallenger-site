<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into load type'."\n");

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
        $name=str_replace('<![CDATA[','',str_replace(']]>','',$name));
		$english_name='Unknown name';
		if(preg_match('#<name lang="en">([^<]+)</name>#isU',$entry))
			$english_name=preg_replace('#^.*<name lang="en">([^<]+)</name>.*$#isU','$1',$entry);
		elseif(preg_match('#<name>([^<]+)</name>#isU',$entry))
			$english_name=preg_replace('#^.*<name>([^<]+)</name>.*$#isU','$1',$entry);
        $english_name=str_replace('<![CDATA[','',str_replace(']]>','',$english_name));
		preg_match_all('#<multiplicator.*/>#isU',$entry,$multiplicator_list);
		foreach($multiplicator_list[0] as $tempmultiplicator)
		{
            if(!preg_match('# number="([^"]+)"#isU',$tempmultiplicator))
                continue;
            if(!preg_match('# to="([^"]+)"#isU',$tempmultiplicator))
                continue;
            $number=(float)preg_replace('#^.* number="([^"]+)".*$#isU','$1',$tempmultiplicator);
			$to=preg_replace('#^.* to="([^"]+)".*$#isU','$1',$tempmultiplicator);
			$to_list=explode(';',$to);
			foreach($to_list as $to)
				$multiplicator[$to]=$number;
		}
		$type_meta[$name]=array('english_name'=>$english_name,'multiplicator'=>$multiplicator);
	}
}