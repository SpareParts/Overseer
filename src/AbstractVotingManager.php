<?php
namespace SpareParts\Overseer;

use SpareParts\Overseer\Assembly\IVotingAbilityAwareAssembly;
use SpareParts\Overseer\Context\IVotingContext;

abstract class AbstractVotingManager
{
    /**
     * @var IVotingAbilityAwareAssembly[]
     */
    private $votingAssemblies;


    /**
     * VotingManager constructor.
     * @param IVotingAbilityAwareAssembly[] $votingAssemblies
     */
    public function __construct(array $votingAssemblies)
    {
        $this->votingAssemblies = $votingAssemblies;
    }


    /**
     * @param string $action
     * @param mixed $votingSubject
     * @param \SpareParts\Overseer\Context\IVotingContext $votingContext
     * @return \SpareParts\Overseer\IVotingResult
     * @throws \SpareParts\Overseer\InvalidVotingResultException
     */
    protected function innerVote($action, $votingSubject, IVotingContext $votingContext)
    {
        foreach ($this->votingAssemblies as $votingAssembly) {
            if (!($votingAssembly instanceof IVotingAbilityAwareAssembly)) {
                throw new InvalidArgumentException('Voting assemblies provided to voting manager must implement IVotingAbilityAwareAssembly interface!');
            }

            if ($votingAssembly->canVoteOn($action, $votingSubject, $votingContext)) {
                return $votingAssembly->commenceVote($votingSubject, $votingContext);
            }
        }

        throw new InvalidVotingResultException('No voting assembly for subject::action: '.
            (string) $votingSubject.'::'.$action);
    }
}
