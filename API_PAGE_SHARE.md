# Page Link Sharing API

## Endpoint
`POST /api/pages/share`

## Description
This endpoint accepts a page link share payload and returns the client's phone number and message for campaign launching.

## Request Payload
```json
{
    "page_url": "http://localhost:8004/pages/example-page",
    "message": "Hey there! 👋 I thought you might be interested in this: http://localhost:8004/pages/example-page"
}
```

### Required Fields
- `page_url`: The URL of the page to be shared
- `message`: The message to be sent (max 1000 characters)

## Response Format

### Success Response
```json
{
    "success": true,
    "data": {
        "phone_number": "+254740857767",
        "message": "Hey there! 👋 I thought you might be interested in this: http://localhost:8004/pages/example-page",
        "page_title": "Example Page Title",
        "page_url": "http://localhost:8004/pages/example-page",
        "client_name": "John Doe",
        "campaign_launched": true,
        "timestamp": "2025-01-20T10:30:00.000000Z"
    }
}
```

### Error Response
```json
{
    "success": false,
    "error": "Error message description"
}
```

## Error Codes
- `Page URL and message are required` - Missing required fields
- `Message exceeds maximum length of 1000 characters` - Message too long
- `Invalid page URL format` - Malformed URL
- `Page not found` - Page doesn't exist
- `Page owner contact information not available` - No phone number found
- `An error occurred while processing your request` - Server error

## Example Usage

### cURL
```bash
curl -X POST http://localhost:8004/api/pages/share \
  -H "Content-Type: application/json" \
  -d '{
    "page_url": "http://localhost:8004/pages/example-page",
    "message": "Hey there! 👋 I thought you might be interested in this: http://localhost:8004/pages/example-page"
  }'
```

### JavaScript/Fetch
```javascript
const response = await fetch('/api/pages/share', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        page_url: 'http://localhost:8004/pages/example-page',
        message: 'Hey there! 👋 I thought you might be interested in this: http://localhost:8004/pages/example-page'
    })
});

const result = await response.json();
console.log('Phone Number:', result.data.phone_number);
console.log('Message:', result.data.message);
```

## Notes
- The API extracts the page slug from the URL automatically
- Phone number is retrieved from the page owner's user profile
- Falls back to WhatsApp number if phone number is not available
- Maximum message length is 1000 characters
- The endpoint is public (no authentication required)
- All requests are logged for debugging purposes 