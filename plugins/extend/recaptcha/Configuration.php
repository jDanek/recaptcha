<?php

namespace SunlightExtend\Recaptcha;

use Sunlight\Plugin\Action\ConfigAction;
use Sunlight\Util\Form;

class Configuration extends ConfigAction
{
    protected function getFields()
    {
        $fields = array(
            'site_key' => array(
                'label' => _lang('recaptcha.site_key'),
                'input' => $this->createInput('text', 'site_key'),
                'type' => 'text'
            ),
            'secret_key' => array(
                'label' => _lang('recaptcha.secret_key'),
                'input' => $this->createInput('text', 'secret_key'),
                'type' => 'text'
            ),
            'use_curl' => array(
                'label' => _lang('recaptcha.use_curl'),
                'input' => $this->createInput('checkbox', 'use_curl'),
                'type' => 'checkbox'
            ),
            'use_recaptcha_v3' => array(
                'label' => _lang('recaptcha.use_recaptcha_v3'),
                'input' => $this->createInput('checkbox', 'use_recaptcha_v3'),
                'type' => 'checkbox'
            ),
        );

        return $fields;
    }

    private function createInput($type, $name, $attributes = null)
    {
        $result = "";
        $attr = array();

        if (is_array($attributes)) {
            foreach ($attributes as $k => $v) {
                if (is_integer($k)) {
                    $attr[] = $v . '=' . $v;
                } else {
                    $attr[] = $k . '=' . $v;
                }
            }
        }

        if ($type === 'checkbox') {
            $result = '<input type="checkbox" name="config[' . $name . ']" value="1"' . implode(' ', $attr) . Form::activateCheckbox($this->plugin->getConfig()->offsetGet($name)) . '>';
        } else {
            $result = '<input type="' . $type . '" name="config[' . $name . ']" value="' . $this->plugin->getConfig()->offsetGet($name) . '"' . implode(' ', $attr) . '>';
        }

        return $result;
    }
}