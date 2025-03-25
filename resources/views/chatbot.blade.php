<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel Chatbot</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        #chatbox { 
            width: 80%; 
            max-width: 600px; 
            height: 400px; 
            overflow-y: scroll; 
            border: 1px solid #ccc; 
            padding: 10px; 
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .user { 
            color: #2c3e50; 
            background-color: #ecf0f1;
            padding: 8px;
            border-radius: 8px;
            margin: 5px 0;
        }
        .bot { 
            color: #27ae60; 
            background-color: #f1f8e9;
            padding: 8px;
            border-radius: 8px;
            margin: 5px 0;
        }
        #userMessage {
            width: 70%;
            max-width: 500px;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        button {
            padding: 8px 16px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #2980b9;
        }
        .error {
            color: #e74c3c;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>Laravel AI Chatbot</h2>
    <div id="chatbox"></div>
    <div>
        <input type="text" id="userMessage" placeholder="Type a message...">
        <button onclick="sendMessage()">Send</button>
    </div>
    
    <script>
        // Set up CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        function sendMessage() {
            let message = $('#userMessage').val();
            if (message.trim() === '') return;
            
            // Add user message to chat
            $('#chatbox').append('<p class="user"><strong>You:</strong> ' + message + '</p>');
            $('#userMessage').val('');
            
            // Scroll to bottom
            $("#chatbox").scrollTop($("#chatbox")[0].scrollHeight);
            
            // Show typing indicator
            $('#chatbox').append('<p class="bot typing" id="typing"><em>Bot is typing...</em></p>');
            
            // Send message to server
            $.ajax({
                url: '/chatbot',
                type: 'POST',
                data: { message: message },
                success: function(response) {
                    // Remove typing indicator
                    $('#typing').remove();
                    
                    if (response.error) {
                        $('#chatbox').append('<p class="error"><strong>Error:</strong> ' + response.error + '</p>');
                    } else {
                        $('#chatbox').append('<p class="bot"><strong>Bot:</strong> ' + response.reply + '</p>');
                    }
                    
                    // Scroll to bottom
                    $("#chatbox").scrollTop($("#chatbox")[0].scrollHeight);
                },
                error: function(xhr) {
                    // Remove typing indicator
                    $('#typing').remove();
                    
                    let errorMsg = 'Connection error';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    }
                    
                    $('#chatbox').append('<p class="error"><strong>Error:</strong> ' + errorMsg + '</p>');
                    $("#chatbox").scrollTop($("#chatbox")[0].scrollHeight);
                }
            });
        }
        
        // Allow sending message with Enter key
        $('#userMessage').keypress(function(e) {
            if (e.which == 13) {  // Enter key
                sendMessage();
                return false;
            }
        });
    </script>
</body>
</html>