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
use SpareParts\Overseer\VotingResult;

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
			'article',
			'edit',
			StrategyEnum::ALLOW_UNLESS_DENIED(),
			[
				\Mockery::mock(IVoter::class)->shouldReceive('vote')->with(IVotingSubject::class, IVotingContext::class)->andReturnNull()->getMock(),
				\Mockery::mock(IVoter::class)->shouldReceive('vote')->with(IVotingSubject::class, IVotingContext::class)->andReturn(
					new SingleVoterResult(VotingDecisionEnum::ALLOWED(), 'reason 1')
				)->getMock(),
				\Mockery::mock(IVoter::class)->shouldReceive('vote')->with(IVotingSubject::class, IVotingContext::class)->andReturn(
					new SingleVoterResult(VotingDecisionEnum::DENIED(), 'reason 2')
				)->getMock(),
				\Mockery::mock(IVoter::class)->shouldReceive('vote')->with(IVotingSubject::class, IVotingContext::class)->andReturn(
					new SingleVoterResult(VotingDecisionEnum::DENIED(), 'reason 3')
				)->getMock(),
				\Mockery::mock(IVoter::class)->shouldReceive('vote')->with(IVotingSubject::class, IVotingContext::class)->andReturnNull()->getMock()    ,
			]
		);

		$result = $assembly->commenceVote(
			$this->prepareSubject('article'),
			$this->prepareContext(12, ['lizard', 'hollow earth inhabitant'])
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
			'article',
			'edit',
			StrategyEnum::ALLOW_UNLESS_DENIED(),
			[]
		);
		$result = $assembly->commenceVote(
			$this->prepareSubject('article'),
			$this->prepareContext(12, ['lizard', 'hollow earth inhabitant'])
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
			'article',
			'edit',
			StrategyEnum::ALLOW_UNLESS_DENIED(),
			[
				\Mockery::mock(IVoter::class)->shouldReceive('vote')->with(IVotingSubject::class, IVotingContext::class)->andReturnNull()->getMock(),
				\Mockery::mock(IVoter::class)->shouldReceive('vote')->with(IVotingSubject::class, IVotingContext::class)->andReturnNull()->getMock(),
			]
		);
		$result = $assembly->commenceVote(
			$this->prepareSubject('article'),
			$this->prepareContext(12, ['lizard', 'hollow earth inhabitant'])
		);
		$this->assertSame(VotingDecisionEnum::ALLOWED(), $result->getDecision());
		$this->assertCount(0, $result->getPartialResults());
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