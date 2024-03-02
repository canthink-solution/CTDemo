<?php

include 'init.php';

// Check if user is login or not. if not login will redirect to page set at parameter 3
isLogin(true, 'isLogIn', 'app/views/auth-login');
