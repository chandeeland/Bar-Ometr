<?php
include '../classes/reporter.php'; 


session_start();
if (array_key_exists('v', $_REQUEST)) {
    $v = $_REQUEST['v'];
    $pics = unserialize(urldecode($v));

    $report = new Venue();
    $face = new FaceRestClient('67afa8236381726623decc8f17e909dc','74b08f0899251135728e83bd172135e8');
    $info = $face->faces_group($pics);
    
    $count_people = 0;
    foreach ($info->photos as $photo) {
        $report->population += count($photo->tags);
        foreach ($photo->tags as $tag) {
            $tag->url = $photo->url;
            $tag->total_height = $photo->height;
            $tag->total_width = $photo->width;
            $report->addPerson(new Person($tag), $tag->gid);
            $count_people++;
        }
    }
    if (!$count_people) { header('Location: empty.php'); }
    
    $_SESSION['pics'] = $pics;
    $_SESSION['report'] = $report;
    $_SESSION['qname'] = $qname = $_REQUEST['qname'];

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
        if ($v > 0) {
            $data[] = "['{$k}', $v]";   
        }
    }
    if (empty($data)) return '';

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
	<link href='http://fonts.googleapis.com/css?family=Satisfy' rel='stylesheet' type='text/css'>	<!--link rel="stylesheet" type="text/css" href="css/mobile.css" media="only screen and (min-width: 320px)" />
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
  <!-- Load Feather code -->
<script type="text/javascript" src="http://feather.aviary.com/js/feather.js"></script>

<!-- Instantiate Feather -->
<script type="text/javascript">
        var featherEditor = new Aviary.Feather({
            apiKey: '8d705e3f1',
            apiVersion: 2,
            tools: 'all',
            appendTo: '',
            onSave: function(imageID, newURL) {
                var img = document.getElementById(imageID);
                img.src = newURL;
            }
        });

        function launchEditor(id, src) {
            featherEditor.launch({
                image: id,
                url: src
            });
            return false;
        }
</script>
</head>


<body>	
	<!-- Prompt IE 6 users to install Chrome Frame. Remove this if you support IE 6.
       chromium.org/developers/how-tos/chrome-frame-getting-started -->
  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
  
 	<div class="wrapper">
	
		<header>
			<h1 class="ir"><a href=""><?= $qname; ?></a></h1>
			<form class="find"  method="post" action="/index.php">
				<label class=" visuallyhidden" for="findALocation">Find a Location's mood</label>
				<input name="q" id="findALocation" placeholder="Search a location's mood" />
				<input class="button" type="submit" value="Go" />
			</form>
			
		</header>

        

		<section id="results-container" class="clearfix">
		
			<section id="pulled-data">
			    <h1 class=""><?= $qname; ?></h1>
				<?php $report->stats(); ?>
			</section>
	
			<section id="badges">
				<?php
				foreach ($report->getBadges() as $b) {
					echo '<li>' . $b;
				}
				?>
			</section>
	
            <section>
                <div id="injection_site"></div>
            </section>

			<!-- section id="people">
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
			</section -->

			<div id="results">
                <div id="container"></div>
                <div id="container2"></div>
                <?php foreach ($pics as $k=>$p) : ?>
				<article class="item">
					<a href="/detail.php?i=<?= $k; ?>"><img id="img<?= $k?>" src="<?= $p; ?>" alt="" /></a>

<!-- Add an edit button, passing the HTML id of the image and the public URL ot the image -->
<p><input type="image" src="http://advanced.aviary.com/images/feather/edit-photo.png" 
value="Edit photo" onclick="return launchEditor('<?= "img$k"; ?>', '<?= $p; ?>');" /></p>  
				</article>
                <?php endforeach; ?>
			</div>
		</section>
		
		<?php require 'footer.php'; ?>
	</div>
		

</body>
</html>

