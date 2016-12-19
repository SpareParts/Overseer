<?php
namespace SpareParts\Overseer\Voter;

use SpareParts\Overseer\Context\IVotingContext;
use SpareParts\Overseer\IVotingResult;

interface IVoter
{

	/**
	 * @param \SpareParts\Overseer\Voter\IVotingSubject $votingSubject
	 * @param \SpareParts\Overseer\Context\IVotingContext $votingContext
	 * @return ISingleVoterResult
	 */
	public function vote(IVotingSubject $votingSubject, IVotingContext $votingContext);
}
