<div class="col-12 col-lg-4">
  <div class="d-flex justify-content-between flex-column mb-4 mb-md-0">
    <h5 class="mb-4">Settings</h5>
    <ul class="nav nav-align-left nav-pills flex-column">
      <li class="nav-item mb-1">
        <a class="nav-link waves-effect waves-light 
          {{ $currentRoute === 'settings.gorgias' ? 'active' : '' }}"
          href="{{ route('settings.gorgias') }}">
          <i class="icon-base ti tabler-headset icon-sm me-1_5"></i>
          <span class="align-middle">Gorgias</span>
        </a>
      </li>
      <li class="nav-item mb-1">
        <a class="nav-link waves-effect waves-light 
          {{ $currentRoute === 'settings.smart-assist' ? 'active' : '' }}"
          href="{{ route('settings.smart-assist') }}">
          <i class="icon-base ti tabler-sparkles icon-sm me-1_5"></i>
          <span class="align-middle">Smart Assist</span>
        </a>
      </li>
      <li class="nav-item mb-1">
        <a class="nav-link waves-effect waves-light 
          {{ $currentRoute === 'settings.shopify' ? 'active' : '' }}"
          href="{{ route('settings.shopify') }}">
          <i class="icon-base ti tabler-shopping-bag icon-sm me-1_5"></i>
          <span class="align-middle">Shopify</span>
        </a>
      </li>
      <li class="nav-item mb-1">
        <a class="nav-link waves-effect waves-light 
          {{ $currentRoute === 'settings.fedex' ? 'active' : '' }}"
          href="{{ route('settings.fedex') }}">
          <i class="icon-base ti tabler-truck-delivery icon-sm me-1_5"></i>
          <span class="align-middle">FedEx</span>
        </a>
      </li>
      <li class="nav-item mb-1">
        <a class="nav-link waves-effect waves-light 
          {{ $currentRoute === 'settings.odoo' ? 'active' : '' }}"
          href="{{ route('settings.odoo') }}">
          <i class="icon-base ti tabler-database icon-sm me-1_5"></i>
          <span class="align-middle">Odoo</span>
        </a>
      </li>
    </ul>
  </div>
</div>
