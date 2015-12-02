<?php

namespace MyPrefix\MyNamespace;

use MyPrefix\AnotherNamespace;
class MyClass extends MyPrefix\MyFQExtendedClass implements MyPrefix\MyFQInterface
{
    public function useFullyQualifiedNamespace(MyPrefix\stdClass $class)
    {
    }
}
