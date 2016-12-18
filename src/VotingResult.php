<?php
namespace SpareParts\Overseer;


use SpareParts\Overseer\Voter\IVoter;

class VotingResult implements IVotingResult
{
	/**
	 * @var string|null
	 */
	private $reason;

	/**
	 * @var string
	 */
	private $status;


	/**
	 * VotingResult constructor.
	 * @param string $status
	 * @param string|null $reason
	 */
	public function __construct($status, $reason = null)
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
