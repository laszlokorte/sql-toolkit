<?php

namespace LaszloKorte\Resource\Template;

use LaszloKorte\Resource\Template\Nodes\Filter;
use LaszloKorte\Resource\Template\Nodes\OutputTag;
use LaszloKorte\Resource\Template\Nodes\Path;
use LaszloKorte\Resource\Template\Nodes\Sequence;
use LaszloKorte\Resource\Template\Nodes\StaticText;

final class Parser {

	const STATE_STATIC = 1;
	const STATE_PATH_BEGIN = 2;
	const STATE_PATH_COMPLETE = 4;
	const STATE_FILTER_BEGIN = 8;
	const STATE_FILTER_COMPLETE = 16;
	const STATE_TAG_BEGIN = 32;

	private $state = self::STATE_STATIC;
	private $stack = [];

	public function __construct() {
	}

	public function parse(array $tokens) {
		$this->state = self::STATE_STATIC;
		$this->stack = [new Sequence()];
		foreach($tokens AS $token) {
			$this->state = $this->consume($token);
		}

		return $this->stack[0];
	}

	private function consume($token) {
		switch($this->state) {
			case self::STATE_STATIC:
				switch($token['token_type']) {
					case Lexer::T_OPEN:
						array_unshift($this->stack, new Path());
						return self::STATE_TAG_BEGIN;
						break;
					default:
						$this->stack[0]->append(new StaticText($token['text']));
						return self::STATE_STATIC;
				}
				break;
			case self::STATE_TAG_BEGIN:
			case self::STATE_PATH_BEGIN:
				switch($token['token_type']) {
					case Lexer::T_IDENTIFIER:
						$this->stack[0]->extend($token['text']);
						return self::STATE_PATH_COMPLETE;
						break;
					case Lexer::T_IDENTIFIER_QUOTED:
						$this->stack[0]->extend($this->unquoteIdentifier($token['text']));
						return self::STATE_PATH_COMPLETE;
						break;
					case Lexer::T_WHITESPACE:
						return $this->state;
					default:
						throw $this->expectationFailed($token, [
							Lexer::T_IDENTIFIER,
							Lexer::T_IDENTIFIER_QUOTED,
						]);
				}
				break;
			case self::STATE_PATH_COMPLETE:
				switch($token['token_type']) {
					case Lexer::T_PATH_SEPARATOR:
						return self::STATE_PATH_BEGIN;
						break;
					case Lexer::T_FILTER_SEPARATOR:
						$path = array_shift($this->stack);
						array_unshift($this->stack, new OutputTag($path));
						return self::STATE_FILTER_BEGIN;
						break;
					case Lexer::T_CLOSE:
						$path = array_shift($this->stack);
						$output = new OutputTag($path);
						$this->stack[0]->append($output);
						return self::STATE_STATIC;
						break;
					case Lexer::T_WHITESPACE:
						return $this->state;
					default:
						throw $this->expectationFailed($token, [
							Lexer::T_PATH_SEPARATOR,
							Lexer::T_FILTER_SEPARATOR,
						]);
				}
				break;
			case self::STATE_FILTER_BEGIN:
				switch($token['token_type']) {
					case Lexer::T_IDENTIFIER:
						$this->stack[0]->addFilter(new Filter($token['text']));
						return self::STATE_FILTER_COMPLETE;
						break;
					case Lexer::T_WHITESPACE:
						return $this->state;
					default:
						throw $this->expectationFailed($token, [
							Lexer::T_IDENTIFIER,
						]);
				}
				break;
			case self::STATE_FILTER_COMPLETE:
				switch($token['token_type']) {
					case Lexer::T_FILTER_SEPARATOR:
						return self::STATE_FILTER_BEGIN;
						break;
					case Lexer::T_CLOSE:
						$output = array_shift($this->stack);
						$this->stack[0]->append($output);
						return self::STATE_STATIC;
						break;
					case Lexer::T_WHITESPACE:
						return $this->state;
					default:
						throw $this->expectationFailed($token, [
							Lexer::T_FILTER_SEPARATOR,
							Lexer::T_CLOSE,
						]);
				}
				break;
			default:
				throw new \Exception(sprintf('Invalid state %d at offset %d', $this->state, $token['offset']));
		}
	}

	private function expectationFailed($token, array $expected) {
		return new \Exception(sprintf('Unexpected token "%s" at offset %s expected one of %s (%s)', $token['token_type'], $token['offset'], implode(', ', $expected), $this->state));
	}

	private function unquoteIdentifier($string) {
		return substr($string, 1, -1);
	}
}