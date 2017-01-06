<?php

namespace SpareParts\Overseer\Voter;


use SpareParts\Overseer\Context\IVotingContext;
use SpareParts\Overseer\VotingDecisionEnum;

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
	 * @param mixed $votingSubject
	 * @param \SpareParts\Overseer\Context\IVotingContext $votingContext
	 * @return ISingleVoterResult
	 */
	public function vote($votingSubject, IVotingContext $votingContext)
	{
		$closure = $this->authorizationClosure;
		$result = $closure($votingSubject, $votingContext);

        $result = $this->prepareResult($result);

        return $result;
	}


	/**
     * @param mixed $result
     * @return SingleVoterResult
     */
    public function prepareResult($result)
    {
        if ($result === true) {
            $result = new SingleVoterResult(VotingDecisionEnum::ALLOWED());
            return $result;
        } elseif ($result === false) {
            $result = new SingleVoterResult(VotingDecisionEnum::DENIED());
            return $result;
        } elseif ($result instanceof VotingDecisionEnum) {
            $result = new SingleVoterResult($result);
            return $result;
        }
        return $result;
    }
}
