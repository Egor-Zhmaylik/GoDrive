document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("modal");
    const closeBtn = document.querySelector(".close");
    const loginForm = document.getElementById("login-form");
    const registerForm = document.getElementById("register-form");

    const loginButton = document.getElementById("login-button");
    const registerButton = document.getElementById("register-button");
    const showRegisterLink = document.getElementById("show-register");
    const showLoginLink = document.getElementById("show-login");
    const loginBtn = document.getElementById("login-btn");
    const loginPhone = document.getElementById("login-phone");
    const loginPassword = document.getElementById("login-password");

    // Открытие модального окна входа
    if (loginButton) {
        loginButton.addEventListener("click", function () {
            if (modal && loginForm && registerForm) {
                modal.style.display = "flex";
                loginForm.style.display = "block";
                registerForm.style.display = "none";
            }
        });
    }

    // Открытие модального окна регистрации
    if (registerButton) {
        registerButton.addEventListener("click", function () {
            if (modal && loginForm && registerForm) {
                modal.style.display = "flex";
                loginForm.style.display = "none";
                registerForm.style.display = "block";
            }
        });
    }

    // Переключение: показать регистрацию
    if (showRegisterLink) {
        showRegisterLink.addEventListener("click", function () {
            if (loginForm && registerForm) {
                loginForm.style.display = "none";
                registerForm.style.display = "block";
            }
        });
    }

    // Переключение: показать вход
    if (showLoginLink) {
        showLoginLink.addEventListener("click", function () {
            if (loginForm && registerForm) {
                loginForm.style.display = "block";
                registerForm.style.display = "none";
            }
        });
    }

    // Закрытие модального окна по крестику
    if (closeBtn && modal) {
        closeBtn.addEventListener("click", function () {
            modal.style.display = "none";
        });
    }

    // Закрытие модального окна по клику вне его
    window.addEventListener("click", function (event) {
        if (modal && event.target === modal) {
            modal.style.display = "none";
        }
    });

    // Обработчик кнопки входа в аккаунт
    if (loginBtn && loginPhone && loginPassword) {
        loginBtn.addEventListener("click", function (event) {
            event.preventDefault();

            const phone = loginPhone.value.trim();
            const password = loginPassword.value.trim();

            if (phone === "" || password === "") {
                alert("Заполните все поля!");
                return;
            }

            const formData = new FormData();
            formData.append("phone", phone);
            formData.append("password", password);

            fetch("login.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Вы успешно вошли!");
                    window.location.href = "index.php";
                } else {
                    alert("Ошибка: " + data.message);
                }
            })
            .catch(error => console.error("Ошибка запроса:", error));
        });
    }

    // Модальное окно для входа при попытке действия без авторизации
    function showLoginModal() {
        const loginModal = document.getElementById("login-modal");
        if (loginModal) loginModal.style.display = "block";
    }

    function closeLoginModal() {
        const loginModal = document.getElementById("login-modal");
        if (loginModal) loginModal.style.display = "none";
    }

    function handleCarAction(event) {
        event.preventDefault();

        fetch('check_auth.php')
            .then(response => response.json())
            .then(data => {
                if (!data.isAuthenticated) {
                    showLoginModal();
                } else {
                    window.location.href = 'profile.php';
                }
            })
            .catch(error => console.error('Ошибка запроса:', error));
    }

    document.querySelectorAll('.car-btn').forEach(button => {
        button.addEventListener('click', handleCarAction);
    });

    document.querySelectorAll('.more-btn').forEach(button => {
        button.addEventListener('click', handleCarAction);
    });

    const closeLoginBtn = document.getElementById('close-login');
    const loginModal = document.getElementById('login-modal');

    if (closeLoginBtn) {
        closeLoginBtn.addEventListener('click', closeLoginModal);
    }

    if (loginModal) {
        window.addEventListener('click', function (event) {
            if (event.target === loginModal) {
                closeLoginModal();
            }
        });
    }

        // Функция проверки авторизации и действия по кнопке "Посмотреть другие варианты"
    function handleCarAction(event) {
        event.preventDefault(); // Останавливаем стандартное поведение (переход)

        fetch('check_auth.php')
            .then(response => response.json())
            .then(data => {
                if (data.isAuthenticated) {
                    // Если пользователь авторизован, переходим на cars.php
                    window.location.href = 'cars.php';
                } else {
                    // Если не авторизован, показываем окно входа
                    const modal = document.getElementById("modal");
                    const loginForm = document.getElementById("login-form");
                    const registerForm = document.getElementById("register-form");

                    modal.style.display = "flex";
                    loginForm.style.display = "block";
                    registerForm.style.display = "none";
                }
            })
            .catch(error => {
                console.error('Ошибка при проверке авторизации:', error);
            });
    }

    // Назначаем обработчики всем кнопкам "Посмотреть другие варианты"
    document.querySelectorAll('.more-btn').forEach(button => {
        button.addEventListener('click', handleCarAction);
    });

    
});
