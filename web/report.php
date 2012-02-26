<?php
include '../classes/reporter.php'; 


session_start();
if (array_key_exists('v', $_REQUEST)) {
    $v = $_REQUEST['v'];
    $pics = unserialize(urldecode($v));

    $report = new Venue();
    $face = new FaceRestClient('67afa8236381726623decc8f17e909dc','74b08f0899251135728e83bd172135e8');
    $info = $face->faces_group($pics);
    
    foreach ($info->photos as $photo) {
        $report->population += count($photo->tags);
        foreach ($photo->tags as $tag) {
            $tag->url = $photo->url;
            $report->addPerson(new Person($tag), $tag->gid);
        }
    }
    
    $_SESSION['pics'] = $pics;
    $_SESSION['report'] = $report;
    $_SESSION['qname'] = $_REQUEST['qname'];

} else if (array_key_exists('pics', $_SESSION)) {
    $pics = $_SESSION['pics'];
    $report = $_SESSION['report'];
    $qname = $_SESSION['qname'];

} else {
    header('Location: /search.php');
}



function pie_chart($title, $slices, $container = 'container') {
    $data = array();
    foreach ($slices as $k=>$v) {
        $data[] = "['{$k}', $v]";   
    }
    return "
    $(document).ready(function() {
	chart = new Highcharts.Chart({
		chart: {
			renderTo: '{$container}',
			plotBackgroundColor: null,
			plotBorderWidth: null,
			plotShadow: false
		},
		title: {
			text: '{$title}'
		},
		tooltip: {
			formatter: function() {
				return '<b>'+ this.point.name +'</b>: '+ this.percentage +' %';
			}
		},
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: true,
					color: '#000000',
					connectorColor: '#000000',
					formatter: function() {
						return '<b>'+ this.point.name +'</b>: '+ this.percentage +' %';
					}
				}
			}
		},
		series: [{
			type: 'pie',
			name: '{$title}',
			data: [
                " .implode(',', $data). "
			]
		}]
	});
});
    "; 
}



?>
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
	<script type="text/javascript">
    var chart;
    <?php
        echo pie_chart('Overall Mood', $report->getMood(), 'container');
        echo pie_chart('Gender Ratio', array('Men'=> $report->countMen(),
            'Women' => $report->countWomen(),
            //'???'=> (count($report->people) - ($report->countMen() + $report->countWomen())),
            ),'container2');
    ?>
	</script> 
    <script type="text/javascript" src="js/highcharts.js"></script>
    <script type="text/javascript" src="js/modules/exporting.js"></script>
</head>


<body>	
	<!-- Prompt IE 6 users to install Chrome Frame. Remove this if you support IE 6.
       chromium.org/developers/how-tos/chrome-frame-getting-started -->
  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
  
 	<div class="row wrapper">
	
		<header>
			<h1 class="ir"><a href=""><?= $qname; ?></a></h1>
			<form class="find">
				<label class=" visuallyhidden" for="findALocation">Find a Location</label>
				<input placeholder="Search a location" />
				<button>Go<!-- icon --></button>
			</form>
		</header>

        <section>
    	    <?php $report->stats(); ?>
        </section>

        <section id="badges">
            <?php
            foreach ($report->getBadges() as $b) {
                echo '<li>' . $b;
            }
            ?>
        </section>
		
       <!-- 
        <section id="people">
            <?php foreach ($report->people as $k => $group) : ?>
                <?php $max = 0; $max_i = 0; ?>
                <?php foreach ($group as $i => $curr) : ?>
                    <?php if (($curr->bottom - $curr->top) > $max) : ?>
                        <?php $max = $curr->bottom - $curr->top; $max_i = $i; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php $curr = $report->people[$k][$max_i] ;?>

            <div style="position:relative;height:<?= $curr->bottom - $curr->top; ?>;">
                <div style="position:absolute;clip:rect(<?= "{$curr->top}px {$curr->bottom}px {$curr->right}px {$curr->left}px";?>);">
                    <img src="<?= $curr->image_url; ?>" />
                </div>
            </div>
            <?php endforeach; ?>
        </section>
        -->

		<section id="results-container" class="clearfix">
			<div id="results">
                <div id="container"></div>
                <div id="container2"></div>
                <?php foreach ($pics as $k=>$p) : ?>
				<article class="item">
					<a href="/detail.php?i=<?= $k; ?>"><img src="<?= $p; ?>" alt="" /></a>
				</article>
                <?php endforeach; ?>
			</div>
		</section>
		
		<footer>
			<p>This was created by &copy;2012</p>
		</footer>
		
	</div>
	
	<!-- JavaScript at the bottom for fast page loading -->
	

</body>
</html>

