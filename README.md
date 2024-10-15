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
- Admin dashboard available at `/admin` to update pricing and view print jobs.

## Pricing Calculation

Prices of each type can be set by the admin where the latest pricing will be used where multiple exists.

- **Black and White Pages:** N20 per page
- **Coloured Pages:** N25 per page
- **Image Pixels:** N0.00005 per pixel (optional bonus)

## Deployment

- Deploy on platforms like Heroku, AWS, or similar.
- Ensure environment variables are set appropriately in production.

## Development Notes

- **File Processing:** Uses libraries for PDF and DOCX analysis.
- **Mock Services:** Payment and email functionalities are simulated for demonstration.

## Conclusion

This application streamlines the printing process, reduces corruption risk, and provides an efficient way to handle print jobs online.

## License

This project is unlicensed.

## Contact

For questions or support, please contact [Joel Okoromi](mailto:okmarq@gmail.com)

## Design thoughts & tradeoffs

Processing the file in the PrintJobController synchronously would lead to immediate display of the completed processed job and quote on screen.
   
However, this is not ideal since I/O requests in general degrades sever performance and in production could impact users negatively.

Making use of a Job instead will delay the file processing and Quotation which is remedied by the email being sent after processing.

For simplicity, the roles are used to check if the user is an admin or customer.
In reality, there are different types of Admins and middlewares can be used to group what admin should do what.

Showed processing for DOCX, PDF, JPG, PNG, and JPEG.
