<?php
namespace SpareParts\Overseer;


interface IVotingResult
{
	/**
	 * Voting result
	 */
	const
		ALLOW = 'allow',
		DENY = 'deny';


	/**
	 * @return bool
	 */
	public function isAllowed();



	/**
	 * @return mixed|null
	 */
	public function getReason();
}
