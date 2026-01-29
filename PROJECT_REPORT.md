<!-- 
Final Project Report 
DEPARTMENT: Computer Software Engineering (BIT)  
PREPARED BY: Lotanna Otikpo 
STUDENT ID: 01025117026DE

Project Title 
Inventory and Sales Tracking System 
Background of studies 
Efficient inventory management is the backbone of successful retail businesses. It involves overseeing the flow of goods from manufacturers to warehouses and from these facilities to point of sale. However, many small and medium-sized enterprises (SMEs) in Nigeria still rely on manual methods such as notebooks, paper ledgers, and basic spreadsheets to manage stock and record sales. These methods are often inefficient, leading to discrepancies between physical stock and recorded figures.
This project focuses on developing an Inventory and Sales Tracking System using the Laravel framework to digitize these operations, ensuring real-time tracking of products, accurate sales recording, and automated reporting.
Problem Statement 
Many small businesses manage inventory and sales manually using notebooks, which is inefficient and prone to errors. Manual record-keeping leads to inaccurate stock levels, loss of sales records, difficulty in tracking profits, and a lack of data security. There is a need for a modern, digital solution that automates these processes to prevent revenue loss and operational bottlenecks.
Aim and Objectives 
Aim: 
To design and implement a web-based Inventory and Sales Tracking System using Laravel for Blotanna Nigeria Limited.

Objectives: 
 To automate product registration and stock level management.
 To provide a Point of Sale (POS) interface for fast and accurate transaction recording.
 To track sales history and generate digital receipts/invoices.
 To provide visual dashboards for monitoring business performance (Revenue, Low Stock).
 To apply practical skills in database design, CRUD operations, and authentication.

The project covers: 
 User authentication (Admin access)
 Inventory Management (CRUD and Stock alerts)
 Point of Sale (POS) functionality
 Invoicing and Reporting
Literature Review 
Studies on Small Business Information Systems indicate that shifting from manual to automated systems increases operational efficiency by over 40%. Existing manual systems at Blotanna Nigeria Limited result in data redundancy and slow checkout times. Frameworks like Laravel offer a robust structure (MVC) for building secure business applications, handling complex logic like stock deduction and financial calculations efficiently compared to raw PHP or manual spreadsheets.
Proposed Solution 
The proposed solution is a Digital Inventory and Sales Tracking System developed using Laravel. It allows the business to record products digitally, track sales in real-time, generate professional invoices, and monitor stock levels to prevent stockouts. It replaces physical notebooks with a secure, centralized database.
Framework and Tools Used 
Framework: 
 Laravel 10 (PHP Framework) 
Frontend Technologies: 
 HTML5 
 Tailwind CSS (for styling)
 Alpine.js (for interactivity)
 Blade Templating Engine 
Backend Technologies: 
 PHP 8.1+
 Laravel Controllers and Eloquent Models 
Database: 
 MySQL 
Development Tools: 
 Visual Studio Code 
 Git and GitHub 
 Composer & NPM
System Architecture 
The system follows the Model–View–Controller (MVC) architecture: 
 Model: Represents the data structure (Products, Sales, Invoices) and business rules.
 View: The user interface built with Blade and Tailwind CSS that displays data to the staff.
 Controller: Handles user input (e.g., processing a sale), interacts with the Model, and returns the appropriate View.
This architecture ensures separation of concerns, making the application easier to maintain and scale.
System Implementation 
Backend Implementation 
The backend was implemented using Laravel routes to handle URL requests, Controllers (ProductController, SaleController, InvoiceController) to process logic, and Migrations to structure the MySQL database. Key features like stock deduction upon sale and password hashing were implemented here.
Frontend Implementation 
The frontend was designed using Tailwind CSS for a responsive, modern layout. Blade templates were used to render dynamic data, and Alpine.js was utilized for the dynamic Point of Sale (POS) cart interface to allow adding items without page reloads.
System Testing And Results 
The system was tested to ensure that adding a product updates the database, processing a sale correctly deducts stock, and invoices are generated accurately. Test results confirmed that the application effectively solves the problem of manual record-keeping, providing Blotanna Nigeria Limited with an accurate, digital method for tracking business activities. 
-->
