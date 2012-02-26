<?php
require('../api/FaceRestClient.php');
require('scraper/foursquare.php');


class Person {
    public $gender = null;
    public $glasses = false;
    public $mood = null;

    public function __construct($info) {
        // count gender
        if (isset($info->gender)) {
            if ($info->gender->value == 'male') {
                $this->gender = 'male';
                $this->gender_c = $info->gender->confidence;
            } else {
                $this->gender = 'female';
                $this->gender_c = $info->gender->confidence;
            }
        }

        // nerdiness
        if (isset($info->glasses)) {
            if ($info->glasses->value) {
                $this->glasses = true;
            }
        }

        // mood
        if (!empty($info->mood)) {
            $this->mood = $info->mood->value;
        }
    }
}

class venue {
    public $people = array();
    private $ratio = null;

    private $women = null;
    private $men = null;

    private $men_c = null;
    private $women_c = null;

    private $glasses = null;

    public function addPerson(Person $p) {
        $this->people[] = $p;
    }


    public function countWomen() {
        if (is_null($this->women)) {
            $this->women = 0;
            $this->men = 0;
            foreach ($this->people as $p) {
                if ($p->gender == 'female') {
                    $this->women++;
                    $this->women_c += $p->gender_c;
                } else if ($p->gender == 'male') {
                    $this->men++;
                    $this->men_c += $p->gender_c;
                }
            }
        }
        return $this->women;
    }

    public function countMen() {
        if (is_null($this->men)) {
            $this->countWomen();
        }
        return $this->men;
    }

    public function getRatio() {
        if (is_null($this->ratio)) {
            $this->ratio = round($this->countWomen() / ($this->countMen() + $this->countWomen()),2);
        }
        return $this->ratio;
    }

    // average confidence in the male gender
    public function getManly() {
        $m = $this->countMen();
        return round($this->men_c / $m / 100,2);
    }
    
    // average confidence in the female gender
    public function getGirly() {
        $w = $this->countWomen();
        return round($this->women_c / $w / 100,2);
    }

    public function getNerdly() {
        if (is_null($this->glasses)) {
            $glasses = 0;
            foreach ($this->people as $p) {
                if ($p->glasses) {
                    $glasses++;
                }
            }
        }
        
        return round($this->glasses / count($this->people), 2);
    }


    public function getMood() {
        $moods = array();
        $mood_percent = array();
        $total = 0;

        foreach ($this->people as $p) {
            if (!empty($p->mood)) {
                if (array_key_exists($p->mood, $moods)) {
                    $moods[$p->mood]++;
                } else {
                    $moods[$p->mood] = 1;
                }
            
                $total++;
            }
        }

        foreach ($moods as $k => $m) {
            $mood_percent[$k] = round($m/$total,2);
        }

        return $mood_percent;
    }

    public function stats() {
        echo '<li>POPULATION : ' . count($this->people);
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

