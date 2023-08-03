<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class AnimalBehaviors extends Command
{
    protected $signature = 'animal:behave {animal} {--reset}';

    protected $description = 'Simulate animal behaviors';

    public function handle()
    {
        $animal = $this->argument('animal');
        $reset = $this->option('reset');

        $config = config('animals');

        if (isset($config[$animal])) {
            if ($reset) {
                $this->executeResetBehaviorCount($animal, $config);
                $this->info(ucfirst($animal) . ' behavior count has been reset.');
            } else {
                $totalCount = 0;

                $presentTenseBehavior = $config[$animal]['present_tense'];
                $totalCount += $this->executeBehavior($animal, $presentTenseBehavior, $config);

                $this->info(ucfirst($animal) . ' has ' . $config[$animal]['past_tense'] . ' ' . $totalCount . ' times.');
            }
        } else {
            $this->error('Animal not supported. Please check the available animals.');
        }
    }

    private function executeBehavior($animal, $behavior)
    {
        $count = Cache::get("{$animal}_{$behavior}_count", 0);
        $count++;

        Cache::put("{$animal}_{$behavior}_count", $count);

        $this->info(ucfirst($animal) . ' ' . $behavior . '...');

        return $count;
    }

    private function executeResetBehaviorCount($animal, $config)
    {
        if (isset($config[$animal])) {
            $animalBehaviors = $config[$animal];

            foreach ($animalBehaviors as $behavior) {
                $key = "{$animal}_{$behavior}_count";
                Cache::forget($key);
            }
        }
    }
}
