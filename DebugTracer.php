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
		$str               = '';
		$space             = $basespace = $this->trace_space_separator;
		$depth             = 0;
		$ignored_functions = array(__METHOD__, 'trigger_error', 'include_once', 'include', 'require', 'require_once');
		foreach ($tmp as $t)
		{
			if (!isset($t['file'])) $t['file'] = '[not a file]';
			if (!isset($t['line'])) $t['line'] = '[-1]';
			if (in_array($t['function'], $ignored_functions)) continue;
			$str .= ' '.$space.$t['file']."\t[".$t['line']."]\t";
			if (array_key_exists('class', $t))
			{
				$str .= $t['class'];
				if (isset($t['type'])) $str .= $t['type'];
			}
			$str .= $t['function'];
			if (isset($t['args'][0]))
			{
				$args = array();
				$str .= '(';
				foreach ($t['args'] as $t_arg)
				{
					if (!is_scalar($t_arg))
					{
						if (is_array($t_arg)) $args[] = print_r($t_arg, 1);
						else $args[] = '[scalar]';
					}
					else
					{
						if (strlen($t_arg) > 128) $args[] = '['.substr($t_arg, 0, 128).'...]';
						else $args[] = $t_arg;
					}
				}
				$str .= implode(', ', $args).')';
			}
			else  $str .= '()';
			$str .= "\n";
			$space .= $basespace;
			$depth++;
			if ($depth >= $this->trace_max_depth) break;
		}
		return rtrim($str);
	}
}
