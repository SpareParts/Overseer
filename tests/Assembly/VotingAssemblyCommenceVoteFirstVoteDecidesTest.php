<?php
namespace SpareParts\Overseer\Tests\Assembly;


use SpareParts\Overseer\Assembly\VotingAssembly;
use SpareParts\Overseer\Context\IVotingContext;
use SpareParts\Overseer\StrategyEnum;
use SpareParts\Overseer\Voter\IVoter;
use SpareParts\Overseer\Voter\SingleVoterResult;
use SpareParts\Overseer\VotingDecisionEnum;

class VotingAssemblyCommenceVoteFirstVoteDecidesTest extends \PHPUnit_Framework_TestCase
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
            StrategyEnum::FIRST_VOTE_DECIDES(),
            [
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('AwesomeItem', IVotingContext::class)->andReturnNull()->getMock(),
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('AwesomeItem', IVotingContext::class)->andReturnNull()->getMock(),
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('AwesomeItem', IVotingContext::class)->andReturn(
                    new SingleVoterResult(VotingDecisionEnum::ALLOWED(), 'reason test')
                )->getMock(),
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('AwesomeItem', IVotingContext::class)->andReturnNull()->getMock()    ,
            ]
        );

        $result = $assembly->commenceVote(
            $this->prepareSubject('article'),
            $this->prepareContext()
        );

        $this->assertSame(VotingDecisionEnum::ALLOWED(), $result->getDecision());
        $this->assertCount(1, $result->getPartialResults());
        $this->assertEquals('reason test', $result->getPartialResults()[0]->getReason());
    }


    /**
     * @test
     */
    public function multipleVotersCastVoteFirstIsAccepted()
    {
        $assembly = new VotingAssembly(
            StrategyEnum::FIRST_VOTE_DECIDES(),
            [
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('AwesomeItem', IVotingContext::class)->andReturnNull()->getMock(),
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('AwesomeItem', IVotingContext::class)->andReturn(
                    new SingleVoterResult(VotingDecisionEnum::DENIED(), 'correct reason')
                )->getMock(),
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('AwesomeItem', IVotingContext::class)->andReturnNull()->getMock(),
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('AwesomeItem', IVotingContext::class)->andReturn(
                    new SingleVoterResult(VotingDecisionEnum::ALLOWED(), 'wrong reason')
                )->getMock(),
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('AwesomeItem', IVotingContext::class)->andReturnNull()->getMock(),
            ]
        );

        $result = $assembly->commenceVote(
            $this->prepareSubject('article'),
            $this->prepareContext()
        );

        $this->assertSame(VotingDecisionEnum::DENIED(), $result->getDecision());
        $this->assertCount(1, $result->getPartialResults());
        $this->assertEquals('correct reason', $result->getPartialResults()[0]->getReason());
    }


    /**
     * @test
     * @expectedException \SpareParts\Overseer\InvalidVotingResultException
     */
    public function zeroVotersDontDecide()
    {
        $assembly = new VotingAssembly(
            StrategyEnum::FIRST_VOTE_DECIDES(),
            []
        );
        $assembly->commenceVote(
            $this->prepareSubject('article'),
            $this->prepareContext()
        );
    }


    /**
     * @test
     * @expectedException \SpareParts\Overseer\InvalidVotingResultException
     */
    public function ifVotersDontDecideThrowsException()
    {
        $assembly = new VotingAssembly(
            StrategyEnum::FIRST_VOTE_DECIDES(),
            [
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('AwesomeItem', IVotingContext::class)->andReturnNull()->getMock(),
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('AwesomeItem', IVotingContext::class)->andReturnNull()->getMock(),
            ]
        );
        $assembly->commenceVote(
            $this->prepareSubject('article'),
            $this->prepareContext()
        );
    }


    /**
     * @param string $subject
     *
     * @return \Mockery\MockInterface
     */
    private function prepareSubject($subject)
    {
        $subject = \Mockery::mock('AwesomeItem');
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
