<?php
namespace SpareParts\Overseer;


use SpareParts\Overseer\Voter\IVoter;

class VotingResult implements IVotingResult
{
	/**
	 * @var string
	 */
	private $reason;

	/**
	 * @var string
	 */
	private $status;


	/**
	 * VotingResult constructor.
	 * @param string $status
	 * @param string $reason
	 */
	public function __construct($status, $reason)
	{
		$this->reason = $reason;
		$this->status = $status;
	}


	/**
	 * @return bool
	 */
	public function isAllowed()
	{
		return $this->status === static::ALLOW;
	}


	/**
	 * @return string
	 */
	public function getReason()
	{
		return $this->reason;
	}
}
