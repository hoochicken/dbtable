<?php

namespace Hoochicken\Dbtable;

use PDO;

abstract class Database
{
    const PORT_DEFAULT = 3306;
    const TABLE_NAME = 'table_name';
    const DB_DRIVER = 'mysql';
    const DSN_KEY_HOST = 'host';
    const DSN_KEY_DATABASE = 'dbname';
    const DSN_KEY_PORT = 'port';
    const DSN_KEY_PASSWORD = 'password';
    const DSN_KEY_USER = 'user';
    const COLUMN_ID = 'id';

    private static string $host = '';
    private static int $port = self::PORT_DEFAULT;
    private static string $user = '';
    private static string $password = '';
    private static string $dsn = '';
    private static string $database = '';
    private static string $table = self::TABLE_NAME;
    private static ?PDO $db;


    public function __construct(string $host, string $database, string $user, string $password, int $port = self::PORT_DEFAULT)
    {
        $this->setHost($host);
        $this->setDatabase($database);
        $this->setUser($user);
        $this->setPassword($password);
        $this->setPort($port);
        $this->setDsn($this->generateDsn());
        $this->setDb($this->initDb($this->getDsn(), $this->getUser(), $this->getPassword()));
    }

    private function initDb(string $dsn, string $user, string $password): PDO
    {
        return new PDO($dsn, $user, $password);
    }

    private function generateDsn(): string
    {
        $dsn = [];
        if (!empty($this->getHost())) $dsn[self::DSN_KEY_HOST] = $this->getHost();
        if (!empty($this->getDatabase())) $dsn[self::DSN_KEY_DATABASE] = $this->getDatabase();
        // if (!empty($this->getPort())) $dsn[self::DSN_KEY_PORT] = $this->getPort();
        // if (!empty($this->getUser())) $dsn[self::DSN_KEY_USER] = $this->getUser();
        // if (!empty($this->getPassword())) $dsn[self::DSN_KEY_PASSWORD] = $this->getPassword();
        $dsn = $this->getFlatStringFromArray($dsn);
        return sprintf('%s%s%s', self::DB_DRIVER, ':', $dsn);
    }

    private function getFlatStringFromArray(array $array, string $separator = '=', string $glue = ';'): string
    {
        array_walk($array, function(&$item, $key) use ($separator){
            $item = $key . $separator . $item;
        });
        return implode($glue, $array);
    }

    public function createTable(string $tablename, array $definition)
    {
        $sql = sprintf('CREATE TABLE `%s` (%s)', $tablename, implode(',', $definition));
        $statement = $this->getDb()->prepare($sql);
        $statement->execute();
        $sql = sprintf('ALTER TABLE `%s` ADD PRIMARY KEY (`%s`);', $tablename, self::COLUMN_ID);
        $statement = $this->getDb()->prepare($sql);
        $statement->execute();
        $sql = sprintf('ALTER TABLE `%s` MODIFY `%s` int(20) NOT NULL AUTO_INCREMENT; COMMIT;', $tablename, self::COLUMN_ID);
        $statement = $this->getDb()->prepare($sql);
        $statement->execute();
    }

    public function queryDb()
    {
        $sql = 'SELECT name, color, calories FROM fruit ORDER BY name';
        foreach ($this->db->query($sql) as $row) {
            print $row['name'] . "\t";
            print $row['color'] . "\t";
            print $row['calories'] . "\n";
        }
    }

    public function tableExists(string $tablename): bool
    {
        $sql = sprintf('SELECT table_name FROM information_schema.tables WHERE table_schema = "%s" AND table_name = "%s"',
            $this->getDatabase(),
            $tablename);
        $statement = $this->getDb()->prepare($sql);
        $statement->execute();
        $result = $statement->fetch();
        $statement->fetchObject();
        return is_array($result) && 0 < count($result);
    }

    public function setHost(string $value)
    {
        self::$host = $value;
    }

    public function getHost(): string
    {
        return self::$host;
    }

    public function setPort(int $value)
    {
        self::$port = $value;
    }

    public function getPort(): int
    {
        return self::$port;
    }

    public function setDatabase(string $value)
    {
        self::$database = $value;
    }

    public function getDatabase(): string
    {
        return self::$database;
    }

    public function setUser(string $value)
    {
        self::$user = $value;
    }

    public function getUser(): string
    {
        return self::$user;
    }

    public function setPassword(string $value)
    {
        self::$password = $value;
    }

    public function getPassword(): string
    {
        return self::$password;
    }

    public function setDsn(string $value)
    {
        self::$dsn = $value;
    }

    public function getDsn(): string
    {
        return self::$dsn;
    }

    public function setDb(PDO $value)
    {
        self::$db = $value;
    }

    public function getDb(): PDO
    {
        return self::$db;
    }

    public function setTable(string $value)
    {
        self::$table = $value;
    }

    public function getTable(): string
    {
        return self::$table;
    }
}