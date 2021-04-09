<?php

namespace frontend\models;

use Codeception\Step\Retry;
use frontend\models\query\CategoryQuery;
use frontend\models\query\TaskQuery;
use frontend\models\query\TaskResponseQuery;
use frontend\models\query\UserQuery;
use Yii;

/**
 * This is the model class for table "users".
 *
 * @property TaskChat[] $taskChats
 * @property TaskResponse[] $responses Отклики на задания
 * @property Task[] $customerTasks
 * @property UserAttachment[] $userAttachments
 * @property UserCategory[] $userCategories
 * @property UserFavorite[] $userFavorites
 * @property UserNotification[] $userNotifications
 * @property UserReview[] $userReviews 
 * @property City $city
 * @property UserProfile $profile
 */
class User extends \common\models\User
{

    public $avgRating;
    public $countTasks;
    public $countResponses;

    public function getTaskAggregation()
    {
        return $this->getResponses()
            ->select(['performer_user_id', 'count' => 'count(*)', 'avgRating' => 'AVG(`rate`)'])
            ->groupBy('performer_user_id')
            ->asArray(true);
    }

    // public function getAvgRating()
    // {
    //     if ($this->isNewRecord) {
    //         return null; // this avoid calling a query searching for null primary keys
    //     }

    //     return empty($this->taskAggregation) ? 0 : $this->taskAggregation[0]['avgRating'];
    // }

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
        return $this->hasMany(Task::class, ['customer_user_id' => 'id']);
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
        return $this->hasMany(UserReview::class, ['performer_user_id' => 'id']);
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
    public function getProfile()
    {
        return $this->hasOne(UserProfile::class, ['id' => 'profile_id']);
    }

    /**
     * Является ли пользователь Исполнителем
     *
     * @return bool
     */
    public function getIsPerformer(): bool
    {
        return count($this->categories) > 0;
    }

    public function getIsFreeNow(): bool
    {
        return $this->tasks === null;
    }

    public function isOnline(): bool
    {
        $start = new \DateTime($this->last_logined_at);
        $end = new \DateTime('now');

        return $end->diff($start)->i > 30;
    }

    public function getGender()
    {
        return $this->profile ? $this->profile->gender : null;
    }

    public function getLastLogin()
    {
        return $this->last_logined_at;
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
