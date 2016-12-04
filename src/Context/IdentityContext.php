<?php
namespace SpareParts\Overseer\Identity;


class IdentityContext implements IVotingContext
{

	/**
	 * @var mixed
	 */
	private $id;

	/**
	 * @var string[]
	 */
	private $roles;


	/**
	 * VotingContext constructor.
	 * @param mixed $id
	 * @param string[] $roles
	 */
	public function __construct($id, array $roles)
	{
		$this->id = $id;
		$this->roles = $roles;
	}


	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * @return string[]
	 */
	public function getRoles()
	{
		return $this->roles;
	}
}
