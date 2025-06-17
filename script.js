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

    if (loginButton) {
        loginButton.addEventListener("click", function () {
            if (modal && loginForm && registerForm) {
                modal.style.display = "flex";
                loginForm.style.display = "block";
                registerForm.style.display = "none";
            }
        });
    }

    if (registerButton) {
        registerButton.addEventListener("click", function () {
            if (modal && loginForm && registerForm) {
                modal.style.display = "flex";
                loginForm.style.display = "none";
                registerForm.style.display = "block";
            }
        });
    }

    if (showRegisterLink) {
        showRegisterLink.addEventListener("click", function () {
            if (loginForm && registerForm) {
                loginForm.style.display = "none";
                registerForm.style.display = "block";
            }
        });
    }

    if (showLoginLink) {
        showLoginLink.addEventListener("click", function () {
            if (loginForm && registerForm) {
                loginForm.style.display = "block";
                registerForm.style.display = "none";
            }
        });
    }

    if (closeBtn && modal) {
        closeBtn.addEventListener("click", function () {
            modal.style.display = "none";
        });
    }

    window.addEventListener("click", function (event) {
        if (modal && event.target === modal) {
            modal.style.display = "none";
        }
    });

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

    function handleCarAction(event) {
        event.preventDefault(); 

        fetch('check_auth.php')
            .then(response => response.json())
            .then(data => {
                if (data.isAuthenticated) {
                    window.location.href = 'cars.php';
                } else {
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

    document.querySelectorAll('.more-btn').forEach(button => {
        button.addEventListener('click', handleCarAction);
    });

    
});
