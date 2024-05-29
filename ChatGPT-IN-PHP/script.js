let inProgress = false;
function sendMessage() {
  if (inProgress) {
    return;
  }
  const userInputEle = document.getElementById("user-input");
  const userInput = userInputEle.value;
  if (userInput.trim() === "") return;
  inProgress = true;
  const chatWindow = document.getElementById("chat-window");
  const userMessage = document.createElement("div");
  userMessage.style.color = "blue";
  userMessage.style.fontWeight = "bold";
  userMessage.textContent = `You: ${userInput}`;
  chatWindow.appendChild(userMessage);

  const bot = document.createElement("div");
  bot.style.color = "grey";
  bot.style.fontWeight = "bold";
  bot.innerHTML = `<span class="font-grey">Bot:</span><div class="wave-container" id="waveText">...</div><br />`;
  chatWindow.appendChild(bot);

  const waveText = document.getElementsByClassName("wave-container");
  const text = waveText[0].textContent;
  waveText[0].textContent = "";

  text.split("").forEach((char, index) => {
    const span = document.createElement("span");
    span.textContent = char;
    span.style.animationDelay = `${index * 0.2}s`;
    waveText[0].appendChild(span);
  });
  fetch("test.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ message: userInput }),
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
      const botMessage = document.createElement("div");
      const htmlContent = marked.marked(data.response);
      botMessage.innerHTML = `${htmlContent}<br />`;
      
      chatWindow.appendChild(botMessage);
      chatWindow.scrollTop = chatWindow.scrollHeight;
      waveText[0].style.display="none";
      waveText[0].className = "";
      inProgress = false;
    })
    .catch((error) => {
      console.log(error);
      waveText[0].style.display="none";
      waveText[0].className = "";
      inProgress = false;
    });

  userInputEle.value = "";
}

const inputField = document.getElementById("user-input");
inputField.addEventListener("keydown", function (event) {
  
  if (event.key === "Enter") {
    event.preventDefault();
    handleEnterKey();
  }
});

function handleEnterKey() {
  sendMessage();
}
