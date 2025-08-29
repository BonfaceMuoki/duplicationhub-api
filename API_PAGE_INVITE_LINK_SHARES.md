# Page Invite Link Shares API

## Overview
This API manages page invite link shares, tracking when users share page links and their registration status.

## Database Schema
The `page_invite_link_shares` table contains:
- `id` - Primary key
- `page_id` - Foreign key to pages table
- `page_invite_id` - Foreign key to page_invites table
- `user_page_link` - The actual link that was shared
- `registration_status` - Enum: pending, registered, completed, failed
- `notes` - Optional notes about the share
- `metadata` - JSON field for additional data (UTM parameters, user agent, etc.)
- `created_at`, `updated_at` - Timestamps

## API Endpoints

### 1. Create Link Share
**POST** `/api/page-invite-shares`

Creates a new page invite link share record.

#### Request Body
```json
{
    "page_id": 1,
    "page_invite_id": 5,
    "user_page_link": "https://example.com/pages/my-page?ref=user123",
    "notes": "Shared on Facebook",
    "metadata": {
        "utm_source": "facebook",
        "utm_medium": "social",
        "user_agent": "Mozilla/5.0...",
        "ip_address": "192.168.1.1"
    }
}
```

#### Response
```json
{
    "success": true,
    "message": "Link share created successfully",
    "data": {
        "id": 1,
        "page_id": 1,
        "page_invite_id": 5,
        "user_page_link": "https://example.com/pages/my-page?ref=user123",
        "registration_status": "pending",
        "notes": "Shared on Facebook",
        "metadata": {
            "utm_source": "facebook",
            "utm_medium": "social"
        },
        "created_at": "2025-01-20T10:30:00.000000Z",
        "updated_at": "2025-01-20T10:30:00.000000Z",
        "page": {
            "id": 1,
            "title": "Example Page",
            "slug": "my-page"
        },
        "page_invite": {
            "id": 5,
            "handle": "user123",
            "clicks": 10
        }
    }
}
```

### 2. Get Link Share
**GET** `/api/page-invite-shares/{id}`

Retrieves a specific page invite link share.

#### Response
```json
{
    "success": true,
    "data": {
        "id": 1,
        "page_id": 1,
        "page_invite_id": 5,
        "user_page_link": "https://example.com/pages/my-page?ref=user123",
        "registration_status": "pending",
        "notes": "Shared on Facebook",
        "metadata": {
            "utm_source": "facebook",
            "utm_medium": "social"
        },
        "created_at": "2025-01-20T10:30:00.000000Z",
        "updated_at": "2025-01-20T10:30:00.000000Z",
        "page": {
            "id": 1,
            "title": "Example Page",
            "slug": "my-page"
        },
        "page_invite": {
            "id": 5,
            "handle": "user123",
            "clicks": 10
        }
    }
}
```

### 3. Update Link Share Status
**PUT** `/api/page-invite-shares/{id}/status`

Updates the registration status of a link share.

#### Request Body
```json
{
    "registration_status": "completed",
    "notes": "User successfully registered and completed onboarding"
}
```

#### Response
```json
{
    "success": true,
    "message": "Link share status updated successfully",
    "data": {
        "id": 1,
        "page_id": 1,
        "page_invite_id": 5,
        "user_page_link": "https://example.com/pages/my-page?ref=user123",
        "registration_status": "completed",
        "notes": "User successfully registered and completed onboarding",
        "metadata": {
            "utm_source": "facebook",
            "utm_medium": "social"
        },
        "created_at": "2025-01-20T10:30:00.000000Z",
        "updated_at": "2025-01-20T10:35:00.000000Z",
        "page": {
            "id": 1,
            "title": "Example Page",
            "slug": "my-page"
        },
        "page_invite": {
            "id": 5,
            "handle": "user123",
            "clicks": 10
        }
    }
}
```

## Registration Status Values

- **pending** - Link shared, waiting for user to register
- **registered** - User has registered but not completed full process
- **completed** - User has completed the full registration process
- **failed** - Registration failed or user abandoned the process

## Relationships

The model includes relationships to:
- **Page** - The page being shared
- **PageInvite** - The specific invite being used

## Example Usage

### Track a Facebook Share
```bash
curl -X POST http://localhost:8004/api/page-invite-shares \
  -H "Content-Type: application/json" \
  -d '{
    "page_id": 1,
    "page_invite_id": 5,
    "user_page_link": "https://example.com/pages/my-page?ref=user123&utm_source=facebook",
    "notes": "Shared on Facebook group",
    "metadata": {
        "utm_source": "facebook",
        "utm_medium": "social",
        "utm_campaign": "january_promo"
    }
  }'
```

### Update Status When User Registers
```bash
curl -X PUT http://localhost:8004/api/page-invite-shares/1/status \
  -H "Content-Type: application/json" \
  -d '{
    "registration_status": "registered",
    "notes": "User completed registration form"
  }'
```

## Notes
- All endpoints are public (no authentication required)
- The system validates that page_invite_id belongs to the specified page_id
- Metadata field can store any JSON data for tracking purposes
- Registration status can be updated multiple times as the user progresses 