<?php

use \Kris\LaravelFormBuilder\Filters\Collection\Trim;

class FilterResolverTest extends FormBuilderTestCase
{
    /** @test */
    public function it_resolve_alias_based_filter()
    {
        $expected = \Kris\LaravelFormBuilder\Filters\FilterInterface::class;
        $resolver = $this->filtersResolver;
        $this->assertInstanceOf(
            $expected, $resolver::instance('Trim')
        );
    }

    /** @test */
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

    /**
     * @test
     * @expectedException \Kris\LaravelFormBuilder\Filters\Exception\InvalidInstanceException
     */
    public function it_throws_an_exception_if_object_is_not_instance_of_filterinterface()
    {
        $invalidFilterObj = new stdClass();
        $resolver = $this->filtersResolver;
        $resolver::instance($invalidFilterObj);
    }

    /**
     * @test
     * @expectedException \Kris\LaravelFormBuilder\Filters\Exception\UnableToResolveFilterException
     */
    public function it_throws_an_exception_if_filter_cant_be_resolved()
    {
        $invalidFilterClass = "\\Test\\Not\\Existing\\Class\\";
        $resolver = $this->filtersResolver;
        $resolver::instance($invalidFilterClass);
    }
}