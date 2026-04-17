# CSI EduAid Scholarship Management System

!CSI EduAid 

##  Description

**CSI EduAid** is a full-featured web-based scholarship management system designed to support talented **Chin students** from Myanmar and the diaspora. 

The system allows students to register, login, and submit scholarship applications with required documents. Administrators can efficiently review applications, approve or reject them, award scholarships, manage contact messages, and generate reports.

This project was developed as a **Final Year Bachelor's Project** to solve real-world challenges faced by Chin youth in accessing higher education.

##  Technologies Used

- **Backend**: PHP 8
- **Database**: MySQL (via PDO)
- **Frontend**: HTML5, CSS3, Bootstrap 5.3
- **Icons**: Bootstrap Icons
- **Animations**: AOS (Animate On Scroll)
- **Server**: XAMPP (Apache + MySQL)
- **Security**: Password hashing (`password_hash`), prepared statements, CSRF protection

##  Key Features

### For Students
- Secure Student Registration & Login
- Scholarship Application Form with file uploads (Photo, Marks Sheet, Certificate)
- View Application Status on Dashboard
- Download Official Scholarship Award Letter (if approved)
- Account Settings (Change Password & Delete Account)

### For Administrators
- Secure Admin Login
- Dashboard with statistics and recent applications
- Manage All Applicants (Search, Filter, Sort)
- Update Application Status (Approve / Reject with reason)
- Award Scholarships (Amount + Type: Full / Half / Partial)
- Contact Messages Management (Mark as Read, Delete)
- Selected Students & Report Page with CSV Export
- Admin Settings (Change Password)

### Public Pages
- Home (with animated stats and testimonials)
- About Us
- Programs (Scholarships, Mentorship, Workshops)
- Application Requirements
- Explore International Scholarships (with filters)
- Events
- Contact Form (saves to database)

##  How to Run the Project

### Prerequisites
- XAMPP (or any PHP + MySQL server)
- Web Browser

### Installation Steps

1. **Download / Clone** the project folder.
2. Move the entire `CSI_EduAid` folder into your XAMPP `htdocs` directory.
3. Start **Apache** and **MySQL** from the XAMPP Control Panel.
4. Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
5. Create a new database named **`csi_eduaid`**.
6. Import the database file:
   - Go to `CSI_EduAid/sql/csi_eduaid.sql`
   - Import it into the `csi_eduaid` database.
7. Open your browser and go to:

   
### Default Admin Login
- **Username**: `admin`
- **Password**: `admin` (or the password you set)

### Student Login
Students can register a new account or login after submitting an application.
