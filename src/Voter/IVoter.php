<?php
namespace SpareParts\Overseer\Voter;

use SpareParts\Overseer\Context\IVotingContext;

interface IVoter
{

	/**
	 * @param mixed $votingSubject
	 * @param \SpareParts\Overseer\Context\IVotingContext $votingContext
	 * @return ISingleVoterResult
	 */
	public function vote($votingSubject, IVotingContext $votingContext);
}
