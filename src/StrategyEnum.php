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
    static protected $registry = [];

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
     * @return self
     */
    public static function EVERYONE_MUST_ALLOW_TO_BE_ALLOWED()
    {
        return static::instance('everyone_must_allow_to_be_allowed');
    }


    /**
     * @return self
     */
    public static function EVERYONE_MUST_DENY_TO_BE_DENIED()
    {
        return static::instance('everyone_must_deny_to_be_denied');
    }


    /**
     * @param string $string
     * @return self
     */
    protected static function instance($string)
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
