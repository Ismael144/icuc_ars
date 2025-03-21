<?php

use App\helpers\ValidationHelper;

use App\exceptions\validation\InvalidValidationRuleException;

class ValidationHelperTest extends \PHPUnit\Framework\TestCase
{
    protected ValidationHelper $validateHelper;

    public function setUp(): void
    {
        $this->validateHelper = new ValidationHelper;
    }

    public function testMakeValidationsThrowsException()
    {
        $this->expectException(InvalidValidationRuleException::class);
        $this->validateHelper->makeValidations('blah', '');
    }

    public function testUsernameRuleValidations()
    {
        $response = $this->validateHelper->makeValidations("username", "Ismael@#$", "Username contains invalid characters", true);
        $this->assertSame("Username contains invalid characters", $response);
    }
    
    public function testEmailRuleValidation()
    {
        $response = $this->validateHelper->makeValidations("Email", "ismael@gmail", "Invalid email", true);
        $this->assertSame("Invalid email", $response);
    }

    public function testPasswordValidation() 
    {
        $password = "somepassword@2022";
        $response = $this->validateHelper->makeValidations("password", $password, "Invalid password"); 
        $this->assertSame($password, $response);
    }

    public function testURLRuleValidation() 
    {
        $url = "http://localhost:8000"; 
        $response = $this->validateHelper->makeValidations("url", $url); 
        $this->assertSame($url, $response);
    }

    public function testValidateEmail()
    {
        $result = $this->validateHelper->makeValidations("email", "ismael@gmail.com");
        $this->assertSame("ismael@gmail.com", $result);
    }

    public function isEmptyProvider()
    {
       return [
           [""],
           [[]],
       ]; 
    }

    /** @dataProvider isEmptyProvider */
    public function testIsEmpty($value)
    {
        $result = $this->validateHelper->isEmpty($value);
        $this->assertTrue($result);
    }

    public function testCheckIfRuleIsValid()
    {
        $this->assertFalse($this->validateHelper->checkIfRuleIsValid("some_test"));
    }

    public function testEmptyFields() 
    {
        $value = "";
        $result = $this->validateHelper->isEmptyField($value, "This field is required"); 
        $this->assertSame("This field is required", $result);
    }
}
