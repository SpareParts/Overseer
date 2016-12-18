<?php
namespace SpareParts\Overseer\Voter;


use SpareParts\Overseer\Identity\IVotingContext;
use SpareParts\Overseer\InvalidArgumentException;
use SpareParts\Overseer\IVotingResult;

class RoleVoter implements IVoter
{
	/**
	 * @var string[]
	 */
	private $allowedRoles;

	/**
	 * @var string
	 */
	private $purpose;


	/**
	 * RoleVoter constructor.
	 * @param string $purpose
	 * @param string|string[] $allowedRoles
	 */
	public function __construct($purpose, $allowedRoles)
	{
		if (!in_array($purpose, [IVotingResult::ALLOW, IVotingResult::DENY ])) {
			throw new InvalidArgumentException(sprintf("Wrong voting purpose for this voter: got %s, expected one of allow, deny.", $purpose));
		}

		$this->purpose = $purpose;
		$this->allowedRoles = (array) $allowedRoles;
	}


	/**
	 * @param \SpareParts\Overseer\Voter\IVotingSubject $votingSubject
	 * @param \SpareParts\Overseer\Identity\IVotingContext $votingContext
	 * @return string|null|IVotingResult
	 */
	public function vote(IVotingSubject $votingSubject, IVotingContext $votingContext)
	{
		if (array_intersect($votingContext->getRoles(), $this->allowedRoles)) {
			return $this->purpose;
		}
		return null;
	}
}
