<?php
namespace SpareParts\Overseer;

use SpareParts\Overseer\Voter\ISingleVoterResult;

class VotingResult implements IVotingResult
{
    /**
     * @var VotingDecisionEnum
     */
    private $decision;

    /**
     * @var ISingleVoterResult[]
     */
    private $partialResults;


    /**
     * VotingResult constructor.
     * @param VotingDecisionEnum $decision
     * @param ISingleVoterResult[] $partialResults
     */
    public function __construct(VotingDecisionEnum $decision, $partialResults)
	{
        $this->decision = $decision;
        $this->partialResults = $partialResults;
    }


    /**
     * @return VotingDecisionEnum
     */
    public function getDecision()
    {
        return $this->decision;
    }


    /**
     * @return ISingleVoterResult[]
     */
    public function getPartialResults()
    {
        return $this->partialResults;
    }
}
