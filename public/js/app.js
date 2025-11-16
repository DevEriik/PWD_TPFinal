// ===================== LOGIN =====================
document.addEventListener("submit", function(e) {
    if (e.target.id === "form-login") {
        e.preventDefault();

        const datos = new FormData(e.target);

        fetch("../Control/ajaxSession.php?a=login", {
            method: "POST",
            body: datos
        })
        .then(r => r.json())
        .then(data => {
            const msg = document.getElementById("login-msg");
            if (data.success) {
                msg.innerHTML = "Login correcto. Redirigiendo...";
                setTimeout(() => location.href = "inicioSesion.php", 800);
            } else {
                msg.innerHTML = data.error;
            }
        });
    }
});


// ===================== REGISTRO =====================
document.addEventListener("submit", function(e) {
    if (e.target.id === "form-registro") {
        e.preventDefault();

        const datos = new FormData(e.target);

        fetch("../Control/ajaxUsuario.php?a=registrar", {
            method: "POST",
            body: datos
        })
        .then(r => r.json())
        .then(data => {
            const msg = document.getElementById("registro-msg");
            if (data.success) {
                msg.innerHTML = "Cuenta creada. Redirigiendo...";
                setTimeout(() => location.href = "inicioSesion.php", 800);
            } else {
                msg.innerHTML = data.error;
            }
        });
    }
});


// ===================== MI CUENTA =====================
document.addEventListener("submit", function(e) {
    if (e.target.id === "form-mi-cuenta") {
        e.preventDefault();

        const datos = new FormData(e.target);

        fetch("../Control/ajaxUsuario.php?a=actualizar", {
            method: "POST",
            body: datos
        })
        .then(r => r.json())
        .then(data => {
            const msg = document.getElementById("micuenta-msg");
            if (data.success) {
                msg.innerHTML = "Datos actualizados.";
            } else {
                msg.innerHTML = data.error;
            }
        });
    }
});


// ===================== LOGOUT =====================
document.addEventListener("click", function(e) {
    if (e.target.id === "btn-logout") {
        fetch("../Control/ajaxSession.php?a=logout")
        .then(r => r.json())
        .then(() => location.href = "inicioSesion.php");
    }
});


// ===================== CARGAR MENÚ =====================
function cargarMenu() {
    fetch("../Control/ajaxMenu.php?a=obtenerMenuAjax")
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById("menu").innerHTML = data.html;
        }
    });
}