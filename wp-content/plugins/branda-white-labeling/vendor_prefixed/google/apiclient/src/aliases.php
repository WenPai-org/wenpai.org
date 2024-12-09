<?php

namespace Branda_Vendor; return;

if (\class_exists('Branda_Vendor\\Google_Client', \false)) {
    // Prevent error with preloading in PHP 7.4
    // @see https://github.com/googleapis/google-api-php-client/issues/1976
    return;
}
$classMap = ['Branda_Vendor\\Google\\Client' => 'Google_Client', 'Branda_Vendor\\Google\\Service' => 'Google_Service', 'Branda_Vendor\\Google\\AccessToken\\Revoke' => 'Google_AccessToken_Revoke', 'Branda_Vendor\\Google\\AccessToken\\Verify' => 'Google_AccessToken_Verify', 'Branda_Vendor\\Google\\Model' => 'Google_Model', 'Branda_Vendor\\Google\\Utils\\UriTemplate' => 'Google_Utils_UriTemplate', 'Branda_Vendor\\Google\\AuthHandler\\Guzzle6AuthHandler' => 'Google_AuthHandler_Guzzle6AuthHandler', 'Branda_Vendor\\Google\\AuthHandler\\Guzzle7AuthHandler' => 'Google_AuthHandler_Guzzle7AuthHandler', 'Branda_Vendor\\Google\\AuthHandler\\Guzzle5AuthHandler' => 'Google_AuthHandler_Guzzle5AuthHandler', 'Branda_Vendor\\Google\\AuthHandler\\AuthHandlerFactory' => 'Google_AuthHandler_AuthHandlerFactory', 'Branda_Vendor\\Google\\Http\\Batch' => 'Google_Http_Batch', 'Branda_Vendor\\Google\\Http\\MediaFileUpload' => 'Google_Http_MediaFileUpload', 'Branda_Vendor\\Google\\Http\\REST' => 'Google_Http_REST', 'Branda_Vendor\\Google\\Task\\Retryable' => 'Google_Task_Retryable', 'Branda_Vendor\\Google\\Task\\Exception' => 'Google_Task_Exception', 'Branda_Vendor\\Google\\Task\\Runner' => 'Google_Task_Runner', 'Branda_Vendor\\Google\\Collection' => 'Google_Collection', 'Branda_Vendor\\Google\\Service\\Exception' => 'Google_Service_Exception', 'Branda_Vendor\\Google\\Service\\Resource' => 'Google_Service_Resource', 'Branda_Vendor\\Google\\Exception' => 'Google_Exception'];
foreach ($classMap as $class => $alias) {
    \class_alias($class, $alias);
}
/**
 * This class needs to be defined explicitly as scripts must be recognized by
 * the autoloader.
 */
class Google_Task_Composer extends \Branda_Vendor\Google\Task\Composer
{
}
/**
 * This class needs to be defined explicitly as scripts must be recognized by
 * the autoloader.
 */
\class_alias('Branda_Vendor\\Google_Task_Composer', 'Google_Task_Composer', \false);
if (\false) {
    class Google_AccessToken_Revoke extends \Branda_Vendor\Google\AccessToken\Revoke
    {
    }
    class Google_AccessToken_Verify extends \Branda_Vendor\Google\AccessToken\Verify
    {
    }
    class Google_AuthHandler_AuthHandlerFactory extends \Branda_Vendor\Google\AuthHandler\AuthHandlerFactory
    {
    }
    class Google_AuthHandler_Guzzle5AuthHandler extends \Branda_Vendor\Google\AuthHandler\Guzzle5AuthHandler
    {
    }
    class Google_AuthHandler_Guzzle6AuthHandler extends \Branda_Vendor\Google\AuthHandler\Guzzle6AuthHandler
    {
    }
    class Google_AuthHandler_Guzzle7AuthHandler extends \Branda_Vendor\Google\AuthHandler\Guzzle7AuthHandler
    {
    }
    class Google_Client extends \Branda_Vendor\Google\Client
    {
    }
    class Google_Collection extends \Branda_Vendor\Google\Collection
    {
    }
    class Google_Exception extends \Branda_Vendor\Google\Exception
    {
    }
    class Google_Http_Batch extends \Branda_Vendor\Google\Http\Batch
    {
    }
    class Google_Http_MediaFileUpload extends \Branda_Vendor\Google\Http\MediaFileUpload
    {
    }
    class Google_Http_REST extends \Branda_Vendor\Google\Http\REST
    {
    }
    class Google_Model extends \Branda_Vendor\Google\Model
    {
    }
    class Google_Service extends \Branda_Vendor\Google\Service
    {
    }
    class Google_Service_Exception extends \Branda_Vendor\Google\Service\Exception
    {
    }
    class Google_Service_Resource extends \Branda_Vendor\Google\Service\Resource
    {
    }
    class Google_Task_Exception extends \Branda_Vendor\Google\Task\Exception
    {
    }
    interface Google_Task_Retryable extends \Branda_Vendor\Google\Task\Retryable
    {
    }
    class Google_Task_Runner extends \Branda_Vendor\Google\Task\Runner
    {
    }
    class Google_Utils_UriTemplate extends \Branda_Vendor\Google\Utils\UriTemplate
    {
    }
}
