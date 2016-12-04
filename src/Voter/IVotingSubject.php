<?php
namespace SpareParts\Overseer\Voter;


interface IVotingSubject
{

	/**
	 * Should return the "resource type", for e.g. "product", "category", "presenter_action", ...
	 *
	 * @return string
	 */
	public function getVotingSubjectName();
}
