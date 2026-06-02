<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'user'){
    header("Location: ../login.php");
    exit;
}
include '../config/config.php';
$user_id = $_SESSION['user']['id'];

$msg_status = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $message = $conn->real_escape_string($_POST['message']);
    $prop_id = !empty($_POST['property_id']) ? intval($_POST['property_id']) : 'NULL';
    
    // receiver 0 means Admin
    $sql = "INSERT INTO messages (sender_id, receiver_id, property_id, message) VALUES ($user_id, 0, $prop_id, '$message')";
    if($conn->query($sql)){
        $msg_status = "Message sent successfully! We will get back to you soon.";
    } else {
        $msg_status = "Error sending message.";
    }
}

$pre_prop_id = isset($_GET['property_id']) ? intval($_GET['property_id']) : '';

// Fetch conversation
$chat_q = "SELECT m.*, p.title as prop_title 
           FROM messages m 
           LEFT JOIN properties p ON m.property_id = p.id 
           WHERE m.sender_id = $user_id OR m.receiver_id = $user_id 
           ORDER BY m.created_at ASC";
$chat_res = $conn->query($chat_q);
?>
<?php include '../header.php'; ?>
<div class="container mt-5 min-vh-80 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bolder mb-1">Contact Support</h2>
            <p class="text-muted">Send us an inquiry or ask about a property</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline-custom">Back to Dashboard</a>
    </div>

    <div class="row g-5">
        <!-- Message Form -->
        <div class="col-lg-5">
            <div class="card-custom p-4 sticky-top" style="top: 100px;">
                <h4 class="fw-bold mb-4">Send a Message</h4>
                
                <?php if($msg_status): ?>
                    <div class="alert alert-info shadow-sm">
                        <?php echo $msg_status; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <?php if($pre_prop_id): ?>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Inquiring about Property ID: <?php echo $pre_prop_id; ?></label>
                            <input type="hidden" name="property_id" value="<?php echo $pre_prop_id; ?>">
                        </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Your Message</label>
                        <textarea class="form-control form-control-custom" name="message" rows="6" placeholder="How can we help you?" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary-custom w-100 py-2 fw-bold">
                        <i class="bi bi-send me-2"></i>Send Message
                    </button>
                </form>
            </div>
        </div>

        <!-- Conversation History -->
        <div class="col-lg-7">
            <div class="card-custom p-4 p-md-5 bg-body">
                <h4 class="fw-bold mb-4 border-bottom pb-3">Your Conversation History</h4>
                
                <div class="d-flex flex-column gap-4 chat-container" style="max-height: 600px; overflow-y: auto; padding-right: 10px;">
                    <?php if($chat_res->num_rows > 0): ?>
                        <?php while($msg = $chat_res->fetch_assoc()): ?>
                            
                            <?php if($msg['sender_id'] == $user_id): ?>
                                <!-- User Message -->
                                <div class="align-self-end w-75">
                                    <div class="bg-primary text-white p-3 rounded-3 shadow-sm mb-1" style="border-bottom-right-radius: 0 !important;">
                                        <?php if($msg['prop_title']): ?>
                                            <div class="small text-white-50 mb-1 fw-medium"><i class="bi bi-house me-1"></i>RE: <?php echo htmlspecialchars($msg['prop_title']); ?></div>
                                        <?php endif; ?>
                                        <p class="mb-0" style="white-space: pre-wrap;"><?php echo htmlspecialchars($msg['message']); ?></p>
                                    </div>
                                    <div class="text-end text-muted small">You • <?php echo date('M d, g:i A', strtotime($msg['created_at'])); ?></div>
                                </div>
                            <?php else: ?>
                                <!-- Admin Reply -->
                                <div class="align-self-start w-75">
                                    <div class="bg-body-tertiary border p-3 rounded-3 shadow-sm mb-1" style="border-bottom-left-radius: 0 !important;">
                                        <div class="small text-primary mb-1 fw-bold"><i class="bi bi-shield-lock me-1"></i>Support Team</div>
                                        <p class="mb-0 text-dark" style="white-space: pre-wrap;"><?php echo htmlspecialchars($msg['message']); ?></p>
                                    </div>
                                    <div class="text-muted small">Admin • <?php echo date('M d, g:i A', strtotime($msg['created_at'])); ?></div>
                                </div>
                            <?php endif; ?>

                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-chat-dots fs-1 d-block mb-3 opacity-50"></i>
                            <p>No messages yet. Send us an inquiry!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto-scroll chat to bottom
    const chatContainer = document.querySelector('.chat-container');
    if(chatContainer){
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
</script>
<?php include '../footer.php'; ?>
