<?php
namespace SpareParts\Overseer;

use SpareParts\Overseer\Voter\ISingleVoterResult;

interface IVotingResult extends IResult
{
    /**
     * @return ISingleVoterResult[]
     */
    public function getPartialResults();
}
