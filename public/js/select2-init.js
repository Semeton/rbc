// Universal Select2 Initialization Script
function initializeSelect2() {
    // Wait for jQuery and Select2 to be available
    if (typeof $ !== "undefined" && $.fn.select2) {
        console.log("Initializing Select2 dropdowns...");

        // Initialize all Select2 dropdowns
        $(".select2-customer").each(function () {
            if (!$(this).hasClass("select2-hidden-accessible")) {
                $(this).select2({
                    placeholder: "Select Customer",
                    allowClear: true,
                    width: "100%",
                });
            }
        });

        $(".select2-driver").each(function () {
            if (!$(this).hasClass("select2-hidden-accessible")) {
                $(this).select2({
                    placeholder: "Select Driver",
                    allowClear: true,
                    width: "100%",
                });
            }
        });

        $(".select2-truck").each(function () {
            if (!$(this).hasClass("select2-hidden-accessible")) {
                $(this).select2({
                    placeholder: "Select Truck",
                    allowClear: true,
                    width: "100%",
                });
            }
        });

        $(".select2-atc").each(function () {
            if (!$(this).hasClass("select2-hidden-accessible")) {
                $(this).select2({
                    placeholder: "Select ATC",
                    allowClear: true,
                    width: "100%",
                });
            }
        });

        $(".select2-status").each(function () {
            if (!$(this).hasClass("select2-hidden-accessible")) {
                $(this).select2({
                    placeholder: "Select Status",
                    allowClear: true,
                    width: "100%",
                });
            }
        });

        $(".select2-atc-type").each(function () {
            if (!$(this).hasClass("select2-hidden-accessible")) {
                $(this).select2({
                    placeholder: "Select ATC Type",
                    allowClear: true,
                    width: "100%",
                });
            }
        });

        console.log("Select2 initialization complete");
    } else {
        console.log("jQuery or Select2 not available, retrying in 100ms...");
        setTimeout(initializeSelect2, 100);
    }
}

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", initializeSelect2);

// Also initialize immediately in case DOM is already ready
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initializeSelect2);
} else {
    initializeSelect2();
}

// Handle Livewire updates
document.addEventListener("livewire:init", function () {
    Livewire.on("$refresh", () => {
        console.log("Livewire refresh detected, reinitializing Select2...");
        // Destroy existing Select2 instances
        $(
            ".select2-customer, .select2-driver, .select2-truck, .select2-atc, .select2-status, .select2-atc-type"
        ).select2("destroy");
        // Reinitialize after a short delay
        setTimeout(initializeSelect2, 100);
    });
});

// Also handle livewire:navigated for SPA-like navigation
document.addEventListener("livewire:navigated", function () {
    console.log("Livewire navigation detected, reinitializing Select2...");
    setTimeout(initializeSelect2, 100);
});
