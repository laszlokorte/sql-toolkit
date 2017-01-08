<?php

namespace LaszloKorte\Resource\Template;

require 'Lexer.php';

final class Parser {

	const STATE_FOREIGN = 1;
	const STATE_BLOCK_BEGIN = 2;
	const STATE_PATH_BEGIN = 4;
	const STATE_PATH_COMPLETE = 8;
	const STATE_PATH_AFTER = 16;
	const STATE_FILTER_BEGIN = 32;
	const STATE_FILTER_COMPLETE = 64;

	private $state = self::STATE_FOREIGN;
	private $result = null;

	public function __construct() {
	}

	public function parse(array $tokens) {
		foreach($tokens AS $token) {
			var_dump($token['token_type']);
			$this->state = $this->consume($token);
		}

		return $this->result;
	}

	private function consume($token) {
		switch($this->state) {
			case self::STATE_FOREIGN:
				switch($token['token_type']) {
					case Lexer::T_WHITESPACE:
						return self::STATE_FOREIGN;
						break;
					case Lexer::T_OPEN:
						return self::STATE_BLOCK_BEGIN;
						break;
					default:
						throw $this->expectationFailed($token, [
							Lexer::T_WHITESPACE,
							Lexer::T_OPEN,
						]);
				}
				break;
			case self::STATE_BLOCK_BEGIN:
				switch($token['token_type']) {
					case Lexer::T_WHITESPACE:
						return self::STATE_PATH_BEGIN;
						break;
					case Lexer::T_IDENTIFIER:
						return self::STATE_PATH_COMPLETE;
						break;
					default:
						throw $this->expectationFailed($token, [
							Lexer::T_WHITESPACE,
							Lexer::T_IDENTIFIER,
						]);
				}
				break;
			case self::STATE_PATH_BEGIN:
				switch($token['token_type']) {
					case Lexer::T_IDENTIFIER:
						return self::STATE_PATH_COMPLETE;
						break;
					default:
						throw $this->expectationFailed($token, [
							Lexer::T_IDENTIFIER,
						]);
				}
				break;
			case self::STATE_PATH_COMPLETE:
				switch($token['token_type']) {
					case Lexer::T_PATH_SEPARATOR:
						return self::STATE_PATH_BEGIN;
						break;
					case Lexer::T_FILTER_SEPARATOR:
						return self::STATE_FILTER_BEGIN;
						break;
					case Lexer::T_WHITESPACE:
						return self::STATE_PATH_AFTER;
						break;
					default:
						throw $this->expectationFailed($token, [
							Lexer::T_PATH_SEPARATOR,
							Lexer::T_WHITESPACE,
							Lexer::T_FILTER_SEPARATOR,
						]);
				}
				break;
			case self::STATE_PATH_AFTER:
				switch($token['token_type']) {
					case Lexer::T_FILTER_SEPARATOR:
						return self::STATE_FILTER_BEGIN;
						break;
					default:
						throw $this->expectationFailed($token, [
							Lexer::T_FILTER_SEPARATOR,
						]);
				}
				break;
			case self::STATE_FILTER_BEGIN:
				switch($token['token_type']) {
					case Lexer::T_WHITESPACE:
						return self::STATE_FILTER_BEGIN;
						break;
					case Lexer::T_IDENTIFIER:
						return self::STATE_FILTER_COMPLETE;
						break;
					default:
						throw $this->expectationFailed($token, [
							Lexer::T_WHITESPACE,
							Lexer::T_IDENTIFIER,
						]);
				}
				break;
			case self::STATE_FILTER_COMPLETE:
				switch($token['token_type']) {
					case Lexer::T_WHITESPACE:
						return self::STATE_FILTER_COMPLETE;
						break;
					case Lexer::T_FILTER_SEPARATOR:
						return self::STATE_FILTER_BEGIN;
						break;
					case Lexer::T_CLOSE:
						return self::STATE_FOREIGN;
						break;
					default:
						throw $this->expectationFailed($token, [
							Lexer::T_WHITESPACE,
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
}


$test = '{{ foo.bar.baz | filter | asd }}';
$lexer = new Lexer();
$parser = new Parser();

// preg_match('~(\s+)~iA', $test, $m, 0, 2);
// var_dump($m);

var_dump($parser->parse($lexer->tokenize($test)));