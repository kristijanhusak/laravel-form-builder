<?php namespace Kris\LaravelFormBuilder\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FormMakeCommand extends GeneratorCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'laravel-form-builder:make';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Creates a form builder class.';

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('name', InputArgument::REQUIRED, 'Full class name of the desired form class.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('fields', null, InputOption::VALUE_OPTIONAL, 'Fields for the form'),
		);
	}

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $class = $this->getClassInfo($name)->className;

        $stub = str_replace('{{class}}', $class, $stub);

        return str_replace('{{fields}}', $this->getFields(), $stub);
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name)
    {
        $namespace = $this->getClassInfo($name)->namespace;

        $stub = str_replace(
            '{{namespace}}', $namespace, $stub
        );

        return $this;
    }

    /**
     * @param $name
     * @return object
     */
    private function getClassInfo($name)
    {
        $exploded = explode('/', $name);
        $className = array_pop($exploded);

        return (object)[
            'namespace' => join('\\', $exploded),
            'className' => $className
        ];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/form-class-template.stub';
    }

    /**
     * Get fields from options and add to class
     *
     * @return string
     */
    private function getFields()
    {
        $fields = $this->option('fields');
        if ($fields) {
            return $this->parseFields($fields);
        }

        return '// Add fields here...';
    }

    /**
     * Parse fields from string
     *
     * @param $fields
     * @return string
     */
    private function parseFields($fields)
    {
        $fieldsArray = explode(',', $fields);
        $text = '$this'."\n";

        foreach ($fieldsArray as $field) {
            $text .= $this->prepareAdd($field, end($fieldsArray) == $field);
        }

        return $text.';';
    }

    /**
     * @param      $field
     * @param bool $isLast
     * @return string
     */
    private function prepareAdd($field, $isLast = false)
    {
        $field = trim($field);
        list($name, $type) = explode(':', $field);
        $textArr = [
            "            ",
            "->add('",
            $name,
            "', '",
            $type,
            "')",
            ($isLast) ? "" : "\n"
        ];

        return join('', $textArr);
    }
}
