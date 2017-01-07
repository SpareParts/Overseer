<?php

namespace SpareParts\Overseer\Voter;


use SpareParts\Overseer\Context\IVotingContext;
use SpareParts\Overseer\VotingDecisionEnum;

class ConstVoter implements IVoter
{
    /**
     * @var VotingDecisionEnum
     */
    private $constDecision;

    /**
     * @var mixed
     */
    private $constReason;


    /**
     * ConstVoter constructor.
     * @param VotingDecisionEnum $constDecision
     * @param mixed $constReason
     */
    public function __construct(VotingDecisionEnum $constDecision, $constReason = null)
    {
        $this->constDecision = $constDecision;
        $this->constReason = $constReason;
    }


    /**
     * @param mixed $votingSubject
     * @param \SpareParts\Overseer\Context\IVotingContext $votingContext
     * @return ISingleVoterResult
     */
    public function vote($votingSubject, IVotingContext $votingContext)
    {
        return new SingleVoterResult($this->constDecision, $this->constReason);
    }
}
