<?php
namespace SpareParts\Overseer\Tests\Assembly;


use SpareParts\Overseer\Assembly\VotingAssembly;
use SpareParts\Overseer\Context\IVotingContext;
use SpareParts\Overseer\StrategyEnum;
use SpareParts\Overseer\Voter\IVoter;
use SpareParts\Overseer\Voter\SingleVoterResult;
use SpareParts\Overseer\VotingDecisionEnum;

class VotingAssemblyEveryoneMustComplyTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     * @dataProvider decisionProvider
     *
     * @param \SpareParts\Overseer\StrategyEnum $strategy
     * @param \SpareParts\Overseer\VotingDecisionEnum $correct
     * @param \SpareParts\Overseer\VotingDecisionEnum $opposite
     */
    public function works(StrategyEnum $strategy, VotingDecisionEnum $correct, VotingDecisionEnum $opposite)
    {
        $assembly = new VotingAssembly(
            $strategy,
            [
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('RandomArticleClass', IVotingContext::class)->andReturn(
                    new SingleVoterResult($correct, 'reason 1')
                )->getMock(),
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('RandomArticleClass', IVotingContext::class)->andReturn(
                    new SingleVoterResult($correct, 'reason 2')
                )->getMock(),
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('RandomArticleClass', IVotingContext::class)->andReturn(
                    new SingleVoterResult($correct, 'reason 3')
                )->getMock(),
            ]
        );

        $result = $assembly->commenceVote(
            $this->prepareSubject('article'),
            $this->prepareContext()
        );

        $this->assertSame($correct, $result->getDecision());
        $this->assertCount(3, $result->getPartialResults());
        $this->assertEquals('reason 1', $result->getPartialResults()[0]->getReason());
        $this->assertEquals('reason 2', $result->getPartialResults()[1]->getReason());
        $this->assertEquals('reason 3', $result->getPartialResults()[2]->getReason());
    }


    /**
     * @test
     * @dataProvider decisionProvider
     *
     * @param \SpareParts\Overseer\StrategyEnum $strategy
     * @param \SpareParts\Overseer\VotingDecisionEnum $correct
     * @param \SpareParts\Overseer\VotingDecisionEnum $opposite
     */
    public function singleIncorrectDecisionFailsVoting(StrategyEnum $strategy, VotingDecisionEnum $correct, VotingDecisionEnum $opposite)
    {
        $assembly = new VotingAssembly(
            $strategy,
            [
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('RandomArticleClass', IVotingContext::class)->andReturn(
                    new SingleVoterResult($correct, 'reason 1')
                )->getMock(),
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('RandomArticleClass', IVotingContext::class)->andReturn(
                    new SingleVoterResult($opposite, 'reason 2')
                )->getMock(),
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('RandomArticleClass', IVotingContext::class)->andReturn(
                    new SingleVoterResult($correct, 'reason 3')
                )->getMock(),
            ]
        );

        $result = $assembly->commenceVote(
            $this->prepareSubject('article'),
            $this->prepareContext()
        );

        $this->assertSame($opposite, $result->getDecision());
        $this->assertCount(3, $result->getPartialResults());
        $this->assertEquals('reason 1', $result->getPartialResults()[0]->getReason());
        $this->assertEquals('reason 2', $result->getPartialResults()[1]->getReason());
        $this->assertEquals('reason 3', $result->getPartialResults()[2]->getReason());
    }

    /**
     * @test
     * @dataProvider decisionProvider
     *
     * @param \SpareParts\Overseer\StrategyEnum $strategy
     * @param \SpareParts\Overseer\VotingDecisionEnum $correct
     * @param \SpareParts\Overseer\VotingDecisionEnum $opposite
     */
    public function nullDecisionDoesntFailVoting(StrategyEnum $strategy, VotingDecisionEnum $correct, VotingDecisionEnum $opposite)
    {
        $assembly = new VotingAssembly(
            $strategy,
            [
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('RandomArticleClass', IVotingContext::class)->andReturn(
                    new SingleVoterResult($correct, 'reason 1')
                )->getMock(),
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('RandomArticleClass', IVotingContext::class)->andReturnNull()->getMock(),
                \Mockery::mock(IVoter::class)->shouldReceive('vote')->with('RandomArticleClass', IVotingContext::class)->andReturn(
                    new SingleVoterResult($correct, 'reason 3')
                )->getMock(),
            ]
        );

        $result = $assembly->commenceVote(
            $this->prepareSubject('article'),
            $this->prepareContext()
        );

        $this->assertSame($correct, $result->getDecision());
        $this->assertCount(2, $result->getPartialResults());
        $this->assertEquals('reason 1', $result->getPartialResults()[0]->getReason());
        $this->assertEquals('reason 3', $result->getPartialResults()[1]->getReason());
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
     * @return \Mockery\MockInterface|IVotingContext
     */
    private function prepareContext()
    {
        $context = \Mockery::mock(IVotingContext::class);
        return $context;
    }

    public function decisionProvider()
    {
        return [
            'allow' => [
                'strategy' => StrategyEnum::EVERYONE_MUST_ALLOW_TO_BE_ALLOWED(),
                'correct' => VotingDecisionEnum::ALLOWED(),
                'opposite' => VotingDecisionEnum::DENIED(),
            ],
            'deny' => [
                'strategy' => StrategyEnum::EVERYONE_MUST_DENY_TO_BE_DENIED(),
                'correct' => VotingDecisionEnum::DENIED(),
                'opposite' => VotingDecisionEnum::ALLOWED(),
            ],
        ];
    }

    public function tearDown()
    {
        \Mockery::close();
    }
}