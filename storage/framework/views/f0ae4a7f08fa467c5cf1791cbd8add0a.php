<?php $__env->startSection('title', 'Password Reset - DuplicationHub'); ?>

<?php $__env->startSection('header_title', 'Password Reset'); ?>
<?php $__env->startSection('header_subtitle', 'Secure Your Account'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-section">
    <h2 class="content-title">Hello <?php echo e($user->first_name); ?>! 🔐</h2>
    
    <p class="content-text">
        We received a request to reset your password for your DuplicationHub account. 
        If you didn't make this request, you can safely ignore this email.
    </p>
    
    <div class="info-card">
        <h3 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 18px;">📧 Reset Your Password</h3>
        <p style="margin: 0; color: #555;">
            To reset your password, click the button below. This will take you to a secure page 
            where you can create a new password for your account.
        </p>
    </div>
    
    <div class="btn-container">
        <a href="<?php echo e($resetUrl); ?>" class="btn" target="_blank">
            Reset My Password
        </a>
    </div>
    
    <div class="warning-card">
        <h4 style="color: #856404; margin: 0 0 10px 0; font-size: 16px;">⚠️ Security Information</h4>
        <p style="margin: 0; color: #856404; font-size: 14px;">
            <strong>This password reset link will expire in 24 hours</strong> for your security. 
            If you need more time, you can request a new reset link.
        </p>
    </div>
    
    <p class="content-text">
        If the button above doesn't work, you can copy and paste this link into your browser:
    </p>
    
    <div class="info-card">
        <p style="margin: 0; color: #667eea; font-size: 14px; word-break: break-all; font-family: monospace;">
            <?php echo e($resetUrl); ?>

        </p>
    </div>
    
    <div class="success-card">
        <h4 style="color: #155724; margin: 0 0 10px 0; font-size: 16px;">✅ What happens next?</h4>
        <ol style="margin: 0; padding-left: 20px; color: #155724;">
            <li style="margin: 8px 0;">Click the reset button or copy the link above</li>
            <li style="margin: 8px 0;">Enter your new password on the secure page</li>
            <li style="margin: 8px 0;">Confirm your new password</li>
            <li style="margin: 8px 0;">You'll be redirected to login with your new password</li>
        </ol>
    </div>
    
    <p class="content-text">
        If you have any questions or need assistance, please don't hesitate to contact our support team.
    </p>
    
    <div class="btn-container">
        <a href="mailto:info@duplicationhub.ac.ke" class="btn btn-secondary">
            Contact Support
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('emails.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/emails/password-reset.blade.php ENDPATH**/ ?>