<?php
namespace SpareParts\Overseer\Voter;

use SpareParts\Overseer\Context\IIdentityContext;
use SpareParts\Overseer\Context\IVotingContext;
use SpareParts\Overseer\InvalidArgumentException;
use SpareParts\Overseer\VotingDecisionEnum;

final class RoleVoter implements IVoter
{
    /**
     * @var string[]
     */
    private $allowedRoles;

    /**
     * @var VotingDecisionEnum
     */
    private $resultDecision;

    /**
     * @var mixed|null
     */
    private $reason;


    /**
     * RoleVoter constructor.
     * @param VotingDecisionEnum $resultDecision
     * @param string|string[] $allowedRoles
     * @param mixed $reason
     */
    public function __construct(VotingDecisionEnum $resultDecision, $allowedRoles, $reason = null)
    {
        $this->allowedRoles = (array) $allowedRoles;
        $this->resultDecision = $resultDecision;
        $this->reason = $reason;
    }


    /**
     * @param mixed $votingSubject
     * @param \SpareParts\Overseer\Context\IVotingContext $votingContext
     * @return ISingleVoterResult
     */
    public function vote($votingSubject, IVotingContext $votingContext)
    {
        if (!($votingContext instanceof IIdentityContext)) {
            throw new InvalidArgumentException('RoleVoter can be used only with specific voting context, implementing IIdentityContext.');
        }

        if (array_intersect($votingContext->getRoles(), $this->allowedRoles)) {
            return new SingleVoterResult($this->resultDecision, $this->reason);
        }
        return null;
    }
}
