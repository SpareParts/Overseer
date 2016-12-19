<?php
namespace SpareParts\Overseer\Assembly;

use SpareParts\Overseer\Context\IVotingContext;
use SpareParts\Overseer\Voter\IVotingSubject;

interface IVotingAssembly
{

	/**
	 * @param \SpareParts\Overseer\Voter\IVotingSubject $votingSubject
	 * @param \SpareParts\Overseer\Context\IVotingContext $votingContext
	 * @return \SpareParts\Overseer\IVotingResult
	 */
	public function commenceVote(IVotingSubject $votingSubject, IVotingContext $votingContext);


	/**
	 * @param string $actionName
	 * @param \SpareParts\Overseer\Voter\IVotingSubject $subject
	 * @param \SpareParts\Overseer\Context\IVotingContext $context
	 * @return bool
	 */
	public function canVoteOn($actionName, IVotingSubject $subject, IVotingContext $context);
}
