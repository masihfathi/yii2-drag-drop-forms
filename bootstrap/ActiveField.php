<?php

namespace masihfathi\form\bootstrap;

class ActiveField extends \yii\bootstrap\ActiveField
{
    public $template = "{label}\n{input}\n{hint}\n{description}\n{error}";
    /**
     * @var string description
     */
    public $description = '';
    
    
    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    public function render($content = null)
    {
        if ($content === null) {
            if (!isset($this->parts['{input}'])) {
                $this->textInput();
            }
            if (!isset($this->parts['{label}'])) {
                $this->label();
            }
            if (!isset($this->parts['{error}'])) {
                $this->error();
            }
            if (!isset($this->parts['{hint}'])) {
                $this->hint(null);
            }
            if (!isset($this->parts['{description}'])) {
              $this->description();
            }

            $content = strtr($this->template, $this->parts);
        } elseif (!is_string($content)) {
            $content = call_user_func($content, $this);
        }

        return $this->begin() . "\n" . $content . "\n" . $this->end();
    }

    public function description($options = [])
    {
        if ($options === false) {
            $this->parts['{description}'] = '';
            return $this;
        }

        $this->parts['{description}'] = $this->description;
        return $this;
    }
}

