document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("appointmentForm");
    const appointmentActions = document.getElementById("appointmentActions");
    const scheduledDate = document.getElementById("scheduledDate");
    const scheduledTime = document.getElementById("scheduledTime");

    const cancelPopup = document.getElementById("cancelPopup");
    const cancelBtn = document.getElementById("cancelBtn");
    const confirmCancel = document.getElementById("confirmCancel");
    const closePopup = document.getElementById("closePopup");

    const cancelInfo = document.getElementById("cancelInfo");
    const closeInfo = document.getElementById("closeInfo");

    // Prevent default form submission and handle appointment booking
    form.addEventListener("submit", function (event) {
        event.preventDefault();

        let formData = new FormData(form);

        fetch("book_appointment.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                scheduledDate.textContent = formData.get("date");
                scheduledTime.textContent = formData.get("time");
                appointmentActions.classList.remove("hidden");
                showPopup("successPopup", data.message);
            } else {
                showPopup("errorPopup", data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    });

    function showPopup(popupId, message) {
        let popup = document.getElementById(popupId);
        let messageContainer = popup.querySelector(".popup-message");
        messageContainer.textContent = message;
        popup.classList.remove("hidden");

        setTimeout(() => {
            popup.classList.add("hidden");
        }, 3000);
    }

    document.querySelectorAll(".close-popup").forEach(btn => {
        btn.addEventListener("click", function () {
            this.closest(".popup").classList.add("hidden");
        });
    });

    // Cancel Appointment Popup Logic
    cancelBtn.addEventListener("click", () => cancelPopup.classList.remove("hidden"));
    closePopup.addEventListener("click", () => cancelPopup.classList.add("hidden"));
    confirmCancel.addEventListener("click", () => {
        appointmentActions.classList.add("hidden");
        cancelPopup.classList.add("hidden");
        cancelInfo.classList.remove("hidden");
    });
    closeInfo.addEventListener("click", () => cancelInfo.classList.add("hidden"));
});
