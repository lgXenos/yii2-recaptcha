# lgXenos/yii2-recaptcha
### Yii2 Component-widget with Google Recaptcha. Ajaxed. Multipled. Fixed

`composer require lg-xenos/yii2-recaptcha`

```php
'components' => [
    'reCaptcha' => [
        'name' => 'reCaptcha',
        'class' => 'lgxenos\yii2\recaptcha\ReCaptcha',
        // Get reCAPTCHA API keys: https://www.google.com/recaptcha/admin#createsite
        'siteKey' => 'your siteKey',
        'secret' => 'your secret key',
    ],
    ...
```

* Правила/_rules_ для ActiveRecordModel / ActiveFormAdd:

```php
public $reCaptcha;

public function rules()
{
  return [
      // ...
      [[], \lgxenos\yii2\recaptcha\ReCaptchaValidator::className(), 'uncheckedMessage' => 'Please confirm that you are not a bot.']
  ];
}
```

* {view}.php
```php
<?= $form->field($model, 'reCaptcha')->widget(\lgxenos\yii2\recaptcha\ReCaptcha::className()) ?>
```

Множественные рекапчи / _Multiple reCaptcha on a one page_
-----
У каждой свой ID / _Each of reCaptcha instances must have unique id_
```php
<?= $form1->field($modelForm1, 'reCaptcha')
    ->widget(\lgxenos\yii2\recaptcha\ReCaptcha::className(), [
        'widgetOptions' => [
            'id' => 'form-ONE',
        ]
    ]) ?>

<?= $form2->field($modelForm2, 'reCaptcha')
    ->widget(\lgxenos\yii2\recaptcha\ReCaptcha::className(), [
        'widgetOptions' => [
            'id' => 'form-TWO',
        ]
    ]) ?>
```

Почитать / _Resources_
---------
* [Google reCAPTCHA](https://developers.google.com/recaptcha)


    .   .
      .

### Немного истории / _Some history_ 

**(in English below)**

Предыстория такова, что himiklab сделал базу виджету для гугл-рекапчи. Но она не работала для аякса. Ему [был предложен PR](https://github.com/himiklab/yii2-recaptcha-widget/pull/23), но долго провалявшись с пометкой "conflicted" так и не был реализован.

Сейчас там есть другие issues, которые опять таки приводят предложения, как решить данную проблему. Но автор, к сожалению, ждет PR.
  
В [одной из моих issue](https://github.com/himiklab/yii2-recaptcha-widget/issues/57) мне ответили: "_Я тут кому-то что-то должен?_". Конечно потом это было стерто. Но я получил email с этим сообщением.

Не очень уважаемый himiklab, ни в коем случае вы никому ничего не должны. Как и мы вам. Но на будущее, если у вас нет желания или времени заниматься поддержкой - это стоит указывать заранее. В прочем если в ответ на баги вы будете подсказывать, где искать проблему - вам только будет полезнее. А то, что вы мне ответили - это не ответ. Это слепая агрессия на окружающих. Удачи

Данный код основан на труде himiklab и den67rus, после чего "по-русски доработан напильником". 

**in English - I'm using only Google Translate, because not good know it**
 
The background is that himiklab made the widget base for google-recaptcha. But she did not work for Ajax. To him [was offered PR] (https://github.com/himiklab/yii2-recaptcha-widget/pull/23), but for a long time lying around with the mark "conflicted" was never realized.

Now there are other issues, which again lead to suggestions how to solve this problem. But the author, unfortunately, is waiting for PR.

In [one of my issue] (https://github.com/himiklab/yii2-recaptcha-widget/issues/57) He write to me: "I owe to someone?". Of course then it was erased. But I received an email with this message.

Of course not. But we not owe too. But wrote: Sorry I'm to busy, search bug at <...>  - the best practice. Use it.  

Thnx himiklab & den67rus for base to this release