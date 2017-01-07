<?php
namespace SpareParts\Overseer\Context;

/**
 * Classic "user identity" context, used in many role-based authenticators.
 */
interface IIdentityContext extends IVotingContext
{

    /**
     * @return string|int|null
     */
    public function getId();


    /**
     * @return string[]
     */
    public function getRoles();
}
