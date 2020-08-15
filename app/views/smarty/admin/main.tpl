<ul>
    {if $edit_users || $archive_users}
        <li>
            <a href="{$full_web_url}/admin/users">
                User Management
            </a>
        </li>
    {/if}
    {if $add_roles || $edit_roles || $archive_roles}
        <li>
            <a href="{$full_web_url}/admin/roles">
                Role Management
            </a>
        </li>
    {/if}
    {if $add_content || $edit_content || $archive_content}
        <li>
            <a href="{$full_web_url}/admin/content">
                Content Management
            </a>
        </li>
    {/if}
    {if $manage_css}
        <li>
            <a href="{$full_web_url}/admin/css">
                CSS Style Management
            </a>
        </li>
    {/if}
    {if $manage_css}
        <li>
            <a href="{$full_web_url}/admin/js">
                JS Script Management
            </a>
        </li>
    {/if}
    {if $edit_settings}
        <li>
            <a href="{$full_web_url}/admin/settings">
                Settings Management
            </a>
        </li>
    {/if}
    {if $add_redirects || $edit_redirects || $archive_redirects}
        <li>
            <a href="{$full_web_url}/admin/redirects">
                URL Redirect Management
            </a>
        </li>
    {/if}
    {if $add_routes || $edit_routes || $archive_routes}
        <li>
            <a href="{$full_web_url}/admin/routes">
                URL Pattern Route Management
            </a>
        </li>
    {/if}
    {if $manage_menu}
        <li>
            <a href="{$full_web_url}/admin/menu">
                Site Menu Management
            </a>
        </li>
    {/if}
    {if $perform_updates}
        <li>
            <a href="{$full_web_url}/admin/update">
                Perform Updates
            </a>
        </li>
    {/if}
</ul>