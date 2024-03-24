<?php
//change live (save real change each 24h, then do write)
$filetoupdate='change.php';
if(filemtime($filetoupdate)<(time()-3600*24))
{
    $havethechange=false;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_TIMEOUT,10);

    if(!$havethechange && false)
    {
        curl_setopt($ch, CURLOPT_URL, "https://www.freeforexapi.com/api/live?pairs=EURUSD,USDBOB"); 
        $output = curl_exec($ch); 
        if (!curl_errno($ch))
        {
            switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE))
            {
                case 200:  # OK
                    if($output !== FALSE)
                    {
                        $data=json_decode($output,true);
                        if($data!==FALSE && isset($data['code']) && $data['code']==200 && isset($data['rates']['EURUSD']['rate']) && isset($data['rates']['USDBOB']['rate']))
                        {
                            $EURUSD=(float)$data['rates']['EURUSD']['rate'];
                            $USDBOB=(float)$data['rates']['USDBOB']['rate'];
                            $EURBOB=$USDBOB*$EURUSD;
                            if($USDBOB>6.0 && $USDBOB<8.0 && $EURBOB>6.5 && $EURBOB<9.5)
                            {
                                $havethechange=true;
                                file_put_contents($filetoupdate,'<?php
$usdchange='.$USDBOB.';
$eurchange='.$EURBOB.';');
                            }
                        }
                    }
                break;
                default:
                break;
            }
        }
    }
    if(!$havethechange)
    {
        curl_setopt($ch, CURLOPT_URL, "https://www.bcb.gob.bo/librerias/indicadores/otras/ultimo.php"); 
        $output = curl_exec($ch); 
        if (!curl_errno($ch))
        {
            switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE))
            {
                case 200:  # OK
                    if($output !== FALSE)
                    {
                        if(preg_match('#USD.VENTA      </div>#isU',$output))
                        {
                            $usdcontent=preg_replace('#^.*USD.VENTA      </div>#isU','',$output);
                            $usdcontent=preg_replace('#^.*[^0-9]([0-9]+\.[0-9]+)[^0-9].*$#isU','$1',$usdcontent);
                            $USDBOB=(float)trim($usdcontent);
                            if(preg_match('#EUR      </div>#isU',$output))
                            {
                                $eurcontent=preg_replace('#^.*EUR      </div>#isU','',$output);
                                $eurcontent=preg_replace('#^.*[^0-9]([0-9]+\.[0-9]+)[^0-9].*$#isU','$1',$eurcontent);
                                $EURBOB=(float)trim($eurcontent);
                                if($USDBOB>6.0 && $USDBOB<8.0 && $EURBOB>6.5 && $EURBOB<9.5)
                                {
                                    $havethechange=true;
                                    file_put_contents($filetoupdate,'<?php
$usdchange='.$USDBOB.';
$eurchange='.$EURBOB.';');
                                }
                            }
                        }
                    }
                break;
                default:
                break;
            }
        }
    }
    if(!$havethechange)
        echo 'No valid source of change';
}
