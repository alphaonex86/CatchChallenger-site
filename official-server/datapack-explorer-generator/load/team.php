<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into load team'."\n");

$team_meta=array();
if(file_exists($datapack_path.'map/team.xml'))
{
    $content=file_get_contents($datapack_path.'map/team.xml');
    preg_match_all('#<team [^>]+>(.*)</team>#isU',$content,$temp_text_list);
    foreach($temp_text_list[0] as $team_text)
    {
        $teambal=preg_replace('#^.*(<team [^>]+>).*$#isU','$1',$team_text);
        $id=preg_replace('#^.*<team [^>]*id="([^"]+)"[^>]*>.*$#isU','$1',$teambal);
        $tileid=preg_replace('#^.*<team [^>]*tileid="([0-9]+)"[^>]*>.*$#isU','$1',$teambal);
        if(isset($team_meta[$id]))
            echo $file.': team with id '.$id.' is already found into: '.$bots_found_in[$id]."\n";
        else
        {
            $name='';
            if(preg_match('#<name( lang="en")?>.*</name>#isU',$team_text))
            {
                $name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$team_text);
                $name=str_replace('<![CDATA[','',str_replace(']]>','',$name));
            }
            $name_in_other_lang=array('en'=>$name);
            foreach($lang_to_load as $lang)
            {
                if(preg_match('#<name lang="'.$lang.'">([^<]+)</name>#isU',$team_text))
                {
                    $temp_name=preg_replace('#^.*<name lang="'.$lang.'">([^<]+)</name>.*$#isU','$1',$team_text);
                    $temp_name=str_replace('<![CDATA[','',str_replace(']]>','',$temp_name));
                    $name_in_other_lang[$lang]=$temp_name;
                }
                else
                    $name_in_other_lang[$lang]=$name;
            }
            $team_meta[$id]=array('tileid'=>$tileid,'name'=>$name_in_other_lang);
        }
    }
}
ksort($team_meta);