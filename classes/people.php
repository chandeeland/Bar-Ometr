<?php
class Person {
    public $gender = null;
    public $glasses = false;
    public $mood = null;

    public $image_url;
    public $top;
    public $bottom;
    public $left;
    public $righLt;

    public function __construct($p) {    

        $info = $p->attributes;

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

        $this->image_url = $p->url;
        
        $centerx = $p->total_width * $p->center->x * .01;
        $centery = $p->total_height * $p->center->y * .01;
        $h = $p->total_height * $p->height * .01;
        $w = $p->total_width * $p->width * .01;


        $this->top = round($centery - ($h));
        $this->bottom = round($centery + ($h ));
        $this->left = round($centerx - ($w  ));
        $this->right = round($centerx + ($w ));
    }
}
