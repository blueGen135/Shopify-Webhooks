document.addEventListener('alpine:init', () => {
  Alpine.data('ticketOrderSelection', () => ({
    searchQuery: '',
    expandedOrder: null,
    filterOrders() {
      const query = this.searchQuery.toLowerCase();
      const orderCards = document.querySelectorAll('.order-card');

      orderCards.forEach(card => {
        const orderNumber = card.getAttribute('data-order-number').toLowerCase();
        const orderDate = card.getAttribute('data-order-date').toLowerCase();
        const orderTotal = card.getAttribute('data-order-total').toLowerCase();

        if (orderNumber.includes(query) || orderDate.includes(query) || orderTotal.includes(query)) {
          card.style.display = '';
        } else {
          card.style.display = 'none';
        }
      });
    }
  }));
});
