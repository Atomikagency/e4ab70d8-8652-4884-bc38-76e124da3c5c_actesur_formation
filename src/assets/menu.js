document.addEventListener("DOMContentLoaded", run);

function run() {
    const menuToggle = document.getElementById("actesur-menu-toggle");
    const menu = document.getElementById("actesur-main-menu");
    const subMenu = menu.querySelectorAll("ul");
    const subMenuTrigger = menu.querySelectorAll("li:has(ul) > a");
    const returnButton = menu.querySelectorAll(".menu-return-button");

    menuToggle.addEventListener("click", () => {
        const isOpen = menu.classList.contains("open");
        if (isOpen) {
            menu.classList.remove("open");
            menu.querySelectorAll("ul").forEach((ul) => ul.classList.remove("open"));
        } else {
            menu.classList.add("open");
        }
    });

    subMenuTrigger.forEach((trigger) => {
        trigger.addEventListener("click", (e) => {
            e.preventDefault();
            trigger.nextElementSibling.classList.toggle("open");
        });
    });
    returnButton.forEach((trigger) => {
        trigger.addEventListener("click", (e) => {
            e.preventDefault();
            trigger.parentElement.classList.remove("open");
        });
    });

    subMenu.forEach((ul) => {
        detectSwipeRight(ul, () => {
            ul.classList.remove("open");
        })
    });

    function detectSwipeRight(el, callback) {
        let touchStartX = 0;
        let touchEndX = 0;

        el.addEventListener("touchstart", (e) => {
            e.stopPropagation()
            touchStartX = e.changedTouches[0].screenX;
        });

        el.addEventListener("touchend", (e) => {
            e.stopPropagation()
            touchEndX = e.changedTouches[0].screenX;
            if (touchEndX - touchStartX > 50) {
                callback();
            }
        });
    }
}
