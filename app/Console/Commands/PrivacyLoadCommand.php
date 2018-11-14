<?php

namespace KBox\Console\Commands;

use KBox\Pages\Page;
use KBox\Pages\PageTemplate;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class PrivacyLoadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'privacy:load';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the privacy policy page from the templates';

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
        $pages = collect([
            PageTemplate::PRIVACY_POLICY_SUMMARY,
            PageTemplate::PRIVACY_POLICY_LEGAL,
        ]);
        $language = config('app.locale');

        $created = $pages->map(function ($template_id) use ($language) {
            $template = PageTemplate::find($template_id, $language);

            if (! $this->isPageEqual($template)) {
                $this->createPageFromTemplate($template);

                return $template_id;
            }

            return null;
        })->filter();

        if ($created->isEmpty()) {
            $this->line('Privacy policy already existing, nothing to do');
        } else {
            $this->line('Privacy policy loaded');
        }

        return 0;
    }

    /**
     * Check if a page exists and is equal to the given template
     */
    private function isPageEqual(?PageTemplate $template)
    {
        if (! $template) {
            return false;
        }

        $page = Page::find($template->id, $template->language);

        if ($page instanceof Collection && $page->isEmpty()) {
            return false;
        }
        
        if ($page) {
            return $page->content === $template->content;
        }

        return false;
    }

    private function createPageFromTemplate(PageTemplate $template)
    {
        $page = Page::create([
            'id' => $template->id,
            'title' => $template->title,
            'language' => $template->language,
            'description' => $template->description,
            'authors' => $template->authors,
            'content' => $template->content
        ]);

        $page->save();
    }
}
