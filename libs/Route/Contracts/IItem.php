<?php

namespace Laroute\Route\Contracts;

////////////////////////////////////////////////////////////////

interface IItem
{

	/**
	 * Method setParameters
	 *
	 * @access public
	 *
	 * @param array $parameters
	 *
	 * @return void
	 */
	public function setParameters( array$parameters );

	/**
	 * Method setConditions
	 *
	 * @access public
	 *
	 * @param array $conditions
	 *
	 * @return void
	 */
	public function setConditions( array$conditions );

}
