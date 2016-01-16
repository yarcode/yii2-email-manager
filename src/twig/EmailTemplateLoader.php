<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 * @author Valentin Konusov <rlng-krsk@yandex.ru>
 */

namespace yarcode\email\twig;

use yarcode\email\EmailManager;
use yarcode\email\models\Template;
use yii\base\Component;
use yii\helpers\ArrayHelper;

class EmailTemplateLoader extends Component implements \Twig_LoaderInterface
{
    /** @var string Attribute name to fetch template from */
    public $attributeName = 'text';

    public function getSource($name)
    {
        $currentLanguage = \Yii::$app->language;
        $defaultLanguage = ArrayHelper::getValue(EmailManager::getInstance()->languages, 0, 'en-US');

        /** @var Template $model */
        $model = Template::find()->where(['shortcut' => $name])
            ->andWhere('language = :currentLanguage OR language = :defaultLanguage OR language = :systemDefaultLanguage', [
                ':currentLanguage' => $currentLanguage,
                ':defaultLanguage' => $defaultLanguage,
                ':systemDefaultLanguage' => 'en-US',
            ])->one();

        if (!$model) {
            \Yii::error("Missing template {$name}, current language {$currentLanguage}, default language {$defaultLanguage}", 'email');
            return "!!! UNKNOWN TEMPLATE {$name} !!!";
        }

        return $model->getAttribute($this->attributeName);
    }

    public function getCacheKey($name)
    {
        return $name . $this->attributeName;
    }

    public function isFresh($name, $time)
    {
        return false;
    }
}