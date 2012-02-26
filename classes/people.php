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
        $this->top = round($p->center->y - ($p->height / 2));
        $this->bottom = round($p->center->y + ($p->height / 2));
        $this->left = round($p->center->x - ($p->width / 2));
        $this->right = round($p->center->x - ($p->width / 2));
    }
}
