<?php
namespace SpareParts\Overseer\Voter;

use SpareParts\Overseer\IResult;

interface ISingleVoterResult extends IResult
{
    /**
     * @return mixed
     */
    public function getReason();
}