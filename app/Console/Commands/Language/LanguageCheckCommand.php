<?php

namespace KBox\Console\Commands\Language;

use Illuminate\Console\Command;

use Illuminate\Foundation\Bus\DispatchesJobs;
use KBox\Console\Traits\DebugOutput;
use Carbon\Carbon;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Verify that all the strings in english language files can be found in the other languages.
 */
class LanguageCheckCommand extends Command
{
    use DispatchesJobs, DebugOutput;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang:check {--report= : Wheter the report should be written in a file. With this option the file name and path are required}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify that all the strings in english language files can be found in the other languages.';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Store the report details.
     *
     * Is an associative array with a key for each language
     *
     * @var array
     */
    private $report = null;

    /**
     * Create a new command instance.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     * @return void
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $reportFile = $this->option('report');
        
        $this->comment('Checking language files...');

        $language_path = base_path('resources/lang');
        $en_language_path = base_path('resources/lang/en');
        
        // get the language folders
        // get the en folder content
        if (! $this->filesystem->isDirectory($en_language_path)) {
            throw new Exception("English Language folder not found", 404);
        }

        $en_files = $this->filesystem->files($en_language_path);
        
        $lang_groups = array_map(
            function ($e) {
                return str_replace('.php', '', basename($e));
            },
            $this->filesystem->files($en_language_path)
        );

        $directories = array_filter($this->filesystem->directories($language_path), function ($i) {
            return ! Str::endsWith($i, 'en');
        });

        $translationLoader = app('translation.loader');

        $current_lang_file_paths = null;
        $current_lang_file_names = null;
        $current_lang = null;
        $expected_translations = null;
        $loaded_translations = null;
        $diff_translations = null;
        $this->report = [];
        $count_total_groups = count($lang_groups);
        $count_total_strings = 0;
        $count_strings = 0;

        $current_count_expected = 0;
        $current_count_loaded = 0;
        $current_count_percentage = 0;

        foreach ($directories as $directory) {
            $count_total_strings = 0;
            $count_strings = 0;

            $current_lang = basename($directory);
            $this->line("Checking $current_lang translations...");
            
            foreach ($lang_groups as $group) {
                $expected_translations = array_keys(Arr::dot($translationLoader->load('en', $group)));
                $loaded_translations = array_keys(Arr::dot($translationLoader->load($current_lang, $group)));
                $diff_translations = array_diff($expected_translations, $loaded_translations);

                $current_count_expected = count($expected_translations);
                $current_count_loaded = count($loaded_translations);

                $count_total_strings = $count_total_strings + $current_count_expected;
                $count_strings = $count_strings + $current_count_loaded;
                
                $current_count_percentage = round(($count_strings/$count_total_strings)*100, 2);
                $this->report[$current_lang]['groups'][$group]['expected'] = $current_count_expected;
                $this->report[$current_lang]['groups'][$group]['loaded'] = $current_count_loaded;
                $this->report[$current_lang]['groups'][$group]['diff'] = $diff_translations;
                $this->report[$current_lang]['percentage'] = $current_count_percentage;

                $this->debugLine("  $group {$current_count_loaded}/{$current_count_expected}");
            }

            $this->info("  {$current_count_percentage}% [{$count_strings} / {$count_total_strings}] ");
        }

        $percentages = Arr::pluck($this->report, 'percentage');

        $this->line('');
        $this->comment("languages: ".count($directories));
        $this->comment("translation: ".round(array_sum($percentages) / count($percentages), 2)."%");
        $this->line('');

        if (! is_null($reportFile)) {
            $this->writeReportDetails($reportFile);
        } else {
            $this->outputReportDetails();
        }

        return 0;
    }

    /**
     * Write the language report to a file
     *
     * @param string $file the file path on disk
     */
    private function writeReportDetails($file)
    {
        $this->filesystem->put($file, PHP_EOL);
        $this->filesystem->append($file, 'Language report '.Carbon::now());

        foreach ($this->report as $lang => $details) {
            $this->filesystem->append($file, '-------------------------'.PHP_EOL);
            $this->filesystem->append($file, $lang);
            $this->filesystem->append($file, PHP_EOL.PHP_EOL);

            foreach ($details['groups'] as $grp => $values) {
                if ($values['loaded'] !== $values['expected']) {
                    $this->filesystem->append($file, "$grp {$values['loaded']}/{$values['expected']}".PHP_EOL.PHP_EOL);
                    
                    $this->filesystem->append($file, implode(PHP_EOL, $values['diff']));

                    $this->filesystem->append($file, PHP_EOL);
                }
            }
            $this->filesystem->append($file, PHP_EOL);
            $this->filesystem->append($file, '-------------------------'.PHP_EOL);
        }
    }

    /**
     * Write the language report to the console stream
     */
    private function outputReportDetails()
    {
        $this->line('-------------------------');
        $this->line('Details');
        $this->line('-------------------------');

        foreach ($this->report as $lang => $details) {
            $this->comment($lang);

            foreach ($details['groups'] as $grp => $values) {
                if ($values['loaded'] !== $values['expected']) {
                    $this->comment("$grp {$values['loaded']}/{$values['expected']}");
                    
                    $this->line(implode(PHP_EOL, $values['diff']));

                    $this->line('');
                }
            }
            $this->line('');
        }
        $this->line('-------------------------');
    }
}
