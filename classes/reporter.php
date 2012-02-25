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

    public function getMood() {
        $total = array_sum($this->mood);
        foreach ($this->mood as $k => $m) {
            $mood_percent[$k] = round($m/$total,2);
        }
        return $mood_percent;
    }

    public function getRatio() {
        return round($this->women / ($this->men + $this->women),2);
    }

    public function getManly() {
        return round($this->masculinity / $this->men / 100,2);
    }
    
    public function getGirly() {
        return round($this->fem / $this->women / 100,2);
    }

    public function getNerdly() {
        return round($this->nerds / $this->population,2);
    }

    public function stats() {
        echo '<li>POPULATION : ' . $this->population;
        echo '<li>RATIO :' .$this->getRatio();
        echo '<li>MANLY :' .$this->getManly();
        echo '<li>GIRLY :' .$this->getGirly();
        echo '<li>NERDLY :' .$this->getNerdly();
    }

    public function getBadges() {
        $pm = $this->getMood();
        $badges = array();
        if ($this->getRatio() < 0.5) {
            $badges[] = 'Sausage Fest';
            if ($pm['sad'] + $pm['surprised'] > 0.50) {
                $badges[] = 'Lots of sadness, lots of surprise, are these guys coding?';
            }
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
            if (array_key_exists($tag->attributes->mood->value, $report->mood)) {
                $report->mood[$tag->attributes->mood->value]++;
            } else {
                $report->mood[$tag->attributes->mood->value] = 1;
            }
        }
    }
}

