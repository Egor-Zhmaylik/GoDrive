document.addEventListener("DOMContentLoaded", function () {
    // Общие функции
    const showError = (input, message) => {
        input.classList.add("error-input");
        let error = input.nextElementSibling;
        if (!error || !error.classList.contains("error-message")) {
            error = document.createElement("div");
            error.classList.add("error-message");
            input.after(error);
        }
        error.innerText = message;
    };

    const clearError = (input) => {
        input.classList.remove("error-input");
        let error = input.nextElementSibling;
        if (error && error.classList.contains("error-message")) {
            error.remove();
        }
    };

    const clearErrors = (form) => {
        const errors = form.querySelectorAll(".error-message");
        errors.forEach(err => err.remove());
        const inputs = form.querySelectorAll(".error-input");
        inputs.forEach(inp => inp.classList.remove("error-input"));
    };

    //Регистрация
    const form = document.getElementById("registration-form");

    if (form) {
        // ФИО
        ["lastname", "firstname", "middlename"].forEach(id => {
            const input = document.getElementById(id);
            input.addEventListener("input", () => {
                input.value = input.value.replace(/[^а-яА-ЯёЁ\s-]/g, "").replace(/\s+/g, " ");
                clearError(input);
            });
        });

        // Телефон
        const phoneInput = document.getElementById("phone");
        phoneInput.addEventListener("input", () => {
            let numbers = phoneInput.value.replace(/\D/g, "");
            if (numbers.length > 0) numbers = '+' + numbers;
            if (numbers.startsWith('+375')) {
                let formatted = '+375';
                if (numbers.length > 4) formatted += '(' + numbers.substring(4, 6);
                if (numbers.length > 6) formatted += ')' + numbers.substring(6, 13);
                phoneInput.value = formatted;
            } else {
                phoneInput.value = '+375';
            }
            clearError(phoneInput);
        });

        // Паспорт
        const passportInput = document.getElementById("passport_number");
        passportInput.addEventListener("input", () => {
            let value = passportInput.value.toUpperCase().replace(/[^A-Z0-9]/g, "");
            value = value.slice(0, 16);
            passportInput.value = value;
            clearError(passportInput);
        });

        // ВУ
        const vuInput = document.getElementById("driver_license");
        vuInput.addEventListener("input", () => {
            let raw = vuInput.value.toUpperCase().replace(/[^A-Z0-9]/g, "");
            raw = raw.slice(0, 10);
            if (raw.length > 0) {
                const first = raw.slice(0, 1);
                const letters = raw.slice(1, 3);
                const nums = raw.slice(3);
                vuInput.value = `${first}${letters} ${nums}`;
            }
            clearError(vuInput);
        });

        const emailInput = document.getElementById("email");
        emailInput.addEventListener("input", () => {
            emailInput.value = emailInput.value.trim();
            clearError(emailInput);
        });

        const passwordInput = document.getElementById("password");
        passwordInput.addEventListener("input", () => {
            clearError(passwordInput);
        });

        // Проверка при отправке формы
        form.addEventListener("submit", function (e) {
            let valid = true;
            clearErrors(form);

            ["lastname", "firstname", "middlename"].forEach(id => {
                const input = document.getElementById(id);
                if (!/^[А-ЯЁа-яё\s-]+$/.test(input.value.trim())) {
                    showError(input, "Только русские буквы");
                    valid = false;
                }
            });

            if (!/^\+375\(\d{2}\)\d{7}$/.test(phoneInput.value)) {
                showError(phoneInput, "Формат: +375(XX)XXXXXXX");
                valid = false;
            }

            if (!/^\d{7}[A-Z]{2}\d{7}$/.test(passportInput.value)) {
                showError(passportInput, "Пример: 1234567AB1234567");
                valid = false;
            }

            if (!/^\d{1}[A-Z]{2} \d{7}$/.test(vuInput.value)) {
                showError(vuInput, "Пример: 1AB 1234567");
                valid = false;
            }

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value)) {
                showError(emailInput, "Введите корректный email");
                valid = false;
            }

            if (passwordInput.value.length < 8) {
                showError(passwordInput, "Минимум 8 символов");
                valid = false;
            }

            if (!valid) e.preventDefault();
        });
    }

    // Авторизация
    const loginForm = document.querySelector("#login-form form");
    const loginPhone = document.getElementById("login-phone");
    const loginPassword = document.getElementById("login-password");

    if (loginForm) {
        loginForm.addEventListener("submit", function (e) {
            e.preventDefault();
            clearErrors(loginForm);

            let valid = true;

            const phonePattern = /^\+375\(\d{2}\)\d{7}$/;

            if (!phonePattern.test(loginPhone.value.trim())) {
                showError(loginPhone, "Введите телефон в формате +375(XX)XXXXXXX");
                valid = false;
            }

            if (loginPassword.value.trim().length < 8) {
                showError(loginPassword, "Пароль должен содержать не менее 8 символов");
                valid = false;
            }

            if (valid) {
                loginForm.submit();
            }
        });
    }
});
