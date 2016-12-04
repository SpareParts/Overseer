<?php
namespace SpareParts\Overseer\Tests\Voter;
use SpareParts\Overseer\Identity\IVotingContext;
use SpareParts\Overseer\InvalidArgumentException;
use SpareParts\Overseer\IVotingResult;
use SpareParts\Overseer\Voter\IVotingSubject;
use SpareParts\Overseer\Voter\RoleVoter;

class RoleVoterTest extends \PHPUnit_Framework_TestCase
{


	/**
	 * @param string $purpose
	 * @param string[] $userRoles
	 * @param string[] $checkedRoles
	 * @param string $shouldResult
	 *
	 * @test
	 * @dataProvider dpRoleVoterCanCorrectlyVote
	 */
	public function roleVoterCanCorrectlyVote($purpose, $userRoles, $checkedRoles, $shouldResult)
	{
		$voter = new RoleVoter($purpose, $checkedRoles);

		$result = $voter->vote($this->prepareSubject('tested subject'), $this->prepareContext(1, $userRoles));

		$this->assertEquals($shouldResult, $result);
	}

	public function dpRoleVoterCanCorrectlyVote()
	{
		return [
			'straight_allow' => [
				'purpose' => IVotingResult::ALLOW,
				'user_roles' => ['admin', 'editor'],
				'checked_roles' => ['admin'],
				'should_result' => IVotingResult::ALLOW,
			],
			'straight_deny' => [
				'purpose' => IVotingResult::DENY,
				'user_roles' => ['admin', 'editor'],
				'checked_roles' => ['editor'],
				'should_result' => IVotingResult::DENY,
			],
			'multiple_roles_checking_one_checks' => [
				'purpose' => IVotingResult::ALLOW,
				'user_roles' => ['admin', 'editor', 'user'],
				'checked_roles' => ['admin', 'priest'],
				'should_result' => IVotingResult::ALLOW,
			],
			'multiple_roles_checking_multiple_checks' => [
				'purpose' => IVotingResult::DENY,
				'user_roles' => ['admin', 'editor', 'warrior', 'user'],
				'checked_roles' => ['admin', 'warrior'],
				'should_result' => IVotingResult::DENY,
			],
			'multiple_roles_checking_no_cross' => [
				'purpose' => IVotingResult::ALLOW,
				'user_roles' => ['admin', 'editor', 'user'],
				'checked_roles' => ['cleric', 'priest'],
				'should_result' => null,
			],
			'no_roles_no_check' => [
				'purpose' => IVotingResult::ALLOW,
				'user_roles' => [],
				'checked_roles' => ['cleric', 'priest'],
				'should_result' => null,
			],
			'no_roles_no_check_2' => [
				'purpose' => IVotingResult::DENY,
				'user_roles' => ['admin', 'editor', 'user'],
				'checked_roles' => [],
				'should_result' => null,
			],
		];
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function incorrectPurposeFails()
	{
		new RoleVoter('random purpose', ['warrior', 'sorcerer']);
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