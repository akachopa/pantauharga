<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0, viewport-fit=cover" name="viewport">
<meta name="theme-color" content="#FFFFFF">
<title>@yield('title', 'Informasi Harga Pangan - PANTAU HARGA Bengkulu Tengah')</title>
<script src="https://cdn.tailwindcss.com/3.4.17?plugins=forms,container-queries"></script>
<link rel="icon" type="image/jpeg" href="{{ asset('images/lambang-bengkulu-tengah.jpg') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    display: ['Archivo', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                },
                colors: {
                    brand: {
                        50: '#EEF7F1',
                        100: '#D7EDDF',
                        200: '#B0DBC0',
                        300: '#7FC29A',
                        500: '#2E9C61',
                        600: '#1F7F4D',
                        700: '#17663E',
                        800: '#125032',
                        900: '#0D3B25',
                    },
                    ink: '#101828',
                },
            },
        },
    }
</script>
<style>
    html {
        scroll-behavior: smooth;
    }
    body {
        font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
        background-color: #F6F7F5;
        color: #101828;
        -webkit-tap-highlight-color: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 9999px;
    }
    .card-shadow,
    .shadow-card {
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04), 0 4px 12px rgba(15, 23, 42, 0.03);
    }
    .tabular-nums {
        font-variant-numeric: tabular-nums;
    }
    .form-plain,
    .form-plain:focus,
    .form-plain:focus-visible {
        appearance: none;
        background-color: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
        outline: none !important;
        padding: 0 !important;
        margin: 0;
        min-height: 0;
        line-height: inherit;
        color: inherit;
        width: 100%;
    }
    select.form-plain {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394A3B8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0 center;
        background-size: 14px 14px;
        padding-right: 18px !important;
    }
    input[type="date"].form-plain::-webkit-calendar-picker-indicator {
        cursor: pointer;
        opacity: 0.55;
    }
    table {
        font-family: inherit;
        letter-spacing: 0;
        border-collapse: collapse;
    }
    table thead th {
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748B;
        letter-spacing: 0 !important;
        text-transform: none !important;
    }
    table tbody {
        font-size: 0.875rem;
    }
    table tbody td {
        letter-spacing: 0;
    }
    .no-scrollbar {
        scrollbar-width: none;
    }
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    h1, h2, h3, .font-display {
        font-family: 'Archivo', 'Inter', ui-sans-serif, system-ui, sans-serif;
        letter-spacing: -0.01em;
    }
    .safe-bottom {
        padding-bottom: env(safe-area-inset-bottom, 0px);
    }
</style>
