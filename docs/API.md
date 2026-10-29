# Home Services Booking Platform: API Reference

All JSON responses follow one format so every module (frontend, admin dashboard,
bookings) can handle them the same way.

## Response format

**Success, one record**
```json
{ "data": { "id": 1, "name": "Home Plumbing Repair", "price": 150 } }
```
Create and update also return a message:
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
| 201 | Created | "... created successfully." |
| 204 | Deleted | (empty) |
| 401 | Not logged in | "Unauthenticated." |
| 403 | Logged in but not an admin | "This area is restricted to administrators." |
| 404 | Record or route not found | "Resource not found." |
| 405 | Wrong HTTP method | "Method not allowed." |
| 422 | Validation failed | message + errors |

## Public endpoints (no login)

| Method | URL | Description |
|---|---|---|
| GET | `/api/services` | Active services. Query: `search`, `category`, `page` |
| GET | `/api/services/{id}` | One active service |

## Admin endpoints (login + admin role, session cookie + CSRF token)

| Method | URL | Description |
|---|---|---|
| GET | `/admin/services` | All services (also inactive). Query: `search`, `category`, `page` |
| POST | `/admin/services` | Create. Required: `name`, `category`, `price` |
| GET | `/admin/services/{id}` | One service |
| PUT/PATCH | `/admin/services/{id}` | Update (any of the fields) |
| DELETE | `/admin/services/{id}` | Delete |

Service fields: `name` (string), `category` (string), `description` (text, optional),
`price` (number >= 0), `rating` (0 to 5, optional), `is_active` (boolean, default true).

Calling from the browser (same origin, logged in as admin):
```js
fetch('/admin/services', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
  },
  body: JSON.stringify({ name: 'AC Cleaning', category: 'HVAC', price: 90 }),
});
```
Always send `Accept: application/json` so errors come back as JSON.
