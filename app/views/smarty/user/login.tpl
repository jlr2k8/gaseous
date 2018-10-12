
<div>
    <p class="red_text display_block margin_on_bottom">{$login_message}</p>
</div>
<form action="/login/" id="login" class="floating_form login" method="post">
    <div class="floating_form_label_and_input">
        <label for="username">Username</label>
        <input required="required" id="username" name="username" type="text" />
    </div>
    <div class="floating_form_label_and_input">
        <label for="password">Password</label>
        <input required="required" id="password" name="password" type="password" />
    </div>

    {$recaptcha}

    <input type="submit" value="Login &#187;" />
</form>