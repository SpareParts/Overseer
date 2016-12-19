<?php
namespace SpareParts\Overseer\Tests\Integration;


use SpareParts\Overseer\Voter\IVotingSubject;

class DummyArticle implements IVotingSubject
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

    /**
     * @return string
     */
    public function getVotingSubjectName()
    {
        return 'article';
    }
}