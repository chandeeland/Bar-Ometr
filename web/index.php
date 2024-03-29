<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!-- Consider adding a manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->

<head>
	<title></title>
  	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<meta name="viewport" content="width=device-width, minimum-scale=1.0;">
	
	<meta name="author" content="" />
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	
	<link rel="shortcut icon" href="images/favicon.ico">
    <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png">
	
	<!-- Le styles -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-responsive.css" rel="stylesheet">
	
	<link rel="stylesheet" type="text/css" href="css/base.css" media="screen, handheld" />
	<link href='http://fonts.googleapis.com/css?family=Satisfy' rel='stylesheet' type='text/css'>
	<!--link rel="stylesheet" type="text/css" href="css/mobile.css" media="only screen and (min-width: 320px)" />
	<link rel="stylesheet" type="text/css" href="css/desktop.css" media="only screen and (min-width: 720px)" /-->
	
	<!--[if IE]>
		<script type="text/javascript" src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<link rel="stylesheet" type="text/css" href="css/base.css" media="all" />
		<link rel="stylesheet" type="text/css" href="css/tweaks-IE.css" media="all" />
	<![endif]-->
	<!--[if IE 7]>
		<link rel="stylesheet" type="text/css" href="css/tweaks-IE7.css" media="all" />
	<![endif]-->
	<!--[if IEMobile 7]>
		<script type="text/javascript" src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<link rel="stylesheet" type="text/css" href="css/base.css" media="all" />
		<link rel="stylesheet" type="text/css" href="css/mobile.css" media="all" />
		<link rel="stylesheet" type="text/css" href="css/tweaks-WP7.css" media="all" />
	<![endif]-->

	<script src="js/libs/modernizr-2.5.0.min.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
</head>


<body>	
	<!-- Prompt IE 6 users to install Chrome Frame. Remove this if you support IE 6.
       chromium.org/developers/how-tos/chrome-frame-getting-started -->
  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
  
 	<div class="wrapper">
	
		<header>
			<h1 class="ir"><a href="">Co Name Here</a></h1>
			<form class="find visuallyhidden"  method="post" action="/index.php">
				<label class="visuallyhidden" for="findALocation">Find a Location's mood</label>
				<input name="q" id="findALocation" placeholder="Search a location's mood" />
				<input class="button" type="submit" value="Go" />>
			</form>
		</header>
		
		
		
		<section id="what-we-do">
			<h2>What's The Mood</h2>
			<p>Get a taste of what the vibe is at your next destination to prevent a shitty outting.</p>
		
			<form class="find" method="post" action="/index.php">
				<label class="visuallyhidden" for="findALocation">Find a Location's mood</label>
				<input name="q" id="findALocation" placeholder="Where you heading?" />
				<input class="button" type="submit" value="Go" />
			</form>
		</section>
		
		<section id="scrape-me">
		
		
<?php
require('../classes/scraper/foursquare.php');

if (isset($_REQUEST['q']) && $q = $_REQUEST['q']) {
    $scrape = new foursquare();
    $venues = $scrape->search_venue('40.7,-74', $_REQUEST['q'], 10);

    foreach($venues as $k => $v) {
        $qname = $v['name'];
    	if (isset($v['categories'][0])) {
       	 	$qname .= " ({$v['categories'][0]['name']})";
        }
        $pics = urlencode(serialize($scrape->get_photos($venues[$k]['id'])));
        echo "<li><a href=\"/report.php?v=$pics&qname=".urlencode($qname)."\">$qname </a>\n";
    }

}     
?>
        </section>
<?php require 'footer.php'; ?>
		</div>
	
	<!-- JavaScript at the bottom for fast page loading -->
	
	<script src="../html/js/rspimg.js"></script>


	

</body>
</html>
