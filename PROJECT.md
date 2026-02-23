You are a senior Laravel 12 architect and full-stack developer.

Build a SIMPLE, CLEAN, and PRODUCTION-READY GST-based Inventory & Billing System using Laravel 12.

### TECH STACK & CONSTRAINTS
- Laravel version: 12
- PHP: latest compatible version
- Database: MySQL
- ORM: Eloquent (use scopes wherever reuse is possible)
- Authentication: Laravel built-in login system (email + password)
- UI: Very simple and clean (Blade + Tailwind or minimal CSS)
- Packages already installed:
  - spatie/laravel-pdf (for invoice PDF generation)
  - spatie/laravel-medialibrary (for company logo upload)

### CORE MODULES TO BUILD

#### 1. AUTHENTICATION
- Login & Logout
- Middleware protected routes
- Only authenticated users can access the system

#### 2. DASHBOARD
- Overview cards:
  - Total Products
  - Total Customers
  - Total Invoices
  - Total Sales Amount (GST included)
- Recent invoices list (last 5)

#### 3. COMPANY SETTINGS
- Company name
- GST Number
- Address
- Phone & Email
- Upload Company Logo (using Spatie Media Library)
- Logo must appear on invoice PDF

#### 4. CUSTOMER MANAGEMENT
- Customers table:
  - Name
  - Phone
  - Email
  - Address
  - GST Number (optional)
- Customer should be:
  - Automatically created if not existing when invoice is generated
- Use Eloquent scope for:
  - Searching customer by phone/email

#### 5. PRODUCT / INVENTORY MANAGEMENT
- Product fields:
  - Name
  - SKU
  - Price (without GST)
  - GST Rate (%)
  - Stock Quantity
- Features:
  - Add / Edit / Delete product
  - Stock should automatically reduce when product is sold
- Eloquent scopes:
  - scopeAvailableStock()
  - scopeLowStock()

#### 6. INVOICE MANAGEMENT
- Create GST Invoice with:
  - Invoice Number (auto-generated)
  - Invoice Date
  - Customer Details
  - Product list (multiple items)
  - Quantity
  - Price
  - GST Rate per product
  - CGST / SGST calculation
  - Subtotal
  - Discount (flat or percentage)
  - Final Payable Amount
  - Payment Mode (Cash / UPI / Card / Bank)
  - Payment Description / Notes

- On invoice save:
  - Deduct stock automatically
  - Store customer if new

#### 7. GST CALCULATION LOGIC
- GST should be calculated per item
- Support:
  - CGST + SGST split
- Show:
  - Taxable Amount
  - GST Amount
  - Total Amount

#### 8. PDF GENERATION
- Generate proper GST Invoice PDF using Spatie PDF
- PDF must include:
  - Company Logo
  - Company Details
  - GST Number
  - Invoice Number & Date
  - Customer Details
  - Product Table
  - GST breakup
  - Discount
  - Grand Total
  - Payment Mode
- Clean, printable layout (A4)

#### 9. DATABASE DESIGN
- Proper migrations for:
  - users
  - companies
  - customers
  - products
  - invoices
  - invoice_items
- Use foreign keys and indexing

#### 10. CODE QUALITY REQUIREMENTS
- Use:
  - Form Requests for validation
  - Service classes for invoice calculation logic
  - Eloquent scopes for reuse
- Keep controllers thin
- Follow Laravel best practices
- Simple naming, readable code

### OUTPUT EXPECTATIONS
- Provide:
  - Folder structure
  - Database schema
  - Models with relationships and scopes
  - Controllers
  - Service class for GST calculation
  - Blade views (simple UI)
  - PDF view template
- Do NOT over-engineer
- Focus on correctness of GST & PDF output

### IMPORTANT
- Keep UI minimal and usable
- PDF generation must be accurate and professional
- System should be easy to extend later

Start with database design, then models, then services, then controllers, then views.
