<?php namespace Kris\LaravelFormBuilder\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class FormMakeCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'form:make';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a form builder class.';

    /**
     * @var FormGenerator
     */
    protected $formGenerator;

    public function __construct(Filesystem $files, FormGenerator $formGenerator)
    {
        parent::__construct();
        $this->files = $files;
        $this->formGenerator = $formGenerator;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $path = $this->getNameInput();

        if ($this->files->exists($path)) {
            return $this->error('Form already exists!');
        }

        $this->makeDirectory($path);

        $this->files->put($path.'.php', $this->buildClass($path));

        $this->info('Form created successfully.');
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }

    /**
     * Build the controller class with the given name.
     *
     * @param  string $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('name', InputArgument::REQUIRED, 'Full path for Form class.'),
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
     * @param  string $stub
     * @param  string $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $formGenerator = $this->formGenerator;

        $stub = str_replace(
            '{{class}}',
            $formGenerator->getClassInfo($name)->className,
            $stub
        );

        return str_replace(
            '{{fields}}',
            $formGenerator->getFieldsVariable($this->option('fields')),
            $stub
        );
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string $stub
     * @param  string $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name)
    {
        $stub = str_replace(
            '{{namespace}}',
            $this->formGenerator->getClassInfo($name)->namespace,
            $stub
        );

        return $this;
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return $this->argument('name');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/form-class-template.stub';
    }
}
