<?php
require('../api/FaceRestClient.php');
require('scraper/foursquare.php');

class venue {
    public $population = 0;
    public $mood = array();
    public $men = 0;
    public $women = 0;
    public $masculinity = 0;
    public $fem = 0;
    public $nerds =0;

    public function getRatio() {
        return $women / ($men + $women);
    }

    public function getManly() {
        return $masculinity / $men;
    }
    
    public function getGirly() {
        return $fem / $women;
    }

    public function getNerdly() {
        $nerd_ratio = $nerds / $population;
    }

    public function stats() {
        echo '<li>RATIO :' .$this->getRatio();
        echo '<li>MANLY :' .$this->getManly();
        echo '<li>GIRLY :' .$this->getGirly();
        echo '<li>NERDLY :' .$this->getNerdly();
    }

    public function badges() {
        $badges = array();
        if ($this->getRatio() < .4) {
            $badges[] = 'Sausage Fest';
        } else if ($this->getRatio() > 6) {
            $badges[] = 'Ladies Night';
        }

        if ($this->getManly() < .5 && $this->getNerdly() > .5) {
            $badges[] = 'Hipster Central';
        }

        return $badges;
    }
}

$report = new Venue();

$face = new FaceRestClient('67afa8236381726623decc8f17e909dc','74b08f0899251135728e83bd172135e8');
$scrape = new foursquare();
$venue = $scrape->search_venue('40.7,-74', 'slate', 1);
$pics = $scrape->get_photos($venue[0]['id']);
$info = $face->faces_detect($pics);

foreach ($info->photos as $photo) {
    $report->population += count($photo->tags);
    foreach ($photo->tags as $tag) {
        // count gender
        if (isset($tag->attributes->gender)) {
            if ($tag->attributes->gender->value == 'male') {
                $report->men++;
                $report->masculinity += $tag->attributes->gender->confidence;
            } else {
                $report->women++;
                $report->fem += $tag->attributes->gender->confidence;
            }
        }

        // nerdiness
        if (isset($tag->attributes->glasses)) {
            if ($tag->attributes->glasses->value) {
                $report->nerds++;
            }
        }

        // mood
        if (isset($tag->attributes->mood)) {
            if (array_key_exists($tag->attributes->mood->value, $mood)) {
                $report->mood[$tag->attributes->mood->value]++;
            } else {
                $report->mood[$tag->attributes->mood->value] = 1;
            }
        }
    }
}

