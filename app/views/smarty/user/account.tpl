<div class="one-third_left">
    <div>
        <ul>
            {foreach $errors as $error}
                <li class="red_text display_block margin_on_bottom">{$error}</li>
            {/foreach}
        </ul>
    </div>
    <form action="/account/" id="account" class="slight_margin slight_padding registration" method="post">
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
            <input required="required" readonly="readonly" id="username" name="username" type="text" value="{$username}" />
        </div>
        <div>
            <label for="email">Email</label>
            <input required="required" id="email" name="email" type="email" value="{$email}" />
        </div>

        <div class="gray_background slight_padding">
            <h3>
                Change password?
            </h3>
            {if empty($token_login)}
                <div>
                    <label for="current_password">Current Password</label>
                    <input id="current_password" name="current_password" type="password" />
                </div>
            {/if}
            <div>
                <label for="password">New Password</label>
                <input id="password" name="password" type="password" />
            </div>
            <div>
                <label for="password">Confirm Password</label>
                <input id="confirm_password" name="confirm_password" type="password" />
            </div>
        </div>

        <input type="submit" value="Update &#187" />
    </form>
</div>