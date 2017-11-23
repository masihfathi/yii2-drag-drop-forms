<?php

namespace masihfathi\form\events;

use yii\base\Event;

/**
 * Class FormEvent
 *
 * @package masihfathi\form\events
 */
class FormEvent extends Event{
    /**
     * @var integer $form_id
     */
    public $form_id;
    /**
     * @var integer $item_id
     */
    public $item_id;
    /**
     * array submitted data with key=>value
     * @var array $form_data
     */
    public $form_data;
    /**
     * @var string $form_name
     */
    public $form_name;

}