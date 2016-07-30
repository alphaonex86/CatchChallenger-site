<?php
require '../config.php';
$result=array();
if($argc==3)
$loginserverlist=array(
    array('host'=>$argv[1],'port'=>$argv[2])
);
foreach($loginserverlist as $server)
{
    $state='down';
    $time_start = microtime(true);

    $opts = array(
        'socket' => array(
            'tcp_nodelay' => true,
        ),
    );
    $ctx = stream_context_create();

    $socket=@fsockopen($server['host'],$server['port'],$errno,$errstr,1);//,$ctx
    $time_end = microtime(true);
    $time = $time_end - $time_start;
    if($socket)
    {
        $state='up';
        if(isset($server['step']) && $server['step']=='connect')
        {
            $result[$server['host'].':'.$server['port']]=array('state'=>$state,
                'time'=>array('toconnect'=>$time),
            );
        }
        else
        {
            $contents=fread($socket,1);
            if(!isset($contents[0x00]))
            {
                $state='down';
                $result[$server['host'].':'.$server['port']]=array('state'=>$state,
                    'time'=>array('toconnect'=>$time),
                'error'=>'at first round no data');
            }
            else
            {
                if($contents[0x00]==0x01)
                {
                    $result[$server['host'].':'.$server['port']]=array('state'=>$state,
                        'time'=>array('toconnect'=>$time),
                    'encrypted'=>'encrypted');
                }
                else
                {
                    //Protocol initialization for client
                    $tosend=hex2bin('a0019cd6498d10');
                    $time_start = microtime(true);
                    $returnsize=fwrite($socket,$tosend,2+5);
                    if($returnsize!=7)
                    {
                        $result[$server['host'].':'.$server['port']]=array('state'=>$state,
                            'time'=>array('toconnect'=>$time),
                        'encrypted'=>'clear');
                    }
                    else
                    {
                        $contents=fread($socket,1+1+4+1);
                        $time_end = microtime(true);
                        $timetonegociatetheprotocol = $time_end - $time_start;
                        if(isset($contents[0x06]))
                            $result[$server['host'].':'.$server['port']]=array('state'=>$state,
                                'time'=>array('toconnect'=>$time,'tonegociatetheprotocol'=>$timetonegociatetheprotocol),
                            'encrypted'=>'clear','returncode'=>bindec($contents[0x06]));
                        else
                        {
                            $state='down';
                            $result[$server['host'].':'.$server['port']]=array('state'=>$state,
                                'time'=>array('toconnect'=>$time,'tonegociatetheprotocol'=>$timetonegociatetheprotocol),
                            'encrypted'=>'clear','error'=>'at second round no data');
                        }
                    }
                }
            }
        }
        fclose($socket);
    }
    else
        $result[$server['host'].':'.$server['port']]=array('state'=>$state);
}
echo json_encode($result);