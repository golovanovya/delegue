<?php

namespace app\components;

class VersionLogBehavior extends \yii\base\Behavior
{
    /**
     * Аттрибуты для логирования
     * @var array
     */
    public $loggingAttributes = [];
    
    /**
     * {@inheritdoc}
     */
    public function events(): array
    {
        return [
            \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
        ];
    }
    
    /**
     * Сохранить в лог изменённые атрибуты
     * @param \yii\base\Event $event
     */
    public function beforeUpdate($event)
    {
        $data = $this->filterLogging($event->sender->getDirtyAttributes());
        if (empty($data)) {
            return;
        }
        $action = 'update';
        $class = get_class($event->sender);
    }
    
    /**
     * Получить только отслеживаемые
     * @param array $attributes
     */
    private function filterLogging($attributes)
    {
        return array_filter($attributes, function ($key) {
            return in_array($key, $this->loggingAttributes);
        }, ARRAY_FILTER_USE_KEY);
    }
}
