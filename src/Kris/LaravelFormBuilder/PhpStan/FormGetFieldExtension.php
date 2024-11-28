<?php

namespace Kris\LaravelFormBuilder\PhpStan;

use Kris\LaravelFormBuilder\Fields\ChildFormType;
use Kris\LaravelFormBuilder\Form;
use Kris\LaravelFormBuilder\FormHelper;
use Larastan\Larastan\Concerns\HasContainer;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\Annotations\AnnotationPropertyReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeWithClassName;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;

class FormGetFieldExtension implements DynamicMethodReturnTypeExtension
{

    public function __construct(
        protected ReflectionProvider $reflectionProvider,
    ) {}

    public function getClass(): string {
        return Form::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool {
        return $methodReflection->getName() == 'getField';
    }

    public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): ?Type {
        return $this->getTypeFromMethodCallGetField($methodReflection, $methodCall, $scope);
    }

    protected function getTypeFromMethodCallGetField(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): ?Type
    {
        if (count($methodCall->getArgs()) < 1) {
            return null;
        }

        $fieldNameArg = $methodCall->getArgs()[0]->value;
        if (!($fieldNameArg instanceof String_)) {
            return null;
        }

        $fieldName = $fieldNameArg->value;

        $calledOnType = $scope->getType($methodCall->var);
        assert($calledOnType instanceof TypeWithClassName);
        $formClass = $calledOnType->getClassName();

        $formClassReflection = $this->reflectionProvider->getClass($formClass);

        if (!$formClassReflection->hasProperty($fieldName)) {
            return null;
        }

        $formClassFieldProperty = $formClassReflection->getProperty($fieldName, $scope);
        if (!($formClassFieldProperty instanceof AnnotationPropertyReflection)) {
            return null;
        }

        return $formClassFieldProperty->getReadableType();
    }

}
