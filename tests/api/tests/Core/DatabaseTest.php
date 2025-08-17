<?php

use Bifrost\Core\Database;
use PHPUnit\Framework\TestCase;

/**
 * @covers Bifrost\Core\Database
 */
class DatabaseTest extends TestCase
{
    public function testCanBeUsed()
    {
        if (getenv("BFR_API_SQL_DRIVER") != false) {
            $this->assertInstanceOf(Database::class, new Database());
        } else {
            $this->assertTrue(true);
        }
    }

    public function invokePrivateStaticMethod(string $methodName, array $args = [])
    {
        $reflector = new ReflectionMethod(Database::class, $methodName);
        $reflector->setAccessible(true);
        return $reflector->invokeArgs(null, $args);
    }

    public function testBuildSelectQueryWithStringFields()
    {
        $expected = "SELECT * FROM users";
        $actual = $this->invokePrivateStaticMethod('buildSelectQuery', ['users', '*']);
        $this->assertEquals($expected, $actual);
    }

    public function testBuildSelectQueryWithArrayFields()
    {
        $fields = ['u.id', 'u.name'];
        $expected = "SELECT u.id, u.name FROM users";
        $actual = $this->invokePrivateStaticMethod('buildSelectQuery', ['users', $fields]);
        $this->assertEquals($expected, $actual);
    }

    public function testBuildInsertQuery()
    {
        $data = ['name' => 'John', 'age' => 30, 'active' => null];
        $expected = "INSERT INTO users (name, age, active) VALUES ('John', 30, NULL)";
        $actual = $this->invokePrivateStaticMethod('buildInsertQuery', ['users', $data]);
        $this->assertEquals($expected, $actual);
    }

    public function testBuildUpdateQuery()
    {
        $data = ['name' => 'John', 'age' => 30];
        $expected = "UPDATE users SET name = 'John', age = 30";
        $actual = $this->invokePrivateStaticMethod('buildUpdateQuery', ['users', $data]);
        $this->assertEquals($expected, $actual);
    }

    public function testBuildDeleteQuery()
    {
        $expected = "DELETE FROM users";
        $actual = $this->invokePrivateStaticMethod('buildDeleteQuery', ['users']);
        $this->assertEquals($expected, $actual);
    }

    public function testBuildWhereQueryWithString()
    {
        $where = "id = 1";
        $actual = $this->invokePrivateStaticMethod('buildWhereQuery', [$where]);
        $this->assertEquals($where, $actual);
    }

    public function testBuildWhereQueryWithArrayAnd()
    {
        $where = ['id' => 1, 'name' => 'John'];
        $expected = "id = 1 AND name = 'John'";
        $actual = $this->invokePrivateStaticMethod('buildWhereQuery', [$where]);
        $this->assertEquals($expected, $actual);
    }

    public function testBuildWhereQueryWithOr()
    {
        $where = ['OR' => ['id' => 1, 'name' => 'John']];
        $expected = "(id = 1 OR name = 'John')";
        $actual = $this->invokePrivateStaticMethod('buildWhereQuery', [$where]);
        $this->assertEquals($expected, $actual);
    }

    public function testBuildJoinQuery()
    {
        $join = [
            'INNER JOIN orders o ON o.user_id = u.id',
            'LEFT JOIN addresses a ON a.user_id = u.id'
        ];
        $expected = 'INNER JOIN orders o ON o.user_id = u.id LEFT JOIN addresses a ON a.user_id = u.id';
        $actual = $this->invokePrivateStaticMethod('buildJoinQuery', [$join]);
        $this->assertEquals($expected, $actual);
    }
}
