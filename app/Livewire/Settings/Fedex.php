<?php

namespace App\Livewire\Settings;

use App\Helpers\Helpers;
use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.contentNavbarLayout')]
#[Title('Settings')]
class Fedex extends Component
{
  public $fedexApiKey;
  public $fedexSecretKey;
  public $fedexAccountNumber;
  public $fedexMeterNumber;
  public $fedexEnvironment;
  public $currentRoute = 'settings.fedex';

  public function mount()
  {
    $this->fedexApiKey = Helpers::setting('fedex_api_key');
    $this->fedexSecretKey = Helpers::setting('fedex_secret_key');
    $this->fedexAccountNumber = Helpers::setting('fedex_account_number');
    $this->fedexMeterNumber = Helpers::setting('fedex_meter_number');
    $this->fedexEnvironment = Helpers::setting('fedex_environment') ?? 'sandbox';
  }

  public function save()
  {
    if (!auth()->user()->can('settings.manage')) {
      session()->flash('error', 'You do not have permission to perform this action.');
      return;
    }

    $this->validate([
      'fedexApiKey' => 'required|string',
      'fedexSecretKey' => 'required|string',
      'fedexAccountNumber' => 'required',
      'fedexMeterNumber' => 'required',
      'fedexEnvironment' => 'required|in:sandbox,production',
    ]);

    Setting::updateOrCreate(
      ['key' => 'fedex_api_key'],
      ['value' => $this->fedexApiKey, 'name' => 'FedEx API Key', 'type' => 'string', 'autoload' => true]
    );

    Setting::updateOrCreate(
      ['key' => 'fedex_secret_key'],
      ['value' => $this->fedexSecretKey, 'name' => 'FedEx Secret Key', 'type' => 'string', 'autoload' => true]
    );

    Setting::updateOrCreate(
      ['key' => 'fedex_account_number'],
      ['value' => $this->fedexAccountNumber, 'name' => 'FedEx Account Number', 'type' => 'string', 'autoload' => true]
    );

    Setting::updateOrCreate(
      ['key' => 'fedex_meter_number'],
      ['value' => $this->fedexMeterNumber, 'name' => 'FedEx Meter Number', 'type' => 'string', 'autoload' => true]
    );

    Setting::updateOrCreate(
      ['key' => 'fedex_environment'],
      ['value' => $this->fedexEnvironment, 'name' => 'FedEx Environment', 'type' => 'string', 'autoload' => true]
    );

    session()->flash('success', 'FedEx settings saved successfully.');
  }

  public function render()
  {
    return view('livewire.settings.fedex');
  }
}
