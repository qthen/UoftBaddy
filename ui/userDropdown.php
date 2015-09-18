<ul class="dropdown-menu dropdown-menu-right">
    <li class="dropdown-header">
        {{data.dropdown.user.username}}
    </li>
    <li class="divider"></li>
    <li class="link">
        <a ng-href="profile.php?id={{data.dropdown.user.user_id}}">
            Profile
        </a>
    </li>
    <li class="link">
        <a href="#">
            Your Activity
        </a>
    </li>
    <li class="link">
        <a href="all.php?tab=upcoming">
            {{data.dropdown.fields.upcoming_confirmed_badminton_dates}} Upcoming Badminton Events
        </a>
    </li>
    <li class="link">
        <a href="#">
            {{data.dropdown.fields.upcoming_threads_joined}} Upcoming Threads Joined
        </a>
    </li>
    <li class="divider"></li>
    <li class="link">
        <a href="logout.php">
            Logout
        </a>
    </li>
</ul>