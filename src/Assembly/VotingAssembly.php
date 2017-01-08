<?php
namespace SpareParts\Overseer\Assembly;


use SpareParts\Overseer\Context\IVotingContext;
use SpareParts\Overseer\InvalidVotingResultException;
use SpareParts\Overseer\StrategyEnum;
use SpareParts\Overseer\Voter\ISingleVoterResult;
use SpareParts\Overseer\Voter\IVoter;
use SpareParts\Overseer\VotingDecisionEnum;
use SpareParts\Overseer\VotingResult;

class VotingAssembly implements IVotingAssembly
{

    /**
     * @var StrategyEnum
     */
    private $strategy;

    /**
     * @var IVoter[]
     */
    private $voters;


    /**
     * VotingAssembly constructor.
     * @param StrategyEnum $strategy
     * @param \SpareParts\Overseer\Voter\IVoter[] $voters
     */
    public function __construct(StrategyEnum $strategy, array $voters)
    {
        $this->strategy = $strategy;
        $this->voters = $voters;
    }


    /**
     * @param mixed $votingSubject
     * @param \SpareParts\Overseer\Context\IVotingContext $votingContext
     * @return null|\SpareParts\Overseer\IVotingResult
     * @throws \SpareParts\Overseer\InvalidVotingResultException
     */
    public function commenceVote($votingSubject, IVotingContext $votingContext)
    {
        switch ($this->strategy) {
            case StrategyEnum::FIRST_VOTE_DECIDES():
                return $this->strategyFirstVoteDecides($votingSubject, $votingContext);

            case StrategyEnum::ALLOW_UNLESS_DENIED():
                return $this->strategyAllowUnlessDenied($votingSubject, $votingContext);

            case StrategyEnum::DENY_UNLESS_ALLOWED():
                return $this->strategyDenyUnlessAllowed($votingSubject, $votingContext);

            case StrategyEnum::EVERYONE_MUST_ALLOW_TO_BE_ALLOWED():
                return $this->strategyEveryoneMustComply($votingSubject, $votingContext, VotingDecisionEnum::ALLOWED(), VotingDecisionEnum::DENIED());

            case StrategyEnum::EVERYONE_MUST_DENY_TO_BE_DENIED():
                return $this->strategyEveryoneMustComply($votingSubject, $votingContext, VotingDecisionEnum::DENIED(), VotingDecisionEnum::ALLOWED());

            default:
                throw new InvalidVotingResultException('Unable to decide on result, invalid strategy: '.$this->strategy);
        }
    }


    /**
     * @param mixed $votingSubject
     * @param \SpareParts\Overseer\Context\IVotingContext $votingContext
     * @return \SpareParts\Overseer\IVotingResult
     * @throws \SpareParts\Overseer\InvalidVotingResultException
     */
    private function strategyFirstVoteDecides($votingSubject, IVotingContext $votingContext)
    {
        foreach ($this->voters as $voter) {
            if (($lastResult = $voter->vote($votingSubject, $votingContext)) !== null) {
                return new VotingResult($lastResult->getDecision(), [$lastResult]);
            }
        }
        throw new InvalidVotingResultException('Voting assembly did not decide on any result!');
    }


    /**
     * @param mixed $votingSubject
     * @param \SpareParts\Overseer\Context\IVotingContext $votingContext
     * @return \SpareParts\Overseer\IVotingResult
     */
    private function strategyAllowUnlessDenied($votingSubject, IVotingContext $votingContext)
    {
        $results = [];
        foreach ($this->voters as $name => $voter) {
            if (($lastResult = $voter->vote($votingSubject, $votingContext)) !== null) {
                $results[] = $lastResult;
                if ($lastResult->getDecision() === VotingDecisionEnum::DENIED()) {
                    return new VotingResult(VotingDecisionEnum::DENIED(), $results);
                }
            }
        }
        return new VotingResult(VotingDecisionEnum::ALLOWED(), $results);
    }


    /**
     * @param mixed $votingSubject
     * @param \SpareParts\Overseer\Context\IVotingContext $votingContext
     * @return \SpareParts\Overseer\IVotingResult
     */
    private function strategyDenyUnlessAllowed($votingSubject, IVotingContext $votingContext)
    {
        $results = [];
        foreach ($this->voters as $name => $voter) {
            if (($lastResult = $voter->vote($votingSubject, $votingContext)) !== null) {
                $results[] = $lastResult;
                if ($lastResult->getDecision() === VotingDecisionEnum::ALLOWED()) {
                    return new VotingResult(VotingDecisionEnum::ALLOWED(), $results);
                }
            }
        }
        return new VotingResult(VotingDecisionEnum::DENIED(), $results);
    }


    /**
     * @param mixed $votingSubject
     * @param \SpareParts\Overseer\Context\IVotingContext $votingContext
     * @param \SpareParts\Overseer\VotingDecisionEnum $defaultDecision
     * @param \SpareParts\Overseer\VotingDecisionEnum $counterDecision
     *
     * @return \SpareParts\Overseer\VotingResult
     */
    private function strategyEveryoneMustComply(
        $votingSubject,
        IVotingContext $votingContext,
        VotingDecisionEnum $defaultDecision,
        VotingDecisionEnum $counterDecision
    ) {
        $results = [];
        $decision = $defaultDecision;
        foreach ($this->voters as $voter) {
            $result = $voter->vote($votingSubject, $votingContext);
            if ($result instanceof ISingleVoterResult) {
                if ($result->getDecision() !== $defaultDecision) {
                    $decision = $counterDecision;
                }
                $results[] = $result;
            }
        }
        return new VotingResult($decision, $results);
    }
}
