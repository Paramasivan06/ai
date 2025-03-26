<!-- resources/views/chatbot.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chatbot</title>
    
    {{-- Laravel CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    
    {{-- Font Awesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .main-container {
            width: 100%;
            max-width: 500px;
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .page-header {
            background-color: #ffffff;
            border-bottom: 1px solid #e9ecef;
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .home-button {
            background: none;
            border: none;
            color: #4a90e2;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: color 0.3s ease;
        }

        .home-button:hover {
            color: #357abd;
        }

        .chatbot-container {
            display: flex;
            flex-direction: column;
            max-height: 700px;
        }

        .chat-header {
            background-color: #4a90e2;
            color: white;
            padding: 15px;
            text-align: center;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-header h2 {
            margin: 0;
            font-size: 18px;
        }

        #chatbox {
            flex-grow: 1;
            overflow-y: auto;
            padding: 20px;
            background-color: #f9fafb;
            height: 400px;
        }

        .message {
            margin-bottom: 15px;
            display: flex;
        }

        .message.user {
            justify-content: flex-end;
        }

        .message.bot {
            justify-content: flex-start;
        }

        .message .text {
            max-width: 80%;
            padding: 12px;
            border-radius: 12px;
            line-height: 1.4;
        }

        .message.user .text {
            background-color: #4a90e2;
            color: white;
        }

        .message.bot .text {
            background-color: #e9eef3;
            color: #2c3e50;
        }

        .input-area {
            display: flex;
            padding: 15px;
            background-color: white;
            border-top: 1px solid #e9ecef;
        }

        #userMessage {
            flex-grow: 1;
            padding: 12px;
            border: 1px solid #d1d8e0;
            border-radius: 8px;
            margin-right: 10px;
            font-size: 16px;
        }

        .send-button {
            background-color: #4a90e2;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .send-button:hover {
            background-color: #357abd;
        }

        .typing-indicator {
            font-style: italic;
            color: #7f8c8d;
        }

        @media (max-width: 600px) {
            .main-container {
                width: 100%;
                height: 100vh;
                max-height: none;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="page-header">
            <button class="home-button" onclick="goToHomePage()">
                <i class="fas fa-arrow-left"></i>
                Back to Home
            </button>
        </div>
        <div class="chatbot-container">
            <div class="chat-header">
                <h2>AI Assistant</h2>
            </div>
            <div id="chatbox"></div>
            <div class="input-area">
                <input type="text" id="userMessage" placeholder="Type your message...">
                <button class="send-button" onclick="sendMessage()">Send</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Function to navigate back to home page
        function goToHomePage() {
            // Laravel route to home page
            window.location.href = "{{ route('profile.edit') }}";
        }

        // CSRF Token Setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Welcome Message
        $(document).ready(function() {
            addBotMessage('Hello! I\'m your AI assistant. How can I help you today?');
        });

        // Send Message Function
        function sendMessage() {
            const message = $('#userMessage').val().trim();
            if (message === '') return;

            // Add User Message
            addUserMessage(message);
            $('#userMessage').val('');

            // Show Typing Indicator
            const typingIndicator = addBotMessage('<span class="typing-indicator">Typing...</span>');

            // Send AJAX Request
            $.ajax({
                url: "{{ route('chatbot.chat') }}", // Laravel route for chatbot
                type: 'POST',
                data: { message: message },
                success: function(response) {
                    typingIndicator.remove();
                    if (response.error) {
                        addBotMessage(`Error: ${response.error}`, 'error');
                    } else {
                        addBotMessage(response.reply);
                    }
                },
                error: function(xhr) {
                    typingIndicator.remove();
                    const errorMsg = xhr.responseJSON?.error || 'Connection error';
                    addBotMessage(`Error: ${errorMsg}`, 'error');
                }
            });
        }

        // Add User Message to Chat
        function addUserMessage(message) {
            const messageElement = $(`
                <div class="message user">
                    <div class="text">${escapeHtml(message)}</div>
                </div>
            `);
            $('#chatbox').append(messageElement);
            scrollToBottom();
            return messageElement;
        }

        // Add Bot Message to Chat
        function addBotMessage(message, type = 'normal') {
            const messageElement = $(`
                <div class="message bot">
                    <div class="text ${type === 'error' ? 'error-message' : ''}">${message}</div>
                </div>
            `);
            $('#chatbox').append(messageElement);
            scrollToBottom();
            return messageElement;
        }

        // Scroll to Bottom of Chatbox
        function scrollToBottom() {
            const chatbox = $('#chatbox');
            chatbox.scrollTop(chatbox[0].scrollHeight);
        }

        // HTML Escape Function
        function escapeHtml(unsafe) {
            return unsafe
                 .replace(/&/g, "&amp;")
                 .replace(/</g, "&lt;")
                 .replace(/>/g, "&gt;")
                 .replace(/"/g, "&quot;")
                 .replace(/'/g, "&#039;");
        }

        // Send Message on Enter Key
        $('#userMessage').on('keypress', function(e) {
            if (e.which === 13) {
                sendMessage();
                return false;
            }
        });
    </script>
</body>
</html>