<?php
require_once 'config.php';

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

function createUserDirect($email, $password, $firstName, $lastName) {
    global $conn;
    
    error_log("Creating user directly: $email, $firstName, $lastName");
    
    // Check if connection exists
    if (!$conn) {
        error_log("Database connection is null");
        return ['success' => false, 'error' => 'Database connection failed'];
    }
    
    $hashedPassword = hashPassword($password);
    
    try {
        // Check if user already exists
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if ($result->num_rows > 0) {
            return ['success' => false, 'error' => 'Email already exists'];
        }
        
        $stmt = $conn->prepare("INSERT INTO users (email, password, first_name, last_name, is_verified) VALUES (?, ?, ?, ?, 1)");
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param("ssss", $email, $hashedPassword, $firstName, $lastName);
        if (!$stmt->execute()) {
            throw new Exception('User insert failed: ' . $stmt->error);
        }
        
        $userId = $conn->insert_id;
        error_log("User created directly with ID: $userId");
        
        return ['success' => true, 'user_id' => $userId];
    } catch (Exception $e) {
        error_log("Direct user creation failed: " . $e->getMessage());
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function loginUser($email, $password) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT id, password, first_name, last_name, is_verified FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (verifyPassword($password, $user['password'])) {
            $sessionToken = generateToken();
            $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
            
            // Create session in database
            $stmt2 = $conn->prepare("INSERT INTO user_sessions (user_id, session_token, expires_at) VALUES (?, ?, ?)");
            $stmt2->bind_param("iss", $user['id'], $sessionToken, $expiresAt);
            $stmt2->execute();
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['session_token'] = $sessionToken;
            $_SESSION['logged_in'] = true;
            
            return ['success' => true, 'user' => $user];
        } else {
            return ['success' => false, 'error' => 'Invalid password'];
        }
    }
    
    return ['success' => false, 'error' => 'User not found'];
}

function resetPasswordDirect($email, $newPassword) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $hashedPassword = hashPassword($newPassword);
        
        try {
            $stmt2 = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt2->bind_param("si", $hashedPassword, $user['id']);
            $stmt2->execute();
            
            // Clear all sessions for this user
            $stmt3 = $conn->prepare("DELETE FROM user_sessions WHERE user_id = ?");
            $stmt3->bind_param("i", $user['id']);
            $stmt3->execute();
            
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Failed to reset password'];
        }
    }
    
    return ['success' => false, 'error' => 'Email not found'];
}

function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function logout() {
    if (isset($_SESSION['session_token']) && isset($_SESSION['user_id'])) {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM user_sessions WHERE user_id = ? AND session_token = ?");
        $stmt->bind_param("is", $_SESSION['user_id'], $_SESSION['session_token']);
        $stmt->execute();
    }
    
    session_destroy();
    header('Location: login.php');
    exit();
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePassword($password) {
    return strlen($password) >= 8;
}

function emailExists($email) {
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}
?>