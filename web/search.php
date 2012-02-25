<?php
require('scraper/foursquare.php');

if ($q = $_REQUEST['q']) {
    $scrape = new foursquare();
    $venue = $scrape->search_venue('40.7,-74', $_REQUEST['q'], 10);

    foreach($venues as $k => $v) {
        $name = $v['name'];
        $cat =  $v['categories'][0]['name'];
        $pics = urlencode(serialize($scrape->get_photos($venue[$k]['id'])));
        
        echo "<a href=\"/report.php?v=$pics\">$name ($cat) </a>\n";
    }



    exit;
}     
?>
<html>
<head>
<title>search</title>
</head>

<body>
    <form action='/search.php' method="post">
        Fine a venue nearby...<br>
        <input type="text" name="q" />
        <input type="submit" value="search" />
    </form>
</body>
</html>
