<?php

namespace App\Livewire\Settings;

use App\Helpers\Helpers;
use App\Models\Setting;
use App\Models\Tag;
use App\Models\TicketCustomFieldDefinition;
use App\Services\GorgiasService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.contentNavbarLayout')]
#[Title('Settings')]
class Gorgias extends Component
{
    public $gorgiasApiKey;
    public $gorgiasDomain;
    public $gorgiasEmail;
    public $syncingTags = false;
    public $tagsSyncedCount = 0;
    public $syncingCustomFields = false;
    public $customFieldsSyncedCount = 0;
    public $currentRoute = 'settings.gorgias';

    public function mount()
    {
        $this->gorgiasApiKey = Helpers::setting('gorgias_api_key');
        $this->gorgiasDomain = Helpers::setting('gorgias_domain');
        $this->gorgiasEmail = Helpers::setting('gorgias_email');
    }

    public function save()
    {
        if (!auth()->user()->can('settings.manage')) {
            session()->flash('error', 'You do not have permission to perform this action.');
            return;
        }

        Setting::updateOrCreate(
            ['key' => 'gorgias_api_key'],
            ['value' => $this->gorgiasApiKey, 'name' => 'Gorgias API Key', 'type' => 'string', 'autoload' => true]
        );

        Setting::updateOrCreate(
            ['key' => 'gorgias_domain'],
            ['value' => $this->gorgiasDomain, 'name' => 'Gorgias Domain', 'type' => 'string', 'autoload' => true]
        );

        Setting::updateOrCreate(
            ['key' => 'gorgias_email'],
            ['value' => $this->gorgiasEmail, 'name' => 'Gorgias Email', 'type' => 'string', 'autoload' => true]
        );

        session()->flash('success', 'Settings saved successfully.');
    }

    public function syncTags()
    {
        if (!auth()->user()->can('settings.manage')) {
            session()->flash('error', 'You do not have permission to perform this action.');
            return;
        }

        $this->syncingTags = true;
        $this->tagsSyncedCount = 0;

        try {
            $gorgiasService = new GorgiasService();

            // Fetch all tags from Gorgias API
            $tags = $gorgiasService->allTags();

            // Sync each tag to the database
            foreach ($tags as $tagData) {
                Tag::syncFromGorgias($tagData);
                $this->tagsSyncedCount++;
            }

            session()->flash('success', "Successfully synced {$this->tagsSyncedCount} tags from Gorgias.");
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to sync tags: ' . $e->getMessage());
        } finally {
            $this->syncingTags = false;
        }
    }

    public function syncCustomFields()
    {
        if (!auth()->user()->can('settings.manage')) {
            session()->flash('error', 'You do not have permission to perform this action.');
            return;
        }

        $this->syncingCustomFields = true;
        $this->customFieldsSyncedCount = 0;

        try {
            $gorgiasService = new GorgiasService();

            // Fetch all custom fields from Gorgias API
            $response = $gorgiasService->customFields('Ticket');
            $customFields = $response['data'] ?? [];

            // Sync each custom field to the database
            foreach ($customFields as $fieldData) {
                TicketCustomFieldDefinition::syncFromGorgias($fieldData);
                $this->customFieldsSyncedCount++;
            }

            session()->flash('success', "Successfully synced {$this->customFieldsSyncedCount} custom fields from Gorgias.");
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to sync custom fields: ' . $e->getMessage());
        } finally {
            $this->syncingCustomFields = false;
        }
    }

    public function render()
    {
        return view('livewire.settings.gorgias');
    }
}
