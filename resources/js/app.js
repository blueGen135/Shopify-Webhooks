import './bootstrap';
import 'bootstrap-select/dist/js/bootstrap-select.min.js';
import 'bootstrap-select/dist/css/bootstrap-select.min.css';
import './ticket-detail';
/*
  Add custom scripts here
*/
import.meta.glob([
  '../assets/img/**',
  // '../assets/json/**',
  '../assets/vendor/fonts/**'
]);

// Global function to switch between offcanvas panels
window.switchOffcanvas = function (currentId, targetId) {
  const current = bootstrap.Offcanvas.getInstance(document.getElementById(currentId));
  if (current) {
    const showTarget = target => {
      if (target) {
        setTimeout(() => {
          target.show();
        }, 10);
      }
    };

    document.getElementById(currentId).addEventListener(
      'hidden.bs.offcanvas',
      () => {
        const target = new bootstrap.Offcanvas(document.getElementById(targetId));
        showTarget(target);
      },
      { once: true }
    );

    current.hide();
  } else {
    const target = new bootstrap.Offcanvas(document.getElementById(targetId));
    showTarget(target);
  }
};

document.addEventListener('DOMContentLoaded', function () {
  if (typeof window.$ !== 'undefined' && typeof $.fn.selectpicker === 'function') {
    $('.selectpicker').selectpicker();
  }

  // custom handle backdrop getting removed from dom on livewire updates
  (function () {
    document.addEventListener('shown.bs.offcanvas', function (event) {
      const backdrop = document.querySelector('.offcanvas-backdrop');
      const customBackdrop = event.target.parentElement.querySelector('.custom-backdrop');
      if (backdrop && customBackdrop) {
        customBackdrop.appendChild(backdrop);
      }
    });

    document.addEventListener('hidden.bs.offcanvas', function () {
      const backdrop = document.querySelector('.offcanvas-backdrop');
      if (backdrop) {
        backdrop.remove();
      }
    });
  })();
});
