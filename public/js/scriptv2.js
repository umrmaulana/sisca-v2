// SISCA V2 JavaScript Functions
document.addEventListener("DOMContentLoaded", function () {
    initializeSidebar();
    initializeMenuAccordions();
    initializeUserDropdown();
    initializeScrollToTop();
    initializeTooltips();
    initializeAnimations();
    initializeFormValidation();
    fixMixedContentIssues(); // Add mixed content fix
});

// Sidebar Functionality
function initializeSidebar() {
    const sidebar = document.getElementById("sidebar");
    const sidebarToggle = document.getElementById("sidebarToggle");
    const content = document.getElementById("content");
    const body = document.body;

    // Remove any existing navbar toggler functionality
    const navbarTogglers = document.querySelectorAll(
        '.navbar-toggler, [data-bs-toggle="collapse"]'
    );
    navbarTogglers.forEach((toggler) => {
        toggler.removeAttribute("data-bs-toggle");
        toggler.removeAttribute("data-bs-target");
    });

    if (sidebarToggle) {
        // Remove any existing event listeners
        const newToggle = sidebarToggle.cloneNode(true);
        sidebarToggle.parentNode.replaceChild(newToggle, sidebarToggle);

        // Add our custom event listener
        newToggle.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            toggleSidebar();
        });
    }

    // Mobile responsive - start collapsed on mobile
    if (window.innerWidth <= 768) {
        setSidebarMinimized(true);
        // Ensure body starts with sidebar-collapsed class on mobile
        body.classList.add("sidebar-collapsed");
    }

    // Handle overlay clicks on mobile to close sidebar
    document.addEventListener("click", function (event) {
        if (window.innerWidth <= 768) {
            const isClickInsideSidebar =
                sidebar && sidebar.contains(event.target);
            const isClickOnToggle =
                document.getElementById("sidebarToggle") &&
                document.getElementById("sidebarToggle").contains(event.target);

            // If clicked outside sidebar and toggle when sidebar is open, close it
            if (
                !isClickInsideSidebar &&
                !isClickOnToggle &&
                !isSidebarMinimized()
            ) {
                setSidebarMinimized(true);
                localStorage.setItem("sidebarCollapsed", "true");
            }
        }
    });

    // Handle window resize
    window.addEventListener("resize", function () {
        if (window.innerWidth <= 768) {
            // On mobile, always start collapsed
            setSidebarMinimized(true);
            body.classList.add("sidebar-collapsed");
        } else {
            // On desktop, restore previous state or show sidebar
            const sidebarCollapsed = localStorage.getItem("sidebarCollapsed");
            if (sidebarCollapsed !== "true") {
                setSidebarMinimized(false);
                body.classList.remove("sidebar-collapsed");
            }
        }
    });

    // Restore sidebar state on load (desktop only)
    if (window.innerWidth > 768) {
        const sidebarCollapsed = localStorage.getItem("sidebarCollapsed");
        if (sidebarCollapsed === "true") {
            setSidebarMinimized(true);
        }
    }
}

function toggleSidebar() {
    const body = document.body;
    const sidebar = document.getElementById("sidebar");
    const isMobile = window.innerWidth <= 768;

    // Toggle the minimized state using data attribute like SISCA V1
    const isCurrentlyMinimized = isSidebarMinimized();
    setSidebarMinimized(!isCurrentlyMinimized);

    // Save state to localStorage (only for desktop)
    if (!isMobile) {
        const newState = isSidebarMinimized();
        localStorage.setItem("sidebarCollapsed", newState.toString());
    }

    // Different behavior for mobile vs desktop
    if (sidebar) {
        if (isMobile) {
            // Mobile: show/hide sidebar completely
            sidebar.style.transition =
                "transform 0.3s cubic-bezier(0.4, 0, 0.2, 1)";
            sidebar.offsetHeight; // Force reflow
            if (isCurrentlyMinimized) {
                sidebar.style.transform = "translateX(0)";
            } else {
                sidebar.style.transform = "translateX(-100%)";
            }
        } else {
            // Desktop: minimize to 70px width (show icons only)
            sidebar.style.transition =
                "width 0.3s cubic-bezier(0.4, 0, 0.2, 1)";
            sidebar.offsetHeight; // Force reflow
            if (isCurrentlyMinimized) {
                sidebar.style.width = "280px";
            } else {
                sidebar.style.width = "70px";
            }
        }
    }

    // Force content margin adjustment
    const content = document.getElementById("content");
    if (content && !isMobile) {
        // Only adjust content margin on desktop
        content.style.transition =
            "margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1)";
        content.offsetHeight; // Force reflow
        if (isCurrentlyMinimized) {
            content.style.marginLeft = "280px";
        } else {
            content.style.marginLeft = "70px";
        }
    }
}

// Helper functions for sidebar state management (like SISCA V1)
function isSidebarMinimized() {
    return document.body.getAttribute("data-kt-app-sidebar-minimize") === "on";
}

function setSidebarMinimized(minimize) {
    const body = document.body;
    const sidebar = document.getElementById("sidebar");
    const content = document.getElementById("content");

    // Prevent any other scripts from interfering
    const originalSetAttribute = body.setAttribute.bind(body);
    const originalRemoveAttribute = body.removeAttribute.bind(body);
    const originalClassListAdd = body.classList.add.bind(body.classList);
    const originalClassListRemove = body.classList.remove.bind(body.classList);

    if (minimize) {
        // Add both class and data attribute for compatibility
        body.classList.add("sidebar-collapsed");
        body.setAttribute("data-kt-app-sidebar-minimize", "on");
    } else {
        // Remove both class and data attribute
        body.classList.remove("sidebar-collapsed");
        body.removeAttribute("data-kt-app-sidebar-minimize");
    }

    // Force DOM update multiple times to ensure it sticks
    body.offsetHeight;
    setTimeout(() => {
        if (minimize) {
            if (!body.classList.contains("sidebar-collapsed")) {
                body.classList.add("sidebar-collapsed");
            }
            if (body.getAttribute("data-kt-app-sidebar-minimize") !== "on") {
                body.setAttribute("data-kt-app-sidebar-minimize", "on");
            }
        } else {
            if (body.classList.contains("sidebar-collapsed")) {
                body.classList.remove("sidebar-collapsed");
            }
            if (body.getAttribute("data-kt-app-sidebar-minimize")) {
                body.removeAttribute("data-kt-app-sidebar-minimize");
            }
        }
    }, 50);

    // Update toggle icon
    updateToggleIcon();

    // Debug computed styles
    if (sidebar && content) {
        setTimeout(() => {
            const sidebarStyle = window.getComputedStyle(sidebar);
            const contentStyle = window.getComputedStyle(content);
        }, 100);
    }
}

function updateToggleIcon() {
    const toggleIcon = document.getElementById("sidebarToggleIcon");
    const isMinimized = isSidebarMinimized();

    if (toggleIcon) {
        if (isMinimized) {
            // When minimized/collapsed, show right arrow (to open)
            toggleIcon.className = "fas fa-chevron-right";
            toggleIcon.style.transform = "rotate(0deg)";
        } else {
            // When expanded, show left arrow (to close)
            toggleIcon.className = "fas fa-chevron-left";
            toggleIcon.style.transform = "rotate(0deg)";
        }
        toggleIcon.style.transition = "all 0.3s ease";
    }
}

// Menu Accordion Functionality like SISCA V1 - Simple Accordion Only
function initializeMenuAccordions() {
    const accordionItems = document.querySelectorAll(
        "#app_menu .menu-item.menu-accordion"
    );

    accordionItems.forEach((item) => {
        const link = item.querySelector(":scope > .menu-link");
        const sub = item.querySelector(":scope > .menu-sub");

        if (!link || !sub) return;

        // Add click handler for accordion toggle - works for both expanded and minimized
        link.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();

            // Close other accordions at the same level (sibling accordions only)
            const parentContainer = item.parentElement;
            if (parentContainer) {
                // Find sibling accordions at the same level
                const siblingAccordions = parentContainer.querySelectorAll(
                    ":scope > .menu-item.menu-accordion.show"
                );
                siblingAccordions.forEach((sibling) => {
                    if (sibling !== item) {
                        sibling.classList.remove("show");
                    }
                });
            }

            // Toggle current accordion
            item.classList.toggle("show");
        });
    });

    // Expand active menus on load
    expandActiveMenus();
}

function expandActiveMenus() {
    const activeLinks = document.querySelectorAll(
        "#app_menu .menu-link.active"
    );

    activeLinks.forEach((link) => {
        let currentSub = link.closest(".menu-sub");

        while (currentSub) {
            const accordionItem = currentSub.closest(
                ".menu-item.menu-accordion"
            );
            if (!accordionItem) break;

            accordionItem.classList.add("show");

            // Look for next parent level
            currentSub = accordionItem.closest(".menu-sub");
        }
    });
}

// User Dropdown Functionality like SISCA V1
function initializeUserDropdown() {
    const userProfileLink = document.querySelector(
        '[data-custom-dropdown="user-menu"]'
    );
    const userDropdownMenu = document.getElementById("user-menu");
    const userSection = document.querySelector(".app-sidebar-user");

    if (!userProfileLink || !userDropdownMenu || !userSection) {
        return;
    }

    // Toggle dropdown on click
    userProfileLink.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        const isShown = userSection.classList.contains("show");

        if (isShown) {
            userSection.classList.remove("show");
        } else {
            userSection.classList.add("show");
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", function (e) {
        if (!userSection.contains(e.target)) {
            userSection.classList.remove("show");
        }
    });

    // Prevent dropdown from closing when clicking inside the menu
    userDropdownMenu.addEventListener("click", function (e) {
        e.stopPropagation();
    });

    // Close dropdown when sidebar is toggled
    const toggleBtn = document.getElementById("sidebarToggle");
    if (toggleBtn) {
        toggleBtn.addEventListener("click", function () {
            userSection.classList.remove("show");
        });
    }
}

// Scroll to Top Functionality
function initializeScrollToTop() {
    const scrollBtn = document.getElementById("scrollToTopBtn");

    if (!scrollBtn) return;

    // Show/hide scroll button
    window.addEventListener("scroll", function () {
        if (
            document.body.scrollTop > 20 ||
            document.documentElement.scrollTop > 20
        ) {
            scrollBtn.style.display = "block";
            scrollBtn.style.opacity = "1";
        } else {
            scrollBtn.style.opacity = "0";
            setTimeout(() => {
                if (scrollBtn.style.opacity === "0") {
                    scrollBtn.style.display = "none";
                }
            }, 300);
        }
    });

    // Smooth scroll to top
    scrollBtn.addEventListener("click", function () {
        smoothScrollToTop();
    });
}

function smoothScrollToTop() {
    const scrollToTop = () => {
        const currentPosition =
            document.documentElement.scrollTop || document.body.scrollTop;
        if (currentPosition > 0) {
            window.requestAnimationFrame(scrollToTop);
            window.scrollTo(0, currentPosition - currentPosition / 8);
        }
    };
    scrollToTop();
}

// Initialize Bootstrap Tooltips
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Initialize Animations
function initializeAnimations() {
    // Add animation classes to elements as they come into view
    const observerOptions = {
        threshold: 0.1,
        rootMargin: "0px 0px -50px 0px",
    };

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add("animate-in");
            }
        });
    }, observerOptions);

    // Observe cards and other elements
    document.querySelectorAll(".card, .table, .alert").forEach((el) => {
        observer.observe(el);
    });
}

// Form Validation Enhancement
function initializeFormValidation() {
    const forms = document.querySelectorAll("form[novalidate]");

    forms.forEach((form) => {
        form.addEventListener("submit", function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();

                // Focus on first invalid field
                const firstInvalid = form.querySelector(":invalid");
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({
                        behavior: "smooth",
                        block: "center",
                    });
                }
            }
            form.classList.add("was-validated");
        });

        // Real-time validation
        const inputs = form.querySelectorAll("input, textarea, select");
        inputs.forEach((input) => {
            input.addEventListener("blur", function () {
                if (this.checkValidity()) {
                    this.classList.remove("is-invalid");
                    this.classList.add("is-valid");
                } else {
                    this.classList.remove("is-valid");
                    this.classList.add("is-invalid");
                }
            });

            input.addEventListener("input", function () {
                if (
                    this.classList.contains("is-invalid") &&
                    this.checkValidity()
                ) {
                    this.classList.remove("is-invalid");
                    this.classList.add("is-valid");
                }
            });
        });
    });
}

// Password Toggle Functionality
function togglePassword(inputId, iconId) {
    const passwordInput = document.getElementById(inputId);
    const passwordIcon = document.getElementById(iconId);

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        passwordIcon.classList.remove("bi-eye-slash");
        passwordIcon.classList.add("bi-eye");
    } else {
        passwordInput.type = "password";
        passwordIcon.classList.remove("bi-eye");
        passwordIcon.classList.add("bi-eye-slash");
    }
}

// Confirmation Dialog
function confirmDelete(id, itemName = "item") {
    return new Promise((resolve) => {
        if (
            confirm(
                `Are you sure you want to delete this ${itemName}? This action cannot be undone.`
            )
        ) {
            resolve(true);
        } else {
            resolve(false);
        }
    });
}

// Auto-hide alerts
function autoHideAlerts() {
    const alerts = document.querySelectorAll(".alert");
    alerts.forEach((alert) => {
        setTimeout(() => {
            if (alert && alert.classList.contains("alert")) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    });
}

// Loading spinner
function showLoading(element) {
    const originalContent = element.innerHTML;
    element.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
    element.disabled = true;

    return function hideLoading() {
        element.innerHTML = originalContent;
        element.disabled = false;
    };
}

// Data table search functionality
function initializeTableSearch(tableId, searchInputId) {
    const table = document.getElementById(tableId);
    const searchInput = document.getElementById(searchInputId);

    if (!table || !searchInput) return;

    searchInput.addEventListener("keyup", function () {
        const filter = this.value.toLowerCase();
        const rows = table.getElementsByTagName("tr");

        for (let i = 1; i < rows.length; i++) {
            // Start from 1 to skip header
            const row = rows[i];
            const cells = row.getElementsByTagName("td");
            let found = false;

            for (let j = 0; j < cells.length; j++) {
                if (cells[j].textContent.toLowerCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }

            row.style.display = found ? "" : "none";
        }
    });
}

// Number formatting
function formatNumber(num, decimals = 0) {
    return new Intl.NumberFormat("id-ID", {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    }).format(num);
}

// Format file size to human readable
function formatFileSize(bytes) {
    if (bytes === 0) return "0 Bytes";

    const k = 1024;
    const sizes = ["Bytes", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + " " + sizes[i];
}

// Date formatting
function formatDate(date, format = "dd/mm/yyyy") {
    const d = new Date(date);
    const day = String(d.getDate()).padStart(2, "0");
    const month = String(d.getMonth() + 1).padStart(2, "0");
    const year = d.getFullYear();

    switch (format) {
        case "dd/mm/yyyy":
            return `${day}/${month}/${year}`;
        case "yyyy-mm-dd":
            return `${year}-${month}-${day}`;
        case "dd MMM yyyy":
            const months = [
                "Jan",
                "Feb",
                "Mar",
                "Apr",
                "May",
                "Jun",
                "Jul",
                "Aug",
                "Sep",
                "Oct",
                "Nov",
                "Dec",
            ];
            return `${day} ${months[d.getMonth()]} ${year}`;
        default:
            return d.toLocaleDateString();
    }
}

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
    // Initialize sidebar first
    initializeSidebar();

    // Auto-hide alerts
    autoHideAlerts();

    // Initialize all interactive elements
    initializeAnimations();

    // Fix mixed content issues
    fixMixedContentIssues();

    // Add loading states to form submissions
    const forms = document.querySelectorAll("form");
    forms.forEach((form) => {
        form.addEventListener("submit", function () {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                showLoading(submitBtn);
            }
        });
    });

    // Close accordions and user dropdown when clicking outside sidebar
    document.addEventListener("click", (e) => {
        const sidebar = document.getElementById("sidebar");

        if (!sidebar || sidebar.contains(e.target)) return;

        // Close all accordions
        document
            .querySelectorAll("#app_menu .menu-item.menu-accordion.show")
            .forEach((accordion) => accordion.classList.remove("show"));

        // Close user dropdown
        const userSection = document.querySelector(".app-sidebar-user");
        if (userSection) {
            userSection.classList.remove("show");
        }
    });

    // Close accordions when window is resized for mobile
    window.addEventListener("resize", () => {
        if (window.innerWidth <= 768) {
            document
                .querySelectorAll("#app_menu .menu-item.menu-accordion.show")
                .forEach((accordion) => accordion.classList.remove("show"));
        }
    });
});

// Fix Mixed Content Issues
function fixMixedContentIssues() {
    // Ensure all URLs use the correct protocol
    if (window.location.protocol === "https:") {
        // Fix image src attributes
        const images = document.querySelectorAll("img[src]");
        images.forEach(function (img) {
            const src = img.src;
            if (
                src.startsWith("http:") &&
                src.includes(window.location.hostname)
            ) {
                img.src = src.replace("http:", "https:");
            }
        });

        // Fix link href attributes (stylesheets and other resources)
        const links = document.querySelectorAll("link[href], a[href]");
        links.forEach(function (link) {
            const href = link.href;
            if (
                href &&
                href.startsWith("http:") &&
                href.includes(window.location.hostname)
            ) {
                link.href = href.replace("http:", "https:");
            }
        });

        // Fix script src attributes
        const scripts = document.querySelectorAll("script[src]");
        scripts.forEach(function (script) {
            const src = script.src;
            if (
                src.startsWith("http:") &&
                src.includes(window.location.hostname)
            ) {
                // Create new script element with HTTPS URL
                const newScript = document.createElement("script");
                newScript.src = src.replace("http:", "https:");
                newScript.onload = function () {
                    script.remove();
                };
                script.parentNode.insertBefore(newScript, script.nextSibling);
            }
        });

        // Fix form actions
        const forms = document.querySelectorAll("form[action]");
        forms.forEach(function (form) {
            const action = form.action;
            if (
                action.startsWith("http:") &&
                action.includes(window.location.hostname)
            ) {
                form.action = action.replace("http:", "https:");
            }
        });

        // Fix dynamic content that might be added later
        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    if (node.nodeType === 1) {
                        // Element node
                        // Fix images in newly added content
                        const newImages = node.querySelectorAll
                            ? node.querySelectorAll("img[src]")
                            : [];
                        newImages.forEach(function (img) {
                            const src = img.src;
                            if (
                                src.startsWith("http:") &&
                                src.includes(window.location.hostname)
                            ) {
                                img.src = src.replace("http:", "https:");
                            }
                        });

                        // Fix links in newly added content
                        const newLinks = node.querySelectorAll
                            ? node.querySelectorAll("a[href]")
                            : [];
                        newLinks.forEach(function (link) {
                            const href = link.href;
                            if (
                                href &&
                                href.startsWith("http:") &&
                                href.includes(window.location.hostname)
                            ) {
                                link.href = href.replace("http:", "https:");
                            }
                        });
                    }
                });
            });
        });

        // Start observing for dynamic content
        observer.observe(document.body, {
            childList: true,
            subtree: true,
        });
    }
}

// Global utility functions with sidebar toggle
window.SISCA = {
    toggleSidebar: toggleSidebar,
    togglePassword: togglePassword,
    confirmDelete: confirmDelete,
    showLoading: showLoading,
    formatNumber: formatNumber,
    formatDate: formatDate,
    formatFileSize: formatFileSize,
    initializeTableSearch: initializeTableSearch,
    fixMixedContentIssues: fixMixedContentIssues,
    // Menu functions
    initializeMenuAccordions: initializeMenuAccordions,
    initializeUserDropdown: initializeUserDropdown,
    expandActiveMenus: expandActiveMenus,
    // Image compression utility
    compressImage: compressImage,
};

// Image compression utility function
function compressImage(file, maxSizeKB = 800, quality = 0.7) {
    return new Promise((resolve, reject) => {
        // Validate file type
        const allowedTypes = ["image/jpeg", "image/png", "image/jpg"];
        if (!allowedTypes.includes(file.type)) {
            reject(new Error("Only JPEG, PNG, and JPG files are allowed."));
            return;
        }

        const canvas = document.createElement("canvas");
        const ctx = canvas.getContext("2d");
        const img = new Image();

        img.onload = function () {
            try {
                // Calculate new dimensions while maintaining aspect ratio
                const { width, height } = calculateImageDimensions(
                    img.width,
                    img.height
                );

                canvas.width = width;
                canvas.height = height;

                // Draw and compress
                ctx.drawImage(img, 0, 0, width, height);

                // Convert to blob with compression
                canvas.toBlob(
                    function (blob) {
                        if (!blob) {
                            reject(new Error("Failed to compress image"));
                            return;
                        }

                        // If still too large, reduce quality further
                        if (blob.size > maxSizeKB * 1024 && quality > 0.1) {
                            // Recursive compression with lower quality
                            compressImage(file, maxSizeKB, quality - 0.1)
                                .then(resolve)
                                .catch(reject);
                        } else {
                            // Create new file object
                            const compressedFile = new File([blob], file.name, {
                                type: "image/jpeg",
                                lastModified: Date.now(),
                            });
                            resolve(compressedFile);
                        }
                    },
                    "image/jpeg",
                    quality
                );
            } catch (error) {
                reject(error);
            }
        };

        img.onerror = function () {
            reject(new Error("Failed to load image"));
        };

        img.src = URL.createObjectURL(file);
    });
}

// Calculate optimal dimensions for compression
function calculateImageDimensions(
    width,
    height,
    maxWidth = 1920,
    maxHeight = 1080
) {
    if (width <= maxWidth && height <= maxHeight) {
        return { width, height };
    }

    const ratio = Math.min(maxWidth / width, maxHeight / height);
    return {
        width: Math.round(width * ratio),
        height: Math.round(height * ratio),
    };
}

// Monitor body changes for debugging
const bodyObserver = new MutationObserver(function (mutations) {
    mutations.forEach(function (mutation) {
        if (mutation.type === "attributes") {
            if (
                mutation.attributeName === "class" ||
                mutation.attributeName === "data-kt-app-sidebar-minimize"
            ) {
                // Body attribute changed - can be used for debugging if needed
            }
        }
    });
});

// Start observing body changes
if (document.body) {
    bodyObserver.observe(document.body, {
        attributes: true,
        attributeOldValue: true,
        attributeFilter: ["class", "data-kt-app-sidebar-minimize"],
    });
}

// Notification
async function fetchNotifications() {
    // Cek apakah kita berada di modul P3K
    const currentPath = window.location.pathname;
    const isP3kModule =
        currentPath.includes("/p3k") || currentPath.includes("p3k");

    // Hanya jalankan notifikasi jika di modul P3K
    if (!isP3kModule) {
        return;
    }

    // Cek apakah notificationsUrl tersedia dan elemen notifikasi ada
    if (typeof notificationsUrl === "undefined") {
        return;
    }

    const notifCountEl = document.getElementById("notifCount");
    const notifListEl = document.getElementById("notifList");

    // Jika elemen notifikasi tidak ada, jangan lanjutkan
    if (!notifCountEl || !notifListEl) {
        return;
    }

    try {
        const response = await fetch(notificationsUrl, {
            credentials: "same-origin",
        });

        if (!response.ok) {
            console.error("Gagal fetch notifikasi, status:", response.status);
            return;
        }

        const data = await response.json();

        // Update badge
        if (data.count > 0) {
            notifCountEl.classList.remove("d-none");
            notifCountEl.textContent = data.count;
        } else {
            notifCountEl.classList.add("d-none");
        }

        // Update list
        notifListEl.innerHTML = "";

        if (
            (data.expired && data.expired.length > 0) ||
            (data.low_stock && data.low_stock.length > 0)
        ) {
            data.expired.forEach((item) => {
                notifListEl.innerHTML += `
                    <div class="dropdown-item" style="font-size: 0.85rem;">
                        <a>
                            <span>${item.name} kadaluarsa (${item.expired_date})</span>
                        </a>
                        <div>
                            <span>Lokasi: ${item.location}</span>
                        </div>
                    </div>
                    `;
            });

            data.low_stock.forEach((item) => {
                notifListEl.innerHTML += `
                    <div class="dropdown-item" style="font-size: 0.85rem;">
                        <a href="">
                            <span>${item.name} stok rendah (${item.stock}/${item.minimum_stock})</span>
                        </a>
                        <div>
                            <span>Lokasi: ${item.location}</span>
                        </div>
                    </div>
                    `;
            });
        } else {
            notifListEl.innerHTML = `<span class="dropdown-item text-muted">Notification not available</span>`;
        }
    } catch (error) {
        console.error("Gagal fetch notifikasi", error);
    }
}

// Fungsi untuk mengatur polling notifikasi
function initializeNotifications() {
    const currentPath = window.location.pathname;
    const isP3kModule =
        currentPath.includes("/p3k") || currentPath.includes("p3k");

    // Hanya setup polling jika di modul P3K
    if (isP3kModule && typeof notificationsUrl !== "undefined") {
        // Polling setiap 10 detik
        setInterval(fetchNotifications, 10000);
        fetchNotifications();
    }
}

// Panggil fungsi inisialisasi notifikasi
initializeNotifications();

// Update setiap detik untuk waktu
function updateTime() {
    const now = new Date();
    const options = {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    };
    const tanggal = now.toLocaleDateString("id-ID", options);
    const waktu = now.toLocaleTimeString("id-ID", {
        hour12: false,
    });

    const datetimeEl = document.getElementById("datetime");
    if (datetimeEl) {
        datetimeEl.innerText = `${tanggal} ${waktu}`;
    }
}

setInterval(updateTime, 1000);
updateTime(); // Jalankan pertama kali
// End Notification

// DataTables
$(document).ready(function () {
    $("#customTable").DataTable({
        paging: true,
        searching: true,
        ordering: true,
        lengthMenu: [5, 10, 25, 50],
        pageLength: 10,
    });
    // DataTables Dashboard
    $("#table-dashboard").DataTable({
        paging: true,
        pageLength: 5,
        lengthChange: false,
        searching: false,
        info: false,
        ordering: false,
    });
});
// End Data Tables
