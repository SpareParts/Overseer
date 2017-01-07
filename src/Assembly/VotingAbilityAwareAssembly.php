<?php
namespace SpareParts\Overseer\Assembly;


use SpareParts\Overseer\Context\IIdentityContext;
use SpareParts\Overseer\Context\IVotingContext;
use SpareParts\Overseer\StrategyEnum;
use SpareParts\Overseer\Voter\IVoter;

class VotingAbilityAwareAssembly extends VotingAssembly implements IVotingAbilityAwareAssembly
{

    /**
     * @var string
     */
    protected $subjectClassName;

    /**
     * @var string
     */
    protected $contextClassName;

    /**
     * @var string[]
     */
    protected $actionList;


    /**
     * @param \SpareParts\Overseer\StrategyEnum $strategy
     * @param IVoter[] $voters
     * @param string|string[] $actions
     * @param string $contextClassName
     * @param string $subjectClassName
     */
    public function __construct(
        StrategyEnum $strategy,
        array $voters,
        $actions = null,
        $contextClassName = IIdentityContext::class,
        $subjectClassName = null
    ) {
        parent::__construct($strategy, $voters);

        $this->actionList = (array) $actions;
        $this->subjectClassName = $subjectClassName;
        $this->contextClassName = $contextClassName;
    }


    /**
     * @param string $actionName
     * @param object $subject
     * @param \SpareParts\Overseer\Context\IVotingContext $context
     * @return bool
     */
    public function canVoteOn($actionName, $subject, IVotingContext $context)
    {
        if ($this->subjectClassName && !($subject instanceof $this->subjectClassName)) {
            return false;
        }

        if ($this->contextClassName && !($context instanceof $this->contextClassName)) {
            return false;
        }

        if ($this->actionList && !in_array($actionName, $this->actionList)) {
            return false;
        }
        return true;
    }

}
