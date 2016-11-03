<?php

namespace Laroute\Route\Action;

////////////////////////////////////////////////////////////////

class NormalAction implements Contracts\IAction
{

	/**
	 * Var name
	 *
	 * @access private
	 *
	 * @var    string
	 */
	private $name;

	/**
	 * Method __construct
	 *
	 * @access public
	 *
	 * @param  string $name
	 */
	public function __construct( string$name )
	{
		$this->name= $name;
	}

	/**
	 * Method output
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function output()
	{
		return $this->name;
	}

}
