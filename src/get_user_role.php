<?php
// получить список групп в которых состоит авторизованный пользователь
function getUserGroups ($pdo, $name) {
  $userGroups = [];

  $stmt = $pdo -> prepare("
    SELECT user.id, role.name 
    FROM user 
    LEFT JOIN user_roles ON user_id = user.id 
    LEFT JOIN role ON role.id = role_id 
    WHERE email = :login
    ");
  $stmt->execute(['login' => $name]);

  while ($group = $stmt -> fetch(PDO::FETCH_LAZY)) {
   array_push($userGroups, $group['name']);
  }

  return $userGroups;
}
