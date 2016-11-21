<?php

namespace Laroute\Route;

use Laroute\Document\Line;
use Laroute\Exceptions\LarouteSyntaxException as Exception;
use Laroute\Route\Action\NormalAction;
use Laroute\Route\Action\MultilineClosureAction;
use Laroute\Route\Action\SimpleClosureAction;
use Illuminate\Routing\Router;

////////////////////////////////////////////////////////////////

class Route extends ARoute
{

	/**
	 * Var methods
	 *
	 * @access private
	 *
	 * @var    array
	 */
	private $methods;

	/**
	 * Var conditions
	 *
	 * @access private
	 *
	 * @var    array
	 */
	private $conditions= [];

	/**
	 * Var middlewares
	 *
	 * @access private
	 *
	 * @var    array
	 */
	private $middlewares= [];

	/**
	 * Var openedClosure
	 *
	 * @access private
	 *
	 * @var    \Laroute\Route\Action\ClosureAction
	 */
	private $openedClosure;

	/**
	 * Method parseFirstLine
	 *
	 * @access protected
	 *
	 * @param  \Laroute\Document\Line $line
	 */
	protected function parseFirstLine( Line$line )
	{
		$this
		->parseMethod($line)
		->parsePath($line)
		->parseName($line)
		;
	}

	/**
	 * Method listing
	 *
	 * @access public
	 *
	 * @return array
	 */
	public function listing():array
	{
		return [
			'name'=>    $this->name,
			'methods'=> $this->methods,
			'path'=>    $this->path,
		];
	}

	/**
	 * Method makeCallback
	 *
	 * @access public
	 *
	 * @return callable
	 */
	public function makeCallback():callable
	{
		return function(){
			$route= app('router')->match($this->methods,$this->path,$this->action->output());

			$this->name        and $route->name($this->name);
			$this->middlewares and $route->middleware($this->middlewares);
			$this->conditions  and array_walk($this->conditions,function( string$condition, string$param )use( $route ){ $route->where($param,$condition); });
		};
	}

	/**
	 * Method parseMethod
	 *
	 * @access protected
	 *
	 * @param  \Laroute\Document\Line $line
	 *
	 * @return self
	 */
	protected function parseMethod( Line$line ):self
	{
		$methodString= $line->pregGet('/^(any|[A-Z ]+)/');

		if( 'any'===$methodString ){
			$this->methods= array_diff(Router::$verbs,[ 'OPTIONS', ]);
		}else{
			$this->methods= array_intersect(...[
				explode(...[
					' ',
					trim($methodString),
				]),
				Router::$verbs,
			]);
		}

		in_array('GET',$this->methods) && !in_array('HEAD',$this->methods) and $this->methods[]= 'HEAD';

		return $this;
	}

	/**
	 * Method isNameRequired
	 *
	 * @access protected
	 *
	 * @return bool
	 */
	protected function isNameRequired():bool
	{
		return false;
	}

	/**
	 * Method getFeedingMap
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected function getFeedingMap():array
	{
		static $methodMap= [
			'?'=> 'Condition',
			'>'=> [
				'>'=> 'Action',
				''=> 'Middleware',
			],
			'('=> 'ClosureAction',
		];

		return $methodMap;
	}

	/**
	 * Method feedTheCondition
	 *
	 * @access protected
	 *
	 * @param  \Laroute\Document\Line $line
	 *
	 * @return void
	 */
	protected function feedTheCondition( Line$line )
	{
		$param= $line->pregGet('/^\?(\w+)/',1);

		if( !in_array($param,$this->params) ){
			throw new Exception("Param \${$param} not exists.");
		}

		$condition= $line->pregGet('/^\?\w+ (.*)$/',1);

		if( !$condition ){
			throw new Exception('Missing condition.');
		}

		$this->conditions[$param]= $condition;
	}

	/**
	 * Method feedTheMiddleware
	 *
	 * @access protected
	 *
	 * @param  \Laroute\Document\Line $line
	 *
	 * @return void
	 */
	protected function feedTheMiddleware( Line$line )
	{
		$line->pregMap('/(?:^| )>([^\\s]+)/',function( string...$matches ){
			$this->middlewares[]= $matches[1];
		});
	}

	/**
	 * Method feedTheAction
	 *
	 * @access protected
	 *
	 * @param  \Laroute\Document\Line $line
	 *
	 * @return void
	 */
	protected function feedTheAction( Line$line )
	{
		$this->sureNoAction();

		$this->action= new NormalAction(ltrim($line->content,'>'));
	}

	/**
	 * Method feedTheClosureAction
	 *
	 * @access protected
	 *
	 * @param  \Laroute\Document\Line $line
	 *
	 * @return void
	 */
	protected function feedTheClosureAction( Line$line )
	{
		$this->checkClosureParameters($line);

		if( $line->pregMatch(MultilineClosureAction::PATTERN) ){
			$this->startMultilineClosureAction($line);
		}elseif( $line->pregMatch(SimpleClosureAction::PATTERN) ){
			$this->feedSimpleClosureAction($line);
		}else{
			throw new Exception('Closure syntax error.');
		}
	}

	/**
	 * Method checkClosureParameters
	 *
	 * @access private
	 *
	 * @param  Line $line
	 *
	 * @return void
	 */
	private function checkClosureParameters( Line$line )
	{
		preg_replace_callback('/\\$(\\w+)/',function( $matches ){
			if( !in_array($matches[1],$this->params) ){
				throw new Exception("Param \${$matches[1]} not exists.");
			}
		},$line->pregGet('/^\\((.*)\\)=>/',1));
	}

	/**
	 * Method feedSimpleClosureAction
	 *
	 * @access private
	 *
	 * @param  Line $line
	 *
	 * @return void
	 */
	private function feedSimpleClosureAction( Line$line )
	{
		$this->sureNoAction();

		$this->action= new SimpleClosureAction( $line );
	}

	/**
	 * Method startMultilineClosureAction
	 *
	 * @access private
	 *
	 * @param  Line $line
	 *
	 * @return void
	 */
	private function startMultilineClosureAction( Line$line )
	{
		$this->sureNoAction();

		$this->openedClosure= new MultilineClosureAction($line);
	}

	/**
	 * Method getOpenedClosure
	 *
	 * @access public
	 *
	 * @return \Laroute\Route\Action\MultilineClosureAction | null
	 */
	public function getOpenedClosure()
	{
		return $this->openedClosure;
	}

}
