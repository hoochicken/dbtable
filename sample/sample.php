<?php
$autoload = ['../../../autoload.php', '../src/Database.php', 'SampleUsersTable.php'];
foreach ($autoload as $filename) if (file_exists($filename)) require_once $filename;

$host = 'localhost';
$database = 'users';
$user = 'root';
$password = 'root';

$userTable = new SampleUsersTable($host, $database, $user, $password);

if (!$userTable->tableExistsUsers()) {
    $userTable->createTableUsers();
}

$userTable->addUser('Georgina Marshall', true);
$userTable->addUser('Mel Cobra', true);

print_r($userTable->getData());

$id = 2;
$userTable->removeEntryById($id);
print_r($userTable->getData());
