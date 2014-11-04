<?php
function reputationLevelToText($reputation,$level)
{
    global $reputation_meta;
    if(!isset($reputation_meta[$reputation]))
        return 'Level '.$reputation['level'].' in '.$reputation['type'];
    if(!isset($reputation_meta[$reputation]['level'][$level]))
        return 'Level '.$reputation['level'].' in '.$reputation_meta[$reputation]['name'];
    return 'Level '.$level.' in '.$reputation_meta[$reputation]['name'].' ('.$reputation_meta[$reputation]['level'][(int)$level].')';
}

function reputationToText($reputation)
{
    global $reputation_meta;
    if(!isset($reputation_meta[$reputation]))
        return $reputation['type'];
    return $reputation_meta[$reputation]['name'];
}
