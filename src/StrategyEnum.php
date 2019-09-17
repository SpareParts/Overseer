<?php
namespace SpareParts\Overseer;

use SpareParts\Enum\Enum;

/**
 * @method static StrategyEnum FIRST_VOTE_DECIDES()
 * @method static StrategyEnum ALLOW_UNLESS_DENIED()
 * @method static StrategyEnum DENY_UNLESS_ALLOWED()
 * @method static StrategyEnum EVERYONE_MUST_ALLOW_TO_BE_ALLOWED()
 * @method static StrategyEnum EVERYONE_MUST_DENY_TO_BE_DENIED()
 */
final class StrategyEnum extends Enum
{
    protected static $values = [
        'FIRST_VOTE_DECIDES',
        'ALLOW_UNLESS_DENIED',
        'DENY_UNLESS_ALLOWED',
        'EVERYONE_MUST_ALLOW_TO_BE_ALLOWED',
        'EVERYONE_MUST_DENY_TO_BE_DENIED',
    ];
}
