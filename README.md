# 🏥 Integrated Queue Management System (Clinic & Hospital)

A modern **Queue Management System** built with **Laravel, Vue 3, and Inertia.js**. This application is designed to provide a seamless and efficient patient queue experience through **real-time updates**, **automatic voice announcements (Text-to-Speech)**, an **interactive TV display**, and a **dynamic staff dashboard**.

---

# 📸 Screenshots

<p align="center">
  <b>Modern Queue Management System</b><br>
  Built with Laravel, Vue 3, Inertia.js, PostgreSQL, and Tailwind CSS
</p>

---

## 🔐 Authentication

### Login

<p align="center">
  <img src="https://github.com/user-attachments/assets/2881e8fd-6b58-4e66-aa3b-c70ad8651c51" width="100%" alt="Login Page">
</p>

> Secure authentication for administrators and staff.

---

## 🎟️ Queue Management

### Take Queue

<p align="center">
  <img src="https://github.com/user-attachments/assets/7e12628b-9792-4e68-8df7-b8586c96ca47" width="100%" alt="Take Queue">
</p>

> Patients can take queue numbers quickly with an intuitive interface.

---

## 📊 Dashboard

<p align="center">
  <img src="YOUR_DASHBOARD_IMAGE" width="100%" alt="Dashboard">
</p>

> Monitor queue statistics, active counters, and today's service performance in real time.

---

## 👥 User Management

<p align="center">
  <img src="YOUR_MANAGE_USERS_IMAGE" width="100%" alt="Manage Users">
</p>

> Manage administrators, staff, and user roles with ease.

---

## 🏥 Poli Management

<p align="center">
  <img src="YOUR_MANAGE_POLI_IMAGE" width="100%" alt="Manage Poli">
</p>

> Create and manage departments, queue prefixes, and service configurations.

---

## ⚙️ Application Settings

<p align="center">
  <img src="YOUR_SETTINGS_IMAGE" width="100%" alt="Settings">
</p>

> Configure system preferences, queue behavior, and application settings.

---

## 📺 Realtime Queue Display

<p align="center">
  <img src="YOUR_DISPLAY_QUEUE_IMAGE" width="100%" alt="Realtime Queue Display">
</p>

> Large-screen display showing the current queue, next queue, announcements, and automatic voice calling.

---


# ✨ Features

### 📺 Real-Time TV Display

* Displays the currently called queue number in real time using **WebSocket (Laravel Reverb & Laravel Echo)**.
* Supports **YouTube embedded videos** for educational content or promotional media.
* Includes a customizable **running text (marquee)** for announcements or important information.

### 🔊 Automatic Voice Calling (Text-to-Speech)

* Automatically announces queue numbers using the browser's built-in **Text-to-Speech (TTS)** technology.
* Clear and responsive voice notifications for patients.

### 👨‍⚕️ Staff Dashboard

* Real-time queue monitoring.
* Call the next patient.
* Recall the current queue number.
* Skip unavailable patients.
* Display the remaining number of waiting patients.

### ⚙️ Dynamic System Settings

Administrators can configure the application directly from the dashboard without modifying the source code.

Configurable settings include:

* Clinic/Hospital Name
* Logo
* Running Text
* Educational Video URL (YouTube)
* Other general system information

### 📱 Responsive User Interface

* Modern and clean UI built with **Tailwind CSS**.
* Fully responsive for desktop, tablet, and mobile devices.

---

# 🛠️ Technology Stack

| Layer      | Technology                    |
| ---------- | ----------------------------- |
| Backend    | Laravel 11                    |
| Frontend   | Vue.js 3 (Composition API)    |
| SPA Bridge | Inertia.js                    |
| Styling    | Tailwind CSS                  |
| Real-Time  | Laravel Reverb + Laravel Echo |
| Icons      | Lucide Icons                  |
| Alerts     | SweetAlert2                   |
| Database   | MySQL / PostgreSQL            |

---

# 🚀 System Requirements

Before running this project, make sure your environment has the following installed:

* PHP **8.2** or later
* Composer
* Node.js & npm
* MySQL or PostgreSQL
* Git

---

# 📦 Installation

Clone the repository:

```bash
git clone https://github.com/HRitsFadhila/sistem-antrian.git
```

Go to the project directory:

```bash
cd sistem-antrian
```

Install PHP dependencies:

```bash
composer install
```

Install JavaScript dependencies:

```bash
npm install
```

Copy the environment file:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Configure your database in the `.env` file.

Run the database migrations:

```bash
php artisan migrate
```

(Optional) Seed sample data:

```bash
php artisan db:seed
```

Build frontend assets:

```bash
npm run dev
```

Start the Laravel development server:

```bash
php artisan serve
```

---

# 📡 Running the WebSocket Server

Start Laravel Reverb:

```bash
php artisan reverb:start
```

If using queue workers:

```bash
php artisan queue:work
```

---

# 📂 Project Structure

```
app/
bootstrap/
config/
database/
public/
resources/
 ├── js/
 │   ├── Components/
 │   ├── Layouts/
 │   ├── Pages/
 │   └── app.js
routes/
storage/
```

---

# 👥 User Roles

### Administrator

* Manage system settings
* Manage departments (Polyclinics)
* Manage users
* Configure TV display
* Manage educational videos
* Monitor queue statistics

### Staff

* Call the next queue number
* Recall patients
* Skip queue numbers
* View remaining queues

### Patient

* Take a queue number
* Wait for queue announcements
* Monitor queue status on the TV display

---

# 🔄 Queue Workflow

1. Patient selects a department (polyclinic).
2. The system generates a queue number.
3. The queue number appears in the waiting list.
4. Staff calls the next patient.
5. The TV display updates instantly through WebSocket.
6. The system announces the queue number using Text-to-Speech.
7. The patient proceeds to the designated service counter or examination room.

---

# 📸 Main Modules

* Authentication
* Dashboard
* Queue Management
* Department Management
* TV Display
* Queue History
* User Management
* System Settings

---

# 📄 License

This project is released under the **MIT License**.

---

# 👨‍💻 Author

**Muhammad Harits Fadhila**

If you find this project helpful, consider giving it a ⭐ on GitHub.
