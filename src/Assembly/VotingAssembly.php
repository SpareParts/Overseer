<?php
namespace SpareParts\Overseer\Assembly;


use SpareParts\Overseer\Context\IVotingContext;
use SpareParts\Overseer\InvalidVotingResultException;
use SpareParts\Overseer\StrategyEnum;
use SpareParts\Overseer\Voter\IVoter;
use SpareParts\Overseer\Voter\IVotingSubject;
use SpareParts\Overseer\VotingDecisionEnum;
use SpareParts\Overseer\VotingResult;

class VotingAssembly implements IVotingAssembly
{

	/**
	 * @var string
	 */
	private $strategy;

	/**
	 * @var IVoter[]
	 */
	private $voters;

	/**
	 * @var string
	 */
	private $subjectName;

	/**
	 * @var string
	 */
	private $actionName;


	/**
	 * VotingAssembly constructor.
	 * @param string $subjectName
	 * @param string $actionName
	 * @param string $strategy
	 * @param \SpareParts\Overseer\Voter\IVoter[] $voters
	 */
	public function __construct(
		$subjectName,
		$actionName,
		$strategy,
		array $voters
	) {
		$this->strategy = $strategy;
		$this->voters = $voters;
		$this->subjectName = $subjectName;
		$this->actionName = $actionName;
	}


	/**
	 * @param \SpareParts\Overseer\Voter\IVotingSubject $votingSubject
	 * @param \SpareParts\Overseer\Context\IVotingContext $votingContext
	 * @return null|\SpareParts\Overseer\IVotingResult
	 * @throws \SpareParts\Overseer\InvalidVotingResultException
	 */
	public function commenceVote(IVotingSubject $votingSubject, IVotingContext $votingContext)
	{
		$result = null;
		switch ($this->strategy) {
			case StrategyEnum::FIRST_VOTE_DECIDES():
				return $this->strategyFirstVoteDecides($votingSubject, $votingContext);

			case StrategyEnum::ALLOW_UNLESS_DENIED():
				return $this->strategyAllowUnlessDenied($votingSubject, $votingContext);

			case StrategyEnum::DENY_UNLESS_ALLOWED():
				return $this->strategyDenyUnlessAllowed($votingSubject, $votingContext);

			default:
				throw new InvalidVotingResultException('Unable to decide on result, invalid strategy: '.$this->strategy);
		}
	}


	/**
	 * @param \SpareParts\Overseer\Voter\IVotingSubject $votingSubject
	 * @param \SpareParts\Overseer\Context\IVotingContext $votingContext
	 * @return \SpareParts\Overseer\IVotingResult
	 * @throws \SpareParts\Overseer\InvalidVotingResultException
	 */
	private function strategyFirstVoteDecides(IVotingSubject $votingSubject, IVotingContext $votingContext)
	{
		foreach ($this->voters as $voter) {
			if (($lastResult = $voter->vote($votingSubject, $votingContext)) !== null) {
                return new VotingResult($lastResult->getDecision(), [$lastResult]);
			}
		}
		throw new InvalidVotingResultException('Voting assembly did not decide on any result!');
	}


	/**
	 * @param \SpareParts\Overseer\Voter\IVotingSubject $votingSubject
	 * @param \SpareParts\Overseer\Context\IVotingContext $votingContext
	 * @return \SpareParts\Overseer\IVotingResult
	 */
	private function strategyAllowUnlessDenied($votingSubject, $votingContext)
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
	 * @param \SpareParts\Overseer\Voter\IVotingSubject $votingSubject
	 * @param \SpareParts\Overseer\Context\IVotingContext $votingContext
	 * @return \SpareParts\Overseer\IVotingResult
	 */
	private function strategyDenyUnlessAllowed($votingSubject, $votingContext)
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
	 * @param string $actionName
	 * @param \SpareParts\Overseer\Voter\IVotingSubject $subject
	 * @param \SpareParts\Overseer\Context\IVotingContext $context
	 * @return bool
	 */
	public function canVoteOn($actionName, IVotingSubject $subject, IVotingContext $context)
	{
		if ($subject->getVotingSubjectName() === $this->subjectName && $actionName === $this->actionName) {
			return true;
		}
		return false;
	}
}
