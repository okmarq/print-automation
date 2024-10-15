# Printing Press Automation System

Automate print requests and invoice generating processes, where all print job requests will be taken via the company website.

## Features

- **Customer Interface:** Upload files (`.pdf`, `.docx`, `.png`, `.jpg`, `.jpeg`) and receive instant quotes.
- **Admin Dashboard:** Manage pricing and view print job requests.
- **File Processing:** Determine page counts and color usage for accurate pricing.
- **Email:** Send email confirmations.
- **Mock Payment:** Simulate payment confirmations.
- **Pub/Sub Messaging:** for asynchronous processing of print jobs

## Requirements

- PHP >= 8.0
- Composer
- Laravel 11.x
- MySQL or PostgreSQL
- PHP Imagick

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
5. **Link storage data:**

    ```
    php artisan storage:link
    ```
6. **Run migrations and seed data:**

    ```
    php artisan migrate --seed
    ```

7. **Start queue:**

    ```
    php artisan queue:work
    ```

8. **Serve the application:**

    ```
    php artisan serve
    ```

## Usage

You can access the application [here at Heroku](https://print-automation-0e22a831cb71.herokuapp.com).

The database server is deployed at render.

### Customer

- Upload a file to receive a quote via email after it has been processed asynchronously.
- After which payment can be made and an email is sent to the user for every payment made.
- Incomplete payments made will keep the status as unpaid until a complete payment is made for the print job.

### Admin

- Admin dashboard available at `/admin` to add price settings and view print jobs.
- If there's no admin settings present, a customer cannot make an upload which is checked by a middleware.
- For the purpose of testing, admin details are `test@gmail.com` for email and `password` for password.

## Pricing Calculation

Prices of each type can be set by the admin where the latest pricing will be used where multiple exists.

- **Black and White Pages:** N20 per page
- **Coloured Pages:** N25 per page
- **Image Pixels:** N0.00005 per pixel (optional bonus)

## Deployment

- The Application is deployed on the Heroku platform.

### Potential Issues

- Heroku doesn't accept file uploads thus, this feature has to be tested locally

## Development Notes

- **File Processing:** Uses libraries for PDF and DOCX analysis.
- **Mock Services:** Payment functionalities are simulated for demonstration. However, mailtrap.io server is used for emails
- **Intervention:** For Image processing
- **PhpOffice:** For DOCX processing
- **Smalot:** For PDF processing

## Design thoughts & tradeoffs

Processing the file in the PrintJobController synchronously would lead to immediate display of the completed processed job and quote on screen.

However, this is not ideal since I/O requests in general degrades sever performance and in production could impact users negatively.

Making use of a Job instead will delay the file processing and Quotation which is remedied by the email being sent after processing.

For simplicity, the roles are used to check if the user is an admin or customer.
In reality, there are different types of Admins and middlewares can be used to group what admin should do what.

Showed processing for DOCX, PDF, JPG, PNG, and JPEG.

## Conclusion

This application streamlines the printing process, reduces corruption risk, and provides an efficient way to handle print jobs online.

## License

This project is unlicensed.

## Contact

For questions or support, please contact [Joel Okoromi](mailto:okmarq@gmail.com)
