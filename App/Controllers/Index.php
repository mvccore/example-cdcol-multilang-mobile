<?php

namespace App\Controllers;

class Index extends Base {

	/**
	 * Render homepage with signin form.
	 * If user is already authenticated, redirect user to albums list.
	 * @return void
	 */
	public function IndexAction () {
		if ($this->user !== NULL) 
			self::Redirect($this->Url('CdCollection:Index'));
		$this->view->title = $this->translate('CD Collection');
		$this->view->user = $this->user;
		$this->view->signInForm = \MvcCore\Ext\Auths\Basic::GetInstance()
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
		if ($code === 200) $code = 404;
		$this->view->title = "Error {$code}";
		$this->view->message = $this->request->GetParam('message', FALSE);
		$this->Render('error');
	}
}
