<?php

namespace Laroute\Route;

use Laroute\Document\Line;

////////////////////////////////////////////////////////////////

class Route implements Contracts\IItem, Contracts\IRoute
{

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
		->parseMethod()
		->parsePath()
		->parseName()
		;
	}

	/**
	 * Method parseMethod
	 *
	 * @access private
	 *
	 * @return self
	 */
	private function parseMethod():self
	{
		$methodString= $this->line->pregGet('/^(any|[A-Z ]+)/');

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
	 * @return self
	 */
	private function parsePath():self
	{
		$this->line->pregMap('/ \/([^\\s]+)/',function( string$matches ){
			$this->path= $matches[1];
		});

		return $this;
	}

	/**
	 * Method parseName
	 *
	 * @access private
	 *
	 * @return self
	 */
	private function parseName():self
	{
		$this->line->pregMap('/ ([^\/\\s]+)$/',function( string$matches ){
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
	 * @param  Line $line
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
			'('=> 'Closure',
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
	 * @return void
	 */
	private function feedCondition()
	{
		$praam= $line->pregGet('/^\?(\w+)/',1);

		if( !in_array($param,$this->params) ){
			throw new Exception('Param not exists.');
		}

		$condition= $line->pregGet('/^\?\w+ (.*)$/',1);
	}

	/**
	 * Method feedMiddleware
	 *
	 * @access private
	 *
	 * @return void
	 */
	private function feedMiddleware()
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
	 * @return void
	 */
	private function feedAction()
	{
		if( isset($this->action) ){
			throw new Exception('Action already setted.');
		}

		$this->action= ltrim($line->content,'>');
	}

}
