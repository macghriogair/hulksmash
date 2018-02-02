<?php

declare(strict_types = 1);

namespace App\Commands;

use App\Traits\ExecutesShellCommands;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class GitCloneCommand extends Command
{
    use ExecutesShellCommands;

    /**
     * The name and signature of the command.
     *
     * @var string
     */
    protected $signature = 'clone
        {url : Repository URL to clone from }
        {dir?}
        {--b|branch=master : Fetch a specific branch }';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Clones a GIT repository if you don`t know how to run git from the commandline';

    /**
     * Execute the command. Here goes the code.
     *
     * @return void
     */
    public function handle(): void
    {
        $url = $this->argument('url');
        $branch = ltrim($this->option('branch'), '=');

        // Proactivley knowing the target dir will allow us to check if it exists.
        if (is_null($targetDir = $this->argument('dir'))) {
            $end = last(explode('/', $url));
            $targetDir = trim($end, '.git');
        }

        if (file_exists($targetDir)) {
            throw new \InvalidArgumentException("File or directory already exists!");
        }

        $this->info("Clone branch {$branch} from {$url} into {$targetDir}");
        $this->runShellCommand("git clone -b {$branch} --single-branch {$url} {$targetDir}");
    }
}
