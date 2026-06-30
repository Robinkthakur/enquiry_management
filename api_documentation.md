# Student Mobile API Documentation

This document describes the REST API endpoints available for the Student Mobile Application.

## Global Settings

- **Base URL**: `http://<your-server-ip>/api`
- **Request Headers**:
  - `Accept: application/json`
  - `Content-Type: application/json` (except for file uploads, which use `multipart/form-data`)
  - `Authorization: Bearer <your-api-token>` (for authenticated endpoints)

---

## Authentication Endpoints

### 1. Student Account Registration
Create a new student account (admission and user record) and return a Sanctum access token.

- **URL**: `/register`
- **Method**: `POST`
- **Auth Required**: No
- **Request Body (JSON)**:
```json
{
  "name": "Alice Smith",
  "email": "alice.smith@example.com",
  "phone": "1234567890",
  "password": "newpassword123"
}
```
- **Response (201 Created)**:
```json
{
  "success": true,
  "message": "Account created successfully.",
  "token": "1|abcdef1234567890...",
  "user": {
    "id": "019ef851-bc29-7c2c-8068-15c2ec33ba21",
    "name": "Alice Smith",
    "email": "alice.smith@example.com"
  },
  "student": {
    "id": "019ef851-bc2e-7cb3-8a39-df2df1207e99",
    "admission_no": "ADM-2026-00002",
    "student_name": "Alice Smith",
    "mobile": "1234567890",
    "email": "alice.smith@example.com",
    "photo_url": null
  }
}
```
- **Response (422 Unprocessable Content)** (Validation errors):
```json
{
  "success": false,
  "errors": {
    "email": [
      "The email has already been taken."
    ]
  }
}
```

### 2. Student Login
Authenticate a student and return a Sanctum access token along with basic profile info.

- **URL**: `/login`
- **Method**: `POST`
- **Auth Required**: No
- **Request Body (JSON)**:
```json
{
  "email": "john.doe@example.com",
  "password": "password"
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "token": "1|abcdef1234567890...",
  "user": {
    "id": "019ef851-bc29-7c2c-8068-15c2ec33ba21",
    "name": "John Doe",
    "email": "john.doe@example.com"
  },
  "student": {
    "id": "019ef851-bc2e-7cb3-8a39-df2df1207e99",
    "admission_no": "ADM-2026-00001",
    "student_name": "John Doe",
    "mobile": "+15550199",
    "email": "john.doe@example.com",
    "photo_url": "http://localhost/api/student-photo/student_photos/avatar.jpg?expires=1782297800&signature=abcdef1234567890..."
  }
}
```
- **Response (401 Unauthorized)**:
```json
{
  "success": false,
  "message": "Invalid email or password."
}
```
- **Response (403 Forbidden)** (Non-student account):
```json
{
  "success": false,
  "message": "Access denied. Only student accounts can access this panel."
}
```

### 3. Logout
Revoke the student's active token.

- **URL**: `/logout`
- **Method**: `POST`
- **Auth Required**: Yes
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "Successfully logged out."
}
```

### 4. Student Photo Retrieval
Retrieve the student's profile photo securely. This route requires a valid signature generated dynamically by the API.

- **URL**: `/student-photo/{path}`
- **Method**: `GET`
- **Auth Required**: No (but URL must contain a valid cryptographic signature and expiration timestamp)
- **Response (200 OK)**: File response (image content)
- **Response (403 Forbidden)**: Invalid or expired URL signature

---

## Student Profile Endpoints

### 5. Retrieve Profile Details
Retrieve detailed personal information, profile photo, and uploaded admission documents.

- **URL**: `/profile`
- **Method**: `GET`
- **Auth Required**: Yes
- **Response (200 OK)**:
```json
{
  "success": true,
  "profile": {
    "admission_no": "ADM-2026-00001",
    "student_name": "John Doe",
    "father_name": "Richard Doe",
    "mobile": "+15550199",
    "email": "john.doe@example.com",
    "address": "123 Tech Lane, Silicon Valley",
    "admission_date": "2026-06-03",
    "status": "Active",
    "photo_url": "http://localhost/api/student-photo/student_photos/avatar.jpg?expires=1782297800&signature=abcdef1234567890...",
    "documents": [
      {
        "filename": "id_card.pdf",
        "url": "http://localhost/admin/student-documents/student_documents/id_card.pdf"
      }
    ]
  }
}
```

### 6. Update Profile
Update the student's email and profile photo.

- **URL**: `/profile/update`
- **Method**: `POST`
- **Auth Required**: Yes
- **Content-Type**: `multipart/form-data`
- **Request Body (Form Data)**:
  - `email`: `updated.email@example.com` (required, unique email)
  - `student_photo`: `[File upload - Image]` (optional, max 2MB)
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "Profile updated successfully.",
  "profile": {
    "email": "updated.email@example.com",
    "photo_url": "http://localhost/api/student-photo/student_photos/new_avatar.jpg?expires=1782297800&signature=abcdef1234567890..."
  }
}
```

---

## Courses & Self-Enrollment Endpoints

### 7. List Active Courses
Fetch active courses available for self-enrollment in the institute.

- **URL**: `/courses`
- **Method**: `GET`
- **Auth Required**: Yes
- **Response (200 OK)**:
```json
{
  "success": true,
  "courses": [
    {
      "id": "019ef851-bc29-7c2c-8068-15c2ec33ba21",
      "course_code": "FSWD",
      "course_name": "Full Stack Web Development",
      "description": "Comprehensive training in HTML, CSS, JavaScript, Laravel.",
      "duration_months": 6,
      "total_fee": 1500,
      "registration_fee": 150
    }
  ]
}
```

### 8. List My Enrollments
Fetch courses currently enrolled in by the student.

- **URL**: `/enrollments`
- **Method**: `GET`
- **Auth Required**: Yes
- **Response (200 OK)**:
```json
{
  "success": true,
  "enrollments": [
    {
      "id": "019ef851-c046-7157-a626-35ede9503d68",
      "course_code": "DSML",
      "course_name": "Data Science & Machine Learning",
      "time_slot": "06:00 PM - 08:00 PM",
      "instructor_name": "Instructor Beta",
      "final_fee": 2300,
      "status": "Active"
    }
  ]
}
```

### 9. View Enrollment Details
Get full info about a course enrollment (timings, instructor, installments, payment receipts history).

- **URL**: `/enrollments/{id}`
- **Method**: `GET`
- **Auth Required**: Yes
- **Response (200 OK)**:
```json
{
  "success": true,
  "details": {
    "id": "019ef851-c046-7157-a626-35ede9503d68",
    "course": {
      "course_code": "DSML",
      "course_name": "Data Science & Machine Learning",
      "description": "Learn Python, SQL, and Deep Learning.",
      "duration_months": 8,
      "standard_total_fee": 2200,
      "standard_registration_fee": 200
    },
    "schedule": {
      "time_slot": "06:00 PM - 08:00 PM"
    },
    "instructor": {
      "name": "Instructor Beta",
      "email": "instructor2@sms.com"
    },
    "financials": {
      "final_fee": 2300,
      "registration_fee": 200,
      "total_paid": 200,
      "total_pending": 500,
      "total_due": 2100
    },
    "installments": [
      {
        "id": "019ef861-c046-7157-a626-35ede9503d68",
        "installment_no": 1,
        "due_date": "2026-06-03",
        "amount": 200,
        "paid_amount": 200,
        "due_amount": 0,
        "status": "Paid"
      },
      {
        "id": "019ef861-c052-716b-a25b-ab5e305e94b2",
        "installment_no": 2,
        "due_date": "2026-07-03",
        "amount": 1050,
        "paid_amount": 0,
        "due_amount": 1050,
        "status": "Pending"
      }
    ],
    "payments": [
      {
        "id": "019ef861-c063-7182-93de-b827de58a8a1",
        "receipt_no": "RCPT-2026-00001",
        "amount_paid": 200,
        "payment_method": "UPI",
        "transaction_reference": "TXN9988776655",
        "receipt_date": "2026-06-03",
        "status": "Verified",
        "screenshot_url": null,
        "receipt_download_url": "http://localhost/admin/payments/019ef861-c063-7182-93de-b827de58a8a1/receipt"
      }
    ],
    "status": "Active"
  }
}
```

### 10. Enroll in a Course
Self-enroll in a new active course.

- **URL**: `/enrollments`
- **Method**: `POST`
- **Auth Required**: Yes
- **Request Body (JSON)**:
```json
{
  "course_id": "019ef851-bc29-7c2c-8068-15c2ec33ba21",
  "start_time": "04:00 PM",
  "end_time": "06:00 PM"
}
```
- **Response (201 Created)**:
```json
{
  "success": true,
  "message": "Enrolled successfully.",
  "enrollment_id": "019ef961-c045-8123-b12a-35123910ab31"
}
```

---

## Attendance Endpoints

### 11. Attendance Log History
Get detailed attendance registers with status and course filters.

- **URL**: `/attendance?status=Present&course_id=019ef851-bc29-7c2c-8068-15c2ec33ba21`
- **Method**: `GET`
- **Auth Required**: Yes
- **Parameters (Query)**:
  - `status`: `Present` / `Absent` / `Leave` (optional)
  - `course_id`: Course ID (optional)
- **Response (200 OK)**:
```json
{
  "success": true,
  "logs": [
    {
      "id": "019efa10-b12a-7182-ac1a-12903ab10a12",
      "date": "2026-06-23",
      "course_name": "Data Science & Machine Learning",
      "status": "Present"
    }
  ]
}
```

### 12. Attendance Stats Summary
Get present counts, absent counts, and percentage attendance rates.

- **URL**: `/attendance/stats`
- **Method**: `GET`
- **Auth Required**: Yes
- **Response (200 OK)**:
```json
{
  "success": true,
  "stats": {
    "total_conducted": 10,
    "present_days": 8,
    "absent_days": 1,
    "leave_days": 1,
    "attendance_rate": "90%"
  }
}
```

---

## Leave Application Endpoints

### 13. List Leave Applications
Retrieve leave requests and approval remarks.

- **URL**: `/leaves`
- **Method**: `GET`
- **Auth Required**: Yes
- **Response (200 OK)**:
```json
{
  "success": true,
  "leaves": [
    {
      "id": "019efb39-c12e-8123-81ab-234e1208fb21",
      "start_date": "2026-06-26",
      "end_date": "2026-06-29",
      "reason": "Family function.",
      "status": "Pending",
      "admin_remarks": null
    }
  ]
}
```

### 14. Submit Leave Application
Submit a new leave application. Enforces a rule preventing submission if another leave is active (`Pending`/`Approved`).

- **URL**: `/leaves`
- **Method**: `POST`
- **Auth Required**: Yes
- **Request Body (JSON)**:
```json
{
  "start_date": "2026-06-26",
  "end_date": "2026-06-29",
  "reason": "Family function."
}
```
- **Response (201 Created)**:
```json
{
  "success": true,
  "message": "Leave application submitted successfully.",
  "leave_id": "019efb39-c12e-8123-81ab-234e1208fb21"
}
```
- **Response (422 Unprocessable Content)** (Active leave exists):
```json
{
  "success": false,
  "message": "You already have an active or pending leave application."
}
```

---

## Fee Installments & QR Payments

### 15. List Fee Installments
Fetch due balances and schedules.

- **URL**: `/installments`
- **Method**: `GET`
- **Auth Required**: Yes
- **Response (200 OK)**:
```json
{
  "success": true,
  "installments": [
    {
      "id": "019ef861-c052-716b-a25b-ab5e305e94b2",
      "installment_no": 2,
      "course_name": "Data Science & Machine Learning",
      "due_date": "2026-07-03",
      "amount": 1050,
      "paid_amount": 0,
      "due_amount": 1050,
      "status": "Pending"
    }
  ]
}
```

### 16. Submit QR Payment Proof
Submit UPI/QR Code transaction screenshots.

- **URL**: `/payments/pay-qr`
- **Method**: `POST`
- **Auth Required**: Yes
- **Content-Type**: `multipart/form-data`
- **Request Body (Form Data)**:
  - `fee_installment_id`: `019ef861-c052-716b-a25b-ab5e305e94b2` (required)
  - `amount_paid`: `1050` (required, positive numeric)
  - `payment_method`: `UPI/QR Code` (required)
  - `transaction_reference`: `TXN1234567890` (required, unique reference ID)
  - `receipt_date`: `2026-06-24` (required, date string)
  - `screenshot`: `[File upload - Image]` (required, max 2MB)
- **Response (201 Created)**:
```json
{
  "success": true,
  "message": "Payment proof submitted successfully and is pending verification.",
  "payment_id": "019efc21-b045-8123-b12a-35123910ab15"
}
```

### 17. Download PDF Receipt
Securely stream the verified PDF payment receipt file.

- **URL**: `/payments/{id}/receipt`
- **Method**: `GET`
- **Auth Required**: Yes
- **Response**: File download stream (PDF binary content)
- **Response (403 Forbidden)** (Payment not verified or not student's payment):
```json
{
  "success": false,
  "message": "Only verified payments have receipts available for download."
}
```

---

## FCM Push Notification Endpoints

### 18. Store FCM Token
Register or update a Google FCM token for the authenticated user. If the token is already registered to a different device or user, it will be automatically reassigned.

- **URL**: `/fcm-token`
- **Method**: `POST`
- **Auth Required**: Yes
- **Request Body (JSON)**:
```json
{
  "fcm_token": "fcm-registration-token-here",
  "device_name": "iPhone 13 Pro",
  "device_type": "ios"
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "FCM token stored successfully.",
  "data": {
    "id": "019ef851-bc2e-7cb3-8a39-df2df1207f99",
    "device_name": "iPhone 13 Pro",
    "device_type": "ios"
  }
}
```

### 19. Revoke FCM Token
Delete/revoke an FCM token when a user logs out or stops receiving push notifications on a device.

- **URL**: `/fcm-token`
- **Method**: `DELETE`
- **Auth Required**: Yes
- **Request Body (JSON)**:
```json
{
  "fcm_token": "fcm-registration-token-here"
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "FCM token revoked successfully."
}
```

### 20. Send Test Push Notification
Send a test push notification to all FCM tokens currently registered to the authenticated user.

- **URL**: `/fcm-token/test-send`
- **Method**: `POST`
- **Auth Required**: Yes
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "Test notification dispatch processed.",
  "results": [
    {
      "id": "019ef851-bc2e-7cb3-8a39-df2df1207f99",
      "device_name": "iPhone 13 Pro",
      "sent": true
    }
  ]
}
```

---

## Notifications History Endpoints

### 21. Get Notifications List
Retrieve a paginated list of notifications for the authenticated student.

- **URL**: `/notifications`
- **Method**: `GET`
- **Auth Required**: Yes
- **Response (200 OK)**:
```json
{
  "success": true,
  "notifications": {
    "current_page": 1,
    "data": [
      {
        "id": "019f078a-cbf5-739e-bcb1-c4434c891088",
        "user_id": "0157dd7c-46ff-403e-911f-4165728c49ad",
        "title": "Fee Installment Due Reminder",
        "message": "Dear Riya Matta, this is a reminder that installment #2 of amount ₹1,050.00 is due.",
        "data": {
          "type": "fee_reminder",
          "installment_id": "019ef944-35b6-7173-a25e-e7fb2a9a04aa",
          "due_amount": "1050"
        },
        "is_read": false,
        "created_at": "2026-06-26T10:12:34.000000Z",
        "updated_at": "2026-06-26T10:12:34.000000Z"
      }
    ],
    "first_page_url": "http://localhost/api/notifications?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://localhost/api/notifications?page=1",
    "next_page_url": null,
    "path": "http://localhost/api/notifications",
    "per_page": 15,
    "prev_page_url": null,
    "to": 1,
    "total": 1
  }
}
```

### 22. Mark Notification as Read
Mark a specific notification record as read.

- **URL**: `/notifications/{id}/read`
- **Method**: `POST`
- **Auth Required**: Yes
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "Notification marked as read."
}
```

### 23. Mark All Notifications as Read
Mark all notifications for the authenticated student as read.

- **URL**: `/notifications/read-all`
- **Method**: `POST`
- **Auth Required**: Yes
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "All notifications marked as read."
}
```

---

## Authentication & Password Management

### 24. Change Password
Change the password for the authenticated student.

- **URL**: `/password/change`
- **Method**: `POST`
- **Auth Required**: Yes (Sanctum Token)
- **Request Body (JSON)**:
```json
{
  "current_password": "oldpassword123",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "Password changed successfully."
}
```
- **Response (400 Bad Request)**:
```json
{
  "success": false,
  "message": "Current password does not match."
}
```
- **Response (422 Unprocessable Content)**:
```json
{
  "success": false,
  "errors": {
    "password": [
      "The password field confirmation does not match."
    ]
  }
}
```

### 25. Reset Password
Reset the password for a guest student using email and mobile verification.

- **URL**: `/password/reset`
- **Method**: `POST`
- **Auth Required**: No
- **Request Body (JSON)**:
```json
{
  "email": "student@example.com",
  "mobile": "+15550199",
  "password": "newresetpassword123",
  "password_confirmation": "newresetpassword123"
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "Password has been reset successfully."
}
```
- **Response (404 Not Found)**:
```json
{
  "success": false,
  "message": "No matching student record found for the provided email and mobile number."
}
```
- **Response (422 Unprocessable Content)**:
```json
{
  "success": false,
  "errors": {
    "email": [
      "The selected email is invalid."
    ]
  }
}
```

