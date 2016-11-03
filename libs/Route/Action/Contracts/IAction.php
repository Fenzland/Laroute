<?php

namespace Laroute\Route\Action\Contracts;

////////////////////////////////////////////////////////////////

interface IAction
{

	/**
	 * Method output
	 *
	 * @access public
	 *
	 * @return \Closure | string
	 */
	public function output();

}
