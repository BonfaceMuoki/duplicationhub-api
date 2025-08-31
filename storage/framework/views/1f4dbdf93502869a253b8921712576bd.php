<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $__env->yieldContent('title', 'DuplicationHub'); ?></title>
    
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    
    <style>
        /* Reset styles */
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }
        
        /* Base styles */
        body {
            margin: 0 !important;
            padding: 0 !important;
            background-color: #f4f4f4 !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
            font-size: 16px !important;
            line-height: 1.6 !important;
            color: #333333 !important;
        }
        
        /* Container */
        .email-container {
            max-width: 600px !important;
            margin: 0 auto !important;
            background-color: #ffffff !important;
        }
        
        /* Header */
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
        }
        
        .logo {
            max-width: 200px;
            height: auto;
        }
        
        .header-title {
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            margin: 20px 0 10px 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .header-subtitle {
            color: #ffffff;
            font-size: 16px;
            margin: 0;
            opacity: 0.9;
        }
        
        /* Content */
        .email-content {
            padding: 40px 30px;
            background-color: #ffffff;
        }
        
        .content-section {
            margin-bottom: 30px;
        }
        
        .content-title {
            color: #2c3e50;
            font-size: 24px;
            font-weight: 600;
            margin: 0 0 20px 0;
            text-align: center;
        }
        
        .content-text {
            color: #555555;
            font-size: 16px;
            line-height: 1.7;
            margin: 0 0 20px 0;
        }
        
        /* Buttons */
        .btn {
            display: inline-block;
            padding: 14px 28px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            box-shadow: 0 4px 15px rgba(240, 147, 251, 0.3);
        }
        
        .btn-secondary:hover {
            box-shadow: 0 6px 20px rgba(240, 147, 251, 0.4);
        }
        
        .btn-container {
            text-align: center;
            margin: 30px 0;
        }
        
        /* Cards */
        .info-card {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .warning-card {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .success-card {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        /* Footer */
        .email-footer {
            background-color: #2c3e50;
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        
        .social-links {
            margin-bottom: 20px;
        }
        
        .social-link {
            display: inline-block;
            margin: 0 10px;
            text-decoration: none;
        }
        
        .social-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #667eea;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 18px;
            transition: all 0.3s ease;
        }
        
        .social-icon:hover {
            background-color: #764ba2;
            transform: scale(1.1);
        }
        
        .footer-text {
            color: #bdc3c7;
            font-size: 14px;
            margin: 10px 0;
        }
        
        .footer-link {
            color: #667eea;
            text-decoration: none;
        }
        
        .footer-link:hover {
            text-decoration: underline;
        }
        
        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
            }
            
            .email-header,
            .email-content,
            .email-footer {
                padding: 20px 15px !important;
            }
            
            .header-title {
                font-size: 24px !important;
            }
            
            .content-title {
                font-size: 20px !important;
            }
            
            .btn {
                padding: 12px 24px !important;
                font-size: 14px !important;
            }
        }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .email-container {
                background-color: #1a1a1a !important;
            }
            
            .email-content {
                background-color: #1a1a1a !important;
            }
            
            .content-title {
                color: #ffffff !important;
            }
            
            .content-text {
                color: #cccccc !important;
            }
            
            .info-card {
                background-color: #2a2a2a !important;
            }
        }
    </style>
</head>

<body>
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td align="center" style="background-color: #f4f4f4; padding: 20px 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" class="email-container">
                    <!-- Header -->
                    <tr>
                        <td class="email-header">
                            <img src="<?php echo e(asset('assets/emails/logo.png')); ?>" alt="DuplicationHub Logo" class="logo">
                            <h1 class="header-title"><?php echo $__env->yieldContent('header_title', 'DuplicationHub'); ?></h1>
                            <p class="header-subtitle"><?php echo $__env->yieldContent('header_subtitle', 'Empowering Your Digital Success'); ?></p>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td class="email-content">
                            <?php echo $__env->yieldContent('content'); ?>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td class="email-footer">
                            <div class="social-links">
                                <a href="https://linkedin.com/company/ewob" class="social-link" target="_blank">
                                    <div class="social-icon">in</div>
                                </a>
                                <a href="mailto:info@duplicationhub.ac.ke" class="social-link" target="_blank">
                                    <div class="social-icon">✉</div>
                                </a>
                            </div>
                            
                            <p class="footer-text">
                                Questions? <a href="mailto:info@duplicationhub.ac.ke" class="footer-link">Contact our support team</a>
                            </p>
                            
                            <p class="footer-text">
                                © <?php echo e(date('Y')); ?> <a href="https://duplicationhub.ac.ke/" class="footer-link">DuplicationHub School</a>. All rights reserved.
                            </p>
                            
                            <p class="footer-text" style="font-size: 12px; margin-top: 20px;">
                                This email was sent to you because you have an account with DuplicationHub. 
                                If you didn't expect this email, please ignore it.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html><?php /**PATH /var/www/html/resources/views/emails/master.blade.php ENDPATH**/ ?>