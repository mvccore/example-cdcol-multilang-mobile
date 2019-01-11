<?php

namespace App\Controllers;

class Base extends \MvcCore\Controller
{
	/**
	 * Authenticated user instance is automatically assigned
	 * by authentication extension before `Controller::Init();`.
	 * @var \MvcCore\Ext\Auths\Basics\IUser
	 */
	protected $user = NULL;

	/**
	 * @var \MvcCore\Ext\Routers\MediaAndLocalization 
	 */
	protected $router = NULL;

	/**
	 * Translator instance.
	 * @var \MvcCore\Ext\Models\Translators\ITranslator
	 */
	protected $translator = NULL;

	public function Init() {
		parent::Init();
		// when any CSRF token is outdated or not the same - sign out user by default
		\MvcCore\Ext\Form::AddCsrfErrorHandler(function (\MvcCore\Ext\Form & $form, $errorMsg) {
			\MvcCore\Ext\Auths\Basics\User::LogOut();
			self::Redirect($this->Url(
				'Index:Index',
				['absolute' => TRUE, 'sourceUrl'	=> rawurlencode($form->GetErrorUrl())]
			));
		});
		// create translator instance
		$this->translator = \MvcCore\Ext\Models\Translators\Csv::GetInstance(
			$this->router->GetLocalization(TRUE)
		);
	}

	public function PreDispatch () {
		parent::PreDispatch();
		if ($this->viewEnabled) {
			$this->_preDispatchSetUpAuth();
			$this->_preDispatchSetUpBundles();
			$this->view->MediaSiteVersion = $this->request->GetMediaSiteVersion();
			$this->view->Localization = $this->router->GetLocalization(TRUE);
			$this->view->BasePath = $this->GetRequest()->GetBasePath();
			$this->view->CurrentRouteCssClass = str_replace(
				':', '-', strtolower(
					$this->router->GetCurrentRoute()->GetName()
				)
			);
			$this->view->SetHelper('Translate', $this->translator);
		}
	}

	protected function translate ($key, $replacements = []) {
		return $this->translator->Translate($key, $replacements);
	}

	private function _preDispatchSetUpAuth () {
		// init user in view
		$this->view->User = $this->user;
		$authentication = \MvcCore\Ext\Auths\Basic::GetInstance()
			->SetTranslator(function ($key) {
				return $this->translator->Translate($key);
			});
		if ($this->user) 
			// set sign-out form into view, set signed-out url to homepage:
			$this->view->SignOutForm = $authentication->GetSignOutForm()
				->SetValues([
					'successUrl' => $this->Url('Index:Index', ['absolute' => TRUE])
				]);
	}

	private function _preDispatchSetUpBundles () {
		\MvcCore\Ext\Views\Helpers\Assets::SetGlobalOptions([
			'cssMinify'	=> 1,
			'cssJoin'	=> 1,
			'jsMinify'	=> 1,
			'jsJoin'	=> 1,
		]);
		$static = self::$staticPath;
		$mediaVersion = $this->request->GetMediaSiteVersion();
		$this->view->Css('fixedHead')
			->Append($static . '/css/components/resets.css')
			->Append($static . '/css/components/old-browsers-warning.css')
			->AppendRendered($static . '/css/components/fonts.css')
			->AppendRendered($static . '/css/components/forms-and-controls.css')
			->AppendRendered($static . '/css/components/content-buttons.css')
			->AppendRendered($static . '/css/components/content-tables.css')
			->AppendRendered($static . '/css/layout.css')
			->AppendRendered($static . '/css/layout.' . $mediaVersion . '.css')
			->AppendRendered($static . '/css/content.css')
			->AppendRendered($static . '/css/content.' . $mediaVersion . '.css');
		$this->view->Js('fixedHead')
			->Append($static . '/js/libs/class.min.js')
			->Append($static . '/js/libs/ajax.min.js')
			->Append($static . '/js/libs/Module.js');
		$this->view->Js('varFoot')
			->Append($static . '/js/Front.js');
	}
}
