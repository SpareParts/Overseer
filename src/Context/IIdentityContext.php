<?php
namespace SpareParts\Overseer\Context;

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
