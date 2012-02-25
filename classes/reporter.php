<?php
require '../api/FaceRestClient.php';
require 'scraper/test.php';

$face = new FaceRestClient('67afa8236381726623decc8f17e909dc','74b08f0899251135728e83bd172135e8');
$scrape = new scrapper_test();

$pics = $scrape->getImages();

$info = $face->faces_detect($pics);


var_dump($info);
