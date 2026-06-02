<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: ../login.php");
    exit;
}
include '../config/config.php';

$active_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$active_prop_id = isset($_GET['property_id']) ? $_GET['property_id'] : '-1';

// Handle Reply
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply_msg']) && $active_user_id > 0){
    $msg = $conn->real_escape_string($_POST['reply_msg']);
    $prop_val = ($active_prop_id != '-1' && $active_prop_id != '') ? intval($active_prop_id) : 'NULL';
    $sql = "INSERT INTO messages (sender_id, receiver_id, property_id, message) VALUES (0, $active_user_id, $prop_val, '$msg')";
    $conn->query($sql);
    header("Location: messages.php?user_id=$active_user_id&property_id=$active_prop_id");
    exit;
}

// Mark messages as read
if($active_user_id > 0){
    if($active_prop_id != '-1' && $active_prop_id != ''){
        $prop_val = intval($active_prop_id);
        $conn->query("UPDATE messages SET is_read=1 WHERE sender_id=$active_user_id AND receiver_id=0 AND property_id=$prop_val");
    } else {
        $conn->query("UPDATE messages SET is_read=1 WHERE sender_id=$active_user_id AND receiver_id=0 AND property_id IS NULL");
    }
}

// Fetch list of conversations (grouped by user and property)
// receiver=0 means to admin, sender=0 means from admin
$users_q = "SELECT u.id as user_id, u.name, u.email, m.property_id, p.title as prop_title,
                   SUM(CASE WHEN m.sender_id=u.id AND m.receiver_id=0 AND m.is_read=0 THEN 1 ELSE 0 END) as unread_count,
                   MAX(m.created_at) as last_msg_time
            FROM users u 
            JOIN messages m ON (m.sender_id=u.id OR m.receiver_id=u.id)
            LEFT JOIN properties p ON m.property_id = p.id
            WHERE u.role='user'
            GROUP BY u.id, m.property_id
            ORDER BY unread_count DESC, last_msg_time DESC";
$users_res = $conn->query($users_q);

// Fetch chat for active user and property
$chat_res = null;
$active_user_name = '';
$active_prop_title = '';
if($active_user_id > 0){
    $u_info = $conn->query("SELECT name FROM users WHERE id=$active_user_id")->fetch_assoc();
    $active_user_name = $u_info ? $u_info['name'] : 'Unknown User';
    
    $prop_cond = ($active_prop_id != '-1' && $active_prop_id != '') ? "m.property_id = " . intval($active_prop_id) : "m.property_id IS NULL";

    $chat_q = "SELECT m.*, p.title as prop_title 
               FROM messages m 
               LEFT JOIN properties p ON m.property_id = p.id 
               WHERE ((m.sender_id = $active_user_id AND m.receiver_id = 0) 
                  OR (m.sender_id = 0 AND m.receiver_id = $active_user_id))
                  AND $prop_cond
               ORDER BY m.created_at ASC";
    $chat_res = $conn->query($chat_q);
    
    if($active_prop_id != '-1' && $active_prop_id != ''){
        $p_info = $conn->query("SELECT title FROM properties WHERE id=".intval($active_prop_id))->fetch_assoc();
        $active_prop_title = $p_info ? $p_info['title'] : 'Unknown Property';
    }
}
?>
<?php include '../header.php'; ?>
<div class="container mt-5 min-vh-80 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bolder mb-1">User Messages</h2>
            <p class="text-muted">Manage inquiries and support requests</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline-custom">Back to Dashboard</a>
    </div>

    <div class="row g-0 card-custom overflow-hidden">
        
        <!-- Users List -->
        <div class="col-md-4 border-end bg-body">
            <div class="p-3 border-bottom bg-body-tertiary fw-bold">Conversations</div>
            <div class="overflow-auto" style="height: 600px;">
                <?php if($users_res->num_rows > 0): ?>
                    <?php while($u = $users_res->fetch_assoc()): ?>
                        <?php 
                            $u_prop = $u['property_id'] ? $u['property_id'] : '';
                            $is_active = ($active_user_id == $u['user_id'] && $active_prop_id == $u_prop);
                        ?>
                        <a href="messages.php?user_id=<?php echo $u['user_id']; ?>&property_id=<?php echo $u_prop; ?>" class="text-decoration-none text-dark">
                            <div class="chat-list-item p-3 border-bottom <?php echo $is_active ? 'active' : ''; ?>">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($u['name']); ?></h6>
                                    <?php if($u['unread_count'] > 0): ?>
                                        <span class="badge bg-danger rounded-pill"><?php echo $u['unread_count']; ?> new</span>
                                    <?php endif; ?>
                                </div>
                                <small class="text-muted text-truncate d-block mb-2"><?php echo htmlspecialchars($u['email']); ?></small>
                                <?php if($u['prop_title']): ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25" style="font-size: 0.7rem;"><i class="bi bi-house me-1"></i><?php echo htmlspecialchars($u['prop_title']); ?></span>
                                <?php else: ?>
                                    <span class="badge bg-body-tertiary text-dark border" style="font-size: 0.7rem;">General Inquiry</span>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="p-4 text-center text-muted">No conversations found.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Chat Window -->
        <div class="col-md-8 bg-body-tertiary d-flex flex-column" style="height: 600px;">
            <?php if($active_user_id > 0): ?>
                
                <!-- Chat Header -->
                <div class="p-3 bg-body border-bottom shadow-sm z-1">
                    <h5 class="mb-1 fw-bold"><?php echo htmlspecialchars($active_user_name); ?></h5>
                    <?php if($active_prop_id != '-1' && $active_prop_id != ''): ?>
                        <small class="text-muted"><i class="bi bi-house me-1"></i>Inquiry: <?php echo htmlspecialchars($active_prop_title); ?></small>
                    <?php else: ?>
                        <small class="text-muted">General Inquiry</small>
                    <?php endif; ?>
                </div>

                <!-- Chat Messages -->
                <div class="p-4 chat-container flex-grow-1 bg-body-tertiary d-flex flex-column gap-3">
                    <?php if($chat_res && $chat_res->num_rows > 0): ?>
                        <?php while($msg = $chat_res->fetch_assoc()): ?>
                            
                            <?php if($msg['sender_id'] == 0): ?>
                                <!-- Admin Reply -->
                                <div class="align-self-end w-75">
                                    <div class="bg-primary text-white p-3 rounded-3 shadow-sm mb-1" style="border-bottom-right-radius: 0 !important;">
                                        <p class="mb-0" style="white-space: pre-wrap;"><?php echo htmlspecialchars($msg['message']); ?></p>
                                    </div>
                                    <div class="text-end text-muted small">You • <?php echo date('M d, g:i A', strtotime($msg['created_at'])); ?></div>
                                </div>
                            <?php else: ?>
                                <!-- User Message -->
                                <div class="align-self-start w-75">
                                    <div class="bg-body border p-3 rounded-3 shadow-sm mb-1" style="border-bottom-left-radius: 0 !important;">
                                        <?php if($msg['prop_title']): ?>
                                            <div class="small text-muted mb-1 fw-medium border-bottom pb-1 mb-2"><i class="bi bi-house me-1"></i>Inquiry: <?php echo htmlspecialchars($msg['prop_title']); ?></div>
                                        <?php endif; ?>
                                        <p class="mb-0 text-dark" style="white-space: pre-wrap;"><?php echo htmlspecialchars($msg['message']); ?></p>
                                    </div>
                                    <div class="text-muted small"><?php echo htmlspecialchars($active_user_name); ?> • <?php echo date('M d, g:i A', strtotime($msg['created_at'])); ?></div>
                                </div>
                            <?php endif; ?>

                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center text-muted mt-5">No messages yet.</div>
                    <?php endif; ?>
                </div>

                <!-- Reply Form -->
                <div class="p-3 bg-body border-top">
                    <form method="POST" class="d-flex gap-2">
                        <textarea class="form-control" name="reply_msg" rows="2" placeholder="Type your reply here..." required></textarea>
                        <button type="submit" class="btn btn-primary-custom px-4"><i class="bi bi-send"></i></button>
                    </form>
                </div>

            <?php else: ?>
                <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                    <i class="bi bi-chat-square-dots fs-1 mb-3 opacity-25"></i>
                    <p class="fs-5">Select a conversation to start messaging</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
    // Auto-scroll chat to bottom
    const chatContainer = document.querySelector('.chat-container');
    if(chatContainer){
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
</script>
<?php include '../footer.php'; ?>
