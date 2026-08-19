<?php

use \Kris\LaravelFormBuilder\Filters\Collection\Trim;
use PHPUnit\Framework\Attributes\Test;

class FilterResolverTest extends FormBuilderTestCase
{
    #[Test]
    public function it_resolve_alias_based_filter()
    {
        $expected = \Kris\LaravelFormBuilder\Filters\FilterInterface::class;
        $resolver = $this->filtersResolver;
        $this->assertInstanceOf(
            $expected, $resolver::instance('Trim')
        );
    }

    #[Test]
    public function it_resolve_object_based_filter()
    {
        $expected  = \Kris\LaravelFormBuilder\Filters\FilterInterface::class;
        $filterObj = new Trim();
        $resolver  = $this->filtersResolver;

        $resolvedFilterObj = $resolver::instance($filterObj);

        $this->assertInstanceOf(
            $expected, $filterObj
        );

        $this->assertEquals(
            $filterObj, $resolvedFilterObj
        );
    }

    #[Test]
    public function it_throws_an_exception_if_object_is_not_instance_of_filterinterface()
    {
        $this->expectException(\Kris\LaravelFormBuilder\Filters\Exception\InvalidInstanceException::class);

        $invalidFilterObj = new stdClass();
        $resolver = $this->filtersResolver;
        $resolver::instance($invalidFilterObj);
    }

    #[Test]
    public function it_throws_an_exception_if_filter_cant_be_resolved()
    {
        $this->expectException(\Kris\LaravelFormBuilder\Filters\Exception\UnableToResolveFilterException::class);

        $invalidFilterClass = "\\Test\\Not\\Existing\\Class\\";
        $resolver = $this->filtersResolver;
        $resolver::instance($invalidFilterClass);
    }
}