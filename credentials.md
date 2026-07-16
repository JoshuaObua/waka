# Waka PMS Backend Credentials & Testing Guide

This guide contains the local testing URLs, bootstrap credentials, and instructions for testing the Waka PMS backend offline.

## 1. Local Testing Links

| Endpoint | Method | URL | Description |
| :--- | :--- | :--- | :--- |
| **Health Check** | `GET` | [http://localhost:8080/healthz](http://localhost:8080/healthz) | Returns server health and timestamp |
| **Readiness Check** | `GET` | [http://localhost:8080/readyz](http://localhost:8080/readyz) | Verifies database connectivity |
| **Metrics** | `GET` | [http://localhost:8080/metrics](http://localhost:8080/metrics) | System memory and goroutine stats |
| **Bootstrap Tenant** | `POST` | `http://localhost:8080/api/v1/auth/bootstrap` | Bootstraps initial tenant and admin user |
| **User Login** | `POST` | `http://localhost:8080/api/v1/auth/login` | Authenticates users and returns JWT |

---

## 2. Bootstrapping the Admin Account

Before any admin user can log in, you must initialize the first tenant database entry. You can do this by sending a `POST` request to `http://localhost:8080/api/v1/auth/bootstrap` with the payload below.

### Bootstrap Request Payload
*   **Method:** `POST`
*   **URL:** `http://localhost:8080/api/v1/auth/bootstrap`
*   **JSON Body:**
```json
{
    "tenant_name": "Acme Estates Ltd",
    "subdomain": "acme",
    "email": "admin@acme.com",
    "password": "supersecurepassword123",
    "first_name": "John",
    "last_name": "Doe"
}
```

### Admin Credentials (Post-Bootstrap)
After the bootstrap endpoint successfully completes, the admin credentials will be:
*   **Email:** `admin@acme.com`
*   **Password:** `supersecurepassword123`

---

## 3. Onboarding Tenants (Clients)

To onboard a tenant, make an authenticated request as the Admin to `/api/v1/tenants`.

### Onboard Tenant Request
*   **Method:** `POST`
*   **URL:** `http://localhost:8080/api/v1/tenants`
*   **Headers:** `Authorization: Bearer <JWT_TOKEN_FROM_LOGIN>`
*   **JSON Body:**
```json
{
    "email": "tenant@example.com",
    "first_name": "Jane",
    "last_name": "Smith",
    "phone_number": "+1234567890",
    "id_number": "ID123456",
    "id_type": "NationalID",
    "guarantor_name": "Guarantor Name",
    "guarantor_phone": "+9876543210",
    "bank_name": "Acme Bank",
    "bank_account_number": "987654321"
}
```

### Tenant Login Credentials
*   **Email:** `tenant@example.com`
*   **Initial State:** Onboarded as a shadow account with `PasswordHash: "onboarded_shadow_account_no_password"` and status `pending_verification`.
*   **Action Required:** The tenant needs to complete the verification flow or perform a password reset/activation before logging in.
