# ServiceHub Mobile API

Base URL: `/api` (e.g. `http://localhost:8000/api`). JSON in/out. Send `Accept: application/json`.
Authenticated routes need `Authorization: Bearer <token>` (Laravel Sanctum). Validation errors return `422` with `{message, errors}`.

Roles: `client`, `technician`, `admin`. Booking statuses: `pending`, `assigned`, `in_progress`, `completed`, `cancelled`.

## Auth (public)
| Method | Path | Body | Notes |
|---|---|---|---|
| POST | `/auth/register` | `name`, `email`, `phone?`, `password`, `password_confirmation`, `device_name?` | Always creates a `client`. 201 → `{user, token, token_type}` |
| POST | `/auth/login` | `email`, `password`, `device_name?` | Throttled 6/min. → `{user, token, token_type}` |
| POST | `/auth/forgot-password` | `email` | Throttled 6/min |
| POST | `/auth/reset-password` | `token`, `email`, `password`, `password_confirmation` | Throttled 6/min |
| POST | `/auth/logout` | – | Auth. Revokes current token, 204 |

## Catalog (public)
| Method | Path | Notes |
|---|---|---|
| GET | `/services` | Active services → `{data: [...]}` |
| GET | `/services/{id}/availability?date=YYYY-MM-DD` | Hourly slots 09:00–17:00 that are free → `{service_id, date, duration_minutes, slots: [ISO8601]}` |

## Profile (auth, any role)
| Method | Path | Body |
|---|---|---|
| GET | `/me` | – |
| PATCH | `/me` | `name?`, `email?`, `phone?` |

## Notifications (auth, any role)
| Method | Path | Notes |
|---|---|---|
| GET | `/notifications?unread=1` | Paginated (30). Each item's `data` = `{title, body, booking_id, status}` |
| PATCH | `/notifications/{id}/read` | Marks one as read |

## Bookings
| Method | Path | Role | Body / Notes |
|---|---|---|---|
| POST | `/bookings` | client | `service_id`, `scheduled_at` (future), `address`, `latitude?`, `longitude?`, `description?`, `photos[]?` (≤8 images, 5 MB each; multipart). 201 |
| GET | `/bookings?status=` | client | Own bookings, paginated (20) |
| GET | `/bookings/{id}` | any | Authorized by policy; fields vary by viewer role |

## Technician (`role:technician`)
| Method | Path | Body / Notes |
|---|---|---|
| GET | `/technician/jobs?status=assigned\|in_progress\|completed` | Paginated |
| POST | `/technician/jobs/{id}/start` | Only from `assigned`; notifies client |
| POST | `/technician/jobs/{id}/complete` | `report`, `metadata?` (object), `photos[]?` (≤12). Only from `in_progress`; notifies client |
| PATCH | `/technician/availability` | `availability` (object/array) |

## Admin (`role:admin`)
| Method | Path | Body / Notes |
|---|---|---|
| GET | `/admin/dashboard` | Counts: bookings by status, customers, technicians |
| GET | `/admin/bookings?status=&date=YYYY-MM-DD` | |
| PATCH | `/admin/bookings/{id}/technician` | `technician_id` (active technician) → status becomes `assigned` |
| GET | `/admin/customers?search=` | |
| POST | `/admin/customers` | `name`, `email`, `phone?`, `password`, `password_confirmation` |
| GET | `/admin/customers/{id}` | Includes `bookings_count`, `recent_bookings` |
| GET | `/admin/technicians` | |
| GET | `/admin/technicians/{id}` | Includes `availability`, `assigned_bookings_count` |

## Booking object
`id, service{id,name,duration_minutes}, scheduled_at, address, latitude, longitude, description, status, started_at, completed_at, client{id,name,phone,email(admin only)} (admin/technician), technician{id,name} (admin/owner client), media[{id,type,url}], report{report,metadata,completed_by}, created_at`
