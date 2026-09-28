const form = document.querySelector("#guestbook-form");
const messagesBox = document.querySelector("#messages");
const themeButton = document.querySelector("#toggle-theme");
const messageInput = document.querySelector("#message");
const counter = document.querySelector("#message-counter");

function showMessages(messages, type) {
    messagesBox.className = type;
    messagesBox.innerHTML = messages.map((message) => `<p>${message}</p>`).join("");
}

function updateCounter() {
    const length = Array.from(messageInput.value).length;
    counter.textContent = `${length} / 300 caracteres`;
    counter.classList.toggle("warning", length >= 280);
}

form.addEventListener("submit", (event) => {
    const errors = [];

    const firstname = document.querySelector("#firstname").value.trim();
    const lastname = document.querySelector("#lastname").value.trim();
    const usermail = document.querySelector("#usermail").value.trim();
    const phone = document.querySelector("#phone").value.trim();
    const postcode = document.querySelector("#postcode").value.trim();
    const message = messageInput.value.trim();

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phoneClean = phone.replace(/[\s.\-]/g, "");
    const phoneRegex = /^(04[0-9]{8}|\+324[0-9]{8}|00324[0-9]{8})$/;

    messagesBox.innerHTML = "";
    messagesBox.className = "";

    if (Array.from(firstname).length < 2 || Array.from(firstname).length > 100) errors.push("Le prenom doit contenir au moins 2 caracteres.");
    if (Array.from(lastname).length < 2 || Array.from(lastname).length > 100) errors.push("Le nom doit contenir au moins 2 caracteres.");
    if (usermail.length > 200 || !emailRegex.test(usermail)) errors.push("L'adresse email n'est pas valide.");
    if (!/^[1-9][0-9]{3}$/.test(postcode)) errors.push("Le code postal belge doit contenir exactement 4 chiffres.");
    if (!phoneRegex.test(phoneClean)) errors.push("Le numero de telephone belge n'est pas valide.");
    if (Array.from(message).length < 10 || Array.from(message).length > 300) errors.push("Le message doit contenir entre 10 et 300 caracteres.");

    if (errors.length > 0) {
        event.preventDefault();
        showMessages(errors, "error");
        return;
    }

    showMessages(["Formulaire valide, envoi en cours."], "success");
});

themeButton.addEventListener("click", () => {
    document.body.classList.toggle("dark-mode");

    if (document.body.classList.contains("dark-mode")) {
        themeButton.textContent = "White Mode";
    } else {
        themeButton.textContent = "Dark Mode";
    }
});

messageInput.addEventListener("input", updateCounter);
updateCounter();
