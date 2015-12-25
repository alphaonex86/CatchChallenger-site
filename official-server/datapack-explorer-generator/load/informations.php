<?php
if(!isset($datapackexplorergeneratorinclude))
	die('abort into load other'."\n");

$informations_meta=array();
if(is_file($datapack_path.'informations.xml'))
{
    $content=file_get_contents($datapack_path.'informations.xml');

    $name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$content);
    $name=str_replace('<![CDATA[','',str_replace(']]>','',$name));
    $name=preg_replace("#[\n\r\t]+#is",'',$name);
    $name_in_other_lang=array('en'=>$name);
    foreach($lang_to_load as $lang)
    {
        if($lang=='en')
            continue;
        if(preg_match('#<name lang="'.$lang.'">([^<]+)</name>#isU',$content))
        {
            $temp_name=preg_replace('#^.*<name lang="'.$lang.'">([^<]+)</name>.*$#isU','$1',$content);
            $temp_name=str_replace('<![CDATA[','',str_replace(']]>','',$temp_name));
            $temp_name=preg_replace("#[\n\r\t]+#is",'',$temp_name);
            $name_in_other_lang[$lang]=$temp_name;
        }
        else
            $name_in_other_lang[$lang]=$name;
    }
    if(!preg_match('#<description( lang="en")?>.*</description>#isU',$content))
        continue;
    $description=text_operation_first_letter_upper(preg_replace('#^.*<description( lang="en")?>(.*)</description>.*$#isU','$2',$content));
    $description=str_replace('<![CDATA[','',str_replace(']]>','',$description));
    $description=preg_replace("#[\n\r\t]+#is",'',$description);
    $description_in_other_lang=array('en'=>$description);
    foreach($lang_to_load as $lang)
    {
        if($lang=='en')
            continue;
        if(preg_match('#<description lang="'.$lang.'">([^<]+)</description>#isU',$content))
        {
            $temp_description=preg_replace('#^.*<description lang="'.$lang.'">([^<]+)</description>.*$#isU','$1',$content);
            $temp_description=str_replace('<![CDATA[','',str_replace(']]>','',$temp_description));
            $temp_description=preg_replace("#[\n\r\t]+#is",'',$temp_description);
            $description_in_other_lang[$lang]=$temp_description;
        }
        else
            $description_in_other_lang[$lang]=$description;
    }
    $informations_meta=array('name'=>$name_in_other_lang,'description'=>$description_in_other_lang,'main'=>array());

    $dir = $datapack_path.'map/main/';
    $dh  = opendir($dir);
    while (false !== ($maindatapackcode = readdir($dh)))
    {
        if($maindatapackcode!='.' && $maindatapackcode!='..')
        {
            if(is_dir($datapack_path.'map/main/'.$maindatapackcode) && preg_match('#^[a-z0-9]+$#isU',$maindatapackcode))
            {
                if(is_file($datapack_path.'map/main/'.$maindatapackcode.'/informations.xml'))
                {
                    $content=file_get_contents($datapack_path.'map/main/'.$maindatapackcode.'/informations.xml');

                    $name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$content);
                    $name=str_replace('<![CDATA[','',str_replace(']]>','',$name));
                    $name=preg_replace("#[\n\r\t]+#is",'',$name);
                    $name_in_other_lang=array('en'=>$name);
                    foreach($lang_to_load as $lang)
                    {
                        if($lang=='en')
                            continue;
                        if(preg_match('#<name lang="'.$lang.'">([^<]+)</name>#isU',$content))
                        {
                            $temp_name=preg_replace('#^.*<name lang="'.$lang.'">([^<]+)</name>.*$#isU','$1',$content);
                            $temp_name=str_replace('<![CDATA[','',str_replace(']]>','',$temp_name));
                            $temp_name=preg_replace("#[\n\r\t]+#is",'',$temp_name);
                            $name_in_other_lang[$lang]=$temp_name;
                        }
                        else
                            $name_in_other_lang[$lang]=$name;
                    }
                    if(preg_match('#<description( lang="en")?>.*</description>#isU',$content))
                    {
                        $description=text_operation_first_letter_upper(preg_replace('#^.*<description( lang="en")?>(.*)</description>.*$#isU','$2',$content));
                        $description=str_replace('<![CDATA[','',str_replace(']]>','',$description));
                        $description=preg_replace("#[\n\r\t]+#is",'',$description);
                        $description_in_other_lang=array('en'=>$description);
                        foreach($lang_to_load as $lang)
                        {
                            if($lang=='en')
                                continue;
                            if(preg_match('#<description lang="'.$lang.'">([^<]+)</description>#isU',$content))
                            {
                                $temp_description=preg_replace('#^.*<description lang="'.$lang.'">([^<]+)</description>.*$#isU','$1',$content);
                                $temp_description=str_replace('<![CDATA[','',str_replace(']]>','',$temp_description));
                                $temp_description=preg_replace("#[\n\r\t]+#is",'',$temp_description);
                                $description_in_other_lang[$lang]=$temp_description;
                            }
                            else
                                $description_in_other_lang[$lang]=$description;
                        }
                    }
                    else
                        $description_in_other_lang=array();

                    $initial='';
                    if(preg_match('#^.*<initial>(.*)</initial>.*$#isU',$content))
                    {
                        $initial=preg_replace('#^.*<initial>(.*)</initial>.*$#isU','$1',$content);
                        $initial=str_replace('<![CDATA[','',str_replace(']]>','',$initial));
                        $initial=preg_replace("#[\n\r\t]+#is",'',$initial);
                    }
                    else
                        $initial=count($informations_meta['main'])+1;

                    $color='';
                    if(preg_match('#^.*<informations color="(.[0-9a-fA-F]{3,6})">.*$#isU',$content))
                    {
                        $color=preg_replace('#^.*<informations color="(.[0-9a-fA-F]{3,6})">.*$#isU','$1',$content);
                        $color=str_replace('<![CDATA[','',str_replace(']]>','',$color));
                        $color=preg_replace("#[\n\r\t]+#is",'',$color);
                    }

                    $informations_meta['main'][$maindatapackcode]=array('name'=>$name_in_other_lang,'description'=>$description_in_other_lang,'initial'=>$initial,'color'=>$color,'sub'=>array(),'monsters'=>array());

                    $dir2 = $datapack_path.'map/main/'.$maindatapackcode.'/sub/';
                    if(is_dir($dir2))
                    {
                        $dh2  = opendir($dir2);
                        if($dh2!==FALSE)
                        while (false !== ($subdatapackcode = readdir($dh2)))
                        {
                            if($subdatapackcode!='.' && $subdatapackcode!='..')
                            {
                                if(is_dir($datapack_path.'map/main/'.$maindatapackcode.'/sub/'.$subdatapackcode) && preg_match('#^[a-z0-9]+$#isU',$subdatapackcode))
                                {
                                    if(is_file($datapack_path.'map/main/'.$maindatapackcode.'/sub/'.$subdatapackcode.'/informations.xml'))
                                    {
                                        $content=file_get_contents($datapack_path.'map/main/'.$maindatapackcode.'/sub/'.$subdatapackcode.'/informations.xml');

                                        $name=preg_replace('#^.*<name( lang="en")?>(.*)</name>.*$#isU','$2',$content);
                                        $name=str_replace('<![CDATA[','',str_replace(']]>','',$name));
                                        $name=preg_replace("#[\n\r\t]+#is",'',$name);
                                        $name_in_other_lang=array('en'=>$name);
                                        foreach($lang_to_load as $lang)
                                        {
                                            if($lang=='en')
                                                continue;
                                            if(preg_match('#<name lang="'.$lang.'">([^<]+)</name>#isU',$content))
                                            {
                                                $temp_name=preg_replace('#^.*<name lang="'.$lang.'">([^<]+)</name>.*$#isU','$1',$content);
                                                $temp_name=str_replace('<![CDATA[','',str_replace(']]>','',$temp_name));
                                                $temp_name=preg_replace("#[\n\r\t]+#is",'',$temp_name);
                                                $name_in_other_lang[$lang]=$temp_name;
                                            }
                                            else
                                                $name_in_other_lang[$lang]=$name;
                                        }
                                        if(preg_match('#<description( lang="en")?>.*</description>#isU',$content))
                                        {
                                            $description=text_operation_first_letter_upper(preg_replace('#^.*<description( lang="en")?>(.*)</description>.*$#isU','$2',$content));
                                            $description=str_replace('<![CDATA[','',str_replace(']]>','',$description));
                                            $description=preg_replace("#[\n\r\t]+#is",'',$description);
                                            $description_in_other_lang=array('en'=>$description);
                                            foreach($lang_to_load as $lang)
                                            {
                                                if($lang=='en')
                                                    continue;
                                                if(preg_match('#<description lang="'.$lang.'">([^<]+)</description>#isU',$content))
                                                {
                                                    $temp_description=preg_replace('#^.*<description lang="'.$lang.'">([^<]+)</description>.*$#isU','$1',$content);
                                                    $temp_description=str_replace('<![CDATA[','',str_replace(']]>','',$temp_description));
                                                    $temp_description=preg_replace("#[\n\r\t]+#is",'',$temp_description);
                                                    $description_in_other_lang[$lang]=$temp_description;
                                                }
                                                else
                                                    $description_in_other_lang[$lang]=$description;
                                            }
                                        }
                                        else
                                            $description_in_other_lang=array();

                                        $initial='';
                                        if(preg_match('#^.*<initial>(.*)</initial>.*$#isU',$content))
                                        {
                                            $initial=preg_replace('#^.*<initial>(.*)</initial>.*$#isU','$1',$content);
                                            $initial=str_replace('<![CDATA[','',str_replace(']]>','',$initial));
                                            $initial=preg_replace("#[\n\r\t]+#is",'',$initial);
                                        }
                                        else
                                            $initial=count($informations_meta['main'])+1;

                                        $color='';
                                        if(preg_match('#^.*<informations color="(.[0-9a-fA-F]{3,6})">.*$#isU',$content))
                                        {
                                            $color=preg_replace('#^.*<informations color="(.[0-9a-fA-F]{3,6})">.*$#isU','$1',$content);
                                            $color=str_replace('<![CDATA[','',str_replace(']]>','',$color));
                                            $color=preg_replace("#[\n\r\t]+#is",'',$color);
                                        }

                                        $informations_meta['main'][$maindatapackcode]['sub'][$subdatapackcode]=array('name'=>$name_in_other_lang,'description'=>$description_in_other_lang,'initial'=>$initial,'color'=>$color,'monsters'=>array());
                                    }
                                }
                                else
                                    echo 'Into '.$datapack_path.'map/main/'.$maindatapackcode.'/sub/ the entry: '.$subdatapackcode.' is not correct!'."\n";
                            }
                        }
                        closedir($dh2);
                        ksort($informations_meta['main'][$maindatapackcode]['sub']);
                    }
                }
            }
            else
                echo 'Into '.$datapack_path.'map/main/ the entry: '.$maindatapackcode.' is not correct!'."\n";
        }
    }
    closedir($dh);
    ksort($informations_meta['main']);
}