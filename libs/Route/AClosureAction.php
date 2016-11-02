<?php

namespace Laroute\Route;

use Laroute\Document\Line;

////////////////////////////////////////////////////////////////

abstract class AClosureAction
{

	/**
	 * Var paramString
	 *
	 * @access private
	 *
	 * @var    string
	 */
	private $paramString;

	/**
	 * Var body
	 *
	 * @access private
	 *
	 * @var    string
	 */
	private $body= '';

	/**
	 * Method __construct
	 *
	 * @access public
	 *
	 * @param  \Laroute\Document\Line $line
	 */
	abstract public function __construct( Line$line );

	/**
	 * Method setParamString
	 *
	 * @access protected
	 *
	 * @param  string $paramString
	 *
	 * @return void
	 */
	protected function setParamString( string$paramString )
	{
		$this->paramString= $paramString;
	}

	/**
	 * Method appendBody
	 *
	 * @access protected
	 *
	 * @param  string $content
	 *
	 * @return void
	 */
	protected function appendBody( string...$content )
	{
		$this->body.= implode('',$content);
	}

}
