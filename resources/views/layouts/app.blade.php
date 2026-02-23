<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@hasSection('title')@yield('title') | @endif{{ $appName ?? config('app.name', 'Inventory Management') }}</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        #global-loader {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(17, 24, 39, 0.45);
            z-index: 9999;
            backdrop-filter: blur(1px);
        }

        #global-loader.visible {
            display: flex;
        }

        .global-loader-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: #ffffff;
            border-radius: 0.75rem;
            padding: 0.875rem 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.18);
            color: #111827;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .global-loader-spinner {
            width: 1.125rem;
            height: 1.125rem;
            border: 2px solid #e5e7eb;
            border-top-color: #4f46e5;
            border-radius: 9999px;
            animation: global-loader-spin 0.7s linear infinite;
        }

        @keyframes global-loader-spin {
            to {
                transform: rotate(360deg);
            }
        }

        button.is-loading {
            opacity: 0.75;
            cursor: wait;
        }

        /* Fixed tooltip is rendered via #fixed-tooltip (no overflow/scroll impact) */
        /* Global input & textarea styling */
        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="date"],
        input[type="password"],
        textarea {
            width: 100%;
            padding: 0.625rem 1rem; /* py-2.5 px-4 */
            border-width: 2px;
            border-style: solid;
            border-color: #d1d5db; /* gray-300 */
            border-radius: 0.5rem; /* rounded-lg */
            background-color: #ffffff;
            color: #111827; /* gray-900 */
            transition: all 0.2s ease-in-out;
        }

        input::placeholder,
        textarea::placeholder {
            color: #9ca3af; /* gray-400 */
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        input[type="password"]:focus,
        textarea:focus {
            border-color: #6366f1; /* indigo-500 */
            outline: none;
            box-shadow: 0 0 0 2px rgba(129, 140, 248, 0.5); /* ring-indigo-200 */
        }

        input[type="text"]:hover,
        input[type="email"]:hover,
        input[type="number"]:hover,
        input[type="date"]:hover,
        input[type="password"]:hover,
        textarea:hover {
            border-color: #9ca3af; /* gray-400 */
        }

        input[type="text"]:disabled,
        input[type="email"]:disabled,
        input[type="number"]:disabled,
        textarea:disabled {
            background-color: #f3f4f6; /* gray-100 */
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* File input styling */
        input[type="file"] {
            width: 100%;
            padding: 0.625rem 1rem;
            border-width: 2px;
            border-style: solid;
            border-color: #d1d5db;
            border-radius: 0.5rem;
            background-color: #ffffff;
            color: #374151; /* gray-700 */
            transition: all 0.2s ease-in-out;
        }

        input[type="file"]:focus {
            border-color: #6366f1;
            outline: none;
            box-shadow: 0 0 0 2px rgba(129, 140, 248, 0.5);
        }

        /* Base select styling (for non-Select2-capable browsers / before init) */
        select {
            width: 100%;
            padding: 0.625rem 1rem;
            border-width: 2px;
            border-style: solid;
            border-color: #d1d5db;
            border-radius: 0.5rem;
            background-color: #ffffff;
            color: #111827;
            transition: all 0.2s ease-in-out;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.25rem 1.25rem;
        }

        select:focus {
            border-color: #6366f1;
            outline: none;
            box-shadow: 0 0 0 2px rgba(129, 140, 248, 0.5);
        }

        select:hover {
            border-color: #9ca3af;
        }

        select:disabled {
            background-color: #f3f4f6;
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* Select2 theme tweaks to match inputs */
        .select2-container--default .select2-selection--single {
            height: auto;
            min-height: 2.75rem;
            padding: 0.25rem 0.75rem;
            border-width: 2px;
            border-color: #d1d5db;
            border-radius: 0.5rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.5rem;
            color: #111827;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
            right: 0.75rem;
        }

        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #6366f1;
            box-shadow: 0 0 0 2px rgba(129, 140, 248, 0.5);
        }

        .select2-dropdown {
            border-width: 2px;
            border-color: #d1d5db;
        }
    </style>
    <!-- jQuery & Select2 JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.jQuery && jQuery().select2) {
                // Apply Select2 to any select with .select2 class
                $('.select2').select2({
                    width: '100%'
                });
            }

            const loader = document.getElementById('global-loader');
            const showLoader = () => loader?.classList.add('visible');
            const hideLoader = () => loader?.classList.remove('visible');

            window.showGlobalLoader = showLoader;

            window.addEventListener('pageshow', hideLoader);
            window.addEventListener('load', hideLoader);

            document.addEventListener('submit', function (event) {
                const form = event.target;
                if (!(form instanceof HTMLFormElement)) {
                    return;
                }
                // Only show loader when form passes HTML5 validation; otherwise user can't fix errors
                if (!form.checkValidity()) {
                    return;
                }

                // If a form submit is cancelled (e.g. user clicks "Cancel" in confirm()),
                // don't show the loader or lock the button.
                if (event.defaultPrevented) {
                    return;
                }

                const submitter = event.submitter;
                if (submitter instanceof HTMLButtonElement) {
                    submitter.classList.add('is-loading');
                    submitter.disabled = true;
                }

                showLoader();
            }, false);

            document.addEventListener('click', function (event) {
                const anchor = event.target.closest('a[href]');
                if (anchor) {
                    const href = anchor.getAttribute('href');
                    const target = anchor.getAttribute('target');
                    const openInNewTab = target === '_blank' || event.ctrlKey || event.metaKey || event.button === 1;

                    if (
                        href &&
                        !href.startsWith('#') &&
                        !href.startsWith('javascript:') &&
                        !openInNewTab &&
                        !anchor.hasAttribute('download')
                    ) {
                        showLoader();
                    }
                    return;
                }

                const button = event.target.closest('button');
                if (!button || button.disabled) {
                    return;
                }

                // Don't show loader for buttons that open modals or trigger in-page actions only
                if (button.hasAttribute('data-no-loader')) {
                    return;
                }

                // Don't show loader for buttons inside rich text editors (e.g. Quill toolbar)
                if (button.closest('.ql-toolbar') || button.closest('[class*="ql-"]')) {
                    return;
                }

                const explicitType = (button.getAttribute('type') || '').toLowerCase();
                const isSubmit = explicitType === 'submit' || (!explicitType && !!button.closest('form'));
                if (isSubmit) {
                    // Don't show loader on submit-button click; submit handler will show it only if form is valid
                    return;
                }
                // type="button" is for in-page actions only (add row, remove, modal, cancel, etc.) — never show loader
                if (explicitType === 'button') {
                    return;
                }

                button.classList.add('is-loading');
                showLoader();
            }, true);

            // Fixed-position tooltip (avoids overflow/scroll in tables)
            const fixedTooltip = document.getElementById('fixed-tooltip');
            if (fixedTooltip) {
                const show = (el) => {
                    const text = el.getAttribute('data-tooltip');
                    if (!text) return;
                    fixedTooltip.textContent = text;
                    fixedTooltip.classList.remove('invisible');
                    fixedTooltip.classList.add('opacity-0');
                    requestAnimationFrame(() => {
                        const rect = el.getBoundingClientRect();
                        const ttRect = fixedTooltip.getBoundingClientRect();
                        const gap = 8;
                        let left = rect.left + (rect.width / 2) - (ttRect.width / 2);
                        let top = rect.top - ttRect.height - gap;
                        const pad = 6;
                        if (left < pad) left = pad;
                        if (left + ttRect.width > window.innerWidth - pad) left = window.innerWidth - ttRect.width - pad;
                        if (top < pad) top = rect.bottom + gap;
                        fixedTooltip.style.left = left + 'px';
                        fixedTooltip.style.top = top + 'px';
                        fixedTooltip.classList.remove('opacity-0');
                    });
                };
                const hide = () => {
                    fixedTooltip.classList.add('invisible', 'opacity-0');
                };
                document.body.addEventListener('mouseover', (e) => {
                    const el = e.target.closest('[data-tooltip]');
                    if (el) show(el);
                    else hide();
                });
                document.body.addEventListener('mouseout', (e) => {
                    if (!e.relatedTarget || (!e.relatedTarget.closest('[data-tooltip]') && e.relatedTarget !== fixedTooltip)) hide();
                });
                document.body.addEventListener('focusin', (e) => {
                    const el = e.target.closest('[data-tooltip]');
                    if (el) show(el);
                });
                document.body.addEventListener('focusout', (e) => {
                    if (!e.relatedTarget || !e.relatedTarget.closest('[data-tooltip]')) hide();
                });
            }
        });
    </script>
</head>
<body class="bg-gray-50">
    <div id="global-loader" aria-live="polite" aria-busy="true" role="status">
        <div class="global-loader-content">
            <span class="global-loader-spinner" aria-hidden="true"></span>
            <span>Processing...</span>
        </div>
    </div>
    <div id="fixed-tooltip" role="tooltip" class="fixed z-[10000] invisible opacity-0 transition-opacity duration-150 pointer-events-none max-w-[260px] px-2.5 py-1.5 text-xs leading-snug text-white bg-gray-900 rounded-lg shadow-lg">
    </div>
    @auth
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}" class="text-xl font-bold text-gray-900">{{ $appName ?? config('app.name', 'Inventory Management') }}</a>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Dashboard</a>
                        <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') || request()->routeIs('stock.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Products</a>
                        <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Customers</a>
                        <a href="{{ route('invoices.index') }}" class="{{ request()->routeIs('invoices.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Invoices</a>
                        <a href="{{ route('account.edit') }}" class="{{ request()->routeIs('account.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Account</a>
                        <a href="{{ route('company.edit') }}" class="{{ request()->routeIs('company.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Settings</a>
                    </div>
                </div>
                <div class="flex items-center">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <main class="py-6 min-w-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 min-w-0">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</body>
</html>
