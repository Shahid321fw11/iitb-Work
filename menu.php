<!-- <head> -->
<style>
    /* General Styles for Menu */
    #menu1 ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    #menu1 a {
        display: block;
        padding: 10px;
        color: #00527A;
        text-decoration: none;
        border-bottom: 1px solid #ccc;
    }

    #menu1 a:hover {
        /* background-color: #f0f0f0; */
        font-weight: bold;
    }

    /* Active Link Style */
    #menu1 a#current {
        background-color: #f0f0f0;
        font-weight: bold;
    }

    /* Submenu Styling */
    .submenu {
        display: none;
        padding-left: 20px;
        background-color: #e8e8e8;
        list-style: none;
    }

    .submenu li a {
        padding: 10px;
        color: #00527A;
    }

    /* Manager/Utilities Dropdown Arrow */
    .has-submenu>a::after {
        content: '\25BC';
        /* Down arrow */
        font-size: 12px;
        float: right;
        margin-left: 10px;
    }

    .has-submenu.open>a::after {
        content: '\25B2';
        /* Up arrow */
    }
</style>
<!-- </head> -->

<!-- <body> -->
<div style="width:20%; float: left;">
    <div id="menu1">
        <ul>
            <li>
                <a href="daily_reporting_entry.php" <?php if ($page == "entry") { ?> id="current" <?php } ?>>Daily Reporting Entry</a>
            </li>
            <li>
                <a href="my_calender.php" <?php if ($page == "calendar") { ?> id="current" <?php } ?>>My Calendar</a>
            </li>
            <li>
                <a href="my_daily_reporting_details.php" <?php if ($page == "details") { ?> id="current" <?php } ?>>My Daily Reporting Details</a>
            </li>
            <li class="has-submenu">
                <a href="#" <?php if ($page == "manager_utilities") { ?> id="current" <?php } ?>>Manager Utilities</a>
                <ul class="submenu">
                    <li>
                        <a href="manager_calender_view.php" <?php if ($page == "team_calendar") { ?> id="current" <?php } ?>>Team Calendar View</a>
                    </li>
                    <li>
                        <a href="team_reporting_details.php" <?php if ($page == "team_details") { ?> id="current" <?php } ?>>Team Reporting Details</a>
                    </li>
                </ul>
            </li>
            <li class="has-submenu">
                <a href="#" <?php if ($page == "admin_utilities") { ?> id="current" <?php } ?>>Admin Utilities</a>
                <ul class="submenu">
                    <li>
                        <a href="all_calendar_view.php" <?php if ($page == "all_calendar") { ?> id="current" <?php } ?>>All Calendar View</a>
                    </li>
                    <li>
                        <a href="all_reporting_details.php" <?php if ($page == "all_details") { ?> id="current" <?php } ?>>All Reporting Details</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="logout.php" <?php if ($page == "logout") { ?> id="current" <?php } ?>>Logout</a>
            </li>
        </ul>
    </div>
</div>

<script>
    // JavaScript for toggling submenus and rotating arrows
    document.addEventListener('DOMContentLoaded', function() {
        const submenuToggles = document.querySelectorAll('.has-submenu > a');

        submenuToggles.forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const parentLi = this.parentElement;

                // Toggle the 'open' class to show/hide submenu
                parentLi.classList.toggle('open');

                // Toggle the submenu display
                const submenu = parentLi.querySelector('.submenu');
                if (submenu) {
                    submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
                }
            });
        });
    });
</script>