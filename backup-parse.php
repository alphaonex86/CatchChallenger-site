<?php
$backup_list=array(
    'backup-1'=>array('catchchallenger-master'),
);
$postgresql_replication=array(
    'catchchallenger-db-common-1-a-slave',
);

$returnVar=array();
foreach($backup_list as $backup=>$hardware_list)
{
    $logfile='/tmp/backup-'.$backup.'.txt';
    
    if(file_exists($logfile))
    {
        if($filecurs=file_get_contents($logfile))
        {
            /*preg_match_all("#/([0-9]+)-[0-9]+:#isU",$filecurs,$out);
            $dates=$out[1];
            sort($dates);
            if(count($dates)==0)
            {
                $returnVar[$backup]=array('state'=>'down','reason'=>'Any date found');
                continue;
            }
            $lastdate=end($dates);
            if($lastdate<(time()-24*3600))
            {
                $returnVar[$backup]=array('state'=>'down','reason'=>'Backup have more than 24h: '.$lastdate);
                continue;
            }
            $pos=strpos($filecurs,'/'.$lastdate.':');
            if($pos===false)
            {
                $returnVar[$backup]=array('state'=>'down','reason'=>'Internal bug');
                continue;
            }
            else
                $filecurs=substr($filecurs,$pos,-1);

            $pos=strpos($filecurs,"\n\n");
            if($pos!==false)
                $filecurs=substr($filecurs,0,$pos);
            $filecurs="\n".$filecurs."\n";

            foreach($hardware_list as $dedicated)
            {
                $pos=strpos($filecurs,"\n".$dedicated."\n");
                if($pos===false)
                {
                    $returnVar[$backup]=array('state'=>'down','reason'=>($dedicated.' not found'));
                    break;
                }
            }
            if($pos===false)
                continue;*/

            $returnVar[$backup]=array('state'=>'up');
        }
        else
            $returnVar[$backup]=array('state'=>'down','reason'=>($logfile.' not readable or empty'));
    }
    else
        $returnVar[$backup]=array('state'=>'down','reason'=>($logfile.' not found'));
}

$backup='sync-postgresql';
$logfile='/tmp/sync-postgresql.txt';
if(file_exists($logfile))
{
    if($filecurs=file_get_contents($logfile))
    {
        $posLog=strpos($filecurs,'Log:'."\n");
        $posProcessOnSlave=strpos($filecurs,'Process on slave:'."\n");
        $posSlaveOnMaster=strpos($filecurs,'Slave connected on master:'."\n");
        if($posLog===false)
            $returnVar[$backup]=array('state'=>'down','reason'=>'String "Log:" not found');
        else if($posProcessOnSlave===false)
            $returnVar[$backup]=array('state'=>'down','reason'=>'String "Process on slave:" not found');
        else if($posSlaveOnMaster===false)
            $returnVar[$backup]=array('state'=>'down','reason'=>'String "Slave connected on master:" not found');
        else if($posLog>=$posProcessOnSlave)
            $returnVar[$backup]=array('state'=>'down','reason'=>'String "Log:" out of scope');
        else if($posProcessOnSlave>=$posSlaveOnMaster)
            $returnVar[$backup]=array('state'=>'down','reason'=>'String "Process on slave:" out of scope');
        else 
        {
            $stringLog=substr($filecurs,$posLog+strlen('Log:'."\n"),$posProcessOnSlave-$posLog-strlen('Log:'."\n"));
            $stringProcessOnSlave=substr($filecurs,$posProcessOnSlave+strlen('Process on slave:'."\n"),$posSlaveOnMaster-$posProcessOnSlave-strlen('Process on slave:'."\n"));
            $stringSlaveOnMaster=substr($filecurs,$posSlaveOnMaster+strlen('Slave connected on master:'."\n"),strlen($filecurs)-$posSlaveOnMaster+strlen('Slave connected on master:'."\n"));
            $stringLogLine=explode("\n",$stringLog);
            $stringLogLine=array_filter($stringLogLine, function($value) { return $value !== ''; });
            $stringProcessOnSlaveLine=explode("\n",$stringProcessOnSlave);
            $stringProcessOnSlaveLine=array_filter($stringProcessOnSlaveLine, function($value) { return $value !== ''; });
            $stringSlaveOnMasterLine=explode("\n",$stringSlaveOnMaster);
            $stringSlaveOnMasterLine=array_filter($stringSlaveOnMasterLine, function($value) { return $value !== ''; });

            $inError=array();
            
            $stringLogArray=array();
            foreach($stringLogLine as $line)
            {
                $posDelim=strpos($line,':');
                if($posDelim===false)
                    die('File format error for '.$logfile);
                $item=substr($line,0,$posDelim);
                $value=substr($line,$posDelim+1,strlen($line)-$posDelim-1);
                $stringLogArray[$item]=$value;
                if($value>3600)
                    $inError[$item]='Sync time too big';
            }
            
            $stringProcessOnSlaveArray=array();
            foreach($stringProcessOnSlaveLine as $line)
            {
                $posDelim=strpos($line,':');
                if($posDelim===false)
                    die('File format error for '.$logfile);
                $item=substr($line,0,$posDelim);
                $value=substr($line,$posDelim+1,strlen($line)-$posDelim-1);
                $stringProcessOnSlaveArray[$item]=$value;
            }

            $stringSlaveOnMasterArray=array();
            foreach($stringSlaveOnMasterLine as $line)
            {
                $posDelim=strpos($line,':');
                if($posDelim===false)
                    die('File format error for '.$logfile);
                $item=substr($line,0,$posDelim);
                $value=substr($line,$posDelim+1,strlen($line)-$posDelim-1);
                $stringSlaveOnMasterArray[$item]=$value;
            }

            foreach($postgresql_replication as $slave)
            {
                if(!isset($stringLogArray[$slave]))
                    $inError[$slave]='Not found in lag';
                else if(!isset($stringProcessOnSlaveArray[$slave]))
                    $inError[$slave]='Not found in ProcessOnSlave';
                else if(!isset($stringSlaveOnMasterArray[$slave]))
                    $inError[$slave]='Not found in SlaveOnMaster';
            }
            if(count($inError)>0)
                $returnVar[$backup]=array('state'=>'down','reason'=>$inError);
            else
                $returnVar[$backup]=array('state'=>'up');
        }
    }
    else
        $returnVar[$backup]=array('state'=>'down','reason'=>($logfile.' not readable or empty'));
}
else
    $returnVar[$backup]=array('state'=>'down','reason'=>($logfile.' not found'));

$backup='backup-date';
$logfile='/tmp/backup-is-up.txt';
if(file_exists($logfile))
{
    if($filecurs=file_get_contents($logfile))
    {
        if($filecurs==1)
            $returnVar[$backup]=array('state'=>'up');
        else
            $returnVar[$backup]=array('state'=>'down','reason'=>'Repported number is wrong');
    }
    else
        $returnVar[$backup]=array('state'=>'down','reason'=>($logfile.' not readable or empty'));
}
else
    $returnVar[$backup]=array('state'=>'down','reason'=>($logfile.' not found'));

echo json_encode($returnVar);
