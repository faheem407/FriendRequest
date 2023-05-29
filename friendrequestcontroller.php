<?php
foreach(array_keys($_POST) as $key) {
    // Call remove friend function if the reomve friend/cancel friend request/decline friend request button is clicked
    if (str_starts_with($key, "removefriend__") || str_starts_with($key, "cancelfriend__") || str_starts_with($key, "declinefriend__")) {
        $username = explode('__', $key, 2)[1];
        $user->remove_friend($_SESSION["login"], $username);
    }

    // Call add_friend function if Add friend button is clicked
    elseif (str_starts_with($key, "addfriend__")) {
        $username = explode('__', $key, 2)[1];
        $user->add_friend($_SESSION["login"], $username);
    }
    
    // Call accept_friend function if Accept Friend Request button is clicked
    elseif (str_starts_with($key, "acceptfriend__")) {
        $username = explode('__', $key, 2)[1];
        $user->accept_friend($_SESSION["login"], $username);
    }
}
?>