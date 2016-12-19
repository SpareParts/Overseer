<?php
namespace SpareParts\Overseer\Voter;

/**
 * Default VotingSubject. If you want to use your own objects as Voting Subjects, let them implement IVotingSubject directly.
 */
class VotingSubject implements IVotingSubject
{
	/**
	 * @var mixed
	 */
	private $subject;


	/**
	 * VotingSubject constructor.
	 * @param mixed $subject
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
	    if (is_string($this->subject)) {
            return $this->subject;
        }
        if (is_object($this->subject)) {
            return get_class($this->subject);
        }
		return (string) $this->subject;
	}
}
