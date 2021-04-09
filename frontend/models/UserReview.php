<?php

namespace frontend\models;

use frontend\models\query\TaskQuery;
use frontend\models\query\UserQuery;

/**
 * {@inheritDoc}
 * @property Task $task
 * @property User $user
 */
class UserReview extends \common\models\UserReview
{
    /**
     * Gets query for [[Task]].
     *
     * @return TaskQuery
     */
    public function getTask(): TaskQuery
    {
        return $this->hasOne(Task::class, ['id' => 'task_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return UserQuery
     */
    public function getUser(): UserQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
