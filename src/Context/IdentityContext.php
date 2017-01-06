<?php
namespace SpareParts\Overseer\Context;


class IdentityContext implements IIdentityContext
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
