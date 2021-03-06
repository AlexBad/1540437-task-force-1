<?php

namespace frontend\models;

use frontend\models\query\CategoryQuery;
use frontend\models\query\TaskQuery;
use frontend\models\query\TaskResponseQuery;
use frontend\models\query\UserQuery;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "users".
 *
 * @property TaskChat[] $taskChats
 * @property TaskResponse[] $responses Отклики на задания
 * @property Task[] $customerTasks Задачи заказчика
 * @property Task[] $performerTasks Задания исполнителя
 * @property UserAttachment[] $userAttachments
 * @property UserCategory[] $userCategory
 * @property Category[] $categories
 * @property UserFavorite[] $userFavorites
 * @property UserNotification[] $userNotifications
 * @property UserReview[] $userReviews 
 * @property City $city
 * @property UserProfile $profile
 * @property-read bool $isCustomer Если пользвоатель заказчик
 * @property-read bool $isPerformer Если пользователь исполнитель
 * @property-read bool $isOnline Если последняя активыность была менее 30 минут
 */
class User extends \common\models\User
{

    public $_avgRating;
    public $countTasks;
    public $countResponses;

    public function getRating(): ?int
    {
        if ($this->isNewRecord) {
            return null; // нет смысла выполнять запрос на поиск по пустым ключам
        }

        return $this->reviewsAggregation[0]['avgRating'];
    }
    
    public function getReviewsAggregation()
    {
        return $this->getUserReviews()
            ->select(['user_id','avgRating' => 'avg(`rate`)'])
            ->groupBy('user_id')
            ->asArray(true);
    }

    public function getAvgRating()
    {
        if ($this->_avgRating === null) {
            $this->_avgRating = $this->getUserReviews()
                ->select(['avgRating' => 'avg(`rate`)'])
                ->groupBy('user_id')
                ->scalar();
        } elseif ($this->_avgRating === 'empty') {
            return null;
        }        

        return $this->_avgRating;
    }

    public function setAvgRating(?string $value)
    {
        if ($value === null) {
            $value = 'empty';
        }

        $this->_avgRating = $value;

    }

    /**
     * Gets query for [[TaskChats]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskChats()
    {
        return $this->hasMany(TaskChat::class, ['user_id' => 'id']);
    }

    /** @return TaskResponseQuery */
    public function getResponses(): TaskResponseQuery
    {
        return $this->hasMany(TaskResponse::class, ['performer_user_id' => 'id']);
    }

    /** @return TaskQuery */
    public function getCustomerTasks(): TaskQuery
    {
        return $this->hasMany(Task::class, ['user_id' => 'id']);
    }

    /** @return TaskQuery */
    public function getPerformerTasks(): TaskQuery
    {
        return $this->hasMany(Task::class, ['performer_user_id' => 'id']);
    }

    /**
     * Gets query for [[UserAttachments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserAttachments()
    {
        return $this->hasMany(UserAttachment::class, ['user_id' => 'id']);
    }

    /**
     * Query class for table [[categories]]
     *
     * @return CategoryQuery
     */
    public function getCategories(): CategoryQuery
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])
            ->viaTable('user_categories', ['user_id' => 'id']);
    }

    /** @return ActiveQuery */
    public function getUserCategories(): ActiveQuery
    {
        return $this->hasMany(UserCategory::class,  ['user_id' => 'id']);
    }

    /**
     * Gets query for [[UserFavorites]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserFavorites()
    {
        return $this->hasMany(UserFavorite::class, ['favorite_user_id' => 'id']);
    }

    /**
     * Gets query for [[UserNotifications]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserNotifications()
    {
        return $this->hasMany(UserNotification::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[UserReviews]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserReviews()
    {
        return $this->hasMany(UserReview::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[City]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    /**
     * Gets query for [[Profile]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfile(): \yii\db\ActiveQuery
    {
        return $this->hasOne(UserProfile::class, ['id' => 'profile_id']);
    }

    /**
     * Является ли пользователь Заказчиком
     * 
     * @return bool
     */
    public function getIsCustomer(): bool
    {
        if ($this->isNewRecord) {
            return false;
        }

        return $this->role === \app\bizzlogic\User::ROLE_CUSTOMER;
    }

    /**
     * Является ли пользователь Исполнителем
     *
     * @return bool
     */
    public function getIsPerformer(): bool
    {
        if ($this->isNewRecord) {
            return false;
        }

        return $this->role === \app\bizzlogic\User::ROLE_PERFORMER;
    }

    /** @return bool */
    public function getIsOnline(): bool
    {
        if ($this->isNewRecord) {
            return false;
        }

        $start = new \DateTime($this->last_logined_at);
        $end = new \DateTime('now');

        return $end->diff($start)->i > 30;
    }

    /** 
     * @return string|null Дата последного входа в систему
     */
    public function getLastLogin(): ?string
    {
        return $this->last_logined_at;
    }

    public function setPassword(string $value)
    {
        $this->password = Yii::$app->getSecurity()->generatePasswordHash($value);
    }


    /**
     * User query class
     *
     * @return UserQuery
     */
    public static function find(): UserQuery
    {
        return new UserQuery(get_called_class());
    }
}
