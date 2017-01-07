<?php
namespace SpareParts\Overseer\Tests\Integration;



class DummyArticle
{

    /**
     * @var bool
     */
    private $isBanned;

    /**
     * DummyArticle constructor.
     * @param bool $isBanned
     */
    public function __construct($isBanned)
    {
        $this->isBanned = $isBanned;
    }

    /**
     * @return bool
     */
    public function isBanned()
    {
        return $this->isBanned;
    }
}
