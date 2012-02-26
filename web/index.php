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
    <link href="../html/css/bootstrap.css" rel="stylesheet">
    <link href="../html/css/bootstrap-responsive.css" rel="stylesheet">
	
	<link rel="stylesheet" type="text/css" href="../html/css/base.css" media="screen, handheld" />
	<!--link rel="stylesheet" type="text/css" href="../html/css/mobile.css" media="only screen and (min-width: 320px)" />
	<link rel="stylesheet" type="text/css" href="../html/css/desktop.css" media="only screen and (min-width: 720px)" /-->
	
	<!--[if IE]>
		<script type="text/javascript" src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<link rel="stylesheet" type="text/css" href="../html/css/base.css" media="all" />
		<link rel="stylesheet" type="text/css" href="../html/css/tweaks-IE.css" media="all" />
	<![endif]-->
	<!--[if IE 7]>
		<link rel="stylesheet" type="text/css" href="../html/css/tweaks-IE7.css" media="all" />
	<![endif]-->
	<!--[if IEMobile 7]>
		<script type="text/javascript" src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<link rel="stylesheet" type="text/css" href="../html/css/base.css" media="all" />
		<link rel="stylesheet" type="text/css" href="../html/css/mobile.css" media="all" />
		<link rel="stylesheet" type="text/css" href="../html/css/tweaks-WP7.css" media="all" />
	<![endif]-->

	<script src="../html/js/libs/modernizr-2.5.0.min.js"></script>
  
</head>


<body>	
	<!-- Prompt IE 6 users to install Chrome Frame. Remove this if you support IE 6.
       chromium.org/developers/how-tos/chrome-frame-getting-started -->
  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
  
 	<div class="row wrapper">
	
		<header>
			<h1 class="ir"><a href="">Co Name Here</a></h1>
		</header>
		
		
		
		<section>
			<h2>Header 2</h2>
			<p>THis is where we will get people to buy in to input a location</p>
		
			<form method="post" action="/index.php">
				<label for="findALocation">Find a Location</label>
				<input name="q" id="findALocation" />
				<button>Go<!-- icon --></button>
			</form>
		</section>
		
		<section>
<?php
require('../classes/scraper/foursquare.php');

if (isset($_REQUEST['q']) && $q = $_REQUEST['q']) {
    $scrape = new foursquare();
    $venues = $scrape->search_venue('40.7,-74', $_REQUEST['q'], 10);

    foreach($venues as $k => $v) {
        $name = $v['name'];
	if (isset($v['categories'][0]))
       	 	$cat =  $v['categories'][0]['name'];
        $pics = urlencode(serialize($scrape->get_photos($venues[$k]['id'])));
        
        echo "<li><a href=\"/report.php?v=$pics\">$name ($cat) </a>\n";
    }

}     
?>
        </section>
		
		<footer>
			<p>This was created by &copy;2012</p>
		</footer>
		
	</div>
	
	<!-- JavaScript at the bottom for fast page loading -->
	
	<!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if necessary -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.1.min.js"><\/script>')</script>
	<!-- Masonry -->
	<script src="../html/masonry.js"></script>
<script src="../html/js/rspimg.js"></script>
	
	<!-- Charting -->
	<script src=""></script>

	

</body>
</html>
