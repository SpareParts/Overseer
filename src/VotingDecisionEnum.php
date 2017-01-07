<?php
namespace SpareParts\Overseer;


final class VotingDecisionEnum
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
    public static function ALLOWED()
    {
        return static::instance('allowed');
    }


    /**
     * @return self
     */
    public static function DENIED()
    {
        return static::instance('denied');
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
