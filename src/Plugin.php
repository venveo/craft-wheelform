<?php
namespace Wheelform;

use Craft;
use craft\base\Plugin as BasePlugin;
use Wheelform\Models\Settings;

use yii\base\Event;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;

class Plugin extends BasePlugin
{

    public static $plugin;

    public $hasCpSettings = true;

    public $controllerNamespace = "Wheelform\\Controllers";

    public function init()
    {
        self::$plugin = $this;
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules[$this->id] = $this->id.'/default/index';
            }
        );
    }

    public function getMailer(): Mailer
    {
        return $this->get('Mailer');
    }

    public function getCpNavItem(): Array
    {
        $ret = [
            'label' => $this->getSettings()->cp_label ? $this->getSettings()->cp_label : $this->name,
            'url' => $this->id,
        ];

        if (($iconPath = $this->cpNavIconPath()) !== null) {
            $ret['icon'] = $iconPath;
        }

        return $ret;
    }

    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

    protected function settingsHtml(): string
    {
        // Get and pre-validate the settings
        $settings = $this->getSettings();
        $settings->validate();

        return Craft::$app->view->renderTemplate('wheelform/_settings', [
            'settings' => $settings,
        ]);
    }
}