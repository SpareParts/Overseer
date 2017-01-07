<?php
namespace SpareParts\Overseer\Assembly;


use SpareParts\Overseer\Context\IVotingContext;

/**
 * Voting assembly with ability to know which combination of action/subject/context it can vote on.
 *
 * Intended to use in "voting managers" that manage multiple assemblies without knowing exactly
 */
interface IVotingAbilityAwareAssembly extends IVotingAssembly
{


    /**
     * @param string $actionName
     * @param mixed $subject
     * @param \SpareParts\Overseer\Context\IVotingContext $context
     * @return bool
     */
    public function canVoteOn($actionName, $subject, IVotingContext $context);
}
