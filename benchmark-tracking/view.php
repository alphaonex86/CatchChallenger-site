<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title>CatchChallenger benchmark</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF8"/>
		<meta name="robots" content="index,follow"/>
		<meta name="description" content="CatchChallenger benchmark" />
		<meta name="keywords" content="catchchallenger,benchmark,continous benchmark" />
		<link rel="stylesheet" type="text/css" media="screen" href="/css/style.css" />
        <link rel="stylesheet" type="text/css" href="/css/jquery.jqplot.min.css" />
		<link rel="alternate" type="application/atom+xml" href="/rss_global.xml" title="All news" />
        <meta name="viewport" content="width=device-width" />

        <script type="text/javascript" src="/js/jquery.js"></script>
        <!--[if lt IE 9]><script language="javascript" type="text/javascript" src="/js/excanvas.min.js"></script><![endif]-->
        <script language="javascript" type="text/javascript" src="/js/jquery.jqplot.min.js"></script>
        <script type="text/javascript" src="/js/plugins/jqplot.highlighter.min.js"></script>
        <script type="text/javascript" src="/js/plugins/jqplot.dateAxisRenderer.min.js"></script>
        <script type="text/javascript" src="/js/plugins/jqplot.canvasTextRenderer.min.js"></script>
        <script type="text/javascript" src="/js/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
        <script type="text/javascript" src="/js/plugins/jqplot.categoryAxisRenderer.min.js"></script>
        <script type="text/javascript" src="/js/plugins/jqplot.barRenderer.min.js"></script>
        <script type="text/javascript" src="/js/plugins/jqplot.categoryAxisRenderer.min.js"></script>
        <script type="text/javascript" src="/js/plugins/jqplot.pointLabels.min.js"></script>

	</head>
	<body>
		<div id="container">
			<div id="header">
				<div id="logo"></div>
				<div id="back_menu">
					<table>
					<tr>
						<td><a href="/">Home</a></td>
						<td><a href="/official-server.html">Official server</a></td>
						<td><a href="/download.html">Download</a></td>
						<td><a href="/screenshot.html">Screenshot</a></td>
						<td><a href="/shop/">Shop</a></td>
						<td><a href="/community.html">Community</a></td>
						<td><a href="/contact.html">Contact</a></td>
					</tr>
					</table>
				</div>
			</div>
			<div id="body">
				<div id="title">Benchmark result view</div>
				<br />
				<br />
<small>Lower is better into all this results</small>
<?php
$result_to_list=array('connectAllPlayer'=>'Connect all player','idle'=>'Players do nothing','move'=>'Players moving','chat'=>'Players chating');

$average_values=array(
    'external'=>array(),
    'internal'=>array(),
);

if(is_dir('results/') && $handle = opendir('results/'))
{
    while(false !== ($entry = readdir($handle)))
    {
        if ($entry != '.' && $entry != '..' && is_dir('results/'.$entry))
        {
            echo '<h2 style="clear:both;">'.$entry.'</h2>';
            if($handle2 = opendir('results/'.$entry.'/'))
            {
                while(false !== ($entry2 = readdir($handle2)))
                {
                    if ($entry2 != '.' && $entry2 != '..' && is_file('results/'.$entry.'/'.$entry2) && preg_match('#\.data$#',$entry2))
                    {
                        $file='results/'.$entry.'/'.$entry2;
                        if(is_file($file))
                        {
                            $content=file_get_contents($file);
                            if($content!='')
                                $json_result=unserialize($content);
                        }
                        if(isset($json_result))
                        {
                            echo '<h3 style="clear:both;">'.$json_result['details'].'</h3>';
                            foreach($result_to_list as $key=>$value)
                            {
                                if(isset($json_result[$key]))
                                {
                                    $random=rand(100000,999999);
                                ?>
                                    <div id="chart<?php echo $random; ?>" style="width:400px;height:300px;float:left;"></div>
                                    <script type="text/javascript">
                                    $(document).ready(function(){
                                    var line1 = [<?php
                                    $arr=array();
                                    $index=0;
                                    $highter_value=0;
                                    $average_temp=(float)0;
                                    $average_count=0;
                                    foreach($json_result[$key] as $commit=>$result)
                                    {
                                        if(count($result)>0)
                                        {
                                            if(false)
                                                $arr[]='['.$index.', '.array_sum($result)/count($result).', \''.substr($commit,0,10).'\']';
                                            else
                                            {
                                                $lower_value=$result[0];
                                                foreach($result as $temp_value)
                                                    if($temp_value<$lower_value)
                                                        $lower_value=$temp_value;
                                                if($lower_value>$highter_value)
                                                    $highter_value=$lower_value;
                                                $arr[]='['.$index.', '.$lower_value.', \''.substr($commit,0,10).'\']';
                                                $average_temp+=(float)$lower_value;
                                                $average_count++;
                                            }
                                        }
                                        else
                                            $arr[]='['.$index.', 0, \''.substr($commit,0,10).'\']';
                                        $index++;
                                    }
                                    if(preg_match('# external$#isU',$json_result['details']))
                                        $type='external';
                                    else
                                        $type='internal';
                                    $average_temp/=$average_count;
                                    foreach($json_result[$key] as $commit=>$result)
                                    {
                                        if(count($result)>0)
                                        {
                                            if(false)
                                                $arr[]='['.$index.', '.array_sum($result)/count($result).', \''.substr($commit,0,10).'\']';
                                            else
                                            {
                                                $lower_value=$result[0];
                                                foreach($result as $temp_value)
                                                    if($temp_value<$lower_value)
                                                        $lower_value=$temp_value;
                                                if($lower_value>$highter_value)
                                                    $highter_value=$lower_value;

                                                if(!isset($average_values[$type][$value]))
                                                    $average_values[$type][$value]=array();
                                                if(!isset($average_values[$type][$value][$commit]))
                                                    $average_values[$type][$value][$commit]=array();
                                                $average_values[$type][$value][$commit][]=(float)$lower_value/$average_temp;
                                            }
                                        }
                                        else
                                            $arr[]='['.$index.', 0, \''.substr($commit,0,10).'\']';
                                        $index++;
                                    }
                                    echo implode(', ',$arr);
                                    ?>];
                                    
                                    var plot1 = $.jqplot('chart<?php echo $random; ?>', [line1], {
                                        title: '<?php echo $value; ?>',
                                        axes:{
                                            yaxis: {min:0<?php if($highter_value<100) {echo ',max:100';} ?>}
                                        },
                                        seriesDefaults: {
                                            pointLabels: { show:true } 
                                        }
                                    });
                                    });
                                    </script><?php
                                }
                            }
                        }
                    }
                }
                closedir($handle2);
            }
        }
    }
    closedir($handle);
}

echo '<h2 style="clear:both;">Average</h2>';
foreach($average_values as $details=>$result_to_list)
{
    echo '<h3 style="clear:both;">'.$details.'</h3>';
    foreach($result_to_list as $key=>$values)
    {

        $random=rand(100000,999999);
        ?>
            <div id="chart<?php echo $random; ?>" style="width:400px;height:300px;float:left;"></div>
            <script type="text/javascript">
            $(document).ready(function(){
            var line1 = [<?php
            $arr=array();
            $index=0;
            $highter_value=0;
            $average_temp=(float)0;
            $average_count=0;
            foreach($values as $commit=>$result)
            {
                $arr[]='['.$index.', '.(array_sum($result)/count($result)).', \''.substr($commit,0,10).'\']';
                $index++;
            }
            echo implode(', ',$arr);
            ?>];
            
            var plot1 = $.jqplot('chart<?php echo $random; ?>', [line1], {
                series: [
            {color: 'orange', highlightColors: 'rgb(255, 245, 185)'}
        ],
                title: '<?php echo $key; ?>',
                axes:{
                    yaxis: {min:0}
                },
                seriesDefaults: {
                    pointLabels: { show:true } 
                }
            });
            });
            </script><?php
    }
}

echo '<hr style="clear:both;" />';
echo 'Failed to compil: <ul>';
$number_of_failed=0;
if(is_dir('failed/') && $handle = opendir('failed/'))
{
    while(false !== ($entry = readdir($handle)))
    {
        if ($entry != '.' && $entry != '..' && is_file('failed/'.$entry) && preg_match('#\.data$#',$entry))
        {
            $file='failed/'.$entry;
            if(is_file($file))
            {
                $content=file_get_contents($file);
                if($content!='')
                    $json_result=unserialize($content);
            }
            if(isset($json_result))
            {
                $number_of_failed++;
                echo '<li>'.preg_replace('#\.data$#','',$entry).'<ul>';
                foreach($json_result as $key=>$value_list)
                {
                    echo '<li>'.$key.': <ul>';
                    foreach($value_list as $value)
                        echo '<li>'.$value.'</li>';
                    echo '</ul>';
                    echo '</li>';
                }
                echo '</ul></li>';
            }
        }
    }
    closedir($handle);
}
if($number_of_failed==0)
    echo '<li>None</li>';
echo '</ul>';
?>
			</div>
			<br />
			<div id="footer">
				<div id="copyright">CatchChallenger - <span style="color:#777;font-size:80%">Donate Bitcoin: </span><span style="color:#999;font-size:70%">1C4VLs16HX5YBoUeCLxEMJq8TpP24dcUJN</span> <span style="color:#777;font-size:80%">Nextcoin: </span><span style="color:#999;font-size:70%">NXT-MY96-548U-A5V5-BSR7R</span></div>
			</div>
		</div>
	</body>
</html>