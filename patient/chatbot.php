<?php
session_start();
require_once '../config/db.php';

if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'patient') {
    echo "Unauthorized access";
    exit();
}

// Check if page is loaded in iframe
$in_iframe = isset($_SERVER['HTTP_SEC_FETCH_DEST']) && $_SERVER['HTTP_SEC_FETCH_DEST'] === 'iframe';

// Adjust the styling if in iframe
$container_style = $in_iframe ? 'height: 100vh; margin: 0; padding: 0;' : '';
?>
<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration
$config = [
    'gemini_api_key' => 'AIzaSyAPe4pHwlPPxH65Z3s_Ukit353vtyi2D6U' // Your API key
];

// Upload directory
$upload_dir = "uploads/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Call Gemini API for chatbot responses
function callGeminiAPI($prompt, $apiKey, $imageBase64 = null) {
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $apiKey;
    
    // Prepare the request data based on whether we have an image
    if ($imageBase64) {
        // Request with both text and image
        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inline_data' => [
                                'mime_type' => 'image/jpeg',
                                'data' => $imageBase64
                            ]
                        ]
                    ]
                ]
            ]
        ];
    } else {
        // Text-only request
        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ];
    }
    
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode($data),
            'timeout' => 120 // Extended timeout for image processing
        ]
    ];
    
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    
    if ($response === FALSE) {
        // Get the last error
        $error = error_get_last();
        return ["success" => false, "message" => "Error calling Gemini API: " . ($error['message'] ?? 'Unknown error')];
    }
    
    $responseData = json_decode($response, true);
    
    // Debug - log the response
    error_log("API Response: " . print_r($responseData, true));
    
    if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
        $botResponse = $responseData['candidates'][0]['content']['parts'][0]['text'];
        return ["success" => true, "message" => $botResponse];
    } else {
        // Return more detailed error info
        return [
            "success" => false, 
            "message" => "No valid response from the API", 
            "debug" => isset($responseData['error']) ? print_r($responseData['error'], true) : print_r($responseData, true)
        ];
    }
}

// Handle AJAX request for chatbot
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header('Content-Type: application/json');
    
    // Check if there's a message or an image
    if (isset($_POST['message']) || isset($_FILES['image'])) {
        $userMessage = $_POST['message'] ?? "Analyze this image";
        $imageBase64 = null;
        $uploadedImage = false;
        
        // Handle image upload if present
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_file = $upload_dir . basename($_FILES['image']['name']);
            $imageFileType = strtolower(pathinfo($upload_file, PATHINFO_EXTENSION));
            
            // Check if it's an actual image
            $check = getimagesize($_FILES['image']['tmp_name']);
            if($check !== false) {
                // Convert image to base64
                $imageData = file_get_contents($_FILES['image']['tmp_name']);
                $imageBase64 = base64_encode($imageData);
                $uploadedImage = true;
                
                // Move the uploaded file to the uploads directory
                move_uploaded_file($_FILES['image']['tmp_name'], $upload_file);
            } else {
                echo json_encode(["success" => false, "message" => "The file is not a valid image."]);
                exit;
            }
        }
        
        // MODIFIED: Updated prompts to request concise 4-5 line responses
        if ($uploadedImage) {
            $prompt = "As a healthcare assistant, briefly analyze this medical image in 4-5 lines maximum. " .
                      "Be concise, provide only essential first aid steps if needed, and mention consulting a doctor. " .
                      "The patient's question is: " . $userMessage . " Keep your entire response to 4-5 lines only.";
        } else {
            $prompt = "As a healthcare assistant, answer the following patient question in 4-5 lines maximum. " .
                      "Be extremely concise and direct. Provide only essential information. " .
                      "Question: " . $userMessage . " Keep your entire response to 4-5 lines only.";
        }
        
        $result = callGeminiAPI($prompt, $config['gemini_api_key'], $imageBase64);
        echo json_encode($result);
        exit;
    }
    
    echo json_encode(["success" => false, "message" => "No message or image provided"]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Healthcare Chatbot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #1a73e8;
            --sidebar-color: #f1f5fe;
            --main-bg-color: #ffffff;
            --user-msg-color: #f7f9fc;
            --bot-msg-color: #ffffff;
            --input-bg-color: #f5f7fa;
            --text-color: #202124;
            --border-color: #e8eaed;
            --sidebar-hover: #e8f0fe;
            --button-color: #1a73e8;
        }
        
        body {
            background-color: var(--main-bg-color);
            color: var(--text-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
        }
        
        /* Sidebar */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-color);
            height: 100%;
            display: flex;
            flex-direction: column;
            color: var(--text-color);
            border-right: 1px solid var(--border-color);
        }
        
        .new-chat-btn {
            margin: 15px;
        }
        
        .new-chat-btn button {
            background-color: var(--primary-color);
            border: none;
            color: white;
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .chat-history {
            flex: 1;
            padding: 10px 15px;
            overflow-y: auto;
        }
        
        .history-item {
            padding: 10px;
            margin-bottom: 5px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background-color 0.2s;
        }
        
        .history-item:hover {
            background-color: var(--sidebar-hover);
        }
        
        .user-info {
            padding: 15px;
            border-top: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-avatar {
            width: 35px;
            height: 35px;
            background-color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }
        
        /* Main Chat Area */
        .main-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .chat-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding-bottom: 20px;
        }
        
        .message-row {
            display: flex;
            padding: 20px;
            transition: background-color 0.2s;
        }
        
        .user-row {
            background-color: var(--user-msg-color);
        }
        
        .bot-row {
            background-color: var(--bot-msg-color);
            border-bottom: 1px solid var(--border-color);
        }
        
        .message-container {
            max-width: 800px;
            margin: 0 auto;
            width: 100%;
            display: flex;
            gap: 20px;
        }
        
        .avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: white;
        }
        
        .user-avatar {
            background-color: #5682db;
        }
        
        .bot-avatar {
            background-color: var(--primary-color);
        }
        
        .message-content {
            flex: 1;
            line-height: 1.6;
        }
        
        /* Input Area */
        .chat-input-container {
            border-top: 1px solid var(--border-color);
            padding: 20px;
            margin-top: auto;
            background-color: white;
        }
        
        .chat-input-wrapper {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
        }
        
        .chat-input {
            width: 100%;
            background-color: var(--input-bg-color);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 15px 50px 15px 15px;
            color: var(--text-color);
            resize: none;
            min-height: 52px;
            max-height: 200px;
            overflow-y: auto;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .chat-input:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        .send-button {
            position: absolute;
            right: 10px;
            bottom: 45px;
            background: var(--primary-color);
            border: none;
            color: white;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
        }
        
        .send-button:hover {
            background-color: #0d61cb;
        }
        
        .input-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            color: #5f6368;
            font-size: 12px;
        }
        
        /* File Upload */
        .file-upload {
            position: relative;
            overflow: hidden;
            cursor: pointer;
            color: var(--primary-color);
            
            
        }
        
        .file-upload input[type=file] {
            position: absolute;
            font-size: 100px;
            right: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
            
        }
        /* File Upload */
.file-upload {
    position: absolute;
    right: 60px;   /* send button ke thoda left me */
    bottom: 45px;
    cursor: pointer;
    color: var(--primary-color);
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background-color: var (--input-bg-color);
}

.file-upload:hover {
    background-color: #e5e5e5;
}

.file-upload input[type=file] {
    position: absolute;
    font-size: 100px;
    right: 0;
    top: 0;
    opacity: 0;
    cursor: pointer;
}

        
        .image-preview-container {
            margin-top: 8px;
            display: none;
            background-color: var(--input-bg-color);
            border-radius: 8px;
            padding: 8px;
        }
        
        .image-preview-inner {
            position: relative;
            display: inline-block;
        }
        
        .image-preview {
            max-width: 150px;
            max-height: 150px;
            border-radius: 8px;
        }
        
        .remove-image {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: rgba(0, 0, 0, 0.7);
            border: none;
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            cursor: pointer;
        }
        
        .typing-indicator {
            display: none;
            padding: 20px;
            background-color: var(--bot-msg-color);
        }
        
        .typing-container {
            max-width: 800px;
            margin: 0 auto;
            width: 100%;
            display: flex;
            gap: 20px;
        }
        
        .typing-dots {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .typing-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: #5f6368;
            animation: typing 1s infinite ease-in-out;
        }
        
        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }
        
        @keyframes typing {
            0% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
            100% { transform: translateY(0); }
        }
        
        .uploaded-image {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            margin-top: 10px;
        }
        
        /* Mobile responsive */
        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
            
            .message-container {
                padding: 10px;
            }
        }

        /* Button to show sidebar on mobile */
        .mobile-sidebar-toggle {
            display: none;
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 1000;
            background-color: var(--primary-color);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-size: 20px;
        }
        
        @media (max-width: 768px) {
            .mobile-sidebar-toggle {
                display: block;
            }
            
            .sidebar.active {
                display: block;
                position: fixed;
                z-index: 999;
                top: 0;
                left: 0;
                height: 100%;
            }
        }
        
        /* Header with logo */
        .chat-header {
            padding: 12px 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            background-color: white;
        }
        
        .chat-logo {
            font-size: 20px;
            font-weight: bold;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .chat-logo i {
            font-size: 24px;
        }
    </style>
</head>
<body style="<?php echo $container_style; ?>">
    <button class="mobile-sidebar-toggle" id="mobileSidebarToggle">
        <i class="bi bi-list"></i>
    </button>

    <div class="sidebar" id="sidebar">
        <div class="new-chat-btn">
            <button id="newChatBtn">
                <i class="bi bi-plus-lg"></i> New chat
            </button>
        </div>
        
        <div class="chat-history" id="chatHistory">
            <div class="history-item">
                <i class="bi bi-chat-left-text"></i>
                <span>Healthcare symptoms</span>
            </div>
            <div class="history-item">
                <i class="bi bi-chat-left-text"></i>
                <span>Medication questions</span>
            </div>
            <div class="history-item">
                <i class="bi bi-chat-left-text"></i>
                <span>First aid advice</span>
            </div>
        </div>
        
        <div class="user-info">
            <div class="user-avatar">U</div>
            <span>User Account</span>
        </div>
    </div>
    
    <div class="main-container">
        <div class="chat-header">
            <div class="chat-logo">
                <i class="bi bi-plus-circle"></i>
                <span>Healthcare Assistant</span>
            </div>
        </div>
        
        <div class="chat-container">
            <div class="chat-messages" id="chatMessages">
                <!-- Bot Welcome Message -->
                <div class="message-row bot-row">
                    <div class="message-container">
                        <div class="avatar bot-avatar">
                            <i class="bi bi-plus"></i>
                        </div>
                        <div class="message-content">
                            Hello! I'm your healthcare assistant. How can I help you today? You can ask about symptoms, medications, or upload medical images for a brief analysis.
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Typing Indicator -->
            <div class="typing-indicator" id="typingIndicator">
                <div class="typing-container">
                    <div class="avatar bot-avatar">
                        <i class="bi bi-plus"></i>
                    </div>
                    <div class="typing-dots">
                        <span class="typing-dot"></span>
                        <span class="typing-dot"></span>
                        <span class="typing-dot"></span>
                    </div>
                </div>
            </div>
            
            <div class="chat-input-container">
                <div class="chat-input-wrapper">
                    <form id="chatForm">
                        <textarea id="userMessage" class="chat-input" rows="1" placeholder="Message Healthcare Assistant..."></textarea>
                        <button type="submit" class="send-button">
                            <i class="bi bi-send"></i>
                        </button>
                        
                        <div class="input-buttons">
                            <div class="file-upload">
                                <i class="bi bi-image"></i> 📷

                                <input type="file" id="imageUpload" accept="image/*">
                            </div>
                            <div>Healthcare Assistant may produce inaccurate information.</div>
                        </div>
                        
                        <div class="image-preview-container" id="imagePreviewContainer">
                            <div class="image-preview-inner">
                                <img id="imagePreview" class="image-preview">
                                <button type="button" id="removeImage" class="remove-image">×</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatForm = document.getElementById('chatForm');
            const userMessageInput = document.getElementById('userMessage');
            const chatMessages = document.getElementById('chatMessages');
            const typingIndicator = document.getElementById('typingIndicator');
            const imageUpload = document.getElementById('imageUpload');
            const imagePreview = document.getElementById('imagePreview');
            const imagePreviewContainer = document.getElementById('imagePreviewContainer');
            const removeImageBtn = document.getElementById('removeImage');
            const newChatBtn = document.getElementById('newChatBtn');
            const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
            const sidebar = document.getElementById('sidebar');
            
            // Function to add a message to the chat
            function addMessage(message, isUser = false) {
                const messageRow = document.createElement('div');
                messageRow.classList.add('message-row');
                messageRow.classList.add(isUser ? 'user-row' : 'bot-row');
                
                const messageContainer = document.createElement('div');
                messageContainer.classList.add('message-container');
                
                const avatar = document.createElement('div');
                avatar.classList.add('avatar');
                avatar.classList.add(isUser ? 'user-avatar' : 'bot-avatar');
                
                const icon = document.createElement('i');
                icon.classList.add('bi');
                icon.classList.add(isUser ? 'bi-person' : 'bi-plus');
                avatar.appendChild(icon);
                
                const messageContent = document.createElement('div');
                messageContent.classList.add('message-content');
                messageContent.textContent = message;
                
                messageContainer.appendChild(avatar);
                messageContainer.appendChild(messageContent);
                messageRow.appendChild(messageContainer);
                
                // Insert before typing indicator
                chatMessages.appendChild(messageRow);
                
                // Scroll to bottom
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
            
            // Function to add image to the chat
            function addImageMessage(imageSrc) {
                const messageRow = document.createElement('div');
                messageRow.classList.add('message-row', 'user-row');
                
                const messageContainer = document.createElement('div');
                messageContainer.classList.add('message-container');
                
                const avatar = document.createElement('div');
                avatar.classList.add('avatar', 'user-avatar');
                
                const icon = document.createElement('i');
                icon.classList.add('bi', 'bi-person');
                avatar.appendChild(icon);
                
                const messageContent = document.createElement('div');
                messageContent.classList.add('message-content');
                
                const imageElement = document.createElement('img');
                imageElement.src = imageSrc;
                imageElement.classList.add('uploaded-image');
                
                messageContent.appendChild(imageElement);
                
                messageContainer.appendChild(avatar);
                messageContainer.appendChild(messageContent);
                messageRow.appendChild(messageContainer);
                
                chatMessages.appendChild(messageRow);
                
                // Scroll to bottom
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
            
            // Function to show typing indicator
            function showTypingIndicator() {
                typingIndicator.style.display = 'block';
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
            
            // Function to hide typing indicator
            function hideTypingIndicator() {
                typingIndicator.style.display = 'none';
            }
            
            // Auto resize textarea as content grows
            userMessageInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
                if (this.scrollHeight > 200) {
                    this.style.overflowY = 'auto';
                } else {
                    this.style.overflowY = 'hidden';
                }
            });
            
            // Handle image upload preview
            imageUpload.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreviewContainer.style.display = 'block';
                    };
                    
                    reader.readAsDataURL(this.files[0]);
                }
            });
            
            // Remove image
            removeImageBtn.addEventListener('click', function() {
                imageUpload.value = '';
                imagePreviewContainer.style.display = 'none';
            });
            
            // New chat button
            newChatBtn.addEventListener('click', function() {
                // Clear chat
                while (chatMessages.children.length > 1) {
                    chatMessages.removeChild(chatMessages.lastChild);
                }
                
                // Clear input
                userMessageInput.value = '';
                imageUpload.value = '';
                imagePreviewContainer.style.display = 'none';
            });
            
            // Mobile sidebar toggle
            mobileSidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
            });
            
            // Handle form submission
            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const userMessage = userMessageInput.value.trim();
                const hasImage = imageUpload.files && imageUpload.files[0];
                
                if (!userMessage && !hasImage) return;
                
                // Add user message to chat
                if (userMessage) {
                    addMessage(userMessage, true);
                }
                
                // Add image to chat if present
                if (hasImage) {
                    addImageMessage(imagePreview.src);
                }
                
                // Clear input
                userMessageInput.value = '';
                userMessageInput.style.height = 'auto';
                
                // Show typing indicator
                showTypingIndicator();
                
                // Prepare form data
                const formData = new FormData();
                if (userMessage) {
                    formData.append('message', userMessage);
                }
                if (hasImage) {
                    formData.append('image', imageUpload.files[0]);
                }
                
                // Reset image upload after submitting
                imageUpload.value = '';
                imagePreviewContainer.style.display = 'none';
                
                // Send request to server
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // Hide typing indicator
                    hideTypingIndicator();
                    
                    if (data.success) {
                        // Add bot response to chat
                        addMessage(data.message);
                    } else {
                        // Add error message with debug info
                        const errorMsg = "I'm sorry, I couldn't process your request. Please try again.";
                        addMessage(errorMsg);
                        console.error('API Error:', data.debug || data.message);
                    }
                })
                .catch(error => {
                    // Hide typing indicator
                    hideTypingIndicator();
                    
                    // Add error message
                    addMessage("There was an error connecting to the server. Please try again later.");
                    console.error('Error:', error);
                });
            });
        });
    </script>
</body>
</html>