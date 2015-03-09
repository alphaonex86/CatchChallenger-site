<?php
function bot_to_wiki_name($bot_id)
{
    global $bots_meta,$current_lang,$bots_name_count;
    if($bots_meta[$bot_id]['name'][$current_lang]=='')
        $link='bot '.$bot_id;
    else if($bots_name_count[$current_lang][$bots_meta[$bot_id]['name'][$current_lang]]==1)
        $link=$bots_meta[$bot_id]['name'][$current_lang];
    else
        $link=$bot_id.' '.$bots_meta[$bot_id]['name'][$current_lang];
    return $link;
} 
