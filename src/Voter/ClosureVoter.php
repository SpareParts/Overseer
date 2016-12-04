<?php

namespace SpareParts\Overseer\Voter;


use SpareParts\Overseer\Identity\IVotingContext;
use SpareParts\Overseer\IVotingResult;

class ClosureVoter implements IVoter
{
	/**
	 * @var \Closure
	 */
	private $authorizationClosure;


	/**
	 * ClosureVoter constructor.
	 * @param \Closure $authorizationClosure
	 */
	public function __construct(\Closure $authorizationClosure)
	{
		$this->authorizationClosure = $authorizationClosure;
	}


	/**
	 * @param \SpareParts\Overseer\Voter\IVotingSubject $votingSubject
	 * @param \SpareParts\Overseer\Identity\IVotingContext $votingContext
	 * @return bool|IVotingResult
	 */
	public function vote(IVotingSubject $votingSubject, IVotingContext $votingContext)
	{
		$closure = $this->authorizationClosure;
		return $closure($votingSubject, $votingContext);
	}
}
