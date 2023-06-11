<?php

namespace Hoochicken\Dbtable;

use PDO;

class Database
{
    const PORT_DEFAULT = 3306;
    const TABLE_NAME = 'statistics';
    const DB_DRIVER = 'mysql';
    const DSN_KEY_HOST = 'host';
    const DSN_KEY_DATABASE = 'dbname';
    const DSN_KEY_PORT = 'port';
    const DSN_KEY_PASSWORD = 'password';
    const DSN_KEY_USER = 'user';

    private string $host = '';
    private int $port = self::PORT_DEFAULT;
    private string $user = '';
    private string $password = '';
    private string $dsn = '';
    private string $database = '';
    private string $table = self::TABLE_NAME;
    private ?PDO $db;


    public function __construct(string $host, string $database, string $user, string $password, int $port = self::PORT_DEFAULT)
    {
        $this->setHost($host);
        $this->setDatabase($database);
        $this->setUser($user);
        $this->setPassword($password);
        $this->setPort($port);
        $this->dsn = $this->generateDsn();
        $this->db = $this->initDb($this->dsn, $this->user, $this->password);
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

    public function test()
    {
        echo 'assssssssssssssssd';
    }

    public function createTable()
    {
        $sql = 'CREATE TABLE `analytics` (
  `id` int(20) NOT NULL,
  `page_url` varchar(150) NOT NULL,
  `entry_time` datetime NOT NULL,
  `exit_time` datetime NOT NULL,
  `ip_address` varchar(30) NOT NULL,
  `country` varchar(50) NOT NULL,
  `operating_system` varchar(20) NOT NULL,
  `browser` varchar(20) NOT NULL,
  `browser_version` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
)';
    }

    public function getDb()
    {
        $sql = 'SELECT name, color, calories FROM fruit ORDER BY name';
        foreach ($this->db->query($sql) as $row) {
            print $row['name'] . "\t";
            print $row['color'] . "\t";
            print $row['calories'] . "\n";
        }
    }

    public function tableExists()
    {
        $sql = sprintf('SELECT table_name FROM information_schema.tables WHERE table_schema = "%s" AND table_name = "%s"',
            $this->database, $this->table);
        return false;
    }

    public function setHost(string $value)
    {
        $this->host = $value;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setPort(int $value)
    {
        $this->port = $value;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function setDatabase(string $value)
    {
        $this->database = $value;
    }

    public function getDatabase(): string
    {
        return $this->database;
    }

    public function setUser(string $value)
    {
        $this->user = $value;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setPassword(string $value)
    {
        $this->password = $value;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setDsn(string $value)
    {
        $this->dsn = $value;
    }

    public function getDsn(): string
    {
        return $this->dsn;
    }
}