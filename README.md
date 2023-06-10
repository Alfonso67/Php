# Php
# Room Reservation System

This is a simple room reservation system implemented using PHP and MySQL. It allows users to search for available rooms based on specified criteria, make bookings, and view their bookings. The system also includes user authentication to ensure that only logged-in users can access certain functionalities.

## Features

- User registration and login
- Search for available rooms based on name and date range
- Make room bookings
- View and manage bookings
- User authentication and access control

## Installation

1. Clone the repository to your local machine.
2. Import the provided SQL file (`room_reservation.sql`) into your MySQL database.
3. Update the database configuration in the `config.php` file with your MySQL credentials.
4. Upload the entire project to your web server.

## Usage

1. Access the application through your web browser.
2. Register a new user account if you don't have one.
3. Log in using your credentials.
4. Use the search feature to find available rooms by name and date range.
5. Make a booking by selecting a room and providing the necessary details.
6. View and manage your bookings in the "My Bookings" section.
7. Log out when you're done.
8. for acces to the dashoboard the credential are: username user
                pass:administrator

## File Structure

- `index.php`: The main entry point of the application. Displays the homepage and allows users to navigate to other sections.
- `login.php`: Handles user login functionality.
- `logout.php`: Logs out the currently logged-in user.
- `registration.php`: Handles user registration functionality.
- `search_bookings.php`: Allows users to search for available rooms based on name and date range.
- `view_bookings.php`: Displays the user's bookings and provides options to update or delete them.
- `config.php`: Contains the database configuration settings.
- `css/`: Directory containing CSS stylesheets.
- 'create_booking.php': allows admin users to make a reservation.
- 'dashboard.php': allow the admin users to manage the system
- 'manage_users.php': allow the admin users to manage registration users
- 'calendar.php' : is the same to index.php but is displayed in dashboard.php for diplaying the date of book.

## Dependencies

- PHP (minimum version 7.0)
- MySQL

## Contributing

Contributions to the Room Reservation System are welcome. If you find any issues or have suggestions for improvements, please submit an issue or a pull request.

Credits

This project was developed by Alfonso Verme. If you have any questions or suggestions, please contact me at alfo.verme67@gmail.com.

## License

The Room Reservation System is open-source and released under the [MIT License](LICENSE).


