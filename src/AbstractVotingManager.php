<?php
namespace SpareParts\Overseer;

use SpareParts\Overseer\Assembly\IVotingAssembly;
use SpareParts\Overseer\Context\IVotingContext;
use SpareParts\Overseer\Voter\IVotingSubject;

abstract class AbstractVotingManager
{
	/**
	 * @var IVotingAssembly[]
	 */
	private $votingAssemblies;


	/**
	 * VotingManager constructor.
	 * @param IVotingAssembly[] $votingAssemblies
	 */
	public function __construct(array $votingAssemblies)
	{
		$this->votingAssemblies = $votingAssemblies;
	}


	/**
	 * @param string $action
	 * @param \SpareParts\Overseer\Voter\IVotingSubject $votingSubject
	 * @param \SpareParts\Overseer\Context\IVotingContext $votingContext
	 * @return \SpareParts\Overseer\IVotingResult
	 * @throws \SpareParts\Overseer\InvalidVotingResultException
	 */
	protected function innerVote($action, IVotingSubject $votingSubject, IVotingContext $votingContext)
	{
		foreach ($this->votingAssemblies as $votingAssembly) {
			if ($votingAssembly->canVoteOn($action, $votingSubject, $votingContext)) {
				return $votingAssembly->commenceVote($votingSubject, $votingContext);
			}
		}

		throw new InvalidVotingResultException('No voting assembly for subject::action: '.
			$votingSubject->getVotingSubjectName().'::'.$action);
	}
}
