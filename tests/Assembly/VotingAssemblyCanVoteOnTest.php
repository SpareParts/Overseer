<?php
namespace SpareParts\Overseer\Tests\Assembly;

use SpareParts\Overseer\Assembly\VotingAssembly;
use SpareParts\Overseer\Context\IdentityContext;
use SpareParts\Overseer\Context\IVotingContext;
use SpareParts\Overseer\StrategyEnum;
use SpareParts\Overseer\Voter\IVotingSubject;

class VotingAssemblyTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var VotingAssembly
	 */
	private $assembly;


	/**
	 * @test
	 */
	public function canVoteOnPass()
	{
		$result = $this->assembly->canVoteOn('read', $this->prepareSubject('category'), $this->prepareContext(1, ['alchemist']));
		$this->assertTrue($result);
	}

	/**
	 * @test
	 */
	public function canVoteOnRejectsSubject()
	{
		$result = $this->assembly->canVoteOn('read', $this->prepareSubject('product'), $this->prepareContext(1, ['alchemist']));
		$this->assertFalse($result);
	}

	/**
	 * @test
	 */
	public function canVoteOnRejectsAction()
	{
		$result = $this->assembly->canVoteOn('write', $this->prepareSubject('category'), $this->prepareContext(1, ['alchemist']));
		$this->assertFalse($result);
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

	public function setUp()
	{
		$this->assembly = new VotingAssembly(
			'category',
			'read',
			StrategyEnum::ALLOW_UNLESS_DENIED(),
			[]
		);
	}
}