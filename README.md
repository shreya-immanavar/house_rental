Project Title

Luxe Rental System – Multipurpose Rental Management Platform

Introduction

Luxe Rental System is a web-based application developed to simplify the process of renting and managing different types of properties and vehicles. The system allows users to browse available rentals, view details, add items to a wishlist, book properties, and communicate with owners. Administrators can manage listings, users, bookings, and rental information through an admin panel.

Objectives
Provide a centralized rental platform.
Allow users to search and book rentals easily.
Manage houses, apartments, PGs/hostels, vehicles, and commercial spaces.
Enable administrators to add, edit, and remove listings.
Maintain booking and user records efficiently.
Features
User Features
User Registration and Login
Browse Rental Listings
Search and Filter Properties
View Property Details
Add to Wishlist
Book Rentals
Send Messages
View Booking Status
Admin Features
Admin Login
Add New Properties
Upload Property Images
Manage Users
Manage Bookings
Approve or Reject Requests
Manage Rental Listings
Property Categories
House

Independent houses and villas for families.

Apartment

1BHK, 2BHK, and 3BHK apartments.

PG/Hostel

Accommodation for students and working professionals.

Vehicle

Cars, scooters, vans, and travel vehicles for rent.

Commercial Space

Offices, shops, and business spaces.

Technology Stack
Frontend
HTML
CSS
JavaScript
Backend
PHP
Database
MySQL
Development Environment
XAMPP
Deployment
InfinityFree Hosting
Database Tables
Users

Stores user and admin information.

Fields:

id
name
email
password
role
Properties

Stores rental property details.

Fields:

id
title
category
location
price
description
tags
Property Images

Stores multiple images for each property.

Fields:

id
property_id
image
Bookings

Stores booking requests.

Fields:

id
user_id
property_id
tenant_phone
move_in_date
end_date
occupants
notes
status
booking_date
Wishlist

Stores favorite properties.

Fields:

id
user_id
property_id
created_at
Messages

Stores communication between users and owners.

Fields:

id
sender_id
receiver_id
property_id
message
is_read
created_at
Workflow
User registers and logs in.
User browses available rentals.
User views detailed information.
User adds favorites to wishlist.
User submits booking request.
Admin reviews request.
Admin approves or rejects booking.
User receives booking status.
Advantages
Easy rental management.
User-friendly interface.
Supports multiple rental categories.
Online booking system.
Centralized database management.
Time-saving and efficient.
Future Enhancements
Online payment gateway integration.
Google Maps location support.
Email and SMS notifications.
AI-based property recommendations.
Mobile application development.
Review and rating system.
Conclusion

The Luxe Rental System provides an efficient and convenient platform for managing rentals across multiple categories including houses, apartments, PGs, vehicles, and commercial spaces. The system simplifies the rental process for both users and administrators while maintaining accurate records and improving accessibility through a web-based interface.


##AUTHOR
->shreya immannavar

##DEPLOYRD LINK
-->   https://luxerentalsystem.rf.gd
