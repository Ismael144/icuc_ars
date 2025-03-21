<?php 

namespace App\attributes\managers;

use App\attributes\RegisterUserActivity;

class UserActivitiesManager
{
    public function __construct(
        // public array $controllers
    )
    {}

    public function registerAttributesFromClasses(array $classes) 
    {
        foreach($classes as $class) {
            $reflectionClass = new \ReflectionClass($class); 

            foreach($reflectionClass->getMethods() as $method) {
                $attributes = $method->getAttributes(RegisterUserActivity::class); 

                foreach($attributes as $attribute) {
                    $attributeClass = $attribute->newInstance(); 

                    $this->recordActivity();
                }
            }
        }
    }


    public function recordActivity()
    {
        print("Hello, world!");   
    }
}