<?php

use Kris\LaravelFormBuilder\Fields\InputType;
use Kris\LaravelFormBuilder\RulesParser;

class RulesParserTest extends FormBuilderTestCase
{
    /**
     * @var RulesParser
     */
    protected $parser;

    public function setUp()
    {
        parent::setUp();
        $field = new InputType('address', 'text', $this->plainForm);
        $this->parser = new RulesParser($field);
    }

    /** @test */
    public function it_parses_the_several_rules_from_string()
    {
        $validAttrs = [
            'required' => 'required',
            'minlength' => '5',
            'pattern' => '[a-zA-Z0-9]+',
            'title' => $this->getTitle('alpha_num')
        ];

        $this->assertEquals($validAttrs , $this->parser->parse('required|min:5|alpha_num'));
    }

    /** @test */
    public function it_parses_the_required_rule()
    {
        $this->assertEquals(
            ['required' => 'required'],
            $this->parser->parse('required')
        );
    }

    /** @test */
    public function it_parses_the_accepted_rule()
    {
        $this->assertEquals(
            ['required' => 'required', 'title' => $this->getTitle('accepted')],
            $this->parser->parse('accepted')
        );
    }

    /** @test */
    public function it_parses_the_alpha_rule()
    {
        $this->assertEquals(
            ['pattern' => '[a-zA-Z]+', 'title' => $this->getTitle('alpha')],
            $this->parser->parse('alpha')
        );
    }

    private function getTitle($rule, $attr = 'Address')
    {
        return trans('validation.' . $rule, ['attribute' => $attr]);
    }
}
