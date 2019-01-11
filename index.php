<?php

	@include_once('vendor/autoload.php');
	
	\App\Bootstrap::Init();

	\MvcCore\Application::GetInstance()->Run();
