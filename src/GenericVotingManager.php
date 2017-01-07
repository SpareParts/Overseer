<?php
namespace SpareParts\Overseer;

use SpareParts\Overseer\Context\IVotingContext;

/**
 * Default implementation.
 *
 * You should provide specific implementation, with exact typehints and function names.
 * Use AbstractVotingManager's innerVote method as a starting point.
 *
 * This one works as well though. It's just a little too... generic :)
 */
final class GenericVotingManager extends AbstractVotingManager
{

    /**
     * @param string $action
     * @param mixed $votingSubject
     * @param \SpareParts\Overseer\Context\IVotingContext $votingContext
     * @return \SpareParts\Overseer\IVotingResult
     * @throws \SpareParts\Overseer\InvalidVotingResultException
     */
    public function vote($action, $votingSubject, IVotingContext $votingContext)
    {
        return $this->innerVote($action, $votingSubject, $votingContext);
    }
}
