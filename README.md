# Orders Manager

## Description

**Orders Manager** is a WordPress plugin designed to enhance the management of WooCommerce orders. It provides a custom admin page to view and manage orders based on their shipping readiness and printing status. This plugin helps streamline the process of tracking orders, managing product statuses, and handling order history.

### Features
- **Order Management:** View orders and their statuses, including whether they are ready to ship.
- **Product Status Updates:** Manage product statuses such as "Cover Needed" and "Printing Needed."
- **Print History Tracking:** Track which orders have been printed and clear the history if necessary.
- **Admin Interface:** Easy-to-use admin interface for managing orders and product statuses.

## Usage

1. **View Orders:**
   - Navigate to `Orders Manager` in the WordPress admin menu.
   - You will see a list of orders with their current statuses, including information on whether products are ready to ship or need additional actions.

2. **Update Product Status:**
   - On the orders page, you can mark products as "Cover Needed" or "Printing Needed" by clicking the corresponding buttons.

3. **Manage Printed Products:**
   - Access the `Printed Products` submenu under the `Orders Manager` menu to view and manage products that have printed orders.
   - Clear the print history or mark individual orders as not done.

## Frequently Asked Questions (FAQ)

### How does the plugin determine if products are ready to ship?
The plugin checks if all products in an order are in stock and if they are marked as "Ready" in their metadata. If all conditions are met, the order is updated as ready to ship.

### Can I use this plugin with other custom post types or products?
This plugin is designed specifically for WooCommerce products. Modifying it to work with other custom post types or product types may require additional customization.

### How can I reset the print history or status of a product?
You can clear the print history for a product from the `Printed Products` page by clicking the "Clear History" button. For individual orders, you can mark them as not done, which updates the product metadata accordingly.

## License

This plugin is licensed under the **GNU General Public License v3.0**. See [LICENSE](LICENSE) for more details.

## Notes

- This plugin is a sample from a project and may require customization to fit specific needs or environments.
- Always test plugins in a staging environment before deploying them on a live site.

## Author

**Yousseif Ahmed**

For more information or support, please contact the author or refer to the plugin documentation.

