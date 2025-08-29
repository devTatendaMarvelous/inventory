# Inventory Management System

**Created by:** Tatenda Marvelous Chimusoro

---

## How to Run the Application

### A. Backend Setup (Laravel)

1. **Clone the repository:**
   ```
   git clone https://github.com/devTatendaMarvelous/inventory.git
2. **Go to the backend directory:**
   ```
   cd inventory
3. **Go to the backend directory:**
   ```
   cd backend
4. **Install dependencies:**

```
composer install 
```

5. **Copy the example environment file and update settings:**
   ``` 
   cp .env.example .env
6. **Generate the application key:**

  ```
php artisan key:generate
```

7. **Set up your database and update `.env` with credentials.**
8. **Run the setup command:**
   ```
   php artisan setup
9. **Start the development server:**
   ```
   php artisan serve
10. **Login using default admin credentials:**
    - Email: `admin@inventory.com`
    - Password: `password`
11. **Configure the Scheduler cron job for stock reports and low stock alerts:**
    ```
    php artisan schedule:run
    ```

---

### B. Frontend Setup (React)

1. **Navigate to the frontend directory:**

```
   cd ../frontend
   ```

2. Install the dependencies using npm or yarn:

 ```  
npm install
   or
   yarn install
   ```

3. Start the development server:
 ```  
npm start
or
yarn start
   ```
4. The frontend application will be accessible at http://localhost:5173


### C. Additional Notes
- Postman collection documenting all API endpoints is available here:
- https://documenter.getpostman.com/view/21277108/2sB3HhrMMr
- To create a new user run:
- ```
  php artisan user:stock
