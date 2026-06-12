<?php

namespace Tests\Unit;

use App\Core\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    public function testRequiredFieldFailsWhenEmpty(): void
    {
        $errors = Validator::validate(['title' => ''], ['title' => 'required|string']);
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('title', $errors);
    }

    public function testValidTaskDataPasses(): void
    {
        $errors = Validator::validate(
            ['title' => 'Minha tarefa', 'priority' => 'alta'],
            ['title' => 'required|string|max:200', 'priority' => 'required|in:baixa,media,alta']
        );
        $this->assertEmpty($errors);
    }

    public function testInvalidPriorityFails(): void
    {
        $errors = Validator::validate(
            ['title' => 'Teste', 'priority' => 'urgente'],
            ['title' => 'required|string', 'priority' => 'required|in:baixa,media,alta']
        );
        $this->assertNotEmpty($errors);
    }

    public function testEmailValidation(): void
    {
        $errors = Validator::validate(
            ['email' => 'invalido'],
            ['email' => 'required|email']
        );
        $this->assertNotEmpty($errors);

        $errors = Validator::validate(
            ['email' => 'user@nexus.com'],
            ['email' => 'required|email']
        );
        $this->assertEmpty($errors);
    }

    public function testNullableAllowsEmpty(): void
    {
        $errors = Validator::validate(
            ['due_date' => ''],
            ['due_date' => 'nullable|date']
        );
        $this->assertEmpty($errors);
    }
}
