<html>
    <head>
        <style>
            .diff_delete {
                background-color: red;
                color: white;
                font-weight: bold;
            }
            .diff_insert {
                background-color: lightgreen;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <table>
            {foreach from=$compare key=key item=val}
                {if $key == 'body'}
                    <tr>
                        <td style="border: 1px solid black" colspan="2">
                            {$key}:<br />{$val}
                        </td>
                    </tr>
                {else}

                    <tr>
                        <td>{$key}:</td>
                        <td>{$val}</td>
                    </tr>
                {/if}
            {/foreach}
        </table>
    </body>
</html>
