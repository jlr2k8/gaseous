<div>
    <ul>
        {foreach $errors as $error}
            <li class="red_text display_block margin_on_bottom">{$error}</li>
        {/foreach}
    </ul>
</div>
<form action="/register/?access_code={$access_code}" id="registration" class="slight_margin slight_padding registration" method="post">
    <div>
        <label for="firstname">First name</label>
        <input required="required" id="firstname" name="firstname" type="text" value="{$firstname}" />
    </div>
    <div>
        <label for="lastname">Last name</label>
        <input required="required" id="lastname" name="lastname" type="text" value="{$lastname}" />
    </div>
    <div>
        <label for="username">Username</label>
        <input required="required" id="username" name="username" type="text" value="{$username}" />
    </div>
    <div>
        <label for="email">Email</label>
        <input required="required" id="email" name="email" type="email" value="{$email}" />
    </div>
    <div>
        <label for="password">Password</label>
        <input required="required" id="password" name="password" type="password" value="{$password}" />
    </div>
    <div>
        <label for="password">Confirm Password</label>
        <input required="required" id="confirm_password" name="confirm_password" type="password" value="{$confirm_password}" />
    </div>
    <input type="submit" value="Register" />
</form>