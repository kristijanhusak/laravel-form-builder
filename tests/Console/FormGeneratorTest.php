<?php

use Kris\LaravelFormBuilder\Console\FormGenerator;
use PHPUnit\Framework\TestCase;

class FormGeneratorTest extends TestCase
{

    /**
     * @var FormGenerator
     */
    protected $formGenerator;

    public function setUp(): void
    {
        $this->formGenerator = new FormGenerator();
    }

    /** @test */
    public function it_returns_comment_when_no_fields_passed()
    {
        $parsedFields = $this->formGenerator->getFieldsVariable();

        $this->assertEquals('// Add fields here...', $parsedFields);
    }

    /** @test */
    public function it_parses_fields_from_options_to_methods()
    {
        $fields = 'first_name:text, last_name:text, user_email:email, user_password:password';

        $expected = join('', [
            "\$this\n",
            "            ->add('first_name', 'text')\n",
            "            ->add('last_name', 'text')\n",
            "            ->add('user_email', 'email')\n",
            "            ->add('user_password', 'password');",
        ]);

        $parsedFields = $this->formGenerator->getFieldsVariable($fields);

        $this->assertSame($expected, $parsedFields);
    }

    /** @test */
    public function it_gets_class_info_for_given_full_class_name()
    {
        // Parsed in this format from Laravels GeneratorCommand
        $className = 'VendorName\\Posts\\Form\\MainForm';

        $expected = (object) [
            'namespace' => 'VendorName\\Posts\\Form',
            'className' => 'MainForm'
        ];

        $shorterName = 'VendorName\\PostForm';

        $expectedForShorter = (object) [
            'namespace' => 'VendorName',
            'className' => 'PostForm'
        ];

        $classInfo = $this->formGenerator->getClassInfo($className);
        $shorterClassInfo = $this->formGenerator->getClassInfo($shorterName);

        $this->assertEquals($expected, $classInfo);
        $this->assertEquals($expectedForShorter, $shorterClassInfo);

    }
}
