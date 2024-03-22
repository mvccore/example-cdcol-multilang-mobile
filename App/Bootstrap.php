<?php

namespace App;

class Bootstrap {

	/**
	 * @return \MvcCore\Application
	 */
	public static function Init () {

		$app = \MvcCore\Application::GetInstance();


		// Patch core to use extended debug class:
		if (class_exists('MvcCore\Ext\Debugs\Tracy')) {
			\MvcCore\Ext\Debugs\Tracy::$Editor = 'MSVS2019';
			$app->SetDebugClass('MvcCore\Ext\Debugs\Tracy');
		}


		//$app->SetConfigClass('MvcCore\Ext\Configs\Yaml');


		$cache = \MvcCore\Ext\Caches\Redis::GetInstance([ // `default` connection to:
			\MvcCore\Ext\ICache::CONNECTION_DATABASE => 'cdcol_multilang_mobile'
		]);
		\MvcCore\Ext\Cache::RegisterStore(
			\MvcCore\Ext\Caches\Redis::class, $cache, TRUE
		);
		//if (!$env->IsDevelopment())
			$cache->Connect();


		/**
		 * Uncomment this line before generate any assets into temporary directory, before application
		 * packing/building, only if you want to pack application without JS/CSS/fonts/images inside
		 * result PHP package and you want to have all those files placed on hard drive.
		 * You can use this variant in modes `PHP_PRESERVE_PACKAGE`, `PHP_PRESERVE_HDD` and `PHP_STRICT_HDD`.
		 */
		//\MvcCore\Ext\Views\Helpers\Assets::SetAssetUrlCompletion(FALSE);


		// Initialize authentication service extension and set custom user class
		\MvcCore\Ext\Auths\Basic::GetInstance()

			// Set unique password hash:
			->SetPasswordHashSalt('s9E56/QH6.a69sJML9aS6s')

			// To use credentials from system config file:
			//->SetUserClass('MvcCore\Ext\Auths\Basics\Users\SystemConfig')

			// To use credentials from database:
			->SetUserClass('MvcCore\Ext\Auths\Basics\Users\Database')

			// To describe basic credentials database structure
			/*->SetTableStructureForDbUsers('users', [
				'id'			=> 'id',
				'userName'		=> 'user_name',
				'passwordHash'	=> 'password_hash',
				'fullName'		=> 'full_name',
			])*/;

		// Display db password hash value by unique password hash for desired user name:
		//die(\MvcCore\Ext\Auths\Basics\User::EncodePasswordToHash('demo'));


		// Patch core to use extended router class:
		$app->SetRouterClass('MvcCore\Ext\Routers\MediaAndLocalization');


		// Set up router localizations and media site versions:
		/** @var \MvcCore\Ext\Routers\MediaAndLocalization $router */
		$router = \MvcCore\Router::GetInstance();
		$router
			->SetAllowedMediaVersionsAndUrlValues([
				\MvcCore\Ext\Routers\IMedia::MEDIA_VERSION_MOBILE	=> 'm',
				\MvcCore\Ext\Routers\IMedia::MEDIA_VERSION_FULL		=> ''
			])
			->SetDefaultLocalization('en-US')
			->SetAllowedLocalizations(['en-US', 'de-DE', 'ru-RU', 'es-ES', 'cs-CZ'])
			->SetLocalizationEquivalents([
				'es-ES' => ['ca-ES', 'gl-ES'],
				'ru-RU' => ['tt-RU', 'uk-UA', 'be-BY'],
				'cs-CZ'	=> ['sk-SK']
			])
			->SetAllowNonLocalizedRoutes(TRUE);


		// Set up application routes (without custom names),
		// defined basically as `Controller::Action` combinations:
		$router->SetRoutes([
			'Index:Index'			=> [
				'match'				=> '#^/(index\.php)?$#',
				'reverse'			=> '/',
			],
			'CdCollection:Index'	=> [
				'pattern'			=> [
					'en'			=> '/albums',
					'de'			=> '/alben',
					'es'			=> '/álbumes',
					'ru'			=> '/альбомы',
					'cs'			=> '/alba'
				]
			],
			'CdCollection:Create'	=> [
				'pattern'			=> [
					'en'			=> '/create',
					'de'			=> '/alben',
					'es'			=> '/álbumes',
					'ru'			=> '/создать',
					'cs'			=> '/vytvořit'
				]
			],
			'CdCollection:Submit'	=> [
				'pattern'			=> [
					'en'			=> '/save',
					'de'			=> '/speichern',
					'es'			=> '/guardar',
					'ru'			=> '/сохранить',
					'cs'			=> '/uložit',
				],
				'method'			=> 'POST'
			],
			'CdCollection:Edit'		=> [
				'pattern'			=> [
					'en'			=> '/edit/<id>',
					'de'			=> '/bearbeiten/<id>',
					'es'			=> '/editar/<id>',
					'ru'			=> '/редактировать/<id>',
					'cs'			=> '/upravit/<id>'
				],
				'defaults'			=> ['id' => 0,],
				'constraints'		=> ['id' => '\d+'],
			]
		]);

		return $app;
	}
}
