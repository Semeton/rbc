<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation to Join RBC Trucking Management System</title>
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
            background-color: #1f2937;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .button {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #2563eb;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6b7280;
            font-size: 14px;
        }
        .role-badge {
            background-color: #dbeafe;
            color: #1e40af;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>RBC Trucking Management System</h1>
        <p>You've been invited to join our team!</p>
    </div>
    
    <div class="content">
        <h2>Welcome to RBC Trucking Management System</h2>
        
        <p>Hello,</p>
        
        <p>You have been invited to join the RBC Trucking Management System by <strong>{{ $invitation->invitedBy->name }}</strong>.</p>
        
        <p>Your assigned role is: <span class="role-badge">{{ $invitation->role_display }}</span></p>
        
        <p>This invitation will expire on <strong>{{ $invitation->expires_at->format('F j, Y \a\t g:i A') }}</strong>.</p>
        
        <p>To accept this invitation and create your account, please click the button below:</p>
        
        <div style="text-align: center;">
            <a href="{{ $acceptUrl }}" class="button">Accept Invitation</a>
        </div>
        
        <p>If you cannot click the button, you can copy and paste the following link into your browser:</p>
        <p style="word-break: break-all; background-color: #e5e7eb; padding: 10px; border-radius: 4px; font-family: monospace;">
            {{ $acceptUrl }}
        </p>
        
        <p>If you did not expect this invitation, you can safely ignore this email.</p>
        
        <p>Best regards,<br>
        RBC Trucking Management System Team</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} RBC Trucking Management System. All rights reserved.</p>
    </div>
</body>
</html>
