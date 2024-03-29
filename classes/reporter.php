<?php
require('../api/FaceRestClient.php');
require('scraper/foursquare.php');
require('people.php');

class venue {
    public $people = array();
    private $ratio = null;

    private $women = null;
    private $men = null;

    private $men_c = null;
    private $women_c = null;

    private $glasses = null;

    public function addPerson(Person $p, $gid) {
        static $i = 0;
        if (empty($gid)) $gid = 'DEFAULT' . $i++;
        $this->people[$gid][] = $p;
    }



    public function countWomen() {
        if (is_null($this->women)) {
            $this->women = 0;
            $this->men = 0;
            foreach ($this->people as $group) {
                $p = array_shift($group);
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
            if  ($this->countMen() + $this->countWomen()) {
                $this->ratio = round($this->countWomen() / ($this->countMen() + $this->countWomen()),2);
            } else {
		$this->ratio = 0;
            }
		}
        return $this->ratio;
    }

    // average confidence in the male gender
    public function getManly() {
        $m = $this->countMen();
        if ($m > 0) {
            return round($this->men_c / $m / 100,2);
        } 
        return 0;
    }
    
    // average confidence in the female gender
    public function getGirly() {
        $w = $this->countWomen();
        if ($w > 0) {
            return round($this->women_c / $w / 100,2);
        }
        return 0;
    }

    public function getNerdly() {
        if (!(count($this->people) > 0)) return 0;

        if (is_null($this->glasses)) {
            $glasses = 0;
            foreach ($this->people as $group) {
                foreach ($group as $p);
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
        foreach ($this->people as $group) {
            foreach ($group as $p) {
                if (!empty($p->mood)) {
                    if (array_key_exists($p->mood, $moods)) {
                        $moods[$p->mood]++;
                    } else {
                        $moods[$p->mood] = 1;
                    }
            
                    $total++;
                }
            }
        }

        foreach ($moods as $k => $m) {
            if ($total > 0 ) {
                $mood_percent[$k] = round($m/$total,2);
            } else {
                $mood_percent[$k] = 0;
            }
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
        if ($this->getRatio() < 0.3 and count($this->people) > 4) {
            $badges[] = 'Sausage Fest';
            
            if (array_key_exists('sad', $pm) && array_key_exists('surprised', $pm))
            if ($pm['sad'] + $pm['surprised'] > 0.50) {
                $badges[] = 'Lots of sadness, lots of surprise, are these guys coding?';
            }
        } else if ($this->getRatio() > 6) {
            $badges[] = 'Ladies Night';
        }

	if (array_key_exists('neutral', $pm) && $pm['neutral'] = 1) {
            $badges[] = 'Meh.';
	}
        
	if ($this->getNerdly() > .3) {
            $badges[] = 'Nerd Alert';
        }
        if ($this->getManly() < .5 && $this->getNerdly() > .5) {
            $badges[] = 'Warning! Hipster Central';
        }
 
        if (array_key_exists('angry', $pm) && array_key_exists('happy', $pm))
        if (($pm['angry'] + $pm['happy']) > 0.5) {
            $badges[] = 'High levels of of excitement here, watching sports?';
        }

        if (array_key_exists('sad', $pm))
        if ($pm['sad'] > .4) {
            $badges[] = 'Who died?';
        }

        if (array_key_exists('happy', $pm))
        if ($pm['happy'] > .4) {
            $badges[] = 'Looks like fun';
        } else if ($pm['happy'] > .8) {
            $badges[] = 'Happy Happy Joy Joy!';
        }

    
        return $badges;
    }
}

