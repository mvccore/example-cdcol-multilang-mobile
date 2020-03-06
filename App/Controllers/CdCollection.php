<?php

namespace App\Controllers;

use \App\Models,
	\MvcCore\Ext\Form,
	\MvcCore\Ext\Forms\Fields;

class CdCollection extends Base {

	/** @var \App\Models\Album */
	protected $album;

	/**
	 * Initialize this controller, before pre-dispatching and before every action
	 * executing in this controller. This method is template method - so
	 * it's necessary to call parent method first.
	 * @return void
	 */
	public function Init () {
		parent::Init();
		// if user is not authorized, redirect to proper location and exit
		if (!$this->user) {
			// if post, get safe value from where the form has been submitted
			$sourceUrl = (
				$this->request->GetMethod() === \MvcCore\Request::METHOD_POST &&
				parse_url($this->request->GetReferer(), PHP_URL_HOST) === $this->request->GetHostName()
			)
				? $this->request->GetReferer()
				: $this->request->GetFullUrl();
			self::Redirect($this->Url(
				'\Index:Index', ['sourceUrl' => rawurlencode($sourceUrl)]
			));
		}
	}

	/**
	 * Pre execute every action in this controller. This method
	 * is template method - so it's necessary to call parent method first.
	 * @return void
	 */
	public function PreDispatch () {
		parent::PreDispatch();
		// if there is any 'id' param in `$_GET` or `$_POST`,
		// try to load album model instance from database
		$id = $this->GetParam("id", "0-9", NULL, 'int');
		$this->album = Models\Album::GetById(intval($id));
		if (!$this->album && $this->actionName == 'edit')
			$this->renderNotFound();
	}

	/**
	 * Load all album items, create empty form  to delete any item
	 * to generate and manage CSRF tokens (once only, not
	 * for every album row) and add supporting js file
	 * to initialize javascript in delete post forms
	 * created multiple times in view only.
	 * @return void
	 */
	public function IndexAction () {

		$this->view->title = $this->translate('CD Collection');
		$this->view->albums = Models\Album::GetAll();
		/** @var $abstractForm \MvcCore\Ext\Form */
		list($this->view->csrfName, $this->view->csrfValue)
			= $this->getVirtualDeleteForm()->SetUpCsrf();
		$this->view->Js('varFoot')
			->Prepend(self::$staticPath . '/js/List.js');
	}

	/**
	 * Create form for new album without hidden id input.
	 * @return void
	 */
	public function CreateAction () {
		$this->view->title = $this->translate('New album');
		$this->view->detailForm = $this->getCreateEditForm(FALSE);
	}

	/**
	 * Load previously saved album data,
	 * create edit form with hidden id input
	 * and set form defaults with album values.
	 * @return void
	 */
	public function EditAction () {
		$this->view->title = $this->translate(
			'Edit album - {0}', [$this->album->Title]
		);
		$this->view->detailForm = $this->getCreateEditForm(TRUE)
			->SetValues($this->album->GetValues(), TRUE, TRUE);
	}

	/**
	 * Handle create and edit action form submit.
	 * @return void
	 */
	public function SubmitAction () {
		$detailForm = $this->getCreateEditForm();
		if (!$this->album) {
			$this->album = new Models\Album();
			$detailForm->SetErrorUrl($this->Url(':Create', ['absolute' => TRUE]));
		} else {
			$detailForm->SetErrorUrl($this->Url(':Edit', ['id' => $this->album->Id, 'absolute' => TRUE]));
		}
		$detailForm->Submit();
		if ($detailForm->GetResult()) 
			$this->album->SetUp(
				$detailForm->GetValues(), \MvcCore\IModel::KEYS_CONVERSION_UNDERSCORES_TO_PASCALCASE
			)->Save();
		$detailForm->SubmittedRedirect();
	}

	/**
	 * Delete album by sent id param, if sent CSRF tokens
	 * are the same as CSRF tokens in session (tokens are managed
	 * by empty virtual delete form initialized once, not for all album rows).
	 * @return void
	 */
	public function DeleteAction () {
		if ($this->getVirtualDeleteForm()->SubmitCsrfTokens($_POST)) 
			$this->album->Delete();
		self::Redirect($this->Url(':Index'));
	}

	/**
	 * Create form instance to create new or edit existing album.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	protected function getCreateEditForm ($editForm = TRUE) {
		$form = (new Form($this))
			->SetId('detail')
			->SetMethod(Form::METHOD_POST)
			->SetAction($this->Url(':Submit'))
			->SetSuccessUrl($this->Url(':Index', ['absolute' => TRUE]))
			->AddCssClasses('theme')
			->SetTranslator(function ($key) {
				return $this->translator->Translate($key);
			})
			->SetDefaultFieldsRenderMode(
				Form::FIELD_RENDER_MODE_LABEL_AROUND
			);
		if ($editForm) {
			$id = (new Fields\Hidden)
				->SetName('id')
				->AddValidators('Number');
			$form->AddField($id);
		}
		$title = (new Fields\Text)
			->SetName('title')
			->SetLabel('Title:')
			->SetMaxLength(200)
			->SetRequired()
			->SetAutocomplete('off');
		$interpret = (new Fields\Text)
			->SetName('interpret')
			->SetLabel('Interpret:')
			->SetMaxLength(200)
			->SetRequired()
			->SetAutocomplete('off');
		$year = (new Fields\Number)
			->SetName('year')
			->SetLabel('Year:')
			->SetSize(4)
			->SetMin(intval(date('Y')) - 500)
			->SetMax(date('Y'))
			->SetValidators(['IntNumber']);
		$send = (new Fields\SubmitButton)
			->SetName('send')
			->SetCssClasses('btn btn-large')
			->SetValue('Save');
		return $form->AddFields($title, $interpret, $year, $send);
	}

	/**
	 * Create empty form, where to store and manage CSRF
	 * tokens for delete links in albums list.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	protected function getVirtualDeleteForm () {
		return (new Form($this))
			->SetId('delete')
			// set error url, where to redirect if CSRF
			// are wrong, see `\App\Controllers\Base::Init();`
			->SetErrorUrl(
				$this->Url('Index:Index', ['absolute' => TRUE])
			);
	}
}
