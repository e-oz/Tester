<?php
namespace Jamm\Tester;

class DebugTracer
{
	protected $trace_max_depth = 7;
	protected $trace_start_depth = 4;
	protected $trace_space_separator = '|';

	public function setTraceSpaceSeparator($trace_space_separator = '|')
	{
		$this->trace_space_separator = $trace_space_separator;
	}

	public function setTraceStartDepth($trace_start_depth = 4)
	{
		$this->trace_start_depth = $trace_start_depth;
	}

	public function setTraceMaxDepth($trace_max_depth = 4)
	{
		$this->trace_max_depth = $trace_max_depth;
	}

	public function getCurrentBacktrace()
	{
		$tmp = array_slice(debug_backtrace(), $this->trace_start_depth);
		if (empty($tmp)) return false;
		$str = '';
		$space = $basespace = $this->trace_space_separator;
		$depth = 0;
		foreach ($tmp as $t)
		{
			if (!isset($t['file'])) $t['file'] = '[not a file]';
			if (!isset($t['line'])) $t['line'] = '[-1]';
			if ($t['function']=='include' || $t['function']=='include_once' || $t['function']==__METHOD__) continue;
			$str .= ' '.$space.$t['file']."\t[".$t['line']."]\t";
			if (array_key_exists('class', $t))
			{
				$str .= $t['class'];
				if (isset($t['type'])) $str .= $t['type'];
			}
			$str .= $t['function'];
			$str .= "\n";
			$space .= $basespace;
			$depth++;
			if ($depth >= $this->trace_max_depth) break;
		}
		return rtrim($str);
	}
}
