<?php $__env->startSection('title', 'Test Email - DuplicationHub'); ?>

<?php $__env->startSection('header_title', 'Test Email'); ?>
<?php $__env->startSection('header_subtitle', 'Testing Our Email System'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-section">
    <h2 class="content-title">Hello from DuplicationHub! 👋</h2>
    
    <p class="content-text">
        This is a test email to verify that our email system is working correctly. 
        If you're receiving this email, congratulations! 🎉 Our email infrastructure is functioning perfectly.
    </p>
    
    <div class="info-card">
        <h3 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 18px;">📧 Email Details</h3>
        <p style="margin: 8px 0; color: #555;">
            <strong>Sent to:</strong> <?php echo e($to ?? 'your email address'); ?>

        </p>
        <p style="margin: 8px 0; color: #555;">
            <strong>Subject:</strong> <?php echo e($subject ?? 'Test Email from DuplicationHub API'); ?>

        </p>
        <p style="margin: 8px 0; color: #555;">
            <strong>Sent at:</strong> <?php echo e(now()->format('F j, Y \a\t g:i A')); ?>

        </p>
    </div>
    
    <p class="content-text">
        This test email demonstrates our new email template system with:
    </p>
    
    <div class="success-card">
        <ul style="margin: 0; padding-left: 20px; color: #155724;">
            <li style="margin: 8px 0;">✨ Professional and modern design</li>
            <li style="margin: 8px 0;">📱 Responsive layout for all devices</li>
            <li style="margin: 8px 0;">🎨 Beautiful gradients and styling</li>
            <li style="margin: 8px 0;">🔧 Easy to customize and extend</li>
            <li style="margin: 8px 0;">📧 Compatible with all major email clients</li>
        </ul>
    </div>
    
    <div class="btn-container">
        <a href="https://duplicationhub.ac.ke" class="btn" target="_blank">
            Visit Our Website
        </a>
    </div>
    
    <div class="warning-card">
        <h4 style="color: #856404; margin: 0 0 10px 0; font-size: 16px;">⚠️ Important Note</h4>
        <p style="margin: 0; color: #856404; font-size: 14px;">
            This is a test email sent from our development environment. 
            In production, you would receive emails for important account activities, 
            password resets, notifications, and more.
        </p>
    </div>
    
    <p class="content-text">
        Thank you for testing our email system! If you have any questions or need assistance, 
        feel free to reach out to our support team.
    </p>
    
    <div class="btn-container">
        <a href="mailto:info@duplicationhub.ac.ke" class="btn btn-secondary">
            Contact Support
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('emails.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/emails/test-email.blade.php ENDPATH**/ ?>