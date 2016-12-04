<?php
namespace SpareParts\Overseer\Tests\Assembly;

use SpareParts\Overseer\Assembly\VotingAssembly;
use SpareParts\Overseer\Identity\IdentityContext;
use SpareParts\Overseer\Identity\IVotingContext;
use SpareParts\Overseer\Strategy;
use SpareParts\Overseer\Voter\IVotingSubject;

class AssemblyTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @test
	 */
	public function canVoteOnPass()
	{
		$assembly = new VotingAssembly(
			'category',
			'read',
			Strategy::ALLOW_UNLESS_DENIED,
			[]
		);

		$result = $assembly->canVoteOn('read', $this->prepareSubject('category'), $this->prepareContext(1, ['alchemist']));
		$this->assertTrue($result);
	}

	/**
	 * @test
	 */
	public function canVoteOnRejectsSubject()
	{
		$assembly = new VotingAssembly(
			'category',
			'read',
			Strategy::ALLOW_UNLESS_DENIED,
			[]
		);

		$result = $assembly->canVoteOn('read', $this->prepareSubject('product'), $this->prepareContext(1, ['alchemist']));
		$this->assertFalse($result);
	}

	/**
	 * @test
	 */
	public function canVoteOnRejectsContext()
	{
		$assembly = new VotingAssembly(
			'category',
			'read',
			Strategy::ALLOW_UNLESS_DENIED,
			[],
			IdentityContext::class
		);

		$result = $assembly->canVoteOn('read', $this->prepareSubject('product'), $this->prepareContext(1, ['alchemist']));
		$this->assertFalse($result);
	}

	/**
	 * @test
	 */
	public function canVoteOnRejectsAction()
	{
		$assembly = new VotingAssembly(
			'category',
			'read',
			Strategy::ALLOW_UNLESS_DENIED,
			[]
		);

		$result = $assembly->canVoteOn('write', $this->prepareSubject('category'), $this->prepareContext(1, ['alchemist']));
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
}