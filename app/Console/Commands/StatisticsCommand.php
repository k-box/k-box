<?php

namespace KBox\Console\Commands;

use DB;
use KBox\File;
use KBox\User;
use DatePeriod;
use KBox\Group;
use KBox\Project;
use Carbon\Carbon;
use KBox\Publication;
use KBox\RecentSearch;
use League\Csv\Writer;
use Carbon\CarbonInterval;
use KBox\DocumentDescriptor;
use Illuminate\Console\Command;

class StatisticsCommand extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistics 
                            {--summary : Print the summary of the statistics according to the selected period. No statistics file will be generate.}
                            {--overall : Consider the whole life of the K-Box. Can be used only with --summary}
                            {--influx : Print the summary according to InfluxDB Line Protocol }
                            {--days=30 : How many days to include in the statistics}
                            {--out-path= : Where the statistics file will be written, default storage folder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate K-Box usage statistics (30 days or monthly).';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $folder = ltrim($this->option('out-path') ?? storage_path(), '/').'/';

        $subtractDays = $this->option('days', 30);
        
        $printSummary = $this->option('summary');
        $printOverallSummary = $this->option('overall');
        $influx = $this->option('influx');

        $today = Carbon::today();

        $from = $today->copy()->subDays($subtractDays)->startOfDay();
        $to = $today->endOfDay();

        if ($printSummary) {
            if (! $influx) {
                $this->comment($printOverallSummary ? 'Overall statistics summary' : 'Statistics for the period '.$from->format('Y-m-d').' to '.$to->format('Y-m-d'));
            }

            if ($printOverallSummary) {
                $this->formatSummary($this->generateOverallReport(), $influx);
            } else {
                $this->formatSummary($this->generateSummary($from, $to), $influx);
            }

            return 0;
        }

        $report = $this->generateReport($from, $to);

        $report_filename = $folder.'statistics-'.$from->format('Y-m-d').'--'.$to->format('Y-m-d').'-exported-on-'.Carbon::now()->format('Y-m-d--H-i-s').'.csv';

        $csv = Writer::createFromPath($report_filename, 'w');

        $csv->insertAll($report);

        return 0;
    }
    
    private function generateReport($from, $to)
    {
        $date_field = DB::raw('CONCAT(YEAR(created_at), "-", MONTH(created_at), "-", DAY(created_at)) as date');
        $count_field = DB::raw('COUNT(*) as count');

        $users_created_per_day = User::where('created_at', '>=', $from)->where('created_at', '<=', $to)->select($date_field, $count_field)->groupBy('date')->orderBy('date')->get()->keyBy('date');
        $document_created_per_day = DocumentDescriptor::where('created_at', '>=', $from)->where('created_at', '<=', $to)->select($date_field, $count_field)->orderBy('date')->groupBy('date')->get()->keyBy('date');
        $files_created_per_day = File::where('created_at', '>=', $from)->where('created_at', '<=', $to)->select($date_field, $count_field)->groupBy('date')->orderBy('date')->get()->keyBy('date');
        $publication_made_per_day = Publication::where('created_at', '>=', $from)->where('created_at', '<=', $to)->select($date_field, $count_field)->groupBy('date')->orderBy('date')->get()->keyBy('date');
        $projects_created_per_day =  Project::where('created_at', '>=', $from)->where('created_at', '<=', $to)->select($date_field, $count_field)->groupBy('date')->orderBy('date')->get()->keyBy('date');
        $collections_created_per_day = Group::where('created_at', '>=', $from)->where('created_at', '<=', $to)->select($date_field, $count_field)->groupBy('date')->orderBy('date')->get()->keyBy('date');
        $personal_collections_created_per_day = Group::where('is_private', true)->where('created_at', '>=', $from)->where('created_at', '<=', $to)->select($date_field, $count_field)->groupBy('date')->orderBy('date')->get()->keyBy('date');

        $period = new DatePeriod($from, CarbonInterval::days(1), $to);

        $graph = [];
        $graph[] = ['date', 'Users Created', 'Documents Created', 'Files uploaded', 'Publications performed', 'Projects created', 'Collections created', 'Personal collections created'];

        foreach ($period as $date) {
            $graph[] = [
                $date->format('Y-n-d'),
                $this->getValueFromDateGrouping($users_created_per_day, $date->format('Y-n-d'), 0),
                $this->getValueFromDateGrouping($document_created_per_day, $date->format('Y-n-d'), 0),
                $this->getValueFromDateGrouping($files_created_per_day, $date->format('Y-n-d'), 0),
                $this->getValueFromDateGrouping($publication_made_per_day, $date->format('Y-n-d'), 0),
                $this->getValueFromDateGrouping($projects_created_per_day, $date->format('Y-n-d'), 0),
                $this->getValueFromDateGrouping($collections_created_per_day, $date->format('Y-n-d'), 0),
                $this->getValueFromDateGrouping($personal_collections_created_per_day, $date->format('Y-n-d'), 0),
            ];
        }
        
        return $graph;
    }

    private function getValueFromDateGrouping($collection, $key, $default = 0)
    {
        if (! $collection->has($key)) {
            return $default;
        }

        return $collection->get($key)->count;
    }

    private function generateOverallReport()
    {
        $most_used_search = RecentSearch::select('*', \DB::raw('(count(*) + times) as total'))->groupBy('terms')->orderBy('total', 'DESC')->first();

        $totals = [
            ['Documents (not considering versions)', DocumentDescriptor::count()],
            ['Uploads', File::count()],
            ['Published documents', DocumentDescriptor::public()->count()],
            ['Registered users', User::count()],
            ['Projects', Project::count()],
            ['Collections', Group::count()],
            ['Personal collections', Group::where('is_private', true)->count()],
            ['Overall searches', RecentSearch::select(\DB::raw('SUM(times + 1) as overall'))->first()->overall],
            ['Most used search keyword', $most_used_search ? "$most_used_search->terms, $most_used_search->total times" : ''],
        ];
        
        return $totals;
    }
    
    private function generateSummary($from, $to)
    {
        $most_used_search = RecentSearch::where('created_at', '>=', $from)->where('created_at', '<=', $to)->select('*', \DB::raw('(count(*) + times) as total'))->groupBy('terms')->orderBy('total', 'DESC')->first();

        $totals = [
            ['Documents (not considering versions)', DocumentDescriptor::where('created_at', '>=', $from)->where('created_at', '<=', $to)->count()],
            ['Uploads', File::where('created_at', '>=', $from)->where('created_at', '<=', $to)->count()],
            ['Published documents', DocumentDescriptor::where('created_at', '>=', $from)->where('created_at', '<=', $to)->public()->count()],
            ['Registered users', User::where('created_at', '>=', $from)->where('created_at', '<=', $to)->count()],
            ['Projects', Project::where('created_at', '>=', $from)->where('created_at', '<=', $to)->count()],
            ['Collections', Group::where('created_at', '>=', $from)->where('created_at', '<=', $to)->count()],
            ['Personal collections', Group::where('created_at', '>=', $from)->where('created_at', '<=', $to)->where('is_private', true)->count()],
            ['Overall searches', RecentSearch::where('created_at', '>=', $from)->where('created_at', '<=', $to)->select(\DB::raw('SUM(times + 1) as overall'))->first()->overall],
            ['Most used search keyword', $most_used_search ? "$most_used_search->terms, $most_used_search->total times" : ''],
        ];
        
        return $totals;
    }

    private function formatSummary($summary, $influx = false)
    {
        if ($influx) {
            $this->line(sprintf(
                    'kbox,domain=%1$s documents=%2$si,uploads=%3$si,published=%4$si,users=%5$si,projects=%6$si,collections=%7$si,personal_collections=%8$si',
                    url('/'),
                    $summary[0][1], //documents
                    $summary[1][1], //uploads
                    $summary[2][1], //published
                    $summary[3][1], //users
                    $summary[4][1], //projects
                    $summary[5][1], //collections
                    $summary[6][1]  //personal collections
                    ));
            return;
        }

        $this->table([], $summary);
    }
}
