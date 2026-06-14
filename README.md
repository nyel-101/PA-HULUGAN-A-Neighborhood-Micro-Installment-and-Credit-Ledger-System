# PA-HULUGAN: Neighborhood Micro-Installment & Credit Ledger System

A web-based management system designed to streamline tracking of micro-installments, credit records, and payments within a community or neighborhood setting.

## Features
* **User Management:** Register and manage lender and lendee profiles.
* **Transaction Ledger:** Track installment schedules and payment history.
* **Credit Monitoring:** Keep a transparent record of balances and due dates.
* **Responsive UI:** Designed for easy access on mobile and desktop devices.

## Tech Stack
* **Backend:** PHP
* **Database:** MySQL (via XAMPP)
* **Frontend:** HTML, CSS, JavaScript

## Installation & Setup
1.  **Clone the repository:**
    ```bash
    git clone https://github.com/nyel-101/PA-HULUGAN-A-Neighborhood-Micro-Installment-and-Credit-Ledger-System.git
    ```
2.  **Setup XAMPP:**
    * Move the project folder into your `htdocs` directory.
    * Start Apache and MySQL in XAMPP Control Panel.
3.  **Database Configuration:**
    * Open `phpMyAdmin` (usually `http://localhost/phpmyadmin`).
    * Create a new database and import the `database_schema.sql` file provided in the repository.
    * Update `config.php` with your database credentials.
4.  **Launch:**
    * Open your browser and navigate to `http://localhost/lending_system`.

## Usage
* Log in to manage lendee accounts, record new loans, and update payment status.
* Use the dashboard to view upcoming payments and overall ledger balance.

## License
This project is licensed under the **MIT License**.
