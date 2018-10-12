<div>
    <ul>
        {foreach $errors as $error}
            <li class="red_text display_block margin_on_bottom">{$error}</li>
        {/foreach}
    </ul>
</div>
<form action="/register/{$access_code}/" id="registration" class="floating_form registration" method="post">
    <div class="floating_form_label_and_input">
        <label for="firstname">First name</label>
        <input required="required" id="firstname" name="firstname" type="text" value="{$firstname}" />
    </div>
    <div class="floating_form_label_and_input">
        <label for="lastname">Last name</label>
        <input required="required" id="lastname" name="lastname" type="text" value="{$lastname}" />
    </div>
    <div class="floating_form_label_and_input">
        <label for="username">Username</label>
        <input required="required" id="username" name="username" type="text" value="{$username}" />
    </div>
    <div class="floating_form_label_and_input">
        <label for="email">Email</label>
        <input required="required" id="email" name="email" type="email" value="{$email}" />
    </div>
    <div class="floating_form_label_and_input">
        <label for="password">Password</label>
        <input required="required" id="password" name="password" type="password" value="{$password}" />
    </div>
    <div class="floating_form_label_and_input">
        <label for="password">Confirm Password</label>
        <input required="required" id="confirm_password" name="confirm_password" type="password" value="{$confirm_password}" />
    </div>
    <input type="submit" value="Register" />
</form>