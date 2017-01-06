<?php
namespace SpareParts\Overseer\Assembly;

use SpareParts\Overseer\Context\IVotingContext;

interface IVotingAssembly
{

	/**
	 * @param mixed $votingSubject
	 * @param \SpareParts\Overseer\Context\IVotingContext $votingContext
	 * @return \SpareParts\Overseer\IVotingResult
	 */
	public function commenceVote($votingSubject, IVotingContext $votingContext);

}
