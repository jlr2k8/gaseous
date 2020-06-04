<ul>
    {if $edit_users || $archive_users}
        <li>
            <a href="/admin/users">
                User Management
            </a>
        </li>
    {/if}
    {if $add_roles || $edit_roles || $archive_roles}
        <li>
            <a href="/admin/roles">
                Role Management
            </a>
        </li>
    {/if}
    {if $add_content || $edit_content || $archive_content}
        <li>
            <a href="/admin/content">
                Content Management
            </a>
        </li>
    {/if}
    {if $manage_css}
        <li>
            <a href="/admin/css">
                CSS Style Management
            </a>
        </li>
    {/if}
    {if $manage_css}
        <li>
            <a href="/admin/js">
                JS Script Management
            </a>
        </li>
    {/if}
    {if $edit_settings}
        <li>
            <a href="/admin/settings">
                Settings Management
            </a>
        </li>
    {/if}
    {if $add_redirects || $edit_redirects || $archive_redirects}
        <li>
            <a href="/admin/redirects">
                URL Redirect Management
            </a>
        </li>
    {/if}
    {if $add_routes || $edit_routes || $archive_routes}
        <li>
            <a href="/admin/routes">
                URL Pattern Route Management
            </a>
        </li>
    {/if}
    {if $manage_menu}
        <li>
            <a href="/admin/menu">
                Site Menu Management
            </a>
        </li>
    {/if}
</ul>