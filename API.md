# API.md — Benta Door REST API

Base URL: `/api/v1`  
Auth: Bearer JWT (`Authorization: Bearer {access_token}`) unless noted **Public**.

JSON responses use Laravel API Resources where applicable (`data` wrapper for resources).

---

## Authentication

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| POST | `/auth/login` | Public | Email + password → JWT |
| POST | `/auth/register` | Public | Register + JWT |
| POST | `/auth/logout` | JWT | Invalidate token |
| POST | `/auth/refresh` | JWT | Refresh token |
| GET | `/users/me` | JWT | Current user (roles, permissions) |

### Login response shape

```json
{
  "authorization": {
    "access_token": "...",
    "token_type": "bearer",
    "expires_in": 3600
  }
}
```

---

## Profile & account (self-service)

Available to **any authenticated user** for their own account.

| Method | Path | Body | Description |
|--------|------|------|-------------|
| GET | `/profile` | — | Current user profile (`UserResource`) |
| PUT | `/profile` | `name`, `email` | Update profile |
| PUT | `/profile/password` | `current_password`, `password`, `password_confirmation` | Change password |

### Update profile

**Request**

```json
{
  "name": "Cyrus Manatad",
  "email": "cyrusmanatad@bentadoor.com"
}
```

**Response** `200`

```json
{
  "message": "Profile updated successfully.",
  "data": {
    "id": 1,
    "name": "Cyrus Manatad",
    "email": "cyrusmanatad@bentadoor.com",
    "roles": ["Super Admin"],
    "permissions": ["..."],
    "is_active": true,
    "last_login_at": "2026-05-17 10:00:00",
    "last_login_ip": "127.0.0.1",
    "login": "2 hours ago",
    "status": "Active",
    "color": "green",
    "created_at": "2026-01-01 00:00:00"
  }
}
```

### Update password

**Request**

```json
{
  "current_password": "Password@1234",
  "password": "NewPassword@1234",
  "password_confirmation": "NewPassword@1234"
}
```

**Response** `200`

```json
{
  "message": "Password updated successfully."
}
```

**Errors** `422` — validation or wrong `current_password`.

---

## Users (staff admin)

| Method | Path | Permission (typical) |
|--------|------|----------------------|
| GET | `/users` | `view users` |
| POST | `/users` | `create users` |
| DELETE | `/users/{user}` | `delete users` |
| PATCH | `/users/{user}/role` | — |
| PATCH | `/users/{user}/status` | — |
| GET | `/users/total` | — |

---

## Customers

| Method | Path |
|--------|------|
| GET | `/customers` |
| GET | `/customers/{user}` |
| GET | `/customers/total` |

Customers are users without assigned roles.

---

## Roles & permissions

| Method | Path |
|--------|------|
| GET | `/roles` |
| POST | `/roles` |
| PUT | `/roles/{role}` |
| DELETE | `/roles/{role}` |
| GET | `/roles/permissions` |

---

## Products & inventory

| Method | Path |
|--------|------|
| GET | `/products` |
| POST | `/products` |
| GET | `/products/{product}` |
| PUT | `/products/{product}` |
| DELETE | `/products/{product}` |
| GET | `/categories` |
| GET | `/inventory/total` |
| GET | `/inventory/sales` |
| GET | `/inventory/stocks` |
| GET | `/inventory/unavailable` |

Query params (products): `search`, `status`, `category`, pagination.

---

## Orders

| Method | Path |
|--------|------|
| GET | `/orders` |
| POST | `/orders` |
| PUT | `/orders/{order}` |
| DELETE | `/orders/{order}` |
| GET | `/orders/total` |
| GET | `/orders/export` | PDF export |

---

## Analytics

| Method | Path |
|--------|------|
| GET | `/analytics/revenue` |
| GET | `/analytics/categories` |
| GET | `/analytics/kpi` |

---

## Forums

| Method | Path |
|--------|------|
| GET/POST | `/forums` |
| GET/PUT/DELETE | `/forums/{forum}` |
| GET/POST | `/forums/{forum}/comments` |

Not yet consumed by bentador Inbox UI.

---

## Error format

Validation errors (`422`):

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["This email is already registered."]
  }
}
```

Unauthorized (`401`):

```json
{
  "error": "Unauthorized"
}
```

---

## Demo accounts (after `db:seed`)

| Role | Email | Password |
|------|-------|----------|
| Super Admin | cyrusmanatad@bentadoor.com | Password@1234 |
| Admin | bryce.douglas@bentadoor.org | Password@1234 |
| Inventory Staff | bartell.toni@bentadoor.com | Password@1234 |
| Support | jules.conn@bentadoor.com | Password@1234 |

---

## Related docs

- [ARCHITECTURE.md](./ARCHITECTURE.md) — Backend layering
- [FRONTEND.md](./FRONTEND.md) — Which stores call which endpoints
