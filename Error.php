<?php
namespace Jamm\Tester;

class Error
{
	protected $message;
	protected $code;
	protected $filepath;
	protected $line;
	protected $timestamp;
	protected $debug_trace;
	protected $debug_trace_level = 4;

	public function __construct()
	{
		$this->timestamp = microtime(true);
		$trace = debug_backtrace();
		$this->debug_trace = array_slice($trace, $this->debug_trace_level);
	}

	public function setCode($code)
	{
		$this->code = $code;
	}

	public function getCode()
	{
		return $this->code;
	}

	public function setDebugTrace($debug_trace)
	{
		$this->debug_trace = $debug_trace;
	}

	public function getDebugTrace()
	{
		return $this->debug_trace;
	}

	public function setFilepath($filepath)
	{
		$this->filepath = $filepath;
	}

	public function getFilepath()
	{
		return $this->filepath;
	}

	public function setLine($line)
	{
		$this->line = $line;
	}

	public function getLine()
	{
		return $this->line;
	}

	public function setMessage($message)
	{
		$this->message = $message;
	}

	public function getMessage()
	{
		return $this->message;
	}

	public function setTimestamp($timestamp)
	{
		$this->timestamp = $timestamp;
	}

	public function getTimestamp()
	{
		return $this->timestamp;
	}
}
