<?php
namespace SpareParts\Overseer;


class StrategyEnum
{
    /**
     * @var string
     */
    private $value;

    /**
     * @var self[]
     */
    static private $registry = [];

    private function __construct($value)
    {
        $this->value = $value;
    }


    /**
     * @return self
     */
    public static function FIRST_VOTE_DECIDES()
    {
        return static::instance('first_vote_decides');
    }


    /**
     * @return self
     */
    public static function ALLOW_UNLESS_DENIED()
    {
        return static::instance('allow_unless_denied');
    }


    /**
     * @return self
     */
    public static function DENY_UNLESS_ALLOWED()
    {
        return static::instance('deny_unless_allowed');
    }


    /**
     * @param $string
     * @return self
     */
    private static function instance($string)
    {
        if (!isset(static::$registry[$string])) {
            static::$registry[$string] = new static($string);
        }
        return static::$registry[$string];
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }
}
