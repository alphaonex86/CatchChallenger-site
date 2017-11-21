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
        $posLag=strpos($filecurs,'Lag:'."\n");
        $posProcessOnSlave=strpos($filecurs,'Process on slave:'."\n");
        $posSlaveOnMaster=strpos($filecurs,'Slave connected on master:'."\n");
        if($posLag===false)
            $returnVar[$backup]=array('state'=>'down','reason'=>'String "Lag:" not found');
        else if($posProcessOnSlave===false)
            $returnVar[$backup]=array('state'=>'down','reason'=>'String "Process on slave:" not found');
        else if($posSlaveOnMaster===false)
            $returnVar[$backup]=array('state'=>'down','reason'=>'String "Slave connected on master:" not found');
        else if($posLag>=$posProcessOnSlave)
            $returnVar[$backup]=array('state'=>'down','reason'=>'String "Lag:" out of scope');
        else if($posProcessOnSlave>=$posSlaveOnMaster)
            $returnVar[$backup]=array('state'=>'down','reason'=>'String "Process on slave:" out of scope');
        else 
        {
            $stringLag=substr($filecurs,$posLag+strlen('Lag:'."\n"),$posProcessOnSlave-$posLag-strlen('Lag:'."\n"));
            $stringProcessOnSlave=substr($filecurs,$posProcessOnSlave+strlen('Process on slave:'."\n"),$posSlaveOnMaster-$posProcessOnSlave-strlen('Process on slave:'."\n"));
            $stringSlaveOnMaster=substr($filecurs,$posSlaveOnMaster+strlen('Slave connected on master:'."\n"),strlen($filecurs)-$posSlaveOnMaster+strlen('Slave connected on master:'."\n"));
            $stringLagLine=explode("\n",$stringLag);
            $stringLagLine=array_filter($stringLagLine, function($value) { return $value !== ''; });
            $stringProcessOnSlaveLine=explode("\n",$stringProcessOnSlave);
            $stringProcessOnSlaveLine=array_filter($stringProcessOnSlaveLine, function($value) { return $value !== ''; });
            $stringSlaveOnMasterLine=explode("\n",$stringSlaveOnMaster);
            $stringSlaveOnMasterLine=array_filter($stringSlaveOnMasterLine, function($value) { return $value !== ''; });

            $inError=array();
            
            $stringLagArray=array();
            foreach($stringLagLine as $line)
            {
                $posDelim=strpos($line,':');
                if($posDelim===false)
                    die('File format error for '.$logfile);
                $item=substr($line,0,$posDelim);
                $value=substr($line,$posDelim+1,strlen($line)-$posDelim-1);
                $stringLagArray[$item]=$value;
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
                if(!isset($stringLagArray[$slave]))
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

//do via user: user ssh root@server '/usr/bin/find /home/backup-1/home/backup/day/last-backup.txt -mtime -1 | wc -l', can be duplicate vm name
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

$backup='backup-user';
$logfile='/var/log/backup.log';
if(file_exists($logfile))
{
    if($filecurs=file_get_contents($logfile))
    {
        if(strpos($filecurs, 'already running')===NULL && strpos($filecurs, 'no vm found')===NULL && strpos($filecurs, 'internal error')===NULL)
            $returnVar[$backup]=array('state'=>'up');
        else
            $returnVar[$backup]=array('state'=>'down','reason'=>'backup already running');
    }
    else
        $returnVar[$backup]=array('state'=>'down','reason'=>($logfile.' not readable or empty'));
}
else
    $returnVar[$backup]=array('state'=>'down','reason'=>($logfile.' not found'));
    
echo json_encode($returnVar);
