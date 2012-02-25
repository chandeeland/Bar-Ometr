<?php

if ($v = $_REQUEST['v']) {
    $pics = unserialize(urldecode($v));
} else {
    header('Location: /search.php');
}

require '../classes/reporter.php'; 

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
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Highcharts Example</title>

		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		<script type="text/javascript">
var chart;
<?php
echo pie_chart('Overall Mood', $mood, 'container');
echo pie_chart('Gender Ratio', array('Men'=> $men, 'Women' => $women, '???'=> ($population - ($men + $women))),'container2');
?>
		</script>
	</head>
	<body>
<script type="text/javascript" src="js/highcharts.js"></script>
<script type="text/javascript" src="js/modules/exporting.js"></script>
<?php 
$report->stats();
foreach ($report->getBadges() as $b) {
    echo '<li>' . $b;
}
?>
<div id="container" style="width: 800px; height: 400px; margin: 0 auto"></div>
<div id="container2" style="width: 800px; height: 400px; margin: 0 auto"></div>

<?php foreach ($pics as $p) {
    echo "<img src=\"$p\">\n";
}?>
	</body>
</html>
