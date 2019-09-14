<?php
namespace SpareParts\Overseer;

use SpareParts\Enum\Enum;

/**
 * @method static VotingDecisionEnum ALLOWED()
 * @method static VotingDecisionEnum DENIED()
 */
final class VotingDecisionEnum extends Enum
{
    protected static $values = [
        'ALLOWED',
        'DENIED',
    ];
}
