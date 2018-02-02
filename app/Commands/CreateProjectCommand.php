<?php

declare(strict_types = 1);

namespace App\Commands;

use App\Traits\ExecutesShellCommands;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class CreateProjectCommand extends Command
{
    use ExecutesShellCommands;

    /**
     * The name and signature of the command.
     *
     * @var string
     */
    protected $signature = 'create {project-name? : Name of your Project.}';

    protected $boilerplates;

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Creates a new PHP Project from a boilerplate.';

    public function __construct(string $name = null)
    {
        parent::__construct($name);

        $this->boilerplates = config('boilerplates');
    }

    /**
     * Execute the command. Here goes the code.
     *
     * @return void
     */
    public function handle(): void
    {
        $selected = $this->askForBoilerplate();
        $config = $this->boilerplates[$selected];
        $projectName = $this->askForProjectName();

        // Clone the boilerplate repo
        $this->call('clone', [
            'url' => $config['url'],
            '--branch' => $this->askForVersion($config),
            'dir' => $projectName
        ]);

        $this->info("Entering directory {$projectName}...");
        chdir($projectName);

        $this->info('Install composer dependencies...');
        $this->runShellCommand("composer install");

        $this->info('Clean up...');
        $this->runShellCommand("rm -rf .git");

        $this->info('Ready to built something awesome!');
    }

    protected function askForBoilerplate()
    {
        $choices = array_keys($this->boilerplates);

        return $this->choice('Please choose a boilerplate.', $choices, 0);
    }

    protected function askForVersion(array $boilerplateConfig)
    {
        if (! isset($boilerplateConfig['versions']) || empty($boilerplateConfig['versions'])) {
            return 'master';
        }

        if (1 === count($boilerplateConfig['versions'])) {
            return $boilerplateConfig['versions'][0];
        }

        return $this->choice('Please choose a version.', $boilerplateConfig['versions'], 0);
    }

    protected function askForProjectName() : string
    {
        if (! is_null($projectName = $this->argument('project-name'))) {
            return $projectName;
        }

        $exists = true;
        while (true === $exists) {
            $projectName = $this->ask(
                'Please enter the name of your Project',
                'new-project'
            );
            if ($exists = file_exists(strtolower($projectName))) {
                $this->error("A project with that name already exists!");
            }
        }

        return $projectName;
    }
}
