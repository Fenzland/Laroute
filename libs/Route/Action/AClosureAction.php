<?php

namespace Laroute\Route\Action;

use Laroute\Document\Line;

////////////////////////////////////////////////////////////////

abstract class AClosureAction implements Contracts\IAction
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
	 * Method output
	 *
	 * @access public
	 *
	 * @return \Closure
	 */
	public function output():\Closure
	{
		$functionString= "return function($this->paramString){ $this->body };";

		if( is_callable('eval') ){
			return eval($functionString);
		}else{
			$path= storage_path('laroute/closure');

			file_exists(dirname($path)) or mkdir(dirname($path),0755,true);

			file_put_contents($path,"<?php\n\n$functionString");

			return include $path;
		}
	}

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
