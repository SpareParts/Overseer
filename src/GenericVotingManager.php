<?php
namespace SpareParts\Overseer;

use SpareParts\Overseer\Identity\IVotingContext;

/**
 * Default implementation.
 *
 * You should provide specific implementation, with exact typehints and function names.
 *
 * This one works as well though. It's just a little too... generic :)
 */
class GenericVotingManager extends AbstractVotingManager
{

	/**
	 * @param string $action
	 * @param \SpareParts\Overseer\Voter\IVotingSubject|mixed $votingSubject
	 * @param \SpareParts\Overseer\Identity\IVotingContext $votingContext
	 * @return \SpareParts\Overseer\IVotingResult
	 * @throws \SpareParts\Overseer\InvalidVotingResultException
	 */
	public function vote($action, $votingSubject, IVotingContext $votingContext)
	{
		return $this->innerVote($action, $votingSubject, $votingContext);
	}

}