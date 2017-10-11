<?php
/**
 * Server tester for the module
 * @package api-mailchimp
 * @version 0.0.1
 * @upgrade true
 */

namespace ApiMailchimp\Library;

class Server{
    static function curlLib(){
        $result = [
            'success' => function_exists('curl_version'),
            'info' => 'Not installed'
        ];
        
        if($result['success'])
            $result['info'] = curl_version()['version'];
        
        return $result;
    }
}