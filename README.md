# MTPE BANK by PHAM MINH THAO

## Introduction

MTPE_BANK is an online banking project developed using PHP, CSS, JavaScript, and Hack. This project aims to provide the fundamental functionalities of an online banking system, including account management, transactions, and security, with a focus on delivering a seamless and secure user experience.

Watch our introductory video here: [MTPE_BANK Introduction Video](https://www.youtube.com/watch?v=3zBvXEDJb-w)
<video src="WEBSITE_MTPE BANK_Sản_phẩm_học_viên_ngành_Software_Engineering _ VTC_Academy.mp4" controls>
   MTPE_BANK Introduction Video
</video>

## Features

- **User Account Management**: Create, update, and manage user accounts with ease.
- **Money Transfer Transactions**: Securely transfer funds between accounts.
- **Transaction History Viewing**: Review past transactions with detailed logs.
- **User Information Security**: Robust encryption and security measures to protect user data.
- **Responsive Design**: Accessible on both desktop and mobile devices.
- **Multi-Language Support**: Interface available in multiple languages for broader accessibility.

## Prerequisites

Before setting up the project, ensure you have the following installed:
- PHP (>= 7.4)
- Composer
- A web server (e.g., Apache, Nginx, or PHP's built-in server)
- MySQL or another compatible database system
- Git

## Installation

1. **Fork the Repository**:
   - Navigate to [MTPE_BANK on GitHub](https://github.com/Trunks-Pham/MTPE_BANK) and click the "Fork" button in the upper right corner to create a copy of this repository in your GitHub account.

2. **Clone the Repository**:
   - Clone the repository to your local machine using the following command:
     ```bash
     git clone https://github.com/Trunks-Pham/MTPE_BANK.git
     ```

3. **Install Dependencies**:
   - Ensure you have PHP and Composer installed.
   - Navigate to the project directory and run:
     ```bash
     composer install
     ```

4. **Configure the Database**:
   - Create a new database in your MySQL or preferred database system.
   - Import the provided SQL schema (if available) or create tables manually.
   - Update the database connection details in the `config.php` file, including:
     - Hostname
     - Database name
     - Username
     - Password

5. **Start the Server**:
   - Use a web server like Apache or Nginx, or start the PHP built-in server:
     ```bash
     php -S localhost:8000
     ```

6. **Verify Setup**:
   - Ensure all dependencies are correctly installed and the database is connected.
   - Check for any error logs in the console or server logs.

## Usage

- **Access the Application**:
  - Open your browser and navigate to `http://localhost:8000` to start using the application.
- **Account Setup**:
  - Register a new account or log in with an existing account.
- **Core Functions**:
  - Transfer money between accounts.
  - View and filter transaction history.
  - Update personal information securely.
- **Security Tips**:
  - Use strong, unique passwords.
  - Log out after each session to protect your account.

## Screenshots

Below are key visuals of the MTPE_BANK project:
- **Main Interface**:
  ![MTPE_BANK Main Interface](MTPE-BANK.png)

## Contributing

We welcome contributions from the community! To contribute:
1. **Fork the Repository**:
   - Create a copy of this repository in your GitHub account.
2. **Make Changes**:
   - Create a new branch for your feature or bug fix:
     ```bash
     git checkout -b feature/your-feature-name
     ```
3. **Commit Changes**:
   - Commit your changes with a descriptive message:
     ```bash
     git commit -m "Add your descriptive message here"
     ```
4. **Push to GitHub**:
   - Push your branch to your forked repository:
     ```bash
     git push origin feature/your-feature-name
     ```
5. **Submit a Pull Request**:
   - Navigate to the original repository and submit a pull request. All contributions will be thoroughly reviewed.

## Code of Conduct

- Be respectful and inclusive in all interactions.
- Follow best practices for coding, documentation, and testing.
- Report any issues or bugs via GitHub Issues.

## Contact

- **Author**: Pham Minh Thao
- **GitHub**: [Trunks-Pham](https://github.com/Trunks-Pham)
- **Email**: For inquiries, please open an issue on GitHub.

## Acknowledgments

- Thanks to the open-source community for providing tools and libraries.
- Special appreciation to contributors and testers of the MTPE_BANK project.
