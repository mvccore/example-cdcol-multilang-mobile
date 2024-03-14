<?php

	define('MVCCORE_APP_ROOT', str_replace('\\', '/', __DIR__));

	@include_once('vendor/autoload.php');

	$app = \App\Bootstrap::Init();

	$app
		//->SetCompiled(\MvcCore\Application::COMPILED_SFU)
		->Dispatch();
