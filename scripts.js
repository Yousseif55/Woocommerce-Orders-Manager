        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('click', function(event) {

                if (event.target && event.target.classList.contains('cover-to-available')) {
                    var productName = event.target.parentElement.querySelector('.product-name').textContent.trim();
                    var productId = event.target.parentElement.querySelector('.product-name').getAttribute('data-product-id');					
                    var newRow = '<td class="available"><a href="post.php?post=' + productId + '&action=edit" target="_blank">' + productName + '</a></td><td></td><td></td>';
                    event.target.parentElement.parentElement.innerHTML = newRow;
                    var data = new FormData();
                    data.append('action', 'cover_to_available');
                    data.append('product_id', productId);

                    fetch(ajaxurl, {
                            method: 'POST',
                            body: data,
                            credentials: 'same-origin',
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Handle response if needed
                        });
                } else if (event.target && event.target.classList.contains('printing-to-available')) {

                    var productName = event.target.parentElement.querySelector('.product-name').textContent.trim();
                    var productId = event.target.parentElement.querySelector('.product-name').getAttribute('data-product-id');					
                    var newRow = '<td class="available"><a href="post.php?post=' + productId + '&action=edit" target="_blank">' + productName + '</a></td><td></td><td></td>';
                    event.target.parentElement.parentElement.innerHTML = newRow;
                    var orderId = event.target.parentElement.querySelector('.product-name').getAttribute('data-order-id');
                    var data = new FormData();
                    data.append('action', 'printing_to_available');
                    data.append('product_id', productId);
                    data.append('order_id', orderId);

                    fetch(ajaxurl, {
                            method: 'POST',
                            body: data,
                            credentials: 'same-origin',
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Handle response if needed
                        });
                }
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('click', function(event) {
                if (event.target && event.target.classList.contains('delete-history')) {
                    var productId = event.target.getAttribute('data-product-id');
                    var orderId = event.target.getAttribute('data-order-id');

                    // Perform AJAX request to delete history
                    var data = new FormData();
                    data.append('action', 'delete_order_from_product_meta');
                    data.append('product_id', productId);
                    data.append('order_id', orderId);

                    fetch(ajaxurl, {
                            method: 'POST',
                            body: data,
                            credentials: 'same-origin',
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update the UI
                                event.target.parentElement.parentElement.remove();
                            }
                        });
                }
                if (event.target && event.target.classList.contains('clear-history')) {
                    var productId = event.target.getAttribute('data-product-id');

                    // Perform AJAX request to clear history
                    var data = new FormData();
                    data.append('action', 'clear_history');
                    data.append('product_id', productId);

                    fetch(ajaxurl, {
                            method: 'POST',
                            body: data,
                            credentials: 'same-origin',
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload(); // Reload the page after successful clear
                            }
                        });
                }
            });
        });
