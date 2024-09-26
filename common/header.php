<!-- ... existing navigation bar code ... -->
<nav>
    <ul>
        <!-- ... other menu items ... -->
        <?php 
        $current_page = basename($_SERVER['PHP_SELF']);
        $profile_class = ($current_page == 'profile.php') ? 'active' : '';
        ?>
        <?php if (isset($_SESSION['user_id'])): ?>
            <li class="<?php echo $profile_class; ?>"><a href="/user/profile/profile.php">Profile</a></li>
        <?php endif; ?>
        <!-- ... other menu items ... -->
    </ul>
</nav>
<!-- ... rest of the header code ... -->