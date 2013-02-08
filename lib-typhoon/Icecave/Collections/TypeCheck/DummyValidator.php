<?php
namespace Icecave\Collections\TypeCheck;

class DummyValidator extends AbstractValidator
{
    public function __call($name, array $arguments)
    {
    }

}
