<?php
namespace SpareParts\Overseer\Tests\Assembly;

use SpareParts\Overseer\Assembly\VotingAbilityAwareAssembly;
use SpareParts\Overseer\Context\IIdentityContext;
use SpareParts\Overseer\Context\IVotingContext;
use SpareParts\Overseer\StrategyEnum;

class VotingAbilityAwareAssemblyCanVoteOnTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @test
     */
    public function canVoteOnPass()
    {
        $assembly = new VotingAbilityAwareAssembly(
            StrategyEnum::ALLOW_UNLESS_DENIED(),
            [],
            'read',
            'SomeKindOfArticle',
            IVotingContext::class
        );

        $result = $assembly->canVoteOn('read', $this->prepareSubject('category'), $this->prepareContext());
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function canVoteOnRejectsSubject()
    {
        $assembly = new VotingAbilityAwareAssembly(
            StrategyEnum::ALLOW_UNLESS_DENIED(),
            [],
            'read',
            'SomeKindOfArticle',
            IIdentityContext::class
        );

        $result = $assembly->canVoteOn('read', $this->prepareSubject('product'), $this->prepareContext());
        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function canVoteOnRejectsAction()
    {
        $assembly = new VotingAbilityAwareAssembly(
            StrategyEnum::ALLOW_UNLESS_DENIED(),
            [],
            'read',
            'SomeKindOfArticle',
            IVotingContext::class
        );

        $result = $assembly->canVoteOn('write', $this->prepareSubject('category'), $this->prepareContext());
        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function canVoteOnRejectsContext()
    {
        $assembly = new VotingAbilityAwareAssembly(
            StrategyEnum::ALLOW_UNLESS_DENIED(),
            [],
            'read',
            'SomeKindOfArticle',
            IIdentityContext::class
        );

        $result = $assembly->canVoteOn('read', $this->prepareSubject('category'), $this->prepareContext());
        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function canVoteOnWorksWithMultipleActions()
    {
        $assembly = new VotingAbilityAwareAssembly(
            StrategyEnum::ALLOW_UNLESS_DENIED(),
            [],
            ['read', 'write'],
            'SomeKindOfArticle',
            IVotingContext::class
        );

        $result = $assembly->canVoteOn('read', $this->prepareSubject('category'), $this->prepareContext());
        $this->assertTrue($result);
        $result = $assembly->canVoteOn('write', $this->prepareSubject('category'), $this->prepareContext());
        $this->assertTrue($result);
        $result = $assembly->canVoteOn('delete', $this->prepareSubject('category'), $this->prepareContext());
        $this->assertFalse($result);
    }

    /**
     * @param string $subject
     *
     * @return \Mockery\MockInterface|SomeKindOfArticle
     */
    private function prepareSubject($subject)
    {
        $subject = \Mockery::mock('SomeKindOfArticle');
        return $subject;
    }

    /**
     * @param mixed $id
     * @param string[] $roles
     *
     * @return \Mockery\MockInterface|IVotingContext
     */
    private function prepareContext()
    {
        $context = \Mockery::mock(IVotingContext::class);
        return $context;
    }

    public function tearDown()
    {
        \Mockery::close();
    }
}
