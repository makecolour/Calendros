<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Invitation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .event-details {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #4F46E5;
        }
        .detail-row {
            margin: 10px 0;
        }
        .label {
            font-weight: bold;
            color: #6b7280;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            margin: 10px 5px;
            background-color: #4F46E5;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .button:hover {
            background-color: #4338CA;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📅 Event Invitation</h1>
    </div>
    
    <div class="content">
        <p>Hello,</p>
        
        <p>You have been invited to an event:</p>
        
        <div class="event-details">
            <div class="detail-row">
                <span class="label">Event:</span>
                <span>{{ $event->title }}</span>
            </div>
            
            @if($event->description)
            <div class="detail-row">
                <span class="label">Description:</span>
                <span>{{ $event->description }}</span>
            </div>
            @endif
            
            <div class="detail-row">
                <span class="label">Start:</span>
                <span>{{ $event->start_time->format('F j, Y \a\t g:i A') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">End:</span>
                <span>{{ $event->end_time->format('F j, Y \a\t g:i A') }}</span>
            </div>
            
            @if($event->location)
            <div class="detail-row">
                <span class="label">Location:</span>
                <span>{{ $event->location }}</span>
            </div>
            @endif
            
            <div class="detail-row">
                <span class="label">Calendar:</span>
                <span>{{ $calendar->name }}</span>
            </div>
        </div>
        
        <p style="text-align: center; margin-top: 30px;">
            <a href="{{ config('app.frontend_url') }}/invites/{{ $invite->id }}/accept" class="button">
                ✓ Accept Invitation
            </a>
            <a href="{{ config('app.frontend_url') }}/invites/{{ $invite->id }}/reject" class="button" style="background-color: #DC2626;">
                ✗ Decline Invitation
            </a>
        </p>
        
        <p style="margin-top: 20px; color: #6b7280; font-size: 14px;">
            If you're unable to click the buttons above, you can copy and paste the following links into your browser:
        </p>
        <p style="font-size: 12px; word-break: break-all; color: #6b7280;">
            Accept: {{ config('app.frontend_url') }}/invites/{{ $invite->id }}/accept<br>
            Decline: {{ config('app.frontend_url') }}/invites/{{ $invite->id }}/reject
        </p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>
