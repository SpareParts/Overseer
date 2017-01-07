<?php
namespace SpareParts\Overseer\Tests\Assembly;


use SpareParts\Overseer\Assembly\VotingAssembly;
use SpareParts\Overseer\Context\IVotingContext;
use SpareParts\Overseer\StrategyEnum;
use SpareParts\Overseer\Voter\IVoter;
use SpareParts\Overseer\Voter\SingleVoterResult;
use SpareParts\Overseer\VotingDecisionEnum;

class VotingAssemblyCommenceVoteAllowUnlessDeniedTest extends \PHPUnit_Framework_TestCase
{


    public function tearDown()
    {
        \Mockery::close();
    }

    /**
     * @test
     */
    public function works()
    {
        $assembly = new VotingAssembly(
            StrategyEnum::ALLOW_UNLESS_DENIED(),
            [
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('RandomArticleClass', IVotingContext::class)->andReturnNull()->getMock(),
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('RandomArticleClass', IVotingContext::class)->andReturn(
                    new SingleVoterResult(VotingDecisionEnum::ALLOWED(), 'reason 1')
                )->getMock(),
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('RandomArticleClass', IVotingContext::class)->andReturn(
                    new SingleVoterResult(VotingDecisionEnum::DENIED(), 'reason 2')
                )->getMock(),
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('RandomArticleClass', IVotingContext::class)->andReturn(
                    new SingleVoterResult(VotingDecisionEnum::DENIED(), 'reason 3')
                )->getMock(),
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('RandomArticleClass', IVotingContext::class)->andReturnNull()->getMock()    ,
            ]
        );

        $result = $assembly->commenceVote(
            $this->prepareSubject('article'),
            $this->prepareContext()
        );

        $this->assertSame(VotingDecisionEnum::DENIED(), $result->getDecision());
        $this->assertCount(2, $result->getPartialResults());
        $this->assertEquals('reason 1', $result->getPartialResults()[0]->getReason());
        $this->assertEquals('reason 2', $result->getPartialResults()[1]->getReason());
    }


    /**
     * @test
     */
    public function zeroVotersDecideAllow()
    {
        $assembly = new VotingAssembly(
            StrategyEnum::ALLOW_UNLESS_DENIED(),
            []
        );
        $result = $assembly->commenceVote(
            $this->prepareSubject('article'),
            $this->prepareContext()
        );
        $this->assertSame(VotingDecisionEnum::ALLOWED(), $result->getDecision());
        $this->assertCount(0, $result->getPartialResults());
    }


    /**
     * @test
     */
    public function ifVotersDontDecideThenAllowed()
    {
        $assembly = new VotingAssembly(
            StrategyEnum::ALLOW_UNLESS_DENIED(),
            [
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('RandomArticleClass', IVotingContext::class)->andReturnNull()->getMock(),
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('RandomArticleClass', IVotingContext::class)->andReturnNull()->getMock(),
            ]
        );
        $result = $assembly->commenceVote(
            $this->prepareSubject('article'),
            $this->prepareContext()
        );
        $this->assertSame(VotingDecisionEnum::ALLOWED(), $result->getDecision());
        $this->assertCount(0, $result->getPartialResults());
    }


    /**
     * @param string $subject
     *
     * @return \Mockery\MockInterface|RandomArticleClass
     */
    private function prepareSubject($subject)
    {
        $subject = \Mockery::mock('RandomArticleClass');
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
}
