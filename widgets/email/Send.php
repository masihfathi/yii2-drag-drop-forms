<?php
namespace masihfathi\form\widgets\email;

use Yii;
use yii\base\Widget;
use yii\validators\EmailValidator;
class Send extends Widget{

    public $from = null;
    public $to = null;
    public $subject = null;
    public $textBody = null;
    public function init() {
        parent::init();
        if ($this->to === null) {
            Yii::$app->session->setFlash('error', Yii::t('builder','Please set recipient\'s email'));
        }
    }

    public function run() {
        $validator = new EmailValidator();

        if ( $this->to && $validator->validate($this->to) ){

            $message = Yii::$app->mailer->compose()
                ->setFrom($this->from)
                ->setTo($this->to)
                ->setSubject($this->subject)
                ->setTextBody($this->textBody);

            $message->send();
        }

    }
}
