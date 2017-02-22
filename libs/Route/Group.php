<?php

namespace Laroute\Route;

use Laroute\RouteContainer;
use Laroute\Document\Line;
use Laroute\Exceptions\LarouteSyntaxException as Exception;
use Illuminate\Routing\Router;

////////////////////////////////////////////////////////////////

class Group implements Contracts\IItem, Contracts\IContainer
{
	use TContainer;

	/**
	 * Var line
	 *
	 * @access private
	 *
	 * @var    \Laroute\Document\Line
	 */
	private $line;

	/**
	 * Var middlewares
	 *
	 * @access private
	 *
	 * @var    array
	 */
	private $middlewares= [];


	/**
	 * Var domain
	 *
	 * @access private
	 *
	 * @var    string
	 */
	private $domain;


	/**
	 * Var pathPrefix
	 *
	 * @access private
	 *
	 * @var    string
	 */
	private $pathPrefix;


	/**
	 * Var namespace
	 *
	 * @access private
	 *
	 * @var    string
	 */
	private $namespace;


	/**
	 * Var namePrefix
	 *
	 * @access private
	 *
	 * @var    string
	 */
	private $namePrefix;

	/**
	 * Method __construct
	 *
	 * @access public
	 *
	 * @param  Line $line
	 */
	public function __construct( Line$line )
	{
		$this->line= $line;

		$this
		->parseMiddleware()
		->parseDomain()
		->parsePathPrefix()
		->parseNamespace()
		->parseNamePrefix()
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
			'middlewares'=> $this->middlewares,
			'path_prefix'=> $this->pathPrefix,
			'domain'=>      $this->domain,
			'namespace'=>   $this->namespace,
			'name_prefix'=> $this->namePrefix,
			'children'=>    $this->mapItems( function( $item ){ return $item->listing(); } ),
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
		return function( Router$router ){
			$router->group(...[
				$this->getOptions(),
				function()use( $router ){
					array_map(...[
						function( $coallback )use( $router ){
							$coallback($router);
						},
						$this->mapItems(
							function( $item ){
								return $item->makeCallback();
							}
						),
					]);
				},
			]);
		};
	}

	/**
	 * Method getOptions
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function getOptions():array
	{
		$options= [];

		$this->middlewares and $options['middleware']= $this->middlewares ;
		$this->pathPrefix  and $options['prefix']=     $this->pathPrefix  ;
		$this->domain      and $options['domain']=     $this->domain      ;
		$this->namespace   and $options['namespace']=  $this->namespace   ;
		$this->namePrefix  and $options['as']=         $this->namePrefix  ;

		return $options;
	}

	/**
	 * Method parseMiddleware
	 *
	 * @access private
	 *
	 * @return self
	 */
	private function parseMiddleware():self
	{
		$this->line->pregMap('/ >([^\\s]+)/',function( string...$matches ){
			$this->middlewares[]= $matches[1];
		});

		return $this;
	}

	/**
	 * Method parseDomain
	 *
	 * @access private
	 *
	 * @return self
	 */
	private function parseDomain():self
	{
		$domain= '';

		$this->line->pregMap('/ @([^\\s]+)/',function( string...$matches )use( &$domain ){
			$domain= $matches[1];
		});

		$this->domain= $this->takeParametersFromPath( $domain );

		return $this;
	}

	/**
	 * Method parsePathPrefix
	 *
	 * @access public
	 *
	 * @return self
	 */
	public function parsePathPrefix():self
	{
		$path= '';

		$this->line->pregMap('/ \\/([^\\s]+)/',function( string...$matches )use( &$path ){
			$path= $matches[1];
		});

		$this->pathPrefix= $this->takeParametersFromPath( $path );

		return $this;
	}

	/**
	 * Method parseNamespace
	 *
	 * @access public
	 *
	 * @return self
	 */
	public function parseNamespace():self
	{
		$this->line->pregMap('/ &([^\\s]+)/',function( string...$matches ){
			$this->namespace= $matches[1];
		});

		return $this;
	}

	/**
	 * Method parseNamePrefix
	 *
	 * @access public
	 *
	 * @return self
	 */
	public function parseNamePrefix():self
	{
		$this->line->pregMap('/ \\+([^\\s]+)/',function( string...$matches ){
			$this->namePrefix= $matches[1];
		});

		return $this;
	}

}
