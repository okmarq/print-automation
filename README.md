# Printing Press Automation System

Automate print requests and invoice generating processes, where all print job requests will be taken via the company website.

## Features

- **Customer Interface:** Upload files (`.pdf`, `.docx`, `.png`, `.jpg`, `.jpeg`) and receive instant quotes.
- **Admin Dashboard:** Manage pricing and view print job requests.
- **File Processing:** Determine page counts and color usage for accurate pricing.
- **Email:** Send email confirmations.
- **Mock Payment:** Simulate payment confirmations.

## Requirements

- PHP >= 8.0
- Composer
- Laravel 11.x
- MySQL or PostgreSQL

## Installation

1. **Clone the repository:**

   ```
   git clone https://github.com/yourusername/printing-press.git
   cd printing-press
   ```
2. **Install dependencies:**

    ```
    composer install
    ```

3. **Set up environment:**

   Copy .env.example to .env and update database credentials.


4. **Generate application key:**

    ```
    php artisan key:generate
    ```
5. **Run migrations and seed data:**

    ```
    php artisan migrate --seed
    ```

6. **Serve the application:**

    ```
    php artisan serve
    ```

## Usage

- Access the application at http://localhost:8000.
- Upload a file to receive a quote.
- Admin dashboard available at /admin to update pricing and view print jobs.

## Pricing Calculation

- Black and White Pages: N20 per page
- Coloured Pages: N25 per page
- Image Pixels: N0.00005 per pixel (optional bonus)

## Deployment

- Deploy on platforms like Heroku, AWS, or similar.
- Ensure environment variables are set appropriately in production.

## Development Notes

- File Processing: Uses libraries for PDF and DOCX analysis.
- Mock Services: Payment and email functionalities are simulated for demonstration.

## Conclusion

This application streamlines the printing process, reduces corruption risk, and provides an efficient way to handle print jobs online.

## License

This project is open-source and available under the MIT License.

## Contact

For questions or support, please contact gbenga@nigerianlawpublications.com.
