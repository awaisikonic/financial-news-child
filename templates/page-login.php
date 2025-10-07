<?php

/**
 * Template Name: User Login
 */

if (is_user_logged_in()) {
    wp_redirect(bp_core_get_user_domain(get_current_user_id()));
    exit;
}

$recaptcha_site_key = get_option('recaptcha_site_key');

get_header(); ?>

<div class="auth-container">
    <div class="auth-form-wrapper">
        <div class="auth-header">
            <h2>Welcome Back</h2>
            <p>Sign in to your account</p>
        </div>

        <form id="user-login-form" class="auth-form">
            <div class="form-group">
                <label for="login-username">Username or Email *</label>
                <input type="text" id="login-username" name="username" required>
                <span class="error-message" id="username-error"></span>
            </div>

            <div class="form-group">
                <label for="login-password">Password *</label>
                <input type="password" id="login-password" name="password" required>
                <span class="error-message" id="password-error"></span>
            </div>

            <div class="form-options">
                <label class="remember-me">
                    <input type="checkbox" name="rememberme" value="forever">
                    <span>Remember me</span>
                </label>
                <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="forgot-password">Forgot password?</a>
            </div>

            <?php wp_nonce_field('user_login_nonce', 'login_nonce'); ?>

            <div class="form-submit">
                <button type="submit" id="login-submit" class="auth-button"
                    data-sitekey="<?php echo esc_attr($recaptcha_site_key); ?>">
                    <span class="button-text">Sign In</span>
                    <span class="loading-spinner" style="display: none;"></span>
                </button>
            </div>

            <div class="auth-links">
                <p>Don't have an account? <a href="<?php echo esc_url(home_url('/register-2')); ?>">Sign Up</a></p>
            </div>
        </form>

        <div id="login-messages"></div>
    </div>
</div>

<!-- Include reCAPTCHA API -->
<?php if (!empty($recaptcha_site_key)): ?>
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo esc_attr($recaptcha_site_key); ?>"></script>
<?php endif; ?>

<style>
    /* Your existing CSS styles remain the same */
    .auth-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px;
    }

    .auth-form-wrapper {
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 450px;
    }

    .auth-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .auth-header h2 {
        color: #333;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .auth-header p {
        color: #666;
        margin: 0;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        color: #333;
        font-weight: 500;
    }

    .form-group input {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e1e5e9;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }

    .form-group input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-group input.error {
        border-color: #e74c3c;
    }

    .error-message {
        color: #e74c3c;
        font-size: 12px;
        margin-top: 5px;
        display: block;
    }

    .form-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .remember-me {
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .remember-me input {
        margin-right: 8px;
        width: auto;
    }

    .forgot-password {
        color: #667eea;
        text-decoration: none;
        font-size: 14px;
    }

    .forgot-password:hover {
        text-decoration: underline;
    }

    .auth-button {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .auth-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    .auth-button:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    .auth-links {
        text-align: center;
        margin-top: 20px;
    }

    .auth-links a {
        color: #667eea;
        text-decoration: none;
        font-weight: 500;
    }

    .auth-links a:hover {
        text-decoration: underline;
    }

    .success-message {
        background: #d4edda;
        color: #155724;
        padding: 12px;
        border-radius: 6px;
        margin-top: 15px;
        text-align: center;
    }

    .error-message-global {
        background: #f8d7da;
        color: #721c24;
        padding: 12px;
        border-radius: 6px;
        margin-top: 15px;
        text-align: center;
    }

    .loading-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #ffffff;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>

<script>
    jQuery(document).ready(function($) {
        var recaptchaSiteKey = '<?php echo esc_js($recaptcha_site_key); ?>';

        $('#user-login-form').on('submit', function(e) {
            e.preventDefault();

            var $form = $(this);
            var $submitBtn = $('#login-submit');
            var $buttonText = $submitBtn.find('.button-text');
            var $loadingSpinner = $submitBtn.find('.loading-spinner');
            var $messages = $('#login-messages');

            // Clear previous messages and errors
            $messages.empty();
            $('.error-message').empty();
            $('.form-group input').removeClass('error');

            // Show loading state
            $buttonText.hide();
            $loadingSpinner.show();
            $submitBtn.prop('disabled', true);

            // Execute reCAPTCHA
            if (recaptchaSiteKey) {
                grecaptcha.ready(function() {
                    grecaptcha.execute(recaptchaSiteKey, {
                        action: 'login'
                    }).then(function(token) {
                        submitLoginForm(token);
                    });
                });
            } else {
                submitLoginForm('');
            }

            function submitLoginForm(recaptchaToken) {
                // Prepare form data
                var formData = {
                    action: 'user_login',
                    username: $('#login-username').val(),
                    password: $('#login-password').val(),
                    rememberme: $('input[name="rememberme"]').is(':checked') ? 'forever' : '',
                    login_nonce: $('#login_nonce').val(),
                    recaptcha_token: recaptchaToken
                };

                $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $messages.html('<div class="success-message">' + response.data.message + '</div>');

                            // Redirect to BuddyPress profile after 1 second
                            setTimeout(function() {
                                window.location.href = response.data.redirect_url;
                            }, 1000);
                        } else {
                            if (response.data.errors) {
                                // Display field-specific errors
                                $.each(response.data.errors, function(field, error) {
                                    $('#' + field + '-error').text(error);
                                    $('#' + field).addClass('error');
                                });
                            }
                            $messages.html('<div class="error-message-global">' + response.data.message + '</div>');
                        }
                    },
                    error: function() {
                        $messages.html('<div class="error-message-global">An error occurred. Please try again.</div>');
                    },
                    complete: function() {
                        $buttonText.show();
                        $loadingSpinner.hide();
                        $submitBtn.prop('disabled', false);
                    }
                });
            }
        });
    });
</script>

<?php get_footer(); ?>