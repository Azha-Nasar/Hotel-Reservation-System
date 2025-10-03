# 🏨 Hotel Reservation System

A comprehensive hotel booking and management system built with PHP and MySQL, featuring role-based access control, bulk booking capabilities, automated cancellation handling, and comprehensive reporting.

## ✨ Features

### For Guests
- **User Registration & Login** - Secure authentication with password hashing
- **Room Booking** - Book single rooms, double rooms, and suites
- **Residential Suites** - Weekly and monthly rental options
- **Additional Services** - Restaurant, room service, laundry, telephone, club facilities
- **Payment Options** - Credit card or cash on arrival
- **Reservation History** - View past and upcoming bookings
- **Automatic Cancellation Protection** - Reservations without payment are auto-cancelled at 7 PM on check-in date

### For Travel Agencies
- **Bulk Booking System** - Book multiple rooms for groups/tours
- **Discount Pricing** - 10% discount for 3+ rooms
- **Coupon Support** - Apply promotional codes (SUMMER5, WELCOME10)
- **Payment Plans** - Full credit, partial payment, or on-arrival options
- **Additional Services** - Daily breakfast, welcome packets, extra beds
- **Agency Dashboard** - Track bookings, revenue, and upcoming check-ins
- **Billing & Export** - Generate invoices and export booking data

### For Managers
- **Dashboard Analytics** - Real-time occupancy, revenue, and booking stats
- **Daily Reports** - View yesterday's revenue and today's new bookings
- **No-Show Tracking** - Monitor and manage no-show incidents
- **Room Management** - Track room availability and status
- **Activity Monitoring** - Recent bookings, checkouts, and payments
- **Alert System** - Notifications for critical events

### For Reservation Clerks
- **Booking Management** - Create and manage reservations
- **Guest Check-in/Check-out** - Process arrivals and departures
- **Payment Processing** - Handle payments and billing

## 🛠️ Technology Stack

- **Backend**: PHP 8.0+
- **Database**: MySQL (MariaDB 10.4+)
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **CSS Framework**: Bootstrap 5.3.3
- **Authentication**: Session-based with bcrypt password hashing

## 📋 Prerequisites

- PHP 8.0 or higher
- MySQL 5.7+ or MariaDB 10.4+
- Apache/Nginx web server
- phpMyAdmin (optional, for database management)

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/hotel-reservation-system.git
cd hotel-reservation-system
```

### 2. Database Setup

1. Create a new database named `hotel_db`
2. Import the database schema:

```bash
mysql -u root -p hotel_db < hotel_db.sql
```

Or use phpMyAdmin to import `hotel_db.sql`

### 3. Configure Database Connection

Edit `config.php` with your database credentials:

```php
$host = "localhost";
$user = "root";
$password = "your_password";
$database = "hotel_db";
```

### 4. Set Up Automated Cancellation (Optional)

For automatic cancellation of unpaid reservations, set up a cron job:

```bash
# Run daily at 7:00 PM
0 19 * * * /usr/bin/php /path/to/your/project/auto_cancel_unpaid.php
```

### 5. Configure File Permissions

```bash
chmod 755 logs/
chmod 644 config.php
```

## 🎯 Usage

### Default User Roles

After installation, you can register users with the following roles:
- **Guest** - Regular customers
- **Travel Agency** - Bulk booking access
- **Manager** - Full dashboard and reporting
- **Reservation Clerk** - Booking management

### Sample Coupon Codes

- `SUMMER5` - 5% discount
- `WELCOME10` - 10% discount

### Room Types & Pricing

| Room Type | Pricing | Capacity |
|-----------|---------|----------|
| Single Room | ₹10,000/night | 1 person |
| Double Room | ₹15,000/night | 2 people |
| Suite | ₹25,000/night | 2-4 people |
| Residential Suite | ₹150,000/week or ₹250,000/month | Long-term |

### Additional Services

- Restaurant Package: ₹5,000
- Room Service: ₹2,000
- Laundry Service: ₹1,500
- Telephone Service: ₹1,000
- Club Facility: ₹3,000

## 📁 Project Structure

```
hotel-reservation-system/
├── index.php                    # Login/Registration page
├── config.php                   # Database configuration
├── login_register.php           # Authentication handler
├── home.php                     # Homepage
├── room.php                     # Room listing
├── reservation.php              # Single booking
├── reservation_history.php      # Booking history
├── bulk_booking.php             # Bulk reservation
├── agency_dashboard.php         # Travel agency dashboard
├── agency_billing.php           # Billing & export
├── manager_page.php             # Manager dashboard
├── report.php                   # Manager reports
├── auto_cancel_unpaid.php       # Automated cancellation script
├── logout.php                   # Logout handler
├── style.css                    # Styles
├── script.js                    # JavaScript
├── hotel_db.sql                 # Database schema
└── logs/                        # Auto-cancel logs
```

## 🔐 Security Features

- Password hashing using bcrypt
- SQL injection prevention with prepared statements
- Session-based authentication
- Role-based access control
- XSS protection with htmlspecialchars()
- CSRF protection (recommended to add tokens)

## 📊 Database Schema

### Main Tables

- **guest** - User accounts and roles
- **reservations** - Single bookings
- **bulk_reservations** - Group/bulk bookings
- **rooms** - Room inventory
- **payments** - Payment records
- **bulk_payments** - Bulk booking payments
- **billing** - Billing records (no-shows, cancellations)
- **coupons** - Promotional discount codes

## 🐛 Known Issues & Limitations

1. Coupons are currently hardcoded in JavaScript (security concern)
2. No email notification system
3. Limited payment gateway integration
4. No real-time room availability calendar

## 🔄 Future Enhancements

- [ ] Integrate payment gateway (Stripe/PayPal)
- [ ] Email notifications (booking confirmations, reminders)
- [ ] SMS notifications
- [ ] Advanced search and filtering
- [ ] Calendar view for room availability
- [ ] Multi-currency support
- [ ] Review and rating system
- [ ] Mobile app
- [ ] API for third-party integrations

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 👥 Authors

**AFA Team**
- Azha Nasar 

## 📧 Contact

For questions or support, please contact:
- Email: azhanasar03@gmail.com
- Linkedin Profile : https://www.linkedin.com/in/azha-nasar-3a7ba2330
- Project Link: [https://github.com/yourusername/hotel-reservation-system](https://github.com/yourusername/hotel-reservation-system)


## 🙏 Acknowledgments

- Bootstrap for the UI framework
- PHP community for excellent documentation
- Contributors and testers

---

**⭐ If you found this project helpful, please give it a star!**
