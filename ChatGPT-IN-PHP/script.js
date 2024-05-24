function sendMessage() {
    const userInput = document.getElementById('user-input').value;
    if (userInput.trim() === '') return;

    const chatWindow = document.getElementById('chat-window');
    const userMessage = document.createElement('div');
    userMessage.style.color = 'blue';
    userMessage.style.fontWeight = 'bold';
    userMessage.textContent = `You: ${userInput}`;
    chatWindow.appendChild(userMessage);

    fetch('fetch_response.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ message: userInput }),
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);
        const botMessage = document.createElement('div');
        // botMessage.textContent = `Bot: ${data.response}`;
        botMessage.innerHTML = `<span class="font-grey">Bot:</span><br /> ${data.response}<br />`;
        chatWindow.appendChild(botMessage);
        chatWindow.scrollTop = chatWindow.scrollHeight;
    })
    .catch(error => console.error('Error:', error));

    document.getElementById('user-input').value = '';
}


const inputField = document.getElementById('user-input');
inputField.addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        handleEnterKey();
    }
});

function handleEnterKey() {
    sendMessage();
}
