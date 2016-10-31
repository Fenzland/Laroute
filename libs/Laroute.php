<?php

namespace Laroute;

use Laroute\Helper\Stack;
use Laroute\Document\Document;
use Laroute\Document\Line;
use Laroute\Document\Group;

////////////////////////////////////////////////////////////////

class Laroute
{

	/**
	 * Var currentLine
	 *
	 * @access private
	 *
	 * @var    \Laroute\Document\Line
	 */
	private $currentLine;

	/**
	 * Var currentClosure
	 *
	 * @access private
	 *
	 * @var    \Laroute\Document\Closure
	 */
	private $currentClosure;

	/**
	 * Var currentRoute
	 *
	 * @access private
	 *
	 * @var
	 */
	private $currentRoute;

	/**
	 * Var groups
	 *
	 * @access private
	 *
	 * @var    \Laroute\Helper\Stack
	 */
	private $groups;

	/**
	 * Current indent level
	 *
	 * @access private
	 *
	 * @var    int
	 */
	private $indentLevel;

	/**
	 * Loading a route file and creating routes.
	 *
	 * @access public
	 *
	 * @param  string $filename
	 */
	public function __construct( string$filename )
	{
		$this->groups= new Stack(Group::class);

		$this->document= new Document($filename);

		$this->parse();
	}

	/**
	 * Execute the route.
	 *
	 * @access public
	 */
	public function execute()
	{
		#
	}

	/**
	 * Parsing route file.
	 *
	 * @access private
	 */
	private function parse()
	{
		while( ($line= $this->document->getLine())->hasMore() ){
			if( $this->currentClosure ){
				$this->closureFeeding($line);
			}elseif( $this->currentRoute ){
				$this->routeFeeding($line);
			}else{
				$this->parseLine($line);
			}
		}
	}

	/**
	 * Feeding the closure or close it.
	 *
	 * @access private
	 *
	 * @param  \Laroute\Document\Line $line
	 *
	 */
	private function closureFeeding( Line$line )
	{
		$this->checkIndentAndProcess(...[
			$line,
			function( Line$line ){
				$this->currentClosure->feed($line);
			},
			function( Line$line ){
				if( $line->content==='}' ){
					$this->closeClosure();
				}else{
					$this->throw('Indent error');
				}
			},
			function( Line$line ){
				$this->throw('Indent error');
			},
		]);
	}

	/**
	 * Method routeFeeding
	 *
	 * @access private
	 *
	 * @param  \Laroute\Document\Line $line
	 *
	 * @return
	 */
	private function routeFeeding( Line$line )
	{
		$this->checkIndentAndProcess(...[
			$line,
			function( Line$line ){
				$this->currentRoute->feed($line);
			},
			function( Line$line ){
				$this->closeRoute($line);
				$this->setIndent($line->getIndentLevel());
			},
		]);
	}

	/**
	 * Method checkIndentAndProcess
	 *
	 * @access private
	 *
	 * @param  \Laroute\Document\Line $line
	 * @param  callable $more
	 * @param  callable $equal
	 * @param  callable $less
	 *
	 * @return void
	 */
	private function checkIndentAndProcess( Line$line, callable$more, callable$equal, callable$less=null ):void
	{
		$diff= $line->indentLevel - $this->indentLevel;

		if( $diff > 1 ){
			$this->throw('Indent error');
		}elseif( $diff == 1 ){
			$more($line);
		}elseif( $diff == 0 ){
			$equal($line);
		}else{
			($less??$equal)($line);
		}
	}

	/**
	 * Method closeClosure
	 *
	 * @access private
	 */
	private function closeClosure()
	{
		$this->closure= null;
	}

	/**
	 * Parsing a line
	 *
	 * @access private
	 *
	 * @param  \Laroute\Document\Line $line
	 */
	private function parseLine( Line$line )
	{
		$this->currentLine= $line;
		$this->setLevel($line->getIndentLevel());

		switch( $line->getChar(0) ){
			default:{
				$this->createRoute($line);
			}break;

			case ':':{
				$this->createGroup($line);
			}break;

			case '@':{
				$this->include($line->pregGet('/^@\s?(.+)/',1));
			}break;
		}
	}

	/**
	 * Setting indent level of this document.
	 *
	 * @access private
	 *
	 * @param int $level
	 */
	private function setLevel( int$level )
	{
		$level-= $this->indentLevel;

		if( $level<=0 ){
			$this->closeGroups(-$level);
		}elseif( $level==1 ){
			$this->indentLevel+= 1;
		}else{
			$this->throw('Indent error.');
		}
	}

	/**
	 * Method createGroup
	 *
	 * @access private
	 *
	 * @param  \Laroute\Document\Line $line
	 */
	private function createGroup( Line$line )
	{
		$this->groups->push(new Group($line));
	}

	/**
	 * Method createRoute
	 *
	 * @access private
	 *
	 * @param  \Laroute\Document\Line $line
	 */
	private function createRoute( Line$line )
	{
		#
	}

	/**
	 * Method include
	 *
	 * @access private
	 *
	 * @param  string $fileName
	 */
	private function include( string$fileName )
	{
		#
	}

	/**
	 * Closing open groups.
	 *
	 * @access protected
	 *
	 * @param  int $level
	 */
	protected function closeGroups( int$level=0 )
	{
		if( $this->groups->isEmpty() ) return;

		while( $level-->=0 ){
			$node= $this->groups->pop();

			$this->indentLevel-= $level>=0 ?1:0;
		}
	}

}
