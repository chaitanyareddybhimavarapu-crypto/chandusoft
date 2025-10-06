<?php
function flash($message = null) {
    if ($message) {
        $_SESSION['flash'] = $message;
        return;
    }

    if (isset($_SESSION['flash'])) {
        $msg = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $msg;
    }

    return null;
}
