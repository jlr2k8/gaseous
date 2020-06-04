<input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response" />
<script src="https://www.google.com/recaptcha/api.js?render={$recaptcha_public_key}"></script>
{literal}
    <script>
        grecaptcha.ready(function() {
            grecaptcha.execute('{/literal}{$recaptcha_public_key}{literal}', {action: 'homepage'}).then(function(token) {
                document.getElementById('g-recaptcha-response').value = token;
                console.log('ReCaptcha v3 enabled');
            });
        });
    </script>
{/literal}