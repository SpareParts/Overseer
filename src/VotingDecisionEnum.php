<?php
namespace SpareParts\Overseer;

use SpareParts\Enum\Enum;

/**
 * @method static ALLOWED
 * @method static DENIED
 */
final class VotingDecisionEnum extends Enum
{
    protected static $values = [
        'ALLOWED',
        'DENIED',
    ];
}
