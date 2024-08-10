# Custom Checkout Cart

## Description
The **Custom Checkout Cart** plugin displays the cart table on the checkout page and limits the cart to specific conditions. It ensures that only one product, or one product plus an additional specific product, can be in the cart at any given time.

## Features
- Displays the cart table on the WooCommerce checkout page.
- Limits the cart to either one product or one product plus a specific additional product.
- Automatically removes duplicate products from the cart.
- Provides a button to clear the cart via AJAX.

## Installation
1. Download the plugin files.
2. Upload the entire plugin folder to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.

## Usage
- The cart will automatically be displayed on the checkout page.
- The cart will enforce the rule of either one product or one product plus a specific additional product.

## Customization
- You can change the specific additional product by modifying the `$additional_product_id` variable in the `limit_cart_to_specific_products` method.

## Development
To contribute or modify the plugin, follow these steps:
1. Clone the repository: `git clone https://github.com/yourusername/your-repo-name.git`
2. Navigate to the plugin directory: `cd your-repo-name`
3. Make your changes and test locally.

## Files
- `custom-checkout-cart.php`: Main plugin file that contains all the functionality for displaying and managing the checkout cart.
- `custom-checkout-cart.css`: Custom styles for the cart display.
- `custom-checkout-cart.js`: JavaScript for handling AJAX requests and other frontend functionalities.

## Changelog
### Version 1.3
- Initial release.
