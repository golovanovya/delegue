<?php

namespace app\components\version;

/**
 * Поведение для сохранения версий
 */
class VersionLogBehavior extends \yii\base\Behavior
{
    /**
     * Аттрибуты для логирования
     * @var array
     */
    public $loggingAttributes = [];
    
    /**
     * @var integer
     */
    private $versionNumber;
    
    /**
     * @var Version
     */
    private $version;
    
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
        /* @var $sender \yii\db\ActiveRecord */
        $sender = $event->sender;
        $changed = $this->filterLogging(array_keys($sender->getDirtyAttributes()));
        $data = $this->filterChanged($sender->getOldAttributes(), $changed);
        if (empty($data)) {
            return;
        }
        $action = 'update';
        $objectClass = get_class($sender);
        $objectId = $sender->id;
        $this->addVersion($action, $objectId, $objectClass, $data);
    }
    
    /**
     * Загрузить предыдущую версию
     */
    public function getVersionPrevious($versionNumber = null)
    {
        $objectClass = get_class($this->owner);
        
        $this->versionNumber = $versionNumber ? $versionNumber : $this->getVersionPreviousNumber();
        /* @var $prev Version */
        $prev = Version::find()
            ->where(['object_class' => $objectClass])
            ->andWhere(['version' => $this->versionNumber])
            ->andWhere(['object_id' => $this->owner->id])
            ->limit(1)
            ->one();
        if (!$prev) {
            return false;
        }
        $this->version = $prev;
        /* @var $model \yii\db\ActiveRecord */
        $model = $this->owner;
        $data = json_decode($prev->data);
        foreach ($data as $attribute => $value) {
            $model->setAttribute($attribute, $value);
        }
        return true;
    }
    
    /**
     * Отредактировал время
     * @return string
     */
    public function getVersionUpdatedAt()
    {
        return $this->version ? $this->version->created_at : null;
    }
    
    /**
     * Отредактирвал username
     * @return string
     */
    public function getVersionUpdatedBy()
    {
        return $this->version ? $this->version->created_by : null;
    }
    
    /**
     * Получить номер предыдущей версии
     * @return integer|null
     */
    public function getVersionPreviousNumber()
    {
        $objectClass = get_class($this->owner);
        $query = Version::find()
            ->where(['object_class' => $objectClass])
            ->andWhere(['object_id' => $this->owner->id]);
        if ($this->versionNumber !== null) {
            $query->andWhere(['<', 'version', $this->versionNumber]);
        }
        return $query->max('version');
    }
    
    /**
     * Добавить версию
     * @param string $action
     * @param integer $objectId
     * @param string $objectClass
     * @param mixed $data
     * @throws \Exception
     */
    private function addVersion($action, $objectId, $objectClass, $data)
    {
        $transaction = Version::getDb()->beginTransaction();
        try {
            $count = Version::find()
                ->where(['object_class' => $objectClass])
                ->andWhere(['object_id' => $objectId])
                ->count();
            $identity = \Yii::$app->user->identity;
            $version = new Version();
            $version->action = $action;
            $version->object_id = $objectId;
            $version->object_class = $objectClass;
            $version->data = json_encode($data);
            $version->created_at = date('Y-m-d H:i:s');
            $version->created_by = $identity ? $identity->username : null;
            $version->version = $count + 1;
            $version->save();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    /**
     * Получить изменённые отслеживаемые атрибуты
     * @param array $attributes
     */
    private function filterLogging($attributes)
    {
        return array_filter($attributes, function ($value) {
            return in_array($value, $this->loggingAttributes);
        });
    }
    
    /**
     * Отфильтровать только изменённые отслеживаемые значения
     * @param array $oldAttributes
     * @param array $changed
     * @return array
     */
    private function filterChanged($oldAttributes, $changed)
    {
        return array_filter($oldAttributes, function ($key) use ($changed) {
            return in_array($key, $changed);
        }, ARRAY_FILTER_USE_KEY);
    }
}
