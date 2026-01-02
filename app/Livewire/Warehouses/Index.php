<?php

namespace App\Livewire\Warehouses;

use App\Models\Warehouse;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.contentNavbarLayout')]
#[Title('Warehouses')]
class Index extends Component
{
  use WithPagination;

  public $warehouseId = null;
  public $name = '';
  public $address = '';
  public $city = '';
  public $state = '';
  public $postal_code = '';
  public $country_code = 'US';
  public $latitude = '';
  public $longitude = '';
  public $status = true;
  public $priority = 0;

  public $isEditMode = false;
  public $search = '';

  protected $queryString = ['search'];

  protected function rules()
  {
    return [
      'name' => 'required|string|max:255',
      'address' => 'required|string|max:255',
      'city' => 'required|string|max:255',
      'state' => 'required|string|max:2',
      'postal_code' => 'required|string|max:10',
      'country_code' => 'required|string|max:2',
      'latitude' => 'nullable|numeric|between:-90,90',
      'longitude' => 'nullable|numeric|between:-180,180',
      'status' => 'boolean',
      'priority' => 'integer|min:0',
    ];
  }

  public function save()
  {
    $this->validate();

    if ($this->isEditMode && $this->warehouseId) {
      $warehouse = Warehouse::findOrFail($this->warehouseId);
      $warehouse->update([
        'name' => $this->name,
        'address' => $this->address,
        'city' => $this->city,
        'state' => $this->state,
        'postal_code' => $this->postal_code,
        'country_code' => $this->country_code,
        'latitude' => $this->latitude ?: null,
        'longitude' => $this->longitude ?: null,
        'status' => $this->status,
        'priority' => $this->priority,
      ]);

      session()->flash('success', 'Warehouse updated successfully!');
    } else {
      Warehouse::create([
        'name' => $this->name,
        'address' => $this->address,
        'city' => $this->city,
        'state' => $this->state,
        'postal_code' => $this->postal_code,
        'country_code' => $this->country_code,
        'latitude' => $this->latitude ?: null,
        'longitude' => $this->longitude ?: null,
        'status' => $this->status,
        'priority' => $this->priority,
      ]);

      session()->flash('success', 'Warehouse created successfully!');
    }

    $this->resetForm();
    $this->dispatch('close-modal');
  }

  public function edit($id)
  {
    $warehouse = Warehouse::findOrFail($id);

    $this->warehouseId = $warehouse->id;
    $this->name = $warehouse->name;
    $this->address = $warehouse->address;
    $this->city = $warehouse->city;
    $this->state = $warehouse->state;
    $this->postal_code = $warehouse->postal_code;
    $this->country_code = $warehouse->country_code;
    $this->latitude = $warehouse->latitude;
    $this->longitude = $warehouse->longitude;
    $this->status = $warehouse->status;
    $this->priority = $warehouse->priority;

    $this->isEditMode = true;

    $this->dispatch('open-modal');
  }

  public function delete($id)
  {
    $warehouse = Warehouse::findOrFail($id);
    $warehouse->delete();

    session()->flash('success', 'Warehouse deleted successfully!');
  }

  public function toggleStatus($id)
  {
    $warehouse = Warehouse::findOrFail($id);
    $warehouse->update(['status' => !$warehouse->status]);

    session()->flash('success', 'Warehouse status updated!');
  }

  public function resetForm()
  {
    $this->reset([
      'warehouseId',
      'name',
      'address',
      'city',
      'state',
      'postal_code',
      'country_code',
      'latitude',
      'longitude',
      'status',
      'priority',
      'isEditMode',
    ]);

    $this->resetValidation();
  }

  public function render()
  {
    $warehouses = Warehouse::query()
      ->when($this->search, function ($query) {
        $query->where('name', 'like', '%' . $this->search . '%')
          ->orWhere('city', 'like', '%' . $this->search . '%')
          ->orWhere('state', 'like', '%' . $this->search . '%');
      })
      ->ordered()
      ->paginate(10);

    return view('livewire.warehouses.index', compact('warehouses'));
  }
}
