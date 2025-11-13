<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Event Invitation - {{ $event->title }}</title>
    
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .email-container {
            max-width: 650px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }
        .email-header::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 20px;
            background: white;
            border-radius: 50% 50% 0 0 / 100% 100% 0 0;
        }
        .email-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }
        .email-header .icon {
            font-size: 3rem;
            margin-bottom: 10px;
            display: block;
        }
        .email-body {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 20px;
        }
        .event-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-left: 5px solid #667eea;
        }
        .event-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 20px;
            text-align: center;
        }
        .detail-item {
            background: white;
            padding: 12px 15px;
            margin: 10px 0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            transition: transform 0.2s;
        }
        .detail-item:hover {
            transform: translateX(5px);
        }
        .detail-icon {
            font-size: 1.3rem;
            color: #667eea;
            margin-right: 15px;
            min-width: 25px;
        }
        .detail-label {
            font-weight: 600;
            color: #555;
            min-width: 100px;
        }
        .detail-value {
            color: #333;
            flex: 1;
        }
        .btn-action {
            padding: 15px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .btn-accept {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }
        .btn-accept:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(17, 153, 142, 0.4);
            color: white;
        }
        .btn-decline {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
            color: white;
        }
        .btn-decline:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(235, 51, 73, 0.4);
            color: white;
        }
        .action-section {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
        }
        .links-section {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .links-section p {
            margin: 5px 0;
            font-size: 0.85rem;
            color: #856404;
        }
        .links-section a {
            color: #004085;
            word-break: break-all;
        }
        .email-footer {
            background: #f8f9fa;
            text-align: center;
            padding: 30px;
            color: #6c757d;
            border-top: 2px solid #e9ecef;
        }
        .email-footer p {
            margin: 5px 0;
            font-size: 0.9rem;
        }
        .divider {
            height: 2px;
            background: linear-gradient(90deg, transparent, #667eea, transparent);
            margin: 30px 0;
        }
        @media (max-width: 600px) {
            .email-body {
                padding: 20px 15px;
            }
            .btn-action {
                display: block;
                margin: 10px 0;
                width: 100%;
            }
            .detail-item {
                flex-direction: column;
                align-items: flex-start;
            }
            .detail-label {
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <span class="icon">📅</span>
            <h1>Event Invitation</h1>
        </div>
        
        <!-- Body -->
        <div class="email-body">
            <p class="greeting">
                <strong>Hello!</strong> 👋
            </p>
            
            <p class="text-muted">
                You've been invited to join an exciting event. Here are the details:
            </p>
            
            <!-- Event Details Card -->
            <div class="event-card">
                <div class="event-title">
                    <i class="bi bi-calendar-event"></i> {{ $event->title }}
                </div>
                
                @if($event->description)
                <div class="detail-item">
                    <i class="bi bi-file-text detail-icon"></i>
                    <span class="detail-label">Description:</span>
                    <span class="detail-value">{{ $event->description }}</span>
                </div>
                @endif
                
                <div class="detail-item">
                    <i class="bi bi-clock detail-icon"></i>
                    <span class="detail-label">Start Time:</span>
                    <span class="detail-value">{{ $event->start_time->format('F j, Y \a\t g:i A') }}</span>
                </div>
                
                <div class="detail-item">
                    <i class="bi bi-clock-history detail-icon"></i>
                    <span class="detail-label">End Time:</span>
                    <span class="detail-value">{{ $event->end_time->format('F j, Y \a\t g:i A') }}</span>
                </div>
                
                @if($event->location)
                <div class="detail-item">
                    <i class="bi bi-geo-alt detail-icon"></i>
                    <span class="detail-label">Location:</span>
                    <span class="detail-value">{{ $event->location }}</span>
                </div>
                @endif
                
                <div class="detail-item">
                    <i class="bi bi-calendar3 detail-icon"></i>
                    <span class="detail-label">Calendar:</span>
                    <span class="detail-value">{{ $calendar->name }}</span>
                </div>
            </div>
            
            <div class="divider"></div>
            
            <!-- Action Buttons -->
            <div class="action-section">
                <p class="mb-3"><strong>Will you attend this event?</strong></p>
                <a href="{{ config('app.url') }}/invites/{{ $invite->id }}/accept" class="btn-action btn-accept">
                    <i class="bi bi-check-circle"></i> Accept Invitation
                </a>
                <a href="{{ config('app.url') }}/invites/{{ $invite->id }}/reject" class="btn-action btn-decline">
                    <i class="bi bi-x-circle"></i> Decline Invitation
                </a>
            </div>
            
            <!-- Alternative Links -->
            <div class="links-section">
                <p><strong><i class="bi bi-info-circle"></i> Having trouble with the buttons?</strong></p>
                <p>Copy and paste these links into your browser:</p>
                <p>
                    <strong>Accept:</strong><br>
                    <a href="{{ config('app.url') }}/invites/{{ $invite->id }}/accept">{{ config('app.url') }}/invites/{{ $invite->id }}/accept</a>
                </p>
                <p>
                    <strong>Decline:</strong><br>
                    <a href="{{ config('app.url') }}/invites/{{ $invite->id }}/reject">{{ config('app.url') }}/invites/{{ $invite->id }}/reject</a>
                </p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <p><i class="bi bi-envelope"></i> This is an automated message. Please do not reply to this email.</p>
            <p class="mt-2"><strong>&copy; {{ date('Y') }} {{ config('app.name') }}</strong></p>
            <p>All rights reserved.</p>
        </div>
    </div>
    
    <!-- Bootstrap JS (optional, for enhanced functionality) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
