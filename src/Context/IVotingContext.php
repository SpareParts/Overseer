<?php
namespace SpareParts\Overseer\Identity;

interface IVotingContext
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
