<?php

namespace _PhpScoper5b2c11ee6df50;

class CoveredParentClass
{
    private function privateMethod()
    {
    }
    protected function protectedMethod()
    {
        $this->privateMethod();
    }
    public function publicMethod()
    {
        $this->protectedMethod();
    }
}
class CoveredClass extends \_PhpScoper5b2c11ee6df50\CoveredParentClass
{
    private function privateMethod()
    {
    }
    protected function protectedMethod()
    {
        parent::protectedMethod();
        $this->privateMethod();
    }
    public function publicMethod()
    {
        parent::publicMethod();
        $this->protectedMethod();
    }
}
