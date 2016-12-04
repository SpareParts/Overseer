<?php
namespace SpareParts\Overseer;

use SpareParts\Overseer\Assembly\IVotingAssembly;
use SpareParts\Overseer\Identity\IVotingContext;
use SpareParts\Overseer\Voter\IVotingSubject;
use SpareParts\Overseer\Voter\VotingSubject;

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
	 * @param \SpareParts\Overseer\Voter\IVotingSubject|mixed $votingSubject
	 * @param \SpareParts\Overseer\Identity\IVotingContext $votingContext
	 * @return \SpareParts\Overseer\IVotingResult
	 * @throws \SpareParts\Overseer\InvalidVotingResultException
	 */
	protected function innerVote($action, $votingSubject, IVotingContext $votingContext)
	{
		if (!($votingSubject instanceof IVotingSubject)) {
			$votingSubject = new VotingSubject($votingSubject);
		}
		foreach ($this->votingAssemblies as $votingAssembly) {
			if ($votingAssembly->canVoteOn($action, $votingSubject, $votingContext)) {
				return $votingAssembly->commenceVote($votingSubject, $votingContext);
			}
		}

		throw new InvalidVotingResultException('No voting assembly for subject::action: '.
			$votingSubject->getVotingSubjectName().'::'.$action);
	}
}
