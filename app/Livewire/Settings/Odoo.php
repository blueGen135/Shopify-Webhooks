<?php

namespace App\Livewire\Settings;

use App\Helpers\Helpers;
use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.contentNavbarLayout')]
#[Title('Settings')]
class Odoo extends Component
{
  public $odooEndpoint;
  public $customerNumber;
  public $odooPassword;
  public $odooCompanies;
  public $currentRoute = 'settings.odoo';

  public function mount()
  {
    $this->odooEndpoint = Helpers::setting('odoo_endpoint');
    $this->customerNumber = Helpers::setting('odoo_customer_number');
    $this->odooPassword = Helpers::setting('odoo_password');
    $this->odooCompanies = Helpers::setting('odoo_companies');
  }

  public function save()
  {
    if (!auth()->user()->can('settings.manage')) {
      session()->flash('error', 'You do not have permission to perform this action.');
      return;
    }

    $this->validate([
      'odooEndpoint' => 'required|url',
      'customerNumber' => 'required',
      'odooPassword' => 'required|string',
      'odooCompanies' => 'nullable|string',
    ]);

    Setting::updateOrCreate(
      ['key' => 'odoo_endpoint'],
      ['value' => $this->odooEndpoint, 'name' => 'Odoo Endpoint', 'type' => 'string', 'autoload' => true]
    );

    Setting::updateOrCreate(
      ['key' => 'odoo_customer_number'],
      ['value' => $this->customerNumber, 'name' => 'Odoo Customer Number', 'type' => 'string', 'autoload' => true]
    );

    Setting::updateOrCreate(
      ['key' => 'odoo_password'],
      ['value' => $this->odooPassword, 'name' => 'Odoo Password', 'type' => 'string', 'autoload' => true]
    );

    Setting::updateOrCreate(
      ['key' => 'odoo_companies'],
      ['value' => $this->odooCompanies, 'name' => 'Odoo Companies', 'type' => 'string', 'autoload' => true]
    );

    session()->flash('success', 'Odoo settings saved successfully.');
  }

  public function render()
  {
    return view('livewire.settings.odoo');
  }
}
