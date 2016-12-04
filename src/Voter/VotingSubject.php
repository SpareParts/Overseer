<?php
namespace SpareParts\Overseer\Voter;

/**
 * Default VotingSubject. If you want to use your own objects as Voting Subjects, let them implement IVotingSubject directly.
 */
class VotingSubject implements IVotingSubject
{
	/**
	 * @var string
	 */
	private $subject;


	/**
	 * VotingSubject constructor.
	 * @param string $subject
	 */
	public function __construct($subject)
	{
		$this->subject = $subject;
	}


	/**
	 * @return string
	 */
	public function getVotingSubjectName()
	{
		return $this->subject;
	}
}
