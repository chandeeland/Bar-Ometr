<?php

// EXAMPLE
/*
$photos = array();

$fs = new foursquare();
$v = $fs->search_venue('40.7,-74', 'slate', 1);
foreach ($v as $venue) {
    $a = $fs->get_photos($venue['id']);
    if (count($a)) {
        $photos[$venue['id']] = $a;
    }
        

}

var_dump($photos);
*/

class foursquare {

    const API_URL = 'https://api.foursquare.com/v2';
    const API_CLIENT_ID = 'BFSMX3K1QWGQEWIDSGBA5WKV1SCXHSHD5QVF5EH2PS2EJ5BF';
    const API_CLIENT_SECRET = 'UESIX4LXRRFL3NZDMBBIG5EFOQSS0VBCYBQNTYMDWYWSUNNH';

    
    public function search_venue($ll, $query, $limit = 10, $intent = 'checkin') {
        $params = array(
            'client_id' => self::API_CLIENT_ID,
            'client_secret' => self::API_CLIENT_SECRET,
            'll' => $ll, 
            'query' => $query, 
            'limit' => $limit, 
            'intent' => $intent,
            );
        $ret = $this->_curl('/venues/search', $params, 'GET');
        $ret = json_decode($ret, 1);
        return $ret['response']['groups'][0]['items'];
    }
    
    /*
    public function get_photos($venue_id, $group = 'venue') {
        $params = array(
            'client_id' => self::API_CLIENT_ID,
            'client_secret' => self::API_CLIENT_SECRET,
            'venue_id' => $venue_id, 
            'group' => $group, 
            );
        $ret = $this->_curl("/venues/$venue_id/photos", $params, 'GET');
        $ret = json_decode($ret, 1);
        $photos = $ret['response']['photos']['items'];

        $r = array();
        foreach ($photos as $k => $c) {
            $r[] = $c['url'];
        }

        return $r;
    }
    */

    public function get_photos($venue_id) {
        $params = array(
            'client_id' => self::API_CLIENT_ID,
            'client_secret' => self::API_CLIENT_SECRET,
            'requests' => "/venues/$venue_id/photos?group=venue,/venues/$venue_id/photos?group=checkin",
        );

        $r = array();
        $ret = $this->_curl("/venues/$venue_id/photos", $params, 'GET');
        $ret = json_decode($ret, 1);
        if (array_key_exists('response', $ret) && array_key_exists('photos', $ret['response']) && array_key_exists('groups', $ret['response']['photos'])) {
            $photos = $ret['response']['photos']['groups'];
            foreach ($photos as $group) {
                if (array_key_exists('items', $group)) {
                    foreach ($group['items'] as $c) {
                        $r[] = $c['url'];
                    }
                }
            }
        }

        return $r;

    }

    public function _curl($url, $parameters, $method = 'GET') {
        $url = self::API_URL . $url;

        $ci = curl_init();

        switch ($method) {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, TRUE);
                if (count($parameters)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, http_build_query($parameters));
                }
                break;

            case 'DELETE':
                curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (count($parameters)) {
                    $url = $url . '?' . http_build_query($parameters);
                }
                break;

            case 'GET':
            default:
                if (count($parameters)) {
                    $url = $url . '?' . http_build_query($parameters);
                }
        }

        curl_setopt($ci, CURLOPT_USERAGENT, 'PHD v0.1');
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ci, CURLOPT_MAXREDIRS, 4);

        curl_setopt($ci, CURLOPT_URL, $url);

        $html = curl_exec($ci);

        curl_close ($ci);


        return $html;
    }



}
