# Aura by Kiyara - Security Implementation Report

This document outlines the critical security features implemented in the "Aura by Kiyara" luxury perfume e-commerce website, built with Laravel 12. These features ensure the platform is robust, secure, and adheres to industry best practices, protecting both user data and system integrity.

---

## 1. SQL Injection Protection

### What is SQL Injection?
SQL Injection (SQLi) is a vulnerability where malicious users can manipulate input fields to execute unauthorized SQL queries on the database. This allows attackers to view, modify, or delete data they shouldn't have access to.

### Why is it Important?
Without SQLi protection, attackers could bypass authentication, steal customer data (like passwords and personal details), or corrupt the database.

### What Happens if NOT Implemented?
An attacker could type `' OR 1=1 --` into a login form to log in as an administrator without knowing the password, or run queries to drop tables.

### Risks and Attacks
- Unauthorized data access (data breach)
- Data loss or corruption
- Account hijacking

### How Laravel Protects Against It
Laravel's **Eloquent ORM** and **Query Builder** use PDO (PHP Data Objects) parameter binding behind the scenes. This ensures that user inputs are treated strictly as data (strings or integers) and not as executable SQL code. 

### Explanation of the Implemented Code
In `app/Http/Controllers/OrderController.php`, the vulnerable raw query for order items was replaced with an Eloquent approach. 

**Unsafe Version:**
```php
$orderItems = DB::select("SELECT * FROM order_items WHERE order_id = $id");
```
*Why it's unsafe: The `$id` variable is directly concatenated into the SQL string. A manipulated ID could execute additional SQL commands.*

**Protected Version:**
```php
$orderItems = \App\Models\OrderItem::where('order_id', $id)->get();
```
*Why it's secure: Laravel safely binds `$id` using PDO.*

### Real-World Example
On the "Aura by Kiyara" admin dashboard, if an administrator searches for a product or message, Eloquent securely filters the input. No matter what special characters a customer enters in their contact form message, it cannot break the database query.

### Advantages
- Completely prevents SQL Injection attacks.
- Code is cleaner, more readable, and easier to maintain.

---

## 2. CSRF Protection

### What is CSRF Protection?
Cross-Site Request Forgery (CSRF) is an attack that forces an authenticated user to execute unwanted actions on a web application in which they are currently authenticated.

### Why is it Important?
It ensures that the requests made to the server are intentionally made by the actual user and not by a malicious third-party site the user happens to visit.

### What Happens if NOT Implemented?
An attacker could trick a logged-in admin into clicking a link on a malicious site that secretly submits a form to delete a product or change an order status on the Aura by Kiyara website.

### Risks and Attacks
- Unauthorized state-changing requests (like modifying account details or placing orders).
- Exploitation of an active session.

### How Laravel Protects Against It
Laravel automatically generates a unique CSRF "token" for each active user session. This token must be included in any POST, PUT, PATCH, or DELETE requests. The `VerifyCsrfToken` middleware automatically verifies that the token in the request matches the token stored in the session.

### Explanation of the Implemented Code
In the checkout form (`resources/views/checkout.blade.php`) and all other forms across the website:
```blade
<form action="{{ route('place.order') }}" method="POST" id="checkout-form">
    @csrf
    <!-- Form Fields -->
</form>
```
The `@csrf` Blade directive generates a hidden HTML input field containing the valid token.

### Real-World Example
When a customer clicks "Place Order" for their perfume, the form includes the CSRF token. If an external site tries to submit a POST request to `/place-order` on the user's behalf, the request will fail with a `419 Page Expired` error because the external site does not have the valid token.

### Advantages
- Automatic, seamless integration.
- Protects all state-modifying routes out-of-the-box.

---

## 3. Password Hashing

### What is Password Hashing?
Hashing is the process of converting a plain-text password into a fixed-length string of unreadable characters using a cryptographic algorithm.

### Why is it Important?
If the database is compromised, hashed passwords cannot be easily read or reversed, protecting users' credentials.

### What Happens if NOT Implemented?
If passwords are stored in plain text, a database leak would immediately expose every customer's actual password, allowing attackers to access their accounts on this site and potentially others (since many people reuse passwords).

### Risks and Attacks
- Credential stuffing
- Full account compromise in the event of a data breach.

### How Laravel Protects Against It
Laravel uses the Bcrypt hashing algorithm by default. Bcrypt incorporates a "salt" (random data added to the password before hashing) to protect against rainbow table attacks and is computationally slow to deter brute-force attacks.

### Explanation of the Implemented Code
In `app/Actions/Fortify/CreateNewUser.php` during user registration:
```php
return User::create([
    'name' => $input['name'],
    'email' => $input['email'],
    'password' => Hash::make($input['password']),
]);
```
The `Hash::make()` function securely hashes the password before saving it to the `users` table.

### Why Hashed Passwords Cannot be Reversed
Hashing is a one-way mathematical function. Unlike encryption, there is no "decryption key." To verify a password during login, Laravel hashes the newly entered password and compares the resulting hash to the one stored in the database.

### Real-World Example
If a customer registers with the password `PerfumeLover123!`, the database stores a value like `$2y$10$w8...`. If the Aura database is breached, the attacker only sees the hash, keeping the user's password safe.

### Advantages
- Industry-standard security (Bcrypt/Argon2).
- Built-in protection against brute force and rainbow table attacks.

---

## 4. Middleware Protection

### What is Middleware?
Middleware acts as a filtering mechanism or a "bridge" between a request entering the application and the application itself. It inspects incoming HTTP requests before they hit the controller.

### Why is it Important?
It centralizes security logic, preventing unauthorized access to specific routes (URLs) without having to write authentication checks inside every single controller method.

### What Happens if NOT Implemented?
Anyone could type `/admin/dashboard` into their browser URL bar and access the restricted administrative panel, viewing customer orders and modifying products.

### Risks and Attacks
- Privilege escalation
- Exposure of sensitive business data
- Unauthorized modifications

### How Laravel Protects Against It
Laravel provides built-in middlewares like `auth` (ensures user is logged in) and allows custom middleware to be attached to route groups.

### Explanation of the Implemented Code
In `routes/web.php`, routes are wrapped in middleware groups:
```php
// Protecting Customer Routes
Route::middleware(['auth', 'verified', 'role:2', 'no_back_history'])->group(function () {
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::get('/dashboard', [OrderController::class, 'index'])->name('dashboard');
});

// Protecting Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'admin', 'no_back_history'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
});
```

### Real-World Example
If an unauthenticated user tries to visit `/checkout` to place a perfume order, the `auth` middleware intercepts the request and automatically redirects them to the `/login` page.

### Advantages
- Keeps controllers clean and focused on business logic.
- Enforces strict access control systematically across the application.

---

## 5. User Role Protection

### What is User Role Protection?
Role-Based Access Control (RBAC) restricts system access based on the roles assigned to users within the application (e.g., Admin, Customer, Guest).

### Why is it Important?
It ensures that while multiple types of users can log into the system, they can only perform actions and view data appropriate for their specific role.

### What Happens if NOT Implemented?
A logged-in customer could access administrative functions, such as deleting products, modifying prices, or viewing other customers' order details.

### Risks and Attacks
- Horizontal and Vertical Privilege Escalation.
- Business logic bypass.

### How Laravel Protects Against It
Laravel allows the creation of custom middleware to check a user's role attribute before granting access to a route.

### Explanation of the Implemented Code
1. **Database Update:** A `role` integer column was added to the `users` table via migration:
   ```php
   $table->integer('role')->default(2)->after('password'); 
   // 1 = Admin, 2 = Customer
   ```

2. **Middleware Implementation (`app/Http/Middleware/CheckRole.php`):**
   ```php
   public function handle(Request $request, Closure $next, $role): Response
   {
       if (!auth()->check()) {
           return redirect('login');
       }
       if (auth()->user()->role !== (int) $role) {
           abort(403, 'Unauthorized action.');
       }
       return $next($request);
   }
   ```

### Real-World Example
- **Guest:** A visitor browsing the site can view the luxury perfumes on the `/shop` page but cannot access `/checkout`.
- **Customer (Role 2):** Can log in, add items to their cart, proceed to checkout, and view their own "My Orders" page. If they try to access `/admin/products`, the system returns a `403 Forbidden` error.
- **Admin (Role 1):** Can access the secure `/admin/dashboard`, add new perfumes to the catalog, and update the status of customer orders.

### Advantages
- Enforces the Principle of Least Privilege.
- Secures the administrative boundary of the website.
- Provides a clear structure for extending user permissions in the future.
