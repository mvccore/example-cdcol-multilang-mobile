<?php

namespace MvcCore\Ext\Models\Translators;

class Csv extends \MvcCore\Model implements \MvcCore\Ext\Models\ITranslator
{
	/**
	 * Relative path to directory with CSV translations, relative to
	 * application root directory. Default value is `/Var/Translations`.
	 * @var string
	 */
	protected static $dataDir = '/Var/Translations';

	/**
	 * Boolean about write unknown translation keys into data store in development mode.
	 * @var bool|null
	 */
	protected static $writeTranslations = NULL;

	/**
	 * Store for unknown translation keys for current request to write at the end.
	 * @var array
	 */
	protected static $notTranslatedKeys = [];

	/**
	 * Boolean about if shutdown handler is registered already to write unknown
	 * translations in current request.
	 * @var bool
	 */
	protected static $shutdownHandlerRegistered = FALSE;

	/**
	 * Translator localization - it could be international language code in
	 * lower case or (international language code in lower case plus dash and
	 * plus international locale code in upper case).
	 * @var string|NULL
	 */
	protected $localization = NULL;

	/**
	 * All parsed translations store. Keys are translations keys, value are
	 * translated terms and phrases.
	 * @var array|NULL
	 */
	protected $translations = NULL;

	/**
	 * Get translator instance by localization key (for example: `en`, `en-US`).
	 * @param string $localization	International language code in lower case or
	 *								(international language code in lower case
	 *								plus dash and plus international locale code
	 *								in upper case).
	 * @return \MvcCore\Model|\MvcCore\IModel|\MvcCore\Ext\Models\Translators\Csv
	 */
	public static function GetInstance () {
		return call_user_func_array('parent::GetInstance', func_get_args());
	}

	/**
	 * Configure relative path to directory with CSV translations, relative to
	 * application root directory. Default value is `/Var/Translations`.
	 * @param string $dataDir
	 * @return string
	 */
	public static function SetDataDir ($dataDir = '/Var/Translations') {
		return self::$dataDir = $dataDir;
	}

	/**
	 * Return relative path to directory with CSV translations, relative to
	 * application root directory.
	 * @return string
	 */
	public static function GetDataDir () {
		return self::$dataDir;
	}

	/**
	 * Create new translator instance. To cache translator instance and it's
	 * parsed CSV data values inside, use static method:
	 * `\MvcCore\Ext\Models\Translators\Csv::GetInstance('en');`.
	 * @param string $localization	International language code in lower case or
	 *								(international language code in lower case
	 *								plus dash and plus international locale code
	 *								in upper case).
	 * @return void
	 */
	public function __construct ($localization) {
		$this->localization = $localization;
		if (self::$writeTranslations === NULL) {
			$environment = \MvcCore\Application::GetInstance()->GetEnvironment();
			self::$writeTranslations = $environment->IsDevelopment();
		}
	}

	/**
	 * Set translator localization (for example as `en` or `en-US` ...).
	 * @param string $localization	International language code in lower case or
	 *								(international language code in lower case
	 *								plus dash and plus international locale code
	 *								in upper case).
	 * @return \MvcCore\Ext\Models\Translators\Csv
	 */
	public function SetLocalization ($localization) {
		$this->localization = $localization;
		return $this;
	}

	/**
	 * Get translator localization - it could be international language code in
	 * lower case or (international language code in lower case plus dash and
	 * plus international locale code in upper case).
	 * @return string
	 */
	public function GetLocalization () {
		return $this->localization;
	}

	/**
	 * Basic translation view helper implementation by `__invoke()` megic method.
	 * Please register translation view helper more better by anonymous closure
	 * function with `$this->Translate()` function call inside. It's much faster.
	 * to handle view helper calls to translate strings.
	 * @param string $key			A key to translate.
	 * @param array $replacements	An array of replacements to process in translated result.
	 * @throws \Exception			En exception if translations store is not successful.
	 * @return string				Translated key or key itself it here is no key in translations store.
	 */
	public function __invoke ($translationKey, $replacements = []) {
		return $this->Translate($translationKey, $replacements);
	}

	/**
	 * Translate given key into target localization. If there is no translation
	 * for given key in translations data, there is returned given key.
	 * @param string $key			A key to translate.
	 * @param array $replacements	An array of replacements to process in translated result.
	 * @throws \Exception			En exception if translations store is not successful.
	 * @return string				Translated key or key itself it here is no key in translations store.
	 */
	public function Translate ($translationKey, $replacements = []) {
		$result = $translationKey;
		if ($this->translations === NULL)
			$this->translations = $this->LoadTranslationsStore();
		if (isset($this->translations[$translationKey])) {
			$result = $this->translations[$translationKey];
		} else {
			self::writeNotTranslatedKey($this->localization, $translationKey);
		}
		foreach ($replacements as $key => $val)
			$result = str_replace('{'.$key.'}', (string) $val, $result);
		return $result;
	}

	/**
	 * Load CSV translation store (result is not cached, this function loads
	 * and parse CSV every time is called.
	 * @throws \Exception
	 * @return array
	 */
	public function LoadTranslationsStore () {
		$store = [];
		$appRoot = \MvcCore\Application::GetInstance()->GetRequest()->GetAppRoot() ;
		$fileFullPath = $appRoot . self::$dataDir . '/' . $this->localization . '.csv';
		if (!file_exists($fileFullPath)) {
			if (!self::$writeTranslations)
				self::thrownAnException(
					"No translations found in path: `{$fileFullPath}`."
				);
			$rawCsvRows = [];
		} else {
			$rawCsv = file_get_contents($fileFullPath);
			$rawCsvRows = explode("\n", str_replace("\r\n", "\n", $rawCsv));
		}
		foreach ($rawCsvRows as $rowKey => $rawCsvRow) {
			list($key, $value) = str_getcsv($rawCsvRow, ";", '');
			if (isset($store[$key])) {
				$rowKey += 1;
				self::thrownAnException(
					"Translation key already defined. "
					."(path: '{$fileFullPath}', row: '{$rowKey}', key: '{$key}')"
				);
			}
			$store[$key] = str_replace('\\n', "\n", $value);
		}
		return $store;
	}

	/**
	 * Thrown an exception in CSV store loading process.
	 * @param string $msg
	 * @throws \Exception
	 * @return void
	 */
	protected static function thrownAnException ($msg) {
		throw new \Exception("[".get_class()."] {$msg}");
	}

	/**
	 * Add not translated keys into translations CSV after request is terminated.
	 * @param string $localization
	 * @param string $translationKey
	 * @return void
	 */
	protected static function writeNotTranslatedKey ($localization, $translationKey) {
		if (!self::$writeTranslations) return;
		if (!isset(self::$notTranslatedKeys[$localization]))
			self::$notTranslatedKeys[$localization] = [];
		self::$notTranslatedKeys[$localization][$translationKey] = TRUE;
		if (self::$shutdownHandlerRegistered) return;
		\MvcCore\Application::GetInstance()->AddPostDispatchHandler(
			function (\MvcCore\IRequest $req, \MvcCore\IResponse $res) {
				if ($req->IsAjax()) return TRUE;
				/**
				 * To not run translations write in real background process,
				 * comment following line, the line closes connection and also
				 * it kills any tracy debug output:
				 */
				$res->SetHeader('Connection', 'close')->SetHeader('Content-Length', strlen($res->GetBody()));
				return TRUE;
			}
		);
		$staticClassName = get_called_class();
		\MvcCore\Application::GetInstance()->AddPostTerminateHandler(
			function (\MvcCore\IRequest $req, $res) use ($staticClassName) {
				if ($req->IsAjax()) return TRUE;
				// run in background processes:
				$app = \MvcCore\Application::GetInstance();
				$translationsPath = $app->GetRequest()->GetAppRoot()
					. self::$dataDir;
				$toolsClass = $app->GetToolClass();
				register_shutdown_function(
					function() use ($staticClassName, $translationsPath, $toolsClass) {
						$notTranslatedKeys = $staticClassName::$notTranslatedKeys;
						foreach ($notTranslatedKeys as $localization => $translationKeys) {
							$translationKeys = array_keys($translationKeys);
							$csvFullPath = $translationsPath . '/' . $localization . '.csv';
							$rawContent = '';
							if (file_exists($csvFullPath))
								$rawContent = trim(file_get_contents($csvFullPath), "\r\n");
							foreach ($translationKeys as $translationKey)
								$rawContent .= PHP_EOL . $translationKey . ';+' . $translationKey;
							$toolsClass::AtomicWrite(
								$csvFullPath,  $rawContent
							);
						}
						exit;
					}
				);
				return TRUE;
			}
		);
		self::$shutdownHandlerRegistered = TRUE;
	}
}
