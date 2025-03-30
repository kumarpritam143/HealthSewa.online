<!-- Floating Chat Button -->
<div id="chat-button" class="chat-float-btn">
    <i class="fas fa-comments"></i>
</div>

<!-- Chat Popup Container -->
<div id="chat-popup" class="chat-popup">
    <div class="chat-popup-header">
        <h5>Healthcare Assistant</h5>
        <button id="minimize-chat" class="btn-close"></button>
    </div>
    <iframe src="chatbot.php" frameborder="0"></iframe>
</div>

<style>
.chat-float-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    background-color: #2c71d3;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    z-index: 1000;
    transition: transform 0.3s;
}

.chat-float-btn:hover {
    transform: scale(1.1);
}

.chat-float-btn i {
    font-size: 24px;
}

.chat-popup {
    position: fixed;
    bottom: 100px;
    right: 30px;
    width: 350px;
    height: 500px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    display: none;
    z-index: 1000;
    overflow: hidden;
}

.chat-popup-header {
    padding: 15px;
    background: #2c71d3;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chat-popup-header h5 {
    margin: 0;
    font-size: 16px;
}

.chat-popup-header .btn-close {
    filter: brightness(0) invert(1);
}

.chat-popup iframe {
    width: 100%;
    height: calc(100% - 50px);
    border: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatButton = document.getElementById('chat-button');
    const chatPopup = document.getElementById('chat-popup');
    const minimizeChat = document.getElementById('minimize-chat');
    
    chatButton.addEventListener('click', function() {
        if(chatPopup.style.display === 'none' || !chatPopup.style.display) {
            chatPopup.style.display = 'block';
        } else {
            chatPopup.style.display = 'none';
        }
    });
    
    minimizeChat.addEventListener('click', function() {
        chatPopup.style.display = 'none';
    });
});
</script>
