# Invoice Management System

A comprehensive web-based invoice management system built with PHP, featuring user management, customer management, product management, and invoice generation capabilities.

## Project Structure

### Core Files
- `index.php` - Main entry point of the application
- `dashboard.php` - User dashboard interface
- `functions.php` - Core utility functions
- `session.php` - Session management
- `response.php` - API response handling
- `header.php` - Main header template
- `footer.php` - Footer template
- `header-login.php` - Login page header
- `header_main.php` - Main application header

### Authentication Module
- `login.php` - User authentication
- `logout.php` - Session termination
- `session.php` - Session management

### User Management Module
- `user-add.php` - Add new users
- `user-edit.php` - Edit existing users
- `user-list.php` - List all users

### Customer Management Module
- `customer-add.php` - Add new customers
- `customer-edit.php` - Edit existing customers
- `customer-list.php` - List all customers

### Product Management Module
- `product-add.php` - Add new products
- `product-edit.php` - Edit existing products
- `product-list.php` - List all products

### Invoice Management Module
- `invoice.php` - Main invoice interface
- `invoice-create.php` - Create new invoices
- `invoice-edit.php` - Edit existing invoices
- `invoice-list.php` - List all invoices
- `tcpdf_invoice.php` - PDF invoice generation
- `test_invoice.php` - Invoice testing
- `test_tcpdf.php` - PDF generation testing

### Directory Structure
```
├── css/              # Stylesheets
├── js/               # JavaScript files
├── images/           # Image assets
├── fonts/            # Font files
├── languages/        # Language files
├── includes/         # Included PHP files
├── vendor/           # Third-party libraries
├── downloads/        # Downloadable files
├── invoices/         # Generated invoice files
└── DATABASE FILE/    # Database related files
```

## Dependencies
- PHP 7.0 or higher
- MySQL Database
- TCPDF Library (for PDF generation)
- PHPMailer (for email functionality)

## Installation
1. Clone the repository
2. Import the database schema from the `DATABASE FILE` directory
3. Configure database connection in `includes/config.php`
4. Install dependencies using Composer:
   ```bash
   composer install
   ```
5. Set up your web server to point to the project directory
6. Access the application through your web browser

## Features
- User authentication and authorization
- Customer management
- Product management
- Invoice creation and management
- PDF invoice generation
- Email notifications
- Multi-language support
- Responsive design

## Security
- Session-based authentication
- Input validation
- SQL injection prevention
- XSS protection
- CSRF protection

## Modularity
The system is built with a modular architecture:
1. **Authentication Module**: Handles user login, logout, and session management
2. **User Management Module**: Manages system users and their permissions
3. **Customer Module**: Handles customer data and relationships
4. **Product Module**: Manages product catalog and pricing
5. **Invoice Module**: Core functionality for invoice creation and management
6. **PDF Generation Module**: Handles invoice PDF creation
7. **Email Module**: Manages email notifications

Each module is self-contained with its own set of files and follows the same pattern:
- List view (list.php)
- Add view (add.php)
- Edit view (edit.php)
- Main functionality file (module.php)

## Best Practices
- Follows MVC-like architecture
- Separation of concerns
- Reusable components
- Consistent file naming conventions
- Modular code structure
- Secure coding practices

## Support
For support and issues, please contact the development team. 
