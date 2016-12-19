<?php
namespace SpareParts\Overseer\Voter;

use SpareParts\Overseer\VotingDecisionEnum;

class SingleVoterResult implements ISingleVoterResult
{

    /**
     * @var VotingDecisionEnum
     */
    private $decision;

    /**
     * @var mixed
     */
    private $reason;


    /**
     * SingleVoterResult constructor.
     * @param VotingDecisionEnum $decision
     * @param mixed $reason
     */
    public function __construct(VotingDecisionEnum $decision, $reason = null)
    {
        $this->decision = $decision;
        $this->reason = $reason;
    }


    /**
     * @return VotingDecisionEnum
     */
    public function getDecision()
    {
        return $this->decision;
    }

    /**
     * @return mixed
     */
    public function getReason()
    {
        return $this->reason;
    }
}