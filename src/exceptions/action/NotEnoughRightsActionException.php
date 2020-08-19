<?php

namespace app\exceptions\action;

use app\exceptions\base\ActionException;

/**
 * Если нет прав на выполенние Действия
 */
class NotEnoughRightsActionException extends ActionException
{
    /**
     * Схема "Исключения"
     *
     * @param string $action Действие
     * @param int $code Код исключения, по умолчанию 0
     */
    public function __construct(string $action, int $code = 0)
    {
        $message = "Не хватает прав на выполнения \"Действия\": '{$action}'";
        parent::__construct($message, $code);
    }
}
