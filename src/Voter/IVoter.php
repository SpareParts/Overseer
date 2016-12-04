<?php
namespace SpareParts\Overseer\Voter;

use SpareParts\Overseer\Identity\IVotingContext;
use SpareParts\Overseer\IVotingResult;

interface IVoter
{

	/**
	 * @param \SpareParts\Overseer\Voter\IVotingSubject $votingSubject
	 * @param \SpareParts\Overseer\Identity\IVotingContext $votingContext
	 * @return IVotingResult|string|null String must be IVotingResult::ALLOW or DENY, null means voter is not voting
	 */
	public function vote(IVotingSubject $votingSubject, IVotingContext $votingContext);
}
