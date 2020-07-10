<?php
error_reporting(E_ALL);
// ini_set('zend.assertions', -1);
require_once 'Task.php';

echo "\n---- Begin -----\n";
$task = new Task(1, 2);
echo "Сценарий 1: 'Испольнитель' откликнулся, 'Заказчик' подтвердил выполнение:";
try {
  echo "\nStatus after Pending  - '{$task->actionPending()}'";
  echo "\nStatus after Complete - '{$task->actionComplete()}'";
  echo "\nРезультат: 'Success'";
} catch (Exception $e) {
  echo "\nСценарий 1: Fail with message '{$e->getMessage()}'";
}

$task = new Task(1, 2);
echo "\n--\n";
echo "Сценарий 2: 'Исполнитель' откликнулся и не смог выполнить задание:";
try {
  echo "\nStatus after actionPending - '{$task->actionPending()}'";
  echo "\nStatus after actionRefuse  - '{$task->actionRefuse()}'";
  echo "\nРезультат: 'Success'";
} catch (Exception $e) {
  echo "\nРезультат: 'Fail' {$e->getMessage()}";
}

$task = new Task(1, 2);
echo "\n--\n";
echo "Сценарий 3: 'Заказчик' закрыл заявку до начала выполнения.";
try {
  echo "\nStatus after actionCancel - '{$task->actionCancel()}'";
  echo "\nРезультат: 'Success'";
} catch (Exception $e) {
  echo "\nResult: Fail with message '{$e->getMessage()}'";
}

$task = new Task(1, 2);
echo "\n--\n";
echo "Сценарий 4: 'Заказчик' отменил задачу после начала работы 'Исполнителем'";
try {
  echo "\nStatus after actionPending - '{$task->actionPending()}'";
  echo "\nStatus after actionCancel - '{$task->actionCancel()}'";
  echo "\nРезультат: 'Fail'";
} catch (Exception $e) {
  echo "\nРезультат: 'Success' {$e->getMessage()}";
}

$task = new Task(1, 2);
echo "\n--\n";
echo "Сценарий 5: 'Исполнитель' отказался от не принятого задания";
try {
  echo "\nStatus after actionRefuse - '{$task->actionRefuse()}'";
  echo "\nРезультат: 'Fail'";
} catch (Exception $e) {
  echo "\nРезультат: 'Success' {$e->getMessage()}";
}

$task = new Task(1);
echo "\n--\n";
echo "Сценарий 6: 'Заказчик' выполнил задание без 'Исполнителя'";
try {
  echo "\nStatus after actionPending - '{$task->actionPending()}'";
  echo "\nStatus after actionComplete - '{$task->actionComplete()}'";
  echo "\nРезультат: 'Fail'";
} catch (Exception $e) {
  echo "\nРезультат: 'Success' {$e->getMessage()}";
}

$task = new Task(1, 20);
echo "\n--\n";
echo "Сценарий 7: 'Заказчик' выбрал второго 'Исполнителя'.";
try {
  echo "\nStatus after actionPending - '{$task->actionPending()}'";
  echo "\nStatus after actionPending - '{$task->actionPending()}'";
  echo "\nРезультат: 'Fail'";
} catch (Exception $e) {
  echo "\nРезультат: 'Success' {$e->getMessage()}";
}

echo "\n----- End ------\n";


echo "\n--- Проверка на фальшивые 'Действия' и 'Состояния'\n";

$task = new Task(255, 500);
$status = 'Личный';
$action = 'Косвенное';

try {
  $task->changeStatus($status, $action);
} catch (NotAllowedActionException $e) {
  echo "{$e->getMessage()}\n";
} catch (NotAllowedChangeStatusException $e) {
  echo "{$e->getMessage()}\n";
}
$status = $task::STATUS_NEW;
$action = 'Косвенное';
try {
  $task->changeStatus($status, $action);
} catch (NotAllowedActionException $e) {
  echo "{$e->getMessage()}\n";
} catch (NotAllowedChangeStatusException $e) {
  echo "{$e->getMessage()}\n";
}


$status = 'New';
$action = 'InProgress';
try {
  $task->changeStatus($status, $action);
} catch (NotAllowedActionException $e) {
  echo "{$e->getMessage()}\n";
} catch (NotAllowedChangeStatusException $e) {
  echo "{$e->getMessage()}\n";
}
