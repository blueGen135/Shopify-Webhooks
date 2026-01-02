@php
  $configData = \App\Helpers\Helpers::appClasses();
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu"
  @foreach ($configData['menuAttributes'] as $attribute => $value)
  {{ $attribute }}="{{ $value }}" @endforeach>

  <!-- ! Hide app brand if navbar-full -->
  @if (!isset($navbarFull))
    <div class="app-brand demo mb-4 justify-content-between">
      <a href="{{ url('/') }}" class="app-brand-link">
        <span class="app-brand-logo demo">@include('_partials.macros')</span>
        <span class="app-brand-text demo menu-text fw-bold ms-3">{{ config('variables.templateName') }}</span>
      </a>

      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
        <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
        <i class="icon-base ti tabler-x d-block d-xl-none"></i>
      </a>
    </div>
  @endif

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    <li class="menu-item {{ \App\Helpers\Helpers::active_class(['/']) }}">
      <a href="{{ url('/') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-smart-home"></i>
        <div>Home</div>
      </a>
    </li>

    @can('roles.view')
      <li
        class="menu-item {{ \App\Helpers\Helpers::active_class(['roles']) }} {{ \App\Helpers\Helpers::active_class(['roles/*']) }}">
        <a href="{{ url('/roles') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-shield"></i>
          <div>Roles</div>
        </a>
      </li>
    @endcan

    @can('permissions.view')
      <li
        class="menu-item {{ \App\Helpers\Helpers::active_class(['permissions']) }} {{ \App\Helpers\Helpers::active_class(['permissions/*']) }}">
        <a href="{{ url('/permissions') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-lock"></i>
          <div>Permissions</div>
        </a>
      </li>
    @endcan

    @can('users.view')
      <li
        class="menu-item {{ \App\Helpers\Helpers::active_class(['users']) }} {{ \App\Helpers\Helpers::active_class(['users/*']) }}">
        <a href="{{ url('/users') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-users"></i>
          <div>Users</div>
        </a>
      </li>
    @endcan

    @can('warehouses.manage')
      <li
        class="menu-item {{ \App\Helpers\Helpers::active_class(['warehouses']) }} {{ \App\Helpers\Helpers::active_class(['warehouses/*']) }}">
        <a href="{{ url('/warehouses') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-building-warehouse"></i>
          <div>Warehouses</div>
        </a>
      </li>
    @endcan

    @can('tickets.view')
      <li
        class="menu-item {{ \App\Helpers\Helpers::active_class(['tickets']) }} {{ \App\Helpers\Helpers::active_class(['tickets/*']) }}">
        <a href="{{ url('/tickets') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-ticket"></i>
          <div>Tickets</div>
        </a>
      </li>
    @endcan

    @can('settings.manage')
      <li
        class="menu-item {{ \App\Helpers\Helpers::active_class(['settings.gorgias']) }} {{ \App\Helpers\Helpers::active_class(['settings/*']) }}">
        <a href="{{ url('/settings/gorgias') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-settings"></i>
          <div>Settings</div>
        </a>
      </li>
    @endcan

  </ul>

</aside>
