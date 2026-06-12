<?php

namespace Tests\Unit;

use App\Core\Env;
use PHPUnit\Framework\TestCase;

class EnvTest extends TestCase
{
    protected function setUp(): void
    {
        putenv('TEST_ENV_VAR=hello');
        $_ENV['TEST_ENV_VAR'] = 'hello';
    }

    public function testGetReturnsEnvValue(): void
    {
        $this->assertSame('hello', Env::get('TEST_ENV_VAR'));
    }

    public function testGetReturnsDefaultWhenMissing(): void
    {
        $this->assertSame('default', Env::get('NONEXISTENT_VAR_XYZ', 'default'));
    }

    public function testBoolParsesTrue(): void
    {
        putenv('BOOL_TRUE=true');
        $_ENV['BOOL_TRUE'] = 'true';
        $this->assertTrue(Env::bool('BOOL_TRUE'));
    }

    public function testBoolParsesFalse(): void
    {
        putenv('BOOL_FALSE=false');
        $_ENV['BOOL_FALSE'] = 'false';
        $this->assertFalse(Env::bool('BOOL_FALSE'));
    }
}
