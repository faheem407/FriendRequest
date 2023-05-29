<?php 
    // Import DB file for the connection to database
    require("DB.php");

    // Create class user to interact with User related tables
    class User {
        public function __construct() {
             // DB connection created
            $this->db = new DB();
        }
        public function getUserInfoLogged($search=NULL) {
            // Get the Info of users if the user is logged in
            $sql = "SELECT fullname, username, img_path, email, addr, dob, rel_status, loc FROM user";
            if ($search != NULL) {
                $sql = $sql . " WHERE username LIKE '" . $search . "%'";
            }
            return $this->db->get_results($sql);
        }
        public function getUserInfoNotLogged($search=NULL) {
            // Get the Info of users if the user is not logged in
            $sql = "SELECT fullname, username, img_path FROM user";
            if ($search != NULL) {
                $sql = $sql . " WHERE username LIKE '" . $search . "%'";
            }
            return $this->db->get_results($sql);
        }
        public function register($email=NULL, $fullname=NULL, $username=NULL, $img_path="", $addr="", $dob="", $rel_status="", $loc="", $pass=NULL) {
            // Register a new user
            if (!$email || !$fullname || !$username || !$pass) {
                return FALSE;
            }
            $sql = "INSERT INTO user(email, fullname, username, img_path, addr, dob, rel_status, loc, pass) VALUES ('$email', '$fullname', '$username', '$img_path', '$addr', STR_TO_DATE('$dob', '%Y-%m-%d'), '$rel_status', '$loc', '" . password_hash($pass, PASSWORD_DEFAULT) . "');";
            if($this->db->conn->query($sql) !== TRUE) {
                return FALSE;
            }
            return TRUE;
        }
        public function login($username, $password) {
            // Login the user based in details
            $sql = "SELECT pass FROM user WHERE username='$username'";
            $res = $this->db->conn->query($sql);
            if ($res->num_rows > 0) {
                if (password_verify($password, $res->fetch_row()[0])) {
                    return TRUE;
                }
            }
            return FALSE;
        }
        public function getUserImgPath() {
            // Get Image storage path of the user logged in
            $username = $_SESSION["login"];
            $sql = "SELECT img_path FROM user WHERE username='$username'";
            $res = $this->db->get_results($sql);
            if ($res[0][0] != "") {
                return $res[0][0];
            }
            else {
                return "default.jpeg";
            }
        }
        public function get_friends_list($username) {
            // Get list of friends of the user
            $sql = "SELECT username FROM user WHERE user.UserID IN ((SELECT friend_id FROM user_friends WHERE user_id=(SELECT UserID FROM user WHERE username='$username') AND accepted=TRUE) UNION (SELECT user_id FROM user_friends WHERE friend_id=(SELECT UserID FROM user WHERE username='$username') AND accepted=TRUE));";
            return $this->db->get_results($sql);
        }
        public function get_friend_request_sent_list($username) {
            // Get list of people who the logged in user has sent friend request
            $sql = "SELECT username FROM user WHERE user.UserID IN (SELECT friend_id FROM user_friends WHERE user_id=(SELECT UserID FROM user WHERE username='$username') AND accepted=FALSE);";
            return $this->db->get_results($sql);
        }
        public function get_friend_request_received_list($username) {
            // Get list of people from whom the logged in user has received friend request
            $sql = "SELECT username FROM user WHERE user.UserID IN (SELECT user_id FROM user_friends WHERE friend_id=(SELECT UserID FROM user WHERE username='$username') AND accepted=FALSE);";
            return $this->db->get_results($sql);
        }
        public function remove_friend($username, $friendname) {
            // Remove a friend/friend request
            $sql = "DELETE FROM user_friends WHERE (user_id=(SELECT UserID FROM user WHERE username='$friendname') AND friend_id=(SELECT UserID FROM user WHERE username='$username')) OR (friend_id=(SELECT UserID FROM user WHERE username='$friendname') AND user_id=(SELECT UserID FROM user WHERE username='$username'));";
            $res = $this->db->conn->query($sql);
            return TRUE;
        }
        public function add_friend($username, $friendname) {
            // Add a new friend (Send a request)
            $my_id = $this->db->conn->query("SELECT UserID FROM user WHERE username='$username'")->fetch_row()[0];
            $user_id = $this->db->conn->query("SELECT UserID FROM user WHERE username='$friendname'")->fetch_row()[0];
            $sql = "INSERT INTO user_friends(user_id, friend_id) VALUES ($my_id, $user_id);";
            $res = $this->db->conn->query($sql);
            return TRUE;
        }
        public function accept_friend($username, $friendname) {
            // Accept friend request
            $my_id = $this->db->conn->query("SELECT UserID FROM user WHERE username='$username'")->fetch_row()[0];
            $user_id = $this->db->conn->query("SELECT UserID FROM user WHERE username='$friendname'")->fetch_row()[0];
            $sql = "UPDATE user_friends SET accepted=TRUE WHERE user_id=$user_id AND friend_id=$my_id;";
            $res = $this->db->conn->query($sql);
            return TRUE;
        }
    }
?>