<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance

<!-- jQuery and Select2 CSS and JS -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="/js/select2-init.js"></script>

<!-- Custom Select2 Styling for Dark Mode -->
<style>
.select2-container--default .select2-selection--single {
    height: 42px !important;
    border: 1px solid #d1d5db !important;
    border-radius: 0.375rem !important;
    background-color: white !important;
}

.dark .select2-container--default .select2-selection--single {
    border-color: #4b5563 !important;
    background-color: #374151 !important;
    color: white !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 40px !important;
    padding-left: 12px !important;
    color: #374151 !important;
}

.dark .select2-container--default .select2-selection--single .select2-selection__rendered {
    color: white !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px !important;
    right: 10px !important;
}

.select2-dropdown {
    border: 1px solid #d1d5db !important;
    border-radius: 0.375rem !important;
    background-color: white !important;
}

.dark .select2-dropdown {
    border-color: #4b5563 !important;
    background-color: #374151 !important;
}

.select2-container--default .select2-results__option {
    padding: 8px 12px !important;
    color: #374151 !important;
}

.dark .select2-container--default .select2-results__option {
    color: white !important;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #3b82f6 !important;
    color: white !important;
}

.select2-container--default .select2-search--dropdown .select2-search__field {
    border: 1px solid #d1d5db !important;
    border-radius: 0.25rem !important;
    padding: 8px !important;
    background-color: white !important;
    color: #374151 !important;
}

.dark .select2-container--default .select2-search--dropdown .select2-search__field {
    border-color: #4b5563 !important;
    background-color: #4b5563 !important;
    color: white !important;
}

.select2-container--default .select2-selection--single:focus {
    outline: none !important;
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
}
</style>
