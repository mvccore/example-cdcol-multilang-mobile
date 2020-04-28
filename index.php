<?php

	@include_once('vendor/autoload.php');

	$app = \App\Bootstrap::Init();

	$app
		//->SetCompiled(\MvcCore\Application::COMPILED_SFU)
		->Dispatch();
