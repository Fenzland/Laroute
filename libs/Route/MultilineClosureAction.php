<?php

namespace Laroute\Route;

use Laroute\Document\Line;

////////////////////////////////////////////////////////////////

class MultilineClosureAction extends AClosureAction
{

	/**
	 * Var var
	 *
	 * @access public
	 *
	 * @const    string
	 */
	const PATTERN= '/^\\((.*?)\\)=>\\{$/';

	/**
	 * Method __construct
	 *
	 * @access public
	 *
	 * @param  \Laroute\Document\Line $line
	 */
	public function __construct( Line$line )
	{
		$this->setParamString($line->pregGet(self::PATTERN,1));
	}

}
