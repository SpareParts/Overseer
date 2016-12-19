<?php
namespace SpareParts\Overseer;


interface IResult
{
    /**
     * @return VotingDecisionEnum
     */
    public function getDecision();
}