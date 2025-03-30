<div id="chatbot-button" class="chatbot-float">
    <i class="fas fa-robot"></i>
</div>

<div id="chatbot-popup" class="chatbot-popup">
    <div class="chat-header">
        <h5><i class="fas fa-robot"></i> Healthcare Assistant</h5>
        <button onclick="toggleChatbot()" class="close-btn">&times;</button>
    </div>
    <div class="chat-body">
        <iframe src="chatbot.php" frameborder="0"></iframe>
    </div>
</div>

<style>
.chatbot-float {
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

.chatbot-float:hover {
    transform: scale(1.1);
}

.chatbot-float i {
    font-size: 24px;
}

.chatbot-popup {
    position: fixed;
    bottom: 100px;
    right: 30px;
    width: 350px;
    height: 500px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    display: none;
    z-index: 999;
    overflow: hidden;
}

.chat-header {
    background: #2c71d3;
    color: white;
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chat-header h5 {
    margin: 0;
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.close-btn {
    background: none;
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    padding: 0;
    line-height: 1;
}

.chat-body {
    height: calc(100% - 50px);
}

.chat-body iframe {
    width: 100%;
    height: 100%;
}
</style>

<script>
function toggleChatbot() {
    const popup = document.getElementById('chatbot-popup');
    popup.style.display = popup.style.display === 'none' || !popup.style.display ? 'block' : 'none';
}

document.getElementById('chatbot-button').addEventListener('click', function() {
    toggleChatbot();
});
</script>
