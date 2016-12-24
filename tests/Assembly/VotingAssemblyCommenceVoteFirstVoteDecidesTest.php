<?php
namespace SpareParts\Overseer\Tests\Assembly;


use SpareParts\Overseer\Assembly\VotingAssembly;
use SpareParts\Overseer\Context\IVotingContext;
use SpareParts\Overseer\InvalidVotingResultException;
use SpareParts\Overseer\StrategyEnum;
use SpareParts\Overseer\Voter\IVoter;
use SpareParts\Overseer\Voter\IVotingSubject;
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
			'article',
			'edit',
			StrategyEnum::FIRST_VOTE_DECIDES(),
			[
				\Mockery::mock(IVoter::class)->shouldReceive('vote')->with(IVotingSubject::class, IVotingContext::class)->andReturnNull()->getMock(),
				\Mockery::mock(IVoter::class)->shouldReceive('vote')->with(IVotingSubject::class, IVotingContext::class)->andReturnNull()->getMock(),
				\Mockery::mock(IVoter::class)->shouldReceive('vote')->with(IVotingSubject::class, IVotingContext::class)->andReturn(
					new SingleVoterResult(VotingDecisionEnum::ALLOWED(), 'reason test')
				)->getMock(),
				\Mockery::mock(IVoter::class)->shouldReceive('vote')->with(IVotingSubject::class, IVotingContext::class)->andReturnNull()->getMock()    ,
			]
		);

		$result = $assembly->commenceVote(
			$this->prepareSubject('article'),
			$this->prepareContext(12, ['lizard', 'hollow earth inhabitant'])
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
			'article',
			'edit',
			StrategyEnum::FIRST_VOTE_DECIDES(),
			[
				\Mockery::mock(IVoter::class)->shouldReceive('vote')->with(IVotingSubject::class, IVotingContext::class)->andReturnNull()->getMock(),
				\Mockery::mock(IVoter::class)->shouldReceive('vote')->with(IVotingSubject::class, IVotingContext::class)->andReturn(
					new SingleVoterResult(VotingDecisionEnum::DENIED(), 'correct reason')
				)->getMock(),
				\Mockery::mock(IVoter::class)->shouldReceive('vote')->with(IVotingSubject::class, IVotingContext::class)->andReturnNull()->getMock(),
				\Mockery::mock(IVoter::class)->shouldReceive('vote')->with(IVotingSubject::class, IVotingContext::class)->andReturn(
					new SingleVoterResult(VotingDecisionEnum::ALLOWED(), 'wrong reason')
				)->getMock(),
				\Mockery::mock(IVoter::class)->shouldReceive('vote')->with(IVotingSubject::class, IVotingContext::class)->andReturnNull()->getMock(),
			]
		);

		$result = $assembly->commenceVote(
			$this->prepareSubject('article'),
			$this->prepareContext(12, ['lizard', 'hollow earth inhabitant'])
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
			'article',
			'edit',
			StrategyEnum::FIRST_VOTE_DECIDES(),
			[]
		);
		$assembly->commenceVote(
			$this->prepareSubject('article'),
			$this->prepareContext(12, ['lizard', 'hollow earth inhabitant'])
		);
	}


	/**
	 * @test
	 * @expectedException \SpareParts\Overseer\InvalidVotingResultException
	 */
	public function ifVotersDontDecideThrowsException()
	{
		$assembly = new VotingAssembly(
			'article',
			'edit',
			StrategyEnum::FIRST_VOTE_DECIDES(),
			[
				\Mockery::mock(IVoter::class)->shouldReceive('vote')->with(IVotingSubject::class, IVotingContext::class)->andReturnNull()->getMock(),
				\Mockery::mock(IVoter::class)->shouldReceive('vote')->with(IVotingSubject::class, IVotingContext::class)->andReturnNull()->getMock(),
			]
		);
		$assembly->commenceVote(
			$this->prepareSubject('article'),
			$this->prepareContext(12, ['lizard', 'hollow earth inhabitant'])
		);
	}


	/**
	 * @param string $subject
	 *
	 * @return \Mockery\MockInterface|IVotingSubject
	 */
	private function prepareSubject($subject)
	{
		$subject = \Mockery::mock(IVotingSubject::class)
			->shouldReceive('getVotingSubjectName')->andReturn($subject)->getMock();
		return $subject;
	}

	/**
	 * @param mixed $id
	 * @param string[] $roles
	 *
	 * @return \Mockery\MockInterface|IVotingContext
	 */
	private function prepareContext($id, $roles)
	{
		$context = \Mockery::mock(IVotingContext::class)
			->shouldReceive('getId')->andReturn($id)->getMock()
			->shouldReceive('getRoles')->andReturn($roles)->getMock();
		return $context;
	}
}