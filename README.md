===============================================================================
                            LARAVEL USER MANAGEMENT
===============================================================================

A sample Laravel application demonstrating role-based user management, 
including viewing a list of users and deleting them with a confirmation modal. 
Only admin users can access user management features.

===============================================================================
1. OVERVIEW
===============================================================================

This project illustrates a basic user-management flow in Laravel:
- Admins see a list of all users.
- Admins can delete any user, with a Bootstrap confirmation modal for safety.

===============================================================================
2. FEATURES
===============================================================================

• **Role-Based Access Control**  
  Restricts user-management routes to admin users only.

• **List All Users**  
  Displays each user's ID, email, and assigned roles.

• **Delete Users**  
  Allows an admin to delete a user via a confirmation modal.

===============================================================================
3. REQUIREMENTS
===============================================================================

• PHP >= 8.0  
• Composer  
• Laravel >= 9.x  
• A Database (MySQL, PostgreSQL, etc.)  
• Node.js & npm (only if you're compiling front-end assets)

===============================================================================
4. INSTALLATION
===============================================================================

1. **Clone the repository**  
   git clone https://github.com/your-username/your-repo.git  
   cd your-repo

2. **Install PHP dependencies**  
   composer install

3. **Copy and configure environment**  
   cp .env.example .env  
   php artisan key:generate  
   (Update your .env with database credentials)

4. **Run migrations**  
   php artisan migrate

===============================================================================
5. CONFIGURATION
===============================================================================

• Ensure you have a roles system in place (e.g. spatie/laravel-permission).  
• Assign the "admin" role to any user who should manage others.  
  Example (spatie package):
      php artisan permission:create-role admin
      php artisan permission:create-role user
      // ...
      $user->assignRole('admin');

===============================================================================
6. RUNNING THE PROJECT
===============================================================================

Use the Laravel development server:

    php artisan serve

Access the app at http://127.0.0.1:8000 (by default).

===============================================================================
7. ROUTES
===============================================================================

In routes/web.php:

    Route::middleware('auth')->group(function () {
        Route::get('/users', [
            \App\Http\Controllers\Admin\UserController::class, 
            'index'
        ])->name('users.index');

        Route::delete('/users/{id}', [
            \App\Http\Controllers\Admin\UserController::class, 
            'destroy'
        ])->name('users.destroy');
    });

===============================================================================
8. USAGE
===============================================================================

• **Log in** as an admin.  
• **Navigate** to /users to see all users.  
• **Delete** a user:
  - Click the "Delete" button.
  - Confirm the deletion in the Bootstrap modal.
  - A success or error message will appear.

===============================================================================
9. PROJECT STRUCTURE
===============================================================================

app/
 └── Http/
     └── Controllers/
         └── Admin/
             └── UserController.php   (Manages listing/deletion of users)
 └── Models/
     └── User.php                     (Eloquent user model)
 └── Services/
     └── Admin/
         └── UserService.php         (Business logic: getUsers, deleteUser)
resources/
 └── views/
     └── layouts/
         └── dashboard-layout.blade.php (Main layout w/ optional @stack('scripts'))
     └── admin/
         └── users.blade.php            (User list, delete modal)
routes/
 └── web.php                            (Definitions for users.index, users.destroy)

===============================================================================
10. LICENSE
===============================================================================

This project is available under the MIT License. You can adapt and modify it 
as needed. See the LICENSE file for more information.

===============================================================================
CONTRIBUTIONS
===============================================================================

Contributions, issue reports, and feature suggestions are welcome! 
- Fork the repository.
- Make your changes.
- Open a pull request for review.

Feel free to open an issue if you have questions or suggestions.
