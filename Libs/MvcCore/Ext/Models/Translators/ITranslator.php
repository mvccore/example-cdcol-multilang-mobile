<?php

namespace MvcCore\Ext\Models\Translators;

interface ITranslator
{
    /**
	 * Get translator instance by localization key (for example: `en`, `en-US`).
	 * @param string $localization	International language code in lower case or 
	 *								(international language code in lower case 
	 *								plus dash and plus international locale code 
	 *								in upper case).
	 * @return \MvcCore\Model|\MvcCore\IModel|\MvcCore\Ext\Models\ITranslator
	 */
	public static function & GetInstance ($args);

	/**
	 * Set translator localization (for example as `en` or `en-US` ...).
	 * @param string $localization	International language code in lower case or 
	 *								(international language code in lower case 
	 *								plus dash and plus international locale code 
	 *								in upper case).
	 * @return \MvcCore\Ext\Models\ITranslator
	 */
	public function SetLocalization ($localization);

	/**
	 * Get translator localization - it could be international language code in 
	 * lower case or (international language code in lower case plus dash and 
	 * plus international locale code in upper case).
	 * @return string
	 */
	public function GetLocalization ();

	/**
	 * Translate given key into target localization. If there is no translation
	 * for given key in translations data, there is returned given key.
	 * If key 
	 * @param string $key			A key to translate.
	 * @param array $replacements	An array of replacements to process in translated result.
	 * @throws \Exception			En exception if translations store is not successful.
	 * @return string				Translated key or key itself it here is no key in translations store.
	 */
	public function Translate ($translationKey, $replacements = []);


}
