<?php
$serverlist=array(
    array('host'=>'localhost','port'=>3306)
);
$result=array();
foreach($serverlist as $server)
{
    $state='down';
    $fp=fsockopen($server['host'],$server['port'],$errno,$errstr,1);
    if($fp)
    {
        $state='up';
        fclose($fp);
    }

    $result[$server['host']]=array('state'=>$state);
}
echo json_encode($result);