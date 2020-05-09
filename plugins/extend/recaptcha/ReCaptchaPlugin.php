<?php

namespace SunlightExtend\Recaptcha;

use ReCaptcha\ReCaptcha;
use ReCaptcha\RequestMethod\CurlPost;
use ReCaptcha\RequestMethod\SocketPost;
use Sunlight\Extend;
use Sunlight\Plugin\ExtendPlugin;
use Sunlight\Util\Url;

/**
 * ReCaptcha plugin
 *
 * @author Jirka Daněk <jdanek.eu>
 */
class ReCaptchaPlugin extends ExtendPlugin
{

    public function initialize()
    {
        parent::initialize();
        if ($this->getConfig()->offsetExists('site_key')
            && $this->getConfig()->offsetExists('secret_key')) {
            // reCaptcha v2 + v3
            Extend::regm([
                'tpl.head' => [$this, 'onHead'],
                'captcha.init' => [$this, 'onCaptchaInit'],
                'captcha.check' => [$this, 'onCaptchaCheck'],
            ]);

            // reCaptcha v3
            if ($this->getConfig()->offsetGet('use_recaptcha_v3')) {
                Extend::regm([
                    'form.output' => [$this, 'onFormAppend']
                ]);
            }
        }
    }

    protected function getConfigDefaults()
    {
        return [
            'site_key' => null,
            'secret_key' => null,
            'use_curl' => false,
            'use_recaptcha_v3' => false,
        ];
    }

    /**
     * @param array $args
     */
    public function onHead(array $args)
    {

        if (!_logged_in) {
            if (!$this->getConfig()->offsetGet('use_recaptcha_v3')) {
                // reCaptcha v2
                $args['js_before'] .= "\n<script type='text/javascript' src='https://www.google.com/recaptcha/api.js?hl=" . _language . "'></script>";
            } else {
                // reCaptcha v3
                $args['js_before'] .= "\n<script src='https://www.google.com/recaptcha/api.js?render=" . $this->getConfig()->offsetGet('site_key') . "'></script>";
                // skryti badge povoleno v urcitem pripade vice: DEC2018 https://developers.google.com/recaptcha/docs/faq
                // https://developers.google.com/recaptcha/docs/faq#id-like-to-hide-the-recaptcha-badge.-what-is-allowed
                $args['css_after'] .= "<style>.grecaptcha-badge{display: none;}</style>";
            }
        }
    }

    /**
     * @param array $args
     */
    public function onCaptchaInit(array $args)
    {
        if (!_logged_in) {

            $content = (!$this->getConfig()->offsetGet('use_recaptcha_v3')
                // reCaptcha v2
                ? "<div class='g-recaptcha' data-sitekey='" . $this->getConfig()->offsetGet('site_key') . "'></div>"
                // reCaptcha v3
                : "<span class='hint'>This site is protected by reCAPTCHA and the Google <a href='https://policies.google.com/privacy' target='_blank'>Privacy Policy</a> and <a href='https://policies.google.com/terms' target='_blank'>Terms of Service</a> apply.</span>"
            );

            $args['value'] = [
                'label' => _lang('captcha.input'),
                'content' => $content,
                'top' => true,
                'class' => ''
            ];
        }
    }

    /**
     * @param $args
     */
    public function onCaptchaCheck(array $args)
    {
        if (!_logged_in) {
            if (isset($_POST['g-recaptcha-response'])) {
                $requestMethod = null;
                if (!ini_get('allow_url_fopen')) {
                    if ($this->getConfig()->offsetGet('use_curl')) {
                        $requestMethod = new CurlPost();
                    } else {
                        $requestMethod = new SocketPost();
                    }
                }
                $recaptcha = new ReCaptcha($this->getConfig()->offsetGet('secret_key'), $requestMethod);
                $recaptcha->setExpectedHostname(Url::current()->getFullHost());
                // reCaptcha v3
                if ($this->getConfig()->offsetGet('use_recaptcha_v3')) {
                    $recaptcha->setScoreThreshold(0.5);
                }

                $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
                $args['value'] = $resp->isSuccess();
            } else {
                $args['value'] = false;
            }
        }
    }

    /**
     * Add the necessary javascript to the form
     * Used in reCaptcha v3
     *
     * @param $args
     */
    public function onFormAppend(array $args)
    {
        if (!_logged_in && $this->getConfig()->offsetGet('use_recaptcha_v3')) {
            $args['options']['form_prepend'] = trim(preg_replace('/\s+/', ' ', "<script>
            grecaptcha.ready(function() {
                grecaptcha.execute('" . $this->getConfig()->offsetGet('site_key') . "', {action: '" . $args['options']['name'] . "'})
                    .then(function(token) {
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'g-recaptcha-response',
                            value: token
                        }).prependTo('." . $args['options']['class'] . "')
                });
            });</script>"));
        }
    }

    public function getAction($name)
    {
        if ($name == 'config') {
            return new Configuration($this);
        }
        return parent::getAction($name);
    }

}
