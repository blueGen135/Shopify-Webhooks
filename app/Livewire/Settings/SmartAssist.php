<?php

namespace App\Livewire\Settings;

use App\Helpers\Helpers;
use App\Models\Setting;
use App\Services\SmartAssistService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.contentNavbarLayout')]
#[Title('Settings')]
class SmartAssist extends Component
{
  public $provider;
  public $apiKey;
  public $model;
  public $systemPrompt;
  public $currentRoute = 'settings.smart-assist';

  public function mount()
  {
    $this->provider = Helpers::setting('smart_assist_provider') ?? 'openai';
    $this->apiKey = Helpers::setting('smart_assist_api_key');
    $this->model = Helpers::setting('smart_assist_model') ?? 'gpt-4o';
    $this->systemPrompt = Helpers::setting('smart_assist_system_prompt') ?? $this->getDefaultSystemPrompt();
  }

  private function getDefaultSystemPrompt(): string
  {
    return "You are a professional and empathetic customer support representative for an e-commerce company.\n\nYour role is to:\n- Provide helpful, clear, and concise responses to customer inquiries\n- Show empathy and understanding for customer concerns\n- Offer solutions and next steps when applicable\n- Maintain a professional yet friendly tone\n- Be honest if you don't have enough information to help\n- Suggest escalation to a supervisor if the issue is complex\n\nKeep responses concise (2-3 paragraphs maximum) and actionable. Always end with a clear next step or question to continue helping the customer.";
  }

  public function save()
  {
    if (!auth()->user()->can('settings.manage')) {
      session()->flash('error', 'You do not have permission to perform this action.');
      return;
    }

    $this->validate([
      'provider' => 'required|string',
      'apiKey' => 'required|string',
      'model' => 'required|string',
      'systemPrompt' => 'required|string|min:50',
    ]);

    Setting::updateOrCreate(
      ['key' => 'smart_assist_provider'],
      ['value' => $this->provider, 'name' => 'Smart Assist Provider', 'type' => 'string', 'autoload' => true]
    );

    Setting::updateOrCreate(
      ['key' => 'smart_assist_api_key'],
      ['value' => $this->apiKey, 'name' => 'Smart Assist API Key', 'type' => 'string', 'autoload' => true]
    );

    Setting::updateOrCreate(
      ['key' => 'smart_assist_model'],
      ['value' => $this->model, 'name' => 'Smart Assist Model', 'type' => 'string', 'autoload' => true]
    );

    Setting::updateOrCreate(
      ['key' => 'smart_assist_system_prompt'],
      ['value' => $this->systemPrompt, 'name' => 'Smart Assist System Prompt', 'type' => 'text', 'autoload' => true]
    );

    session()->flash('success', 'Smart Assist settings saved successfully.');
  }

  public function testConnection()
  {
    if (!$this->apiKey) {
      session()->flash('error', 'Please enter an API key first.');
      return;
    }

    try {
      // Create temporary settings for testing
      Setting::updateOrCreate(
        ['key' => 'smart_assist_api_key'],
        ['value' => $this->apiKey, 'name' => 'Smart Assist API Key', 'type' => 'string', 'autoload' => true]
      );

      Setting::updateOrCreate(
        ['key' => 'smart_assist_provider'],
        ['value' => $this->provider, 'name' => 'Smart Assist Provider', 'type' => 'string', 'autoload' => true]
      );

      // Clear cache to use new settings
      cache()->forget('settings');

      $smartAssist = new SmartAssistService();
      $result = $smartAssist->testConnection();

      if ($result['success']) {
        session()->flash('success', 'Connection successful! API key is valid.');
      } else {
        session()->flash('error', 'Connection failed: ' . $result['message']);
      }
    } catch (\Exception $e) {
      session()->flash('error', 'Connection test failed: ' . $e->getMessage());
    }
  }

  public function render()
  {
    return view('livewire.settings.smart-assist');
  }
}
