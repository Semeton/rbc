function initializeSelect2() {
    // Wait for jQuery and Select2 to be available
    if (typeof $ !== "undefined" && $.fn.select2) {
        // Initialize all Select2 dropdowns
        function initSelectWithLivewire(selector, placeholder) {
            $(selector).each(function () {
                if (!$(this).hasClass("select2-hidden-accessible")) {
                    const $select = $(this);
                    const selectElement = $select[0];
                    let isUpdating = false;

                    $select.select2({
                        placeholder: placeholder,
                        allowClear: true,
                        width: "100%",
                    });

                    // Ensure Livewire updates when Select2 changes
                    $select.on("change", function () {
                        // Prevent infinite loops
                        if (isUpdating) {
                            return;
                        }

                        isUpdating = true;
                        const value = $(this).val();

                        // Update the native select element value
                        selectElement.value = value || "";

                        // Trigger both input and change events for Livewire
                        const inputEvent = new Event("input", {
                            bubbles: true,
                            cancelable: true,
                        });

                        const changeEvent = new Event("change", {
                            bubbles: true,
                            cancelable: true,
                        });

                        selectElement.dispatchEvent(inputEvent);
                        selectElement.dispatchEvent(changeEvent);

                        setTimeout(() => {
                            isUpdating = false;
                        }, 100);
                    });
                }
            });
        }

        // Initialize all Select2 dropdowns
        initSelectWithLivewire(".select2-customer", "Select Customer");
        initSelectWithLivewire(".select2-driver", "Select Driver");
        initSelectWithLivewire(".select2-truck", "Select Truck");
        initSelectWithLivewire(".select2-atc", "Select ATC");
        initSelectWithLivewire(".select2-status", "Select Status");
        initSelectWithLivewire(".select2-atc-type", "Select ATC Type");
    } else {
        setTimeout(initializeSelect2, 100);
    }
}

document.addEventListener("DOMContentLoaded", initializeSelect2);

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initializeSelect2);
} else {
    initializeSelect2();
}

document.addEventListener("livewire:init", function () {
    Livewire.hook("commit", ({ component, commit, respond, succeed, fail }) => {
        succeed(({ snapshot, effect }) => {
            setTimeout(initializeSelect2, 100);
        });
    });
});

// Also handle livewire:navigated for SPA-like navigation
document.addEventListener("livewire:navigated", function () {
    setTimeout(initializeSelect2, 100);
});
