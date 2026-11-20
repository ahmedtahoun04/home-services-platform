# Home Services Booking Platform: API Reference

All JSON responses follow one format so every module (frontend, admin dashboard,
bookings) can handle them the same way. Always send `Accept: application/json`.

## Response format

**Success, one record**
```json
{ "data": { "id": 1, "name": "Home Plumbing Repair", "price": 150 } }
```
Create, update and status changes also return a message:
```json
{ "data": { "...": "..." }, "message": "Service created successfully." }
```

**Success, list** (paginated)
```json
{ "data": [ { "...": "..." } ], "links": { "...": "..." }, "meta": { "current_page": 1, "total": 6 } }
```

**Delete:** `204 No Content` (empty body).

**Error**
```json
{ "message": "Resource not found." }
```

**Validation error (422)**
```json
{ "message": "The name field is required.", "errors": { "name": ["The name field is required."] } }
```

## Status codes

| Code | Meaning | Message |
|---|---|---|
| 200 | OK | - |
| 201 | Created | "... successfully." |
| 204 | Deleted | (empty) |
| 401 | Not logged in | "Unauthenticated." |
| 403 | Logged in but not an admin | "This area is restricted to administrators." |
| 404 | Record or route not found | "Resource not found." |
| 405 | Wrong HTTP method | "Method not allowed." |
| 409 | Conflict | e.g. deleting a service that has bookings |
| 422 | Validation failed | message + errors |

## Public endpoints (no login)

| Method | URL | Description |
|---|---|---|
| GET | `/api/services` | Active services. Query: `search`, `category`, `page` |
| GET | `/api/services/{id}` | One active service |

## Customer endpoints (login required, session cookie + CSRF token)

| Method | URL | Description |
|---|---|---|
| POST | `/bookings` | Create a booking request (status starts as `Pending`) |
| GET | `/bookings` | The logged-in customer's own bookings (10 per page, includes the service) |

`POST /bookings` body:

| Field | Rule |
|---|---|
| `service_id` | required, must be an active service |
| `booking_date` | required, a date, today or later (`YYYY-MM-DD`) |
| `booking_time` | required, text, max 50 characters |
| `address` | optional, max 255 |
| `notes` | optional, max 1000 |

The server sets `user_id` and `status`. They cannot be sent by the client.

Example for the booking form (same origin, customer logged in):
```js
const res = await fetch('/bookings', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
  },
  body: JSON.stringify({
    service_id: 1,
    booking_date: '2026-11-05',
    booking_time: '10:30',
    address: 'Some Street 1, Istanbul',
    notes: 'Please call before arriving',
  }),
});
const body = await res.json(); // 201: body.data, body.message | 422: body.errors | 401: log in first
```

## Admin endpoints (login + admin role, session cookie + CSRF token)

**Services**

| Method | URL | Description |
|---|---|---|
| GET | `/admin/services` | All services (also inactive). Query: `search`, `category`, `page` |
| POST | `/admin/services` | Create. Required: `name`, `category`, `price` |
| GET | `/admin/services/{id}` | One service |
| PUT/PATCH | `/admin/services/{id}` | Update (any of the fields) |
| DELETE | `/admin/services/{id}` | Delete. Returns 409 if the service has bookings (set `is_active` to false instead) |

Service fields: `name` (string), `category` (string), `description` (text, optional),
`price` (number >= 0), `rating` (0 to 5, optional), `is_active` (boolean, default true).

**Bookings**

| Method | URL | Description |
|---|---|---|
| GET | `/admin/bookings` | All bookings with the customer and the service. Query: `status` (`Pending`, `Approved`, `Completed`), `page` |
| PATCH | `/admin/bookings/{id}/status` | Body: `{"status": "Approved"}` |

Status values are capitalized: `Pending`, `Approved`, `Completed`.
Allowed changes: `Pending` to `Approved`, and `Approved` to `Completed`.
Skipping a step, going back or sending the same status returns 422 with an error on `status`.
