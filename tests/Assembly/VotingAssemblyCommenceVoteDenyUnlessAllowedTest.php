<?php
namespace SpareParts\Overseer\Tests\Assembly;


use SpareParts\Overseer\Assembly\VotingAssembly;
use SpareParts\Overseer\Context\IVotingContext;
use SpareParts\Overseer\StrategyEnum;
use SpareParts\Overseer\Voter\IVoter;
use SpareParts\Overseer\Voter\SingleVoterResult;
use SpareParts\Overseer\VotingDecisionEnum;

class VotingAssemblyCommenceVoteDenyUnlessAllowedTest extends \PHPUnit_Framework_TestCase
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
            StrategyEnum::DENY_UNLESS_ALLOWED(),
            [
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('SomeKindOfCategory', IVotingContext::class)->andReturnNull()->getMock(),
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('SomeKindOfCategory', IVotingContext::class)->andReturn(
                    new SingleVoterResult(VotingDecisionEnum::DENIED(), 'reason 1')
                )->getMock(),
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('SomeKindOfCategory', IVotingContext::class)->andReturn(
                    new SingleVoterResult(VotingDecisionEnum::DENIED(), 'reason 2')
                )->getMock(),
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('SomeKindOfCategory', IVotingContext::class)->andReturn(
                    new SingleVoterResult(VotingDecisionEnum::ALLOWED(), 'reason 3')
                )->getMock(),
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('SomeKindOfCategory', IVotingContext::class)->andReturn(
                    new SingleVoterResult(VotingDecisionEnum::DENIED(), 'reason 4')
                )->getMock(),
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('SomeKindOfCategory', IVotingContext::class)->andReturnNull()->getMock()    ,
            ]
        );

        $result = $assembly->commenceVote(
            $this->prepareSubject('article'),
            $this->prepareContext()
        );

        $this->assertSame(VotingDecisionEnum::ALLOWED(), $result->getDecision());
        $this->assertCount(3, $result->getPartialResults());
        $this->assertEquals('reason 1', $result->getPartialResults()[0]->getReason());
        $this->assertEquals('reason 2', $result->getPartialResults()[1]->getReason());
        $this->assertEquals('reason 3', $result->getPartialResults()[2]->getReason());
    }


    /**
     * @test
     */
    public function zeroVotersDecideDenied()
    {
        $assembly = new VotingAssembly(
            StrategyEnum::DENY_UNLESS_ALLOWED(),
            []
        );
        $result = $assembly->commenceVote(
            $this->prepareSubject('article'),
            $this->prepareContext()
        );
        $this->assertSame(VotingDecisionEnum::DENIED(), $result->getDecision());
        $this->assertCount(0, $result->getPartialResults());
    }


    /**
     * @test
     */
    public function ifVotersDontDecideThenDenied()
    {
        $assembly = new VotingAssembly(
            StrategyEnum::DENY_UNLESS_ALLOWED(),
            [
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('SomeKindOfCategory', IVotingContext::class)->andReturnNull()->getMock(),
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('SomeKindOfCategory', IVotingContext::class)->andReturnNull()->getMock(),
            ]
        );
        $result = $assembly->commenceVote(
            $this->prepareSubject('article'),
            $this->prepareContext()
        );
        $this->assertSame(VotingDecisionEnum::DENIED(), $result->getDecision());
        $this->assertCount(0, $result->getPartialResults());
    }


    /**
     * @param string $subject
     *
     * @return \Mockery\MockInterface
     */
    private function prepareSubject($subject)
    {
        $subject = \Mockery::mock('SomeKindOfCategory');
        return $subject;
    }

    /**
     * @return \Mockery\MockInterface|IVotingContext
     */
    private function prepareContext()
    {
        $context = \Mockery::mock(IVotingContext::class);
        return $context;
    }
}
