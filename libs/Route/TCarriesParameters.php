<?php

namespace Laroute\Route;

use Laroute\Exceptions\LarouteSyntaxException as Exception;

////////////////////////////////////////////////////////////////

trait TCarriesParameters
{

	/**
	 * Var params
	 *
	 * @access protected
	 *
	 * @var    array
	 */
	protected $params= [];

	/**
	 * Var conditions
	 *
	 * @access protected
	 *
	 * @var    array
	 */
	protected $conditions= [];

	/**
	 * Method setParameters
	 *
	 * @access public
	 *
	 * @param array $parameters
	 *
	 * @return void
	 */
	public function setParameters( array$parameters )
	{
		foreach( $parameters as $parameter )
		{
			$this->setParameter($parameter);
		}
	}

	/**
	 * Method setConditions
	 *
	 * @access public
	 *
	 * @param array $conditions
	 *
	 * @return void
	 */
	public function setConditions( array$conditions )
	{
		foreach( $conditions as $parameter=>$condition )
		{
			$this->setCondition( $parameter, $condition );
		}
	}

	/**
	 * Method setParameter
	 *
	 * @access protected
	 *
	 * @param  string $parameter
	 *
	 * @return void
	 */
	protected function setParameter( string$parameter )
	{
		if( isset($this->params[$parameter]) )
		{
			throw new Exception("Param {$parameter} cannot defined twice.");
		}

		$this->params[$parameter]= $parameter;
	}

	/**
	 * Method setCondition
	 *
	 * @access protected
	 *
	 * @param  string $parameter
	 * @param  string $condition
	 *
	 * @return void
	 */
	protected function setCondition( string$parameter, string$condition )
	{
		if( !in_array($parameter,$this->params) )
		{
			throw new Exception("Param \${$parameter} not exists.");
		}

		$this->conditions[$parameter]= $condition;
	}

	/**
	 * Method takeParametersFromPath
	 *
	 * @access public
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	public function takeParametersFromPath( string$path ):string
	{
		return preg_replace_callback( '/\\{(?P<parameter>\\w+)(?:\\/(?P<condition>(?:(?>\\\\)\\/|[^\\/])+)\\/)?(?P<optional_sign>\\??)\\}/', function( array$matches ){
			$this->setParameter( $matches['parameter'] );

			if( $matches['condition'] ){
				$this->setCondition( $matches['parameter'], $matches['condition'] );
			}

			return "{{$matches['parameter']}{$matches['optional_sign']}}";
		}, $path );

		preg_match_all('/\\{(\\w+)\\??\\}/',$path,$matches);

		$params= array_map( function( string...$matches ){
			$this->setParameter( $matches[1] );
		}, ...$matches );

	}

}
