<?php

namespace App\Controllers;

class System extends Base {

	public function JsErrorsLogAction () {
		$this->SetViewEnabled(FALSE);
		$sysCfg = $this->GetConfigSystem();
		$jsErrorsLog = isset($sysCfg->debug->jsErrorsLog)
			? (bool) $sysCfg->debug->jsErrorsLog
			: FALSE;
		if (!$jsErrorsLog) return;
		$keys = [
			'message'	=> 1,
			'uri'		=> 1,
			'file'		=> 1,
			'line'		=> 0,
			'column'	=> 0,
			'callstack'	=> 1,
			'browser'	=> 1,
			'platform'	=> 0,
		];
		$data = [];
		foreach ($keys as $key => $hex) {
			$param = $this->GetParam($key);
			if ($hex) $param = self::_hexToStr($param);
			$data[$key] = $param;
		}
		$msg = \MvcCore\Tool::JsonEncode($data, JSON_PRETTY_PRINT);
		\MvcCore\Debug::Log($msg, \MvcCore\Debug::JAVASCRIPT);
	}

	private static function _hexToStr ($hex) {
		$string='';
		for ($i = 0; $i < strlen($hex) - 1; $i += 2)
			$string .= chr(hexdec($hex[$i].$hex[$i+1]));
		return $string;
	}
}
