<?php
if(!isset($datapackexplorergeneratorinclude))
    die('abort into generator map preview'."\n");

if(!is_dir($datapack_explorer_local_path.'maps/'))
    if(!mkdir($datapack_explorer_local_path.'maps/'))
        die('Unable to make: '.$datapack_explorer_local_path.'maps/');

foreach($temp_maps as $maindatapackcode=>$map_list)
foreach($map_list as $map)
{
    $map_html=str_replace('.tmx','.html',$map);
    $map_image=str_replace('.tmx','.png',$map);
    if(preg_match('#/#isU',$map))
    {
        $map_folder=preg_replace('#/[^/]+$#','',$maindatapackcode.'/'.$map).'/';
        if(!is_dir($datapack_explorer_local_path.'maps/'.$map_folder))
            if(!mkpath($datapack_explorer_local_path.'maps/'.$map_folder))
                die('Unable to make: '.$datapack_explorer_local_path.'maps/'.$map_folder);
    }
}

$temprand=rand(10000,99999);
if(isset($map_generator) && $map_generator!='')
{
    $pwd=getcwd();
    $return_var=0;
    //echo 'cd '.$datapack_explorer_local_path.'maps/ && '.$map_generator.' -platform offscreen '.$pwd.'/'.$datapack_path.'map/';
    chdir($datapack_explorer_local_path.'maps/');
    
    //all map preview
    if(count($start_meta)>0)
    {
        if(isset($maps_list[$start_meta[0]['map']]))
        {
            //overview
            @unlink('overview.png');
            exec($map_generator.' -platform offscreen '.$pwd.'/'.$datapack_path.'map/'.$start_meta[0]['map'].' overview.png --renderAll',$output,$return_var);
            
            //preview
            if(file_exists('overview.png'))
            {
                if(is_executable('/usr/bin/convert'))
                {
                    $before = microtime(true);
                    exec('/usr/bin/ionice -c 3 /usr/bin/nice -n 19 /usr/bin/convert overview.png -resize 256x256 preview.png');
                    $after = microtime(true);
                    echo 'Preview generation '.(int)($after-$before)."s\n";
                }
                else
                    echo 'no /usr/bin/convert found, install imagemagick'."\n";
            }
            else
                    echo 'overview.png not found'."\n";
        }
        else
            echo 'map for starter '.$start_meta[0]['map'].' missing'."\n";
    }
    else
        echo 'starter to do overview map missing'."\n";

    //single map preview
    exec($map_generator.' -platform offscreen '.$pwd.'/'.$datapack_path.'map/',$output,$return_var);
    if(is_executable('/usr/bin/mogrify'))
    {
        $before = microtime(true);
        exec('/usr/bin/find ./ -name \'*.png\' -exec /usr/bin/ionice -c 3 /usr/bin/nice -n 19 /usr/bin/mogrify -trim +repage {} \;');
        $after = microtime(true);
        echo 'Png trim and repage into '.(int)($after-$before)."s\n";
    }
    else
        echo 'no /usr/bin/mogrify found, install imagemagick'."\n";

    //compression for all png found!
    if(isset($png_compress) && $png_compress!='')
    {
        $before = microtime(true);
        exec($png_compress);
        $after = microtime(true);
        echo 'Png compressed into '.(int)($after-$before)."s\n";
    }
    if(isset($png_compress_zopfli) && is_executable($png_compress_zopfli))
    {
        if(!isset($png_compress_zopfli_level))
            $png_compress_zopfli_level=100;
        $before = microtime(true);
        exec('/usr/bin/find ./ -name \'*.png\' -print -exec /usr/bin/ionice -c 3 /usr/bin/nice -n 19 '.$png_compress_zopfli.' --png --i'.$png_compress_zopfli_level.' {} \;');
        exec('/usr/bin/find ./ -name \'*.png\' -and ! -name \'*.png.png\' -exec mv {}.png {} \;');
        $after = microtime(true);
        echo 'Png trim and repage into '.(int)($after-$before)."s\n";
    }
    else
        echo 'zopfli for png don\'t installed, prefed install it'."\n";

    chdir($pwd);
}
 
