<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title><?php if(isset($title)){echo $title;}?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF8"/>
		<meta name="robots" content="index,follow"/>
		<meta name="description" content="<?php if(isset($title)){echo $title;}?>" />
		<meta name="keywords" content="<?php if(isset($title)){echo $title;}?>" />
		<link rel="stylesheet" type="text/css" media="screen" href="/css/style.min.css" />
        <?php
        if(isset($css_list))
            foreach($css_list as $css)
                echo '<link rel="stylesheet" type="text/css" media="screen" href="'.$css.'" />';
        ?>
		<link rel="alternate" type="application/atom+xml" href="/rss_global.xml" title="All news" />
        <meta name="viewport" content="width=device-width" />
        <meta name="Language" content="en" />
        <meta http-equiv="content-language" content="english" />
	</head>
	<body>
	<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/cookieconsent.min.css" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/cookieconsent.min.js"></script>
    <script>
    window.addEventListener("load", function(){
    window.cookieconsent.initialise({
    "palette": {
        "popup": {
        "background": "#000"
        },
        "button": {
        "background": "#f1d600"
        }
    },
    "showLink": false
    })});
    </script>
    <nav>
        <table>
            <tr>
                <td class="menuselected" style="width:40px"><a href="/"><img src="/images/home.png" alt="Home" title="Home" width="40" height="40" /></a></td>
                <td class="menutext"><a href="/official-server.html">Official server</a></td>
                <td class="menutext"><a href="/download.html">Download</a></td>
                <td class="menutext"><a href="/screenshot.html">Screenshots</a></td>
                <td class="menutext"><a href="https://shop.first-world.info/en/">Shop</a></td>
                <td style="width:40px"><a href="/contact.html"><img src="/images/contact.png" alt="Contact" title="Contact" width="40" height="40" /></a></td>
            </tr>
        </table>
    </nav>
    <center>
