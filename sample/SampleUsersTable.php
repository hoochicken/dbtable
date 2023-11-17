<?php

use Hoochicken\Dbtable\Database;

class SampleUsersTable extends Database
{
    const TABLE_NAME = 'users';
    const COLUMN_ID = 'id';
    const COLUMN_NAME = 'name';
    const COLUMN_STATE = 'state';
    const COLUMN_CREATED_AT = 'created_at';

    protected static array $definition = [
        self::COLUMN_ID => '`id` int(20) NOT NULL',
        self::COLUMN_NAME => '`name` varchar(255) DEFAULT NULL',
        self::COLUMN_STATE => '`state` int(1) DEFAULT 0',
        self::COLUMN_CREATED_AT => '`created_at` datetime DEFAULT NULL',
    ];

    protected static $columnAutoincrement = self::COLUMN_ID;

    public function __construct(string $host, string $database, string $user, string $password, int $port = self::PORT_DEFAULT)
    {
        parent::__construct($host, $database, $user, $password, $port);
        $this->setTable(self::TABLE_NAME);
    }

    public function tableExistsUsers(): bool
    {
        return $this->tableExists(self::TABLE_NAME);
    }

    public function createTableUsers()
    {
        $this->createTable(self::TABLE_NAME, static::$definition, self::$columnAutoincrement);
    }

    public function addUser(string $name, bool $status = false)
    {
        $data = [
            self::COLUMN_NAME => $name,
            self::COLUMN_STATE => $status,
            self::COLUMN_CREATED_AT => date('Y-m-d H:i:s'),
        ];
        $this->addEntry($data);
    }

    public function getData(array $selector = [], int $limit = 1000): array
    {
        return parent::getData($selector, $limit);
    }

    public function getEntryById(int $id): array
    {
        $this->setWhere(sprintf('id = %s', $id));
        $data = parent::getData([], 1);
        return $data[0] ?? [];
    }

    public function updateEntry(int $id, array $data)
    {
        $this->updateEntries($data, sprintf('id = %s', $id));
    }
}