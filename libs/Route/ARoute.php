<?php

namespace Laroute\Route;

use Laroute\Document\Line;
use Laroute\Exceptions\LarouteSyntaxException as Exception;
use Laroute\Helper\TGetter;
use Laroute\Route\Action\NormalAction;
use Laroute\Route\Action\MultilineClosureAction;
use Laroute\Route\Action\SimpleClosureAction;
use Illuminate\Routing\Router;

////////////////////////////////////////////////////////////////

abstract class ARoute implements Contracts\IItem, Contracts\IRoute
{
	use TGetter;

	/**
	 * Var path
	 *
	 * @access protected
	 *
	 * @var    string
	 */
	protected $path;

	/**
	 * Var name
	 *
	 * @access protected
	 *
	 * @var    string
	 */
	protected $name;

	/**
	 * Var action
	 *
	 * @access protected
	 *
	 * @var    \Laroute\Route\Action\Contract\IAction
	 */
	protected $action;

	/**
	 * Var params
	 *
	 * @access protected
	 *
	 * @var    array
	 */
	protected $params= [];

	/**
	 * Method __construct
	 *
	 * @access public
	 *
	 * @param  \Laroute\Document\Line $line
	 */
	final public function __construct( Line$line )
	{
		$this->parseFirstLine($line);
	}

	/**
	 * Method parseFirstLine
	 *
	 * @access protected
	 *
	 * @param  \Laroute\Document\Line $line
	 *
	 * @return void
	 */
	abstract protected function parseFirstLine( Line$line );

	/**
	 * Method listing
	 *
	 * @access public
	 *
	 * @return array
	 */
	abstract public function listing():array;

	/**
	 * Method makeCallback
	 *
	 * @access public
	 *
	 * @return callable
	 */
	abstract public function makeCallback():callable;

	/**
	 * Method getOpenedClosure
	 *
	 * @access public
	 *
	 * @return \Laroute\Route\Closure
	 */
	abstract public function getOpenedClosure();

	/**
	 * Method parsePath
	 *
	 * @access protected
	 *
	 * @param  \Laroute\Document\Line $line
	 *
	 * @return self
	 */
	protected function parsePath( Line$line ):self
	{
		$this->path= $line->pregGet('/ (\\/([^\\s]*))/',1);

		if( !$this->path ){
			throw new Exception('Missing path.');
		}


		preg_match_all('/\\{(\\w+)\\??\\}/',$this->path,$matches);

		$params= array_map( function( string...$matches ){
			if( isset($this->params[$matches[1]]) ){
				throw new Exception("Param {$matches[1]} cannot defined twice.");
			}
			$this->params[$matches[1]]= $matches[1];
		}, ...$matches );

		return $this;
	}

	/**
	 * Method parseName
	 *
	 * @access protected
	 *
	 * @param  \Laroute\Document\Line $line
	 *
	 * @return self
	 */
	protected function parseName( Line$line ):self
	{
		$line->pregMap('/ ([^\/\\s]+)$/',function( string...$matches ){
			$this->name= $matches[1];
		});

		return $this;
	}

	/**
	 * Method getFeedingMap
	 *
	 * @access protected
	 *
	 * @return array
	 */
	abstract protected function getFeedingMap():array;

	/**
	 * Method feed
	 *
	 * @access public
	 *
	 * @param  \Laroute\Document\Line $line
	 *
	 * @return void
	 */
	final public function feed( Line$line )
	{
		$this->{$this->getFeedingMethod($line)}($line);
	}

	/**
	 * Method getFeedingMethod
	 *
	 * @access private
	 *
	 * @param  \Laroute\Document\Line $line
	 *
	 * @return void
	 */
	private function getFeedingMethod( Line$line )
	{
		for(  $map= $this->getFeedingMap(), $i=0;  is_array($map);  ++$i  ){
			$map= $map[$line->getChar($i)]??$map['']??null;
		}

		if( !$map ){
			throw new Exception('Illegal route modifier');
		}

		return "feedThe$map";
	}

	/**
	 * Method feedTheAction
	 *
	 * @access private
	 *
	 * @param  \Laroute\Document\Line $line
	 *
	 * @return void
	 */
	private function feedTheAction( Line$line )
	{
		$this->sureNoAction();

		$this->action= new NormalAction(ltrim($line->content,'>'));
	}

	/**
	 * Method sureNoAction
	 *
	 * @access protected
	 *
	 * @return void
	 */
	final protected function sureNoAction()
	{
		if( isset($this->action) ){
			throw new Exception('Action already setted.');
		}
	}

	/**
	 * Method close
	 *
	 * @access public
	 *
	 * @return
	 */
	final public function close()
	{
		if( !isset($this->action) ){
			throw new Exception('Missing action.');
		}
	}

}
