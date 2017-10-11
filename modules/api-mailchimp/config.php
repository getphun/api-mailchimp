<?php
/**
 * api-mailchimp config file
 * @package api-mailchimp
 * @version 0.0.1
 * @upgrade true
 */

return [
    '__name' => 'api-mailchimp',
    '__version' => '0.0.1',
    '__git' => 'https://github.com/getphun/api-mailchimp',
    '__files' => [
        'modules/api-mailchimp' => [ 'install', 'remove', 'update' ]
    ],
    '__dependencies' => [
        'site-param'
    ],
    '_server' => [
        'PHP Lib cURL' => 'ApiMailchimp\\Library\\Server::curlLib'
    ],
    '_services' => [
        'mc' => 'ApiMailchimp\\Service\\Mailchimp'
    ],
    '_autoload' => [
        'classes' => [
            'DrewM\\MailChimp\\MailChimp'      => 'modules/api-mailchimp/library/MailChimp.php',
            'DrewM\\MailChimp\\Batch'          => 'modules/api-mailchimp/library/Batch.php',
            'ApiMailchimp\\Library\\Server'    => 'modules/api-mailchimp/library/Server.php',
            'ApiMailchimp\\Service\\Mailchimp' => 'modules/api-mailchimp/service/Mailchimp.php'
        ],
        'files' => []
    ]
];