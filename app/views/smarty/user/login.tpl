
<div>
    <p class="red_text display_block margin_on_bottom">{$login_message}</p>
</div>
<form action="{$full_web_url}/login/" id="login" method="post">
    <div>
        <label for="username">Username</label>
        <input required="required" id="username" name="username" type="text" />
    </div>
    <div>
        <label for="password">Password</label>
        <input required="required" id="password" name="password" type="password" />
    </div>

    {$recaptcha}

    <input type="submit" value="Login &#187;" />
</form>

<div>
    <button type="button" id="show_forgot_password">
        Forgot my password &#9785;
    </button>

    <form id="forgot_password_form" class="display_none" method="post" action="{$full_web_url}/login/?forgot_password=true">
        <label for="registered_email">
            Enter the email address associated with your account to continue.
            After submission, you should receive an email with instructions to proceed.
        </label>
        <input id="registered_email" name="registered_email" type="email" required="required" placeholder="Registered Email Address" />
        <input type="submit" value="Send Email &#187;" />
    </form>
</div>