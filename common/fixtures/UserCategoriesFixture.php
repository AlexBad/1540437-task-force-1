<?php

namespace common\fixtures;

use yii\test\ActiveFixture;

class UserCategoriesFixture extends ActiveFixture
{
    public $modelClass = \common\models\UserCategory::class;
    public $depends = [
        UsersFixture::class,
    ];
}
