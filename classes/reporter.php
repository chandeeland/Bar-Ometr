<?php
require('../api/FaceRestClient.php');
require('scraper/test.php');



$face = new FaceRestClient('67afa8236381726623decc8f17e909dc','74b08f0899251135728e83bd172135e8');
$scrape = new scraper_test();

$pics = $scrape->getImages();

$info = $face->faces_detect($pics);

$population = 0;
$mood = array();
$men = 0;
$women = 0;
$masculinity = 0;
$fem = 0;
$nerds =0;

foreach ($info->photos as $photo) {
    $population += count($photo->tags);
    foreach ($photo->tags as $tag) {
        // count gender
        if (isset($tag->attributes->gender)) {
            if ($tag->attributes->gender->value == 'male') {
                $men++;
                $masculinity += $tag->attributes->gender->confidence;
            } else {
                $women++;
                $fem += $tag->attributes->gender->confidence;
            }
        }

        // nerdiness
        if (isset($tag->attributes->glasses)) {
            if ($tag->attributes->glasses->value) {
                $nerds++;
            }
        }

        // mood
        if (isset($tag->attributes->mood)) {
            if (array_key_exists($tag->attributes->mood->value, $mood)) {
                $mood[$tag->attributes->mood->value]++;
            } else {
                $mood[$tag->attributes->mood->value] = 1;
            }
        }
    }
}

$ratio = $women / ($men + $women);
$manly = $masculinity / $men;
$girly = $fem / $women;
$nerd_ratio = $nerds / $population;

echo "POPULATION:  $population
RATIO:  $ratio
MANLINESS OF MEN: $manly
GIRLINESS OF WOMEN: $girly
NERD RATIO: $nerd_ratio ";

