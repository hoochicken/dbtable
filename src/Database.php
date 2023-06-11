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

    protected static string $host = '';
    protected static int $port = self::PORT_DEFAULT;
    protected static string $user = '';
    protected static string $password = '';
    protected static string $dsn = '';
    protected static string $database = '';
    protected static string $table = self::TABLE_NAME;
    protected static ?PDO $db;
    protected static array $definition = [self::COLUMN_ID => '`id` int(20) NOT NULL'];


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
        if (!empty($this->getHost())) $dsn[static::DSN_KEY_HOST] = $this->getHost();
        if (!empty($this->getDatabase())) $dsn[static::DSN_KEY_DATABASE] = $this->getDatabase();
        // if (!empty($this->getPort())) $dsn[static::DSN_KEY_PORT] = $this->getPort();
        // if (!empty($this->getUser())) $dsn[static::DSN_KEY_USER] = $this->getUser();
        // if (!empty($this->getPassword())) $dsn[static::DSN_KEY_PASSWORD] = $this->getPassword();
        $dsn = $this->getFlatStringFromArray($dsn);
        return sprintf('%s%s%s', static::DB_DRIVER, ':', $dsn);
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
        $sql = sprintf('ALTER TABLE `%s` ADD PRIMARY KEY (`%s`);', $tablename, static::COLUMN_ID);
        $statement = $this->getDb()->prepare($sql);
        $statement->execute();
        $sql = sprintf('ALTER TABLE `%s` MODIFY `%s` int(20) NOT NULL AUTO_INCREMENT; COMMIT;', $tablename, static::COLUMN_ID);
        $statement = $this->getDb()->prepare($sql);
        $statement->execute();
    }

    public function getData(): array
    {
        $selector = implode (',', array_keys(static::$definition));
        $sql = sprintf('SELECT %s FROM %s ORDER BY id DESC', $selector, static::TABLE_NAME);
        $statement = $this->getDb()->prepare($sql);
        $statement->execute();

        $data = $statement->fetchAll();
        if (!is_array($data) || empty($data)) {
            return [];
        }
        return $data;
    }

    public function addEntry(array $data)
    {
        $columns = implode(',', array_keys($data));

        $valuesQuestionmarks = $data;
        array_walk($valuesQuestionmarks, function(&$item) { $item = '?'; });
        $valuesQuestionmarks = implode(',', $valuesQuestionmarks);

        $sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', static::TABLE_NAME, $columns, $valuesQuestionmarks);
        $statement = $this->getDb()->prepare($sql);
        $statement->execute(array_values($data));
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
        static::$host = $value;
    }

    public function getHost(): string
    {
        return static::$host;
    }

    public function setPort(int $value)
    {
        static::$port = $value;
    }

    public function getPort(): int
    {
        return static::$port;
    }

    public function setDatabase(string $value)
    {
        static::$database = $value;
    }

    public function getDatabase(): string
    {
        return static::$database;
    }

    public function setUser(string $value)
    {
        static::$user = $value;
    }

    public function getUser(): string
    {
        return static::$user;
    }

    public function setPassword(string $value)
    {
        static::$password = $value;
    }

    public function getPassword(): string
    {
        return static::$password;
    }

    public function setDsn(string $value)
    {
        static::$dsn = $value;
    }

    public function getDsn(): string
    {
        return static::$dsn;
    }

    public function setDb(PDO $value)
    {
        static::$db = $value;
    }

    public function getDb(): PDO
    {
        return static::$db;
    }

    public function setTable(string $value)
    {
        static::$table = $value;
    }

    public function getTable(): string
    {
        return static::$table;
    }
}