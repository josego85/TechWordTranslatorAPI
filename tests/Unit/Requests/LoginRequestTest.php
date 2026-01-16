<?php

declare(strict_types=1);

namespace Tests\Unit\Requests;

use App\Http\Requests\API\V1\LoginRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class LoginRequestTest extends TestCase
{
    use RefreshDatabase;

    protected LoginRequest $request;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new LoginRequest;
    }

    public function test_authorize_returns_true(): void
    {
        $this->assertTrue($this->request->authorize());
    }

    public function test_rules_returns_correct_validation_rules(): void
    {
        $rules = $this->request->rules();

        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('password', $rules);
        $this->assertContains('required', $rules['email']);
        $this->assertContains('email', $rules['email']);
        $this->assertContains('required', $rules['password']);
        $this->assertContains('string', $rules['password']);
    }

    public function test_validation_passes_with_valid_data(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        $this->assertFalse($validator->fails());
    }

    public function test_validation_fails_without_email(): void
    {
        $data = [
            'password' => 'password123',
        ];

        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    public function test_validation_fails_with_invalid_email_format(): void
    {
        $data = [
            'email' => 'invalid-email',
            'password' => 'password123',
        ];

        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    public function test_validation_fails_without_password(): void
    {
        $data = [
            'email' => 'test@example.com',
        ];

        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    public function test_custom_messages_are_defined(): void
    {
        $messages = $this->request->messages();

        $this->assertArrayHasKey('email.required', $messages);
        $this->assertArrayHasKey('email.email', $messages);
        $this->assertArrayHasKey('password.required', $messages);
        $this->assertEquals('Email is required.', $messages['email.required']);
        $this->assertEquals('Email must be a valid email address.', $messages['email.email']);
        $this->assertEquals('Password is required.', $messages['password.required']);
    }

    public function test_validation_errors_use_custom_messages(): void
    {
        $data = [];

        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());
        $validator->fails();

        $errors = $validator->errors();

        $this->assertContains('Email is required.', $errors->get('email'));
        $this->assertContains('Password is required.', $errors->get('password'));
    }

    public function test_validation_fails_with_both_fields_missing(): void
    {
        $data = [];

        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        $this->assertTrue($validator->fails());
        $this->assertCount(2, $validator->errors());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }
}
