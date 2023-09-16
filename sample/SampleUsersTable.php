<?php

use Hoochicken\Dbtable\Database;

class SampleUsersTable extends Database
{
    const TABLE_NAME = 'users';
    const COLUMN_ID = 'id';
    const COLUMN_NAME = 'name';
    const COLUMN_STATUS = 'state';
    const COLUMN_CREATED_AT = 'created_at';

    protected static array $definition = [
        self::COLUMN_ID => '`id` int(20) NOT NULL',
        self::COLUMN_NAME => '`name` varchar(255) DEFAULT NULL',
        self::COLUMN_STATUS => '`state` int(1) DEFAULT 0',
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
}