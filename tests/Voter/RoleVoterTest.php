<?php
namespace SpareParts\Overseer\Tests\Voter;

use SpareParts\Overseer\Context\IVotingContext;
use SpareParts\Overseer\InvalidArgumentException;
use SpareParts\Overseer\Voter\IVotingSubject;
use SpareParts\Overseer\Voter\RoleVoter;
use SpareParts\Overseer\VotingDecisionEnum;

class RoleVoterTest extends \PHPUnit_Framework_TestCase
{


	/**
	 * @param VotingDecisionEnum $decision
	 * @param string[] $userRoles
	 * @param string[] $checkedRoles
	 * @param string $shouldResult
	 *
	 * @test
	 * @dataProvider dpRoleVoterCanCorrectlyVote
	 */
	public function roleVoterCanCorrectlyVote($decision, $userRoles, $checkedRoles, $shouldResult)
	{
		$voter = new RoleVoter($decision, $checkedRoles);

		$result = $voter->vote($this->prepareSubject('tested subject'), $this->prepareContext(1, $userRoles));

		if ($shouldResult) {
            $this->assertEquals($shouldResult, $result->getDecision());
        } else {
		    $this->assertSame($shouldResult, $result);
        }
	}

	public function dpRoleVoterCanCorrectlyVote()
	{
		return [
			'straight_allow' => [
				'decision' => VotingDecisionEnum::ALLOWED(),
				'user_roles' => ['admin', 'editor'],
				'checked_roles' => ['admin'],
				'should_result' => VotingDecisionEnum::ALLOWED(),
			],
			'straight_deny' => [
				'decision' => VotingDecisionEnum::DENIED(),
				'user_roles' => ['admin', 'editor'],
				'checked_roles' => ['editor'],
				'should_result' => VotingDecisionEnum::DENIED(),
			],
			'multiple_roles_checking_one_checks' => [
				'decision' => VotingDecisionEnum::ALLOWED(),
				'user_roles' => ['admin', 'editor', 'user'],
				'checked_roles' => ['admin', 'priest'],
				'should_result' => VotingDecisionEnum::ALLOWED(),
			],
			'multiple_roles_checking_multiple_checks' => [
				'decision' => VotingDecisionEnum::DENIED(),
				'user_roles' => ['admin', 'editor', 'warrior', 'user'],
				'checked_roles' => ['admin', 'warrior'],
				'should_result' => VotingDecisionEnum::DENIED(),
			],
			'multiple_roles_checking_no_cross' => [
				'decision' => VotingDecisionEnum::ALLOWED(),
				'user_roles' => ['admin', 'editor', 'user'],
				'checked_roles' => ['cleric', 'priest'],
				'should_result' => null,
			],
			'no_roles_no_check' => [
				'decision' => VotingDecisionEnum::ALLOWED(),
				'user_roles' => [],
				'checked_roles' => ['cleric', 'priest'],
				'should_result' => null,
			],
			'no_roles_no_check_2' => [
				'decision' => VotingDecisionEnum::DENIED(),
				'user_roles' => ['admin', 'editor', 'user'],
				'checked_roles' => [],
				'should_result' => null,
			],
		];
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