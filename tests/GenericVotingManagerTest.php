<?php
namespace SpareParts\Overseer\Tests;

use SpareParts\Overseer\Assembly\IVotingAssembly;
use SpareParts\Overseer\GenericVotingManager;
use SpareParts\Overseer\Context\IVotingContext;
use SpareParts\Overseer\IVotingResult;
use SpareParts\Overseer\Voter\IVotingSubject;
use SpareParts\Overseer\Voter\VotingSubject;

class GenericVotingManagerTest extends \PHPUnit_Framework_TestCase
{


	/**
	 * @test
	 */
	public function decidesCorrectAssembly()
	{
		$subject = $this->prepareSubject('Category');
		$action = 'edit';
		$context = $this->prepareContext(12, ['editor', 'user']);
		$result = \Mockery::mock(IVotingResult::class);
		$assemblies = [
			$a1 = \Mockery::mock(IVotingAssembly::class)
				->shouldReceive('canVoteOn')->with($action, $subject, $context)->andReturn(false)->getMock()
				->shouldNotReceive('commenceVote')->getMock(),
			$a2 = \Mockery::mock(IVotingAssembly::class)
				->shouldReceive('canVoteOn')->with($action, $subject, $context)->andReturn(true)->getMock()
				->shouldReceive('commenceVote')->with($subject, $context)->andReturn($result)->getMock(),
			$a3 = \Mockery::mock(IVotingAssembly::class)
				->shouldNotReceive('canVoteOn')->getMock()
				->shouldNotReceive('commenceVote')->getMock(),
		];

		$manager = new GenericVotingManager($assemblies);
		$r = $manager->vote($action, $subject, $context);
		$this->assertSame($result, $r);
	}

	/**
	 * @test
	 */
	public function convertsVotingSubject()
	{
		$subject = 'Category';
		$action = 'edit';
		$context = $this->prepareContext(12, ['editor', 'user']);
		$result = \Mockery::mock(IVotingResult::class);
		$assemblies = [
			$a1 = \Mockery::mock(IVotingAssembly::class)
				->shouldReceive('canVoteOn')->andReturnUsing(function($action, $subject, $context) {
					if (!($subject instanceof VotingSubject)) {
						$this->fail('Wrong voting subject.');
					}
					if ($subject->getVotingSubjectName() !== 'Category') {
						$this->fail('Wrong voting subject value.');
					}
					return true;
				})->getMock()
				->shouldReceive('commenceVote')->with(VotingSubject::class, $context)->andReturn($result)->getMock(),
		];

		$manager = new GenericVotingManager($assemblies);
		$r = $manager->vote($action, $subject, $context);
		$this->assertSame($result, $r);
	}

	/**
	 * @test
	 * @expectedException \SpareParts\Overseer\InvalidVotingResultException
	 */
	public function noValidAssemblyMeansException()
	{
		$subject = $this->prepareSubject('Category');
		$action = 'edit';
		$context = $this->prepareContext(12, ['editor', 'user']);
		$assemblies = [
			$a1 = \Mockery::mock(IVotingAssembly::class)
				->shouldReceive('canVoteOn')->with($action, $subject, $context)->andReturn(false)->getMock()
				->shouldNotReceive('commenceVote')->getMock(),
			$a2 = \Mockery::mock(IVotingAssembly::class)
				->shouldReceive('canVoteOn')->with($action, $subject, $context)->andReturn(false)->getMock()
				->shouldNotReceive('commenceVote')->getMock(),
		];

		$manager = new GenericVotingManager($assemblies);
		$r = $manager->vote($action, $subject, $context);
	}


	/**
	 * @test
	 */
	public function decidesCorrectAssemblyWhenMultipleCanVote()
	{
		$subject = $this->prepareSubject('Category');
		$action = 'edit';
		$context = $this->prepareContext(12, ['editor', 'user']);
		$result = \Mockery::mock(IVotingResult::class);
		$assemblies = [
			$a1 = \Mockery::mock(IVotingAssembly::class)
				->shouldReceive('canVoteOn')->with($action, $subject, $context)->andReturn(false)->getMock()
				->shouldNotReceive('commenceVote')->getMock(),
			$a2 = \Mockery::mock(IVotingAssembly::class)
				->shouldReceive('canVoteOn')->with($action, $subject, $context)->andReturn(true)->getMock()
				->shouldReceive('commenceVote')->with($subject, $context)->andReturn($result)->getMock(),
			$a3 = \Mockery::mock(IVotingAssembly::class)
				->shouldReceive('canVoteOn')->with($action, $subject, $context)->andReturn(true)->getMock()
				->shouldNotReceive('commenceVote')->getMock(),
		];

		$manager = new GenericVotingManager($assemblies);
		$r = $manager->vote($action, $subject, $context);
		$this->assertSame($result, $r);
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