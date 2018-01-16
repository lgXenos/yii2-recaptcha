<?php
/**
 * Based on
 *
 * @link      https://github.com/himiklab/yii2-recaptcha-widget/issues/57
 */

namespace lgxenos\yii2\recaptcha;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\widgets\InputWidget;

/**
 * Yii2 Google reCAPTCHA widget.
 *
 * For example:
 *
 * ```php
 * <?= $form->field($model, 'reCaptcha')->widget(
 *  ReCaptcha::className(),
 *  ['siteKey' => 'your siteKey']
 * ) ?>
 * ```
 *
 * or
 *
 * ```php
 * <?= ReCaptcha::widget([
 *  'name' => 'reCaptcha',
 *  'siteKey' => 'your siteKey',
 *  'widgetOptions' => ['class' => 'col-sm-offset-3']
 * ]) ?>
 * ```
 *
 * @see     https://developers.google.com/recaptcha
 */
class ReCaptcha extends InputWidget {
	const JS_API_URL = '//www.google.com/recaptcha/api.js';

	const THEME_LIGHT = 'light';
	const THEME_DARK  = 'dark';

	const TYPE_IMAGE = 'image';
	const TYPE_AUDIO = 'audio';

	const SIZE_NORMAL  = 'normal';
	const SIZE_COMPACT = 'compact';

	/** @var string Your sitekey. */
	public $siteKey;

	/** @var string Your secret. */
	public $secret;

	/** @var string The color theme of the widget. [[THEME_LIGHT]] (default) or [[THEME_DARK]] */
	public $theme;

	/** @var string The type of CAPTCHA to serve. [[TYPE_IMAGE]] (default) or [[TYPE_AUDIO]] */
	public $type;

	/** @var string The size of the widget. [[SIZE_NORMAL]] (default) or [[SIZE_COMPACT]] */
	public $size;

	/** @var int The tabindex of the widget */
	public $tabindex;

	/** @var string Your JS callback function that's executed when the user submits a successful CAPTCHA response. */
	public $jsCallback;

	/**
	 * @var string Your JS callback function that's executed when the recaptcha response expires and the user
	 * needs to solve a new CAPTCHA.
	 */
	public $jsExpiredCallback;

	/** @var array Additional html widget options, such as `class`. */
	public $widgetOptions = [];

	/** @var string Stores the id field input, to generate the id widget. */
	private $inputId;

	public function init() {
		parent::init();

		if (empty($this->siteKey)) {
			if (!empty(Yii::$app->reCaptcha->siteKey)) {
				$this->siteKey = Yii::$app->reCaptcha->siteKey;
			}
			else {
				throw new InvalidConfigException('Required `siteKey` param isn\'t set.');
			}
		}

		$view = $this->view;
		$arguments = http_build_query([
			'hl' => $this->getLanguageSuffix(),
			'render' => 'explicit'
		]);
		$view->registerJsFile(
			self::JS_API_URL . '?' . $arguments,
			['position' => $view::POS_END, 'async' => true, 'defer' => true]
		);

	}

	public function run() {
		$this->customFieldPrepare();

		$divOptions = [
			'id' => $this->inputId . '-recaptcha'
		];

		if (array_key_exists('class', $this->widgetOptions)) {
			$divOptions['class'] = "{$this->widgetOptions['class']}";
		}
		$divOptions = $divOptions + $this->widgetOptions;

		echo Html::tag('div', '', $divOptions);
	}

	protected function getLanguageSuffix() {
		$currentAppLanguage = Yii::$app->language;
		$langsExceptions    = ['zh-CN', 'zh-TW', 'zh-TW'];

		if (strpos($currentAppLanguage, '-') === false) {
			return $currentAppLanguage;
		}

		if (in_array($currentAppLanguage, $langsExceptions)) {
			return $currentAppLanguage;
		}
		else {
			return substr($currentAppLanguage, 0, strpos($currentAppLanguage, '-'));
		}
	}

	protected function customFieldPrepare() {
		$view = $this->view;
		if ($this->hasModel()) {
			$inputName = Html::getInputName($this->model, $this->attribute);
			$inputId   = Html::getInputId($this->model, $this->attribute);
		}
		else {
			$inputName = $this->name;
			$inputId   = 'recaptcha-' . $this->name;
		}
		$this->inputId = $inputId;

		if (empty($this->jsCallback)) {
			$jsCode = "var recaptchaCallback = function(response){jQuery('#{$inputId}').val(response);};";
		}
		else {
			$jsCode = "var recaptchaCallback = function(response){jQuery('#{$inputId}').val(response); {$this->jsCallback}(response);};";
		}

		$funName = 'reCaptchaWorker' . $this->model->formName();

		$jsParam = "'sitekey': '{$this->siteKey}'";
		$jsParam .= empty($this->jsCallback) ? ", 'callback': recaptchaCallback" : ", 'callback': {$this->jsCallback}";
		$jsParam .= empty($this->jsExpiredCallback) ? "" : ", 'data-expired-callback': {$this->jsExpiredCallback}";
		$jsParam .= empty($this->type) ? '' : ", 'type': '{$this->type}'";
		$jsParam .= empty($this->size) ? '' : ", 'size': '{$this->size}'";
		$jsParam .= empty($this->theme) ? '' : ", 'theme': '{$this->theme}'";
		$jsParam .= empty($this->tabindex) ? '' : ", 'tabindex': '{$this->tabindex}'";

		//$jsCode .= "function {$funName}(){try {var recaptchaClientId = grecaptcha.render('{$inputId}-widget', {{$jsParam}});} catch (e) {setTimeout({$funName}, 500);}}{$funName}();";
		$jsCode .= "
        {$funName} = function (){
            var recaptchaClientId = grecaptcha.render('{$inputId}-recaptcha', {{$jsParam}});
            $('#{$inputId}-recaptcha').data('recaptcha-client-id', recaptchaClientId);
        }
        setTimeout({$funName}, 500);
        ";

		$this->jsCallback = 'recaptchaCallback';

		if (empty($this->jsExpiredCallback)) {
			$jsExpCode = "var recaptchaExpiredCallback = function(){jQuery('#{$inputId}').val('');};";
		}
		else {
			$jsExpCode = "var recaptchaExpiredCallback = function(){jQuery('#{$inputId}').val(''); " .
				"{$this->jsExpiredCallback}();};";
		}
		$this->jsExpiredCallback = 'recaptchaExpiredCallback';

		$view->registerJs($jsCode, $view::POS_READY);
		$view->registerJs($jsExpCode, $view::POS_READY);

		echo Html::input('hidden', $inputName, null, ['id' => $inputId]);
	}
}
