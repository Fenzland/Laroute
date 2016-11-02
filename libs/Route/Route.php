<?php

namespace Laroute\Route;

use Laroute\Document\Line;
use Laroute\Exceptions\LarouteSyntaxException as Exception;
use Laroute\Helper\TGetter;
use Illuminate\Routing\Router;

////////////////////////////////////////////////////////////////

class Route implements Contracts\IItem, Contracts\IRoute
{
	use TGetter;

	/**
	 * Var methods
	 *
	 * @access private
	 *
	 * @var    array
	 */
	private $methods;

	/**
	 * Var path
	 *
	 * @access private
	 *
	 * @var    string
	 */
	private $path;

	/**
	 * Var params
	 *
	 * @access private
	 *
	 * @var    array
	 */
	private $params= [];

	/**
	 * Var conditions
	 *
	 * @access private
	 *
	 * @var    array
	 */
	private $conditions= [];

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
	 * @param  \Laroute\Document\Line $line
	 */
	public function __construct( Line$line )
	{
		$this
		->parseMethod($line)
		->parsePath($line)
		->parseName($line)
		;
	}

	/**
	 * Method parseMethod
	 *
	 * @access private
	 *
	 * @param  \Laroute\Document\Line $line
	 *
	 * @return self
	 */
	private function parseMethod( Line$line ):self
	{
		$methodString= $line->pregGet('/^(any|[A-Z ]+)/');

		if( 'any'===$methodString ){
			$this->methods= array_except(Router::$verbs,'OPTIONS');
		}else{
			$this->methods= array_filter(...[
				explode(' ',trim($methodString)),
				function( $item ){
					return in_array($item,Router::$verbs);
				},
			]);
		}

		in_array('GET',$this->methods) && !in_array('HEAD',$this->methods) and $this->methods[]= 'HEAD';

		return $this;
	}

	/**
	 * Method parsePath
	 *
	 * @access private
	 *
	 * @param  \Laroute\Document\Line $line
	 *
	 * @return self
	 */
	private function parsePath( Line$line ):self
	{
		$this->path= $line->pregGet('/ (\\/([^\\s]?))/',1);

		if( !$this->path ){
			throw new Exception('Missing path.');
		}


		preg_match_all('/\\{(\\w+)\\??\\}/',$this->path,$matches);

		$params= array_map( function($matches){
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
	 * @access private
	 *
	 * @param  \Laroute\Document\Line $line
	 *
	 * @return self
	 */
	private function parseName( Line$line ):self
	{
		$line->pregMap('/ ([^\/\\s]+)$/',function( string$matches ){
			$this->name= $matches[1];
		});

		return $this;
	}

	/**
	 * Method feed
	 *
	 * @access public
	 *
	 * @param  \Laroute\Document\Line $line
	 *
	 * @return void
	 */
	public function feed( Line$line )
	{
		$this->{$this->getFeedMethod($line)}($line);
	}

	/**
	 * Method getFeedMethod
	 *
	 * @access private
	 *
	 * @param  \Laroute\Document\Line $line
	 *
	 * @return void
	 */
	private function getFeedMethod( Line$line )
	{
		static $methodMap= [
			'?'=> 'Condition',
			'>'=> [
				'>'=> 'Action',
				''=> 'Middleware',
			],
			'('=> 'ClosureAction',
		];

		for(  $map= $methodMap, $i=0;  is_array($map);  ++$i  ){
			$map= $map[$line->getChar($i)]??$map['']??null;
		}

		if( !$map ){
			throw new Exception('Illegal route modifier');
		}

		return "feed$map";
	}

	/**
	 * Method feedCondition
	 *
	 * @access private
	 *
	 * @param  \Laroute\Document\Line $line
	 *
	 * @return void
	 */
	private function feedCondition( Line$line )
	{
		$praam= $line->pregGet('/^\?(\w+)/',1);

		if( !in_array($param,$this->params) ){
			throw new Exception('Param not exists.');
		}

		$condition= $line->pregGet('/^\?\w+ (.*)$/',1);

		if( !$condition ){
			throw new Exception('Missing condition.');
		}

		$this->conditions[$param]= $conditions;
	}

	/**
	 * Method feedMiddleware
	 *
	 * @access private
	 *
	 * @param  \Laroute\Document\Line $line
	 *
	 * @return void
	 */
	private function feedMiddleware( Line$line )
	{
		$line->pregMap('/(?:^| )>([^\\s]+)/',function( string$matches ){
			$this->middlewares[]= $matches[1];
		});
	}

	/**
	 * Method feedAction
	 *
	 * @access private
	 *
	 * @param  \Laroute\Document\Line $line
	 *
	 * @return void
	 */
	private function feedAction( Line$line )
	{
		$this->sureNoAction();

		$this->action= ltrim($line->content,'>');
	}

	/**
	 * Method feedClosureAction
	 *
	 * @access private
	 *
	 * @param  \Laroute\Document\Line $line
	 *
	 * @return void
	 */
	private function feedClosureAction( Line$line )
	{
		if( $line->pregMatch(ClosureAction::PATTERN) ){
			$this->startClosureAction($line);
		}elseif( $line->pregMatch(SimpleClosureAction::PATTERN) ){
			$this->feedSimpleClosureAction($line);
		}else{
			throw new Exception('Closure syntax error.');
		}
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
	 * Method startClosureAction
	 *
	 * @access private
	 *
	 * @param  Line $line
	 *
	 * @return void
	 */
	private function startClosureAction( Line$line )
	{
		$this->sureNoAction();
	}

	/**
	 * Method sureNoAction
	 *
	 * @access private
	 *
	 * @return void
	 */
	private function sureNoAction()
	{
		if( isset($this->action) ){
			throw new Exception('Action already setted.');
		}
	}

	/**
	 * Method getOpendClosure
	 *
	 * @access public
	 *
	 * @return \Laroute\Route\Closure
	 */
	public function getOpendClosure()
	{
		#
	}

}
