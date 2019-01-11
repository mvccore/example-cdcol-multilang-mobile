<?php

namespace App\Controllers;

class Index extends Base
{
	/**
	 * Render homepage with signin form.
	 * If user is already authenticated, redirect user to albums list.
	 * @return void
	 */
	public function IndexAction () {
		if ($this->user !== NULL) 
			self::Redirect($this->Url('CdCollection:Index'));
		$this->view->Title = $this->translate('CD Collection');
		$this->view->User = $this->user;
		$this->view->SignInForm = \MvcCore\Ext\Auths\Basic::GetInstance()
			->GetSignInForm()
			->AddCssClasses('theme')
			->SetValues([// set signed in url to albums list by default:
				'successUrl' => $this->Url('CdCollection:', ['absolute' => TRUE]),
			]);
	}

	/**
	 * Render not found action.
	 * @return void
	 */
	public function NotFoundAction(){
		$this->ErrorAction();
	}

	/**
	 * Render possible server error action.
	 * @return void
	 */
	public function ErrorAction () {
		$code = $this->response->GetCode();
		$message = $this->request->GetParam('message', '\\a-zA-Z0-9_;, /\-\@\:');
		$message = preg_replace('#`([^`]*)`#', '<code>$1</code>', $message);
		$message = str_replace("\n", '<br />', $message);
		$this->view->Title = $this->translate("Error {0}", [$code]);
		$this->view->Message = $message;
		$this->Render('error');
	}
}
