<?php
namespace Jgauthi\Component\Traits;

trait SingletonTrait
{
    /**
     * private construct, generally defined by using class
     */
    //private function __construct() {}

    public static function getInstance(): self
    {
        static $instance = null;
        return $instance ?: $instance = new self;
    }

    public function __clone()
    {
        trigger_error('Cloning '.__CLASS__.' is not allowed.',E_USER_ERROR);
    }

    public function __wakeup()
    {
        trigger_error('Unserializing '.__CLASS__.' is not allowed.',E_USER_ERROR);
    }
}