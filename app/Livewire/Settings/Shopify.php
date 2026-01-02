<?php

namespace App\Livewire\Settings;

use App\Helpers\Helpers;
use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.contentNavbarLayout')]
#[Title('Settings')]
class Shopify extends Component
{
  public $shopifyDomain;
  public $shopifyAccessToken;
  public $shopifyApiKey;
  public $shopifyApiSecret;
  public $shopifyWebhookSecret;
  public $currentRoute = 'settings.shopify';

  public function mount()
  {
    $this->shopifyDomain = Helpers::setting('shopify_domain');
    $this->shopifyAccessToken = Helpers::setting('shopify_access_token');
    $this->shopifyApiKey = Helpers::setting('shopify_api_key');
    $this->shopifyApiSecret = Helpers::setting('shopify_api_secret');
    $this->shopifyWebhookSecret = Helpers::setting('shopify_webhook_secret');
  }

  public function save()
  {
    if (!auth()->user()->can('settings.manage')) {
      session()->flash('error', 'You do not have permission to perform this action.');
      return;
    }

    $this->validate([
      'shopifyDomain' => 'required|string',
      'shopifyAccessToken' => 'required|string',
      'shopifyApiKey' => 'nullable|string',
      'shopifyApiSecret' => 'nullable|string',
      'shopifyWebhookSecret' => 'nullable|string',
    ]);

    Setting::updateOrCreate(
      ['key' => 'shopify_domain'],
      ['value' => $this->shopifyDomain, 'name' => 'Shopify Domain', 'type' => 'string', 'autoload' => true]
    );

    Setting::updateOrCreate(
      ['key' => 'shopify_access_token'],
      ['value' => $this->shopifyAccessToken, 'name' => 'Shopify Access Token', 'type' => 'string', 'autoload' => true]
    );

    Setting::updateOrCreate(
      ['key' => 'shopify_api_key'],
      ['value' => $this->shopifyApiKey, 'name' => 'Shopify API Key', 'type' => 'string', 'autoload' => true]
    );

    Setting::updateOrCreate(
      ['key' => 'shopify_api_secret'],
      ['value' => $this->shopifyApiSecret, 'name' => 'Shopify API Secret', 'type' => 'string', 'autoload' => true]
    );

    Setting::updateOrCreate(
      ['key' => 'shopify_webhook_secret'],
      ['value' => $this->shopifyWebhookSecret, 'name' => 'Shopify Webhook Secret', 'type' => 'string', 'autoload' => true]
    );

    session()->flash('success', 'Shopify settings saved successfully.');
  }

  public function render()
  {
    return view('livewire.settings.shopify');
  }
}
