<?php
function reputationLevelToText($reputation,$level)
{
    global $reputation_meta,$current_lang;
    if(!isset($reputation_meta[$reputation]))
        return 'Level '.$reputation['level'].' in '.$reputation['type'];
    if(!isset($reputation_meta[$reputation]['level'][$level]))
        return 'Level '.$reputation['level'].' in '.$reputation_meta[$reputation]['name'][$current_lang];
    return 'Level '.$level.' in '.$reputation_meta[$reputation]['name'][$current_lang].' ('.$reputation_meta[$reputation]['level'][(int)$level].')';
}

function reputationToText($reputation)
{
    global $reputation_meta,$current_lang;
    if(!isset($reputation_meta[$reputation]))
        return $reputation['type'];
    return $reputation_meta[$reputation]['name'][$current_lang];
}
