
<html>
<head>
<title>search</title>
</head>

<body>

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



    exit;
}     
?>
    <form action='/search.php' method="post">
        Fine a venue nearby...<br>
        <input type="text" name="q" />
        <input type="submit" value="search" />
    </form>
</body>
</html>
