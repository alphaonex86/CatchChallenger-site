<?php
$backup='backup-user';
$logfile='/var/log/backup.log';
if(file_exists($logfile))
{
    if($filecurs=file_get_contents($logfile))
    {
        if(strpos($filecurs, 'already running')!==FALSE)
            $returnVar[$backup]=array('state'=>'down','reason'=>'backup already running');
        else if(strpos($filecurs, 'no vm found')!==FALSE)
            $returnVar[$backup]=array('state'=>'down','reason'=>'no vm found');
        else if(strpos($filecurs, 'internal error')!==FALSE)
            $returnVar[$backup]=array('state'=>'down','reason'=>'internal error');
        else if(strpos($filecurs, 'rror')!==FALSE)
            $returnVar[$backup]=array('state'=>'down','reason'=>'error');
        else if(strpos($filecurs, 'RROR')!==FALSE)
            $returnVar[$backup]=array('state'=>'down','reason'=>'error');
        else
            $returnVar[$backup]=array('state'=>'up');
    }
    else
        $returnVar[$backup]=array('state'=>'down','reason'=>($logfile.' not readable or empty'));
}
else
    $returnVar[$backup]=array('state'=>'down','reason'=>($logfile.' not found'));

echo json_encode($returnVar);
