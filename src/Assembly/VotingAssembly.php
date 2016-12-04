<?php
namespace SpareParts\Overseer\Assembly;


use SpareParts\Overseer\Identity\IVotingContext;
use SpareParts\Overseer\IVotingResult;
use SpareParts\Overseer\Strategy;
use SpareParts\Overseer\InvalidVotingResultException;
use SpareParts\Overseer\Voter\IVoter;
use SpareParts\Overseer\Voter\IVotingSubject;
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
	 * @var null|string
	 */
	private $contextClassname;


	/**
	 * VotingAssembly constructor.
	 * @param string $subjectName
	 * @param string $actionName
	 * @param string $strategy
	 * @param \SpareParts\Overseer\Voter\IVoter[] $voters
	 * @param string|null $contextClassname If present, this assembly will require specific context class
	 */
	public function __construct(
		$subjectName,
		$actionName,
		$strategy,
		array $voters,
		$contextClassname = IVotingContext::class
	) {
		$this->strategy = $strategy;
		$this->voters = $voters;
		$this->subjectName = $subjectName;
		$this->actionName = $actionName;
		$this->contextClassname = $contextClassname;
	}


	/**
	 * @param \SpareParts\Overseer\Voter\IVotingSubject $votingSubject
	 * @param \SpareParts\Overseer\Identity\IVotingContext $votingContext
	 * @return null|\SpareParts\Overseer\IVotingResult
	 * @throws \SpareParts\Overseer\InvalidVotingResultException
	 */
	public function commenceVote(IVotingSubject $votingSubject, IVotingContext $votingContext)
	{
		$result = null;
		switch ($this->strategy) {
			case Strategy::FIRST_VOTE_DECIDES:
				return $this->strategyFirstVoteDecides($votingSubject, $votingContext);

			case Strategy::ALLOW_UNLESS_DENIED:
				return $this->strategyAllowUnlessDenied($votingSubject, $votingContext);

			default:
				throw new InvalidVotingResultException('Unable to decide on result, invalid strategy: '.$this->strategy);
		}
	}


	/**
	 * @param \SpareParts\Overseer\Voter\IVotingSubject $votingSubject
	 * @param \SpareParts\Overseer\Identity\IVotingContext $votingContext
	 * @return \SpareParts\Overseer\IVotingResult
	 * @throws \SpareParts\Overseer\InvalidVotingResultException
	 */
	private function strategyFirstVoteDecides(IVotingSubject $votingSubject, IVotingContext $votingContext)
	{
		foreach ($this->voters as $name => $voter) {
			if (($result = $voter->vote($votingSubject, $votingContext)) !== null) {
				return $this->prepareResult($name, $result);
			}
		}
		throw new InvalidVotingResultException('Voting assembly did not decide on any result!');
	}


	/**
	 * @param string $voterName
	 * @param string|IVotingResult $partialResult
	 * @return \SpareParts\Overseer\VotingResult|null
	 * @throws \SpareParts\Overseer\InvalidVotingResultException
	 */
	protected function prepareResult($voterName, $partialResult)
	{
		if (is_null($partialResult)) {
			return null;
		}
		if (is_string($partialResult)) {
			return new VotingResult($partialResult, $voterName);
		}
		if (!($partialResult instanceof IVotingResult)) {
			throw new InvalidVotingResultException('Expected bool or IVotingResult, got '.(string)$partialResult);
		}
		return $partialResult;
	}


	private function strategyAllowUnlessDenied($votingSubject, $votingContext)
	{
		foreach ($this->voters as $name => $voter) {
			if (($result = $voter->vote($votingSubject, $votingContext)) !== null) {
				return $this->prepareResult($name, $result);
			}
		}
		throw new InvalidVotingResultException('Voting assembly did not decide on any result!');
	}


	/**
	 * @param string $actionName
	 * @param \SpareParts\Overseer\Voter\IVotingSubject $subject
	 * @param \SpareParts\Overseer\Identity\IVotingContext $context
	 * @return bool
	 */
	public function canVoteOn($actionName, IVotingSubject $subject, IVotingContext $context)
	{
		if ($this->contextClassname && !($context instanceof $this->contextClassname)) {
			return false;
		}

		if ($subject->getVotingSubjectName() === $this->subjectName && $actionName === $this->actionName) {
			return true;
		}
		return false;
	}
}
