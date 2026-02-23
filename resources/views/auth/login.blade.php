<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventory System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --login-bg-start: #eef2ff;
            --login-bg-end: #f8fafc;
            --login-border: #dbe3f5;
            --login-border-focus: #6366f1;
            --login-text-muted: #64748b;
        }

        body {
            margin: 0;
            background: radial-gradient(circle at top left, var(--login-bg-start), var(--login-bg-end) 58%);
            color: #0f172a;
        }

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

        .login-card {
            width: 100%;
            max-width: 430px;
            border-radius: 1rem;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 16px 40px rgba(79, 70, 229, 0.14);
            border: 1px solid rgba(226, 232, 240, 0.95);
        }

        .login-title {
            text-align: center;
            font-size: 1.75rem;
            line-height: 2rem;
            font-weight: 800;
            letter-spacing: -0.01em;
        }

        .login-subtitle {
            margin-top: 0.5rem;
            text-align: center;
            font-size: 0.92rem;
            color: var(--login-text-muted);
        }

        .login-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 0.4rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            width: 1rem;
            height: 1rem;
            color: #94a3b8;
            pointer-events: none;
        }

        .login-input {
            width: 100%;
            height: 2.9rem;
            border: 1.5px solid var(--login-border);
            border-radius: 0.8rem;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            color: #0f172a;
            padding: 0.45rem 0.9rem 0.45rem 2.5rem;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .login-input::placeholder {
            color: #94a3b8;
        }

        .login-input:hover {
            border-color: #b9c5e6;
        }

        .login-input:focus {
            border-color: var(--login-border-focus);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.18);
            transform: translateY(-1px);
        }

        .login-submit {
            width: 100%;
            border: 0;
            border-radius: 0.8rem;
            background: linear-gradient(135deg, #4f46e5, #4338ca);
            color: #ffffff;
            font-weight: 700;
            padding: 0.7rem 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
            box-shadow: 0 10px 22px rgba(79, 70, 229, 0.3);
        }

        .login-submit:hover {
            filter: brightness(1.03);
            transform: translateY(-1px);
        }

        .login-submit:focus {
            outline: none;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.28);
        }

        @media (max-width: 640px) {
            .login-card {
                padding: 1.4rem;
                border-radius: 0.9rem;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const loader = document.getElementById('global-loader');
            const showLoader = () => loader?.classList.add('visible');
            const hideLoader = () => loader?.classList.remove('visible');

            window.addEventListener('pageshow', hideLoader);
            window.addEventListener('load', hideLoader);

            document.addEventListener('submit', function (event) {
                const form = event.target;
                if (!(form instanceof HTMLFormElement)) {
                    return;
                }
                if (!form.checkValidity()) {
                    return;
                }

                const submitter = event.submitter;
                if (submitter instanceof HTMLButtonElement) {
                    submitter.classList.add('is-loading');
                    submitter.disabled = true;
                }

                showLoader();
            }, true);

            document.addEventListener('click', function (event) {
                const anchor = event.target.closest('a[href]');
                if (!anchor) {
                    return;
                }

                const href = anchor.getAttribute('href');
                const target = anchor.getAttribute('target');

                if (
                    href &&
                    !href.startsWith('#') &&
                    !href.startsWith('javascript:') &&
                    target !== '_blank' &&
                    !anchor.hasAttribute('download')
                ) {
                    showLoader();
                }
            }, true);
        });
    </script>
</head>
<body>
    <div id="global-loader" aria-live="polite" aria-busy="true" role="status">
        <div class="global-loader-content">
            <span class="global-loader-spinner" aria-hidden="true"></span>
            <span>Processing...</span>
        </div>
    </div>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="login-card space-y-8">
            <div>
                <h2 class="login-title">Sign in to your account</h2>
                <p class="login-subtitle">Access your inventory dashboard and continue your work.</p>
            </div>
            <form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="email" class="login-label">Email address</label>
                        <div class="input-wrap">
                            <svg class="input-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M2.5 5.75A2.25 2.25 0 0 1 4.75 3.5h10.5A2.25 2.25 0 0 1 17.5 5.75v8.5a2.25 2.25 0 0 1-2.25 2.25H4.75A2.25 2.25 0 0 1 2.5 14.25v-8.5Zm2.46-.75 5.04 3.49L15.04 5H4.96Zm10.79 1.33-5.04 3.49a1.25 1.25 0 0 1-1.42 0L4.25 6.33v7.92c0 .28.22.5.5.5h10.5a.5.5 0 0 0 .5-.5V6.33Z"/>
                            </svg>
                            <input class="login-input" id="email" name="email" type="email" required placeholder="Enter your email" value="{{ old('email') }}">
                        </div>
                    </div>
                    <div>
                        <label for="password" class="login-label">Password</label>
                        <div class="input-wrap">
                            <svg class="input-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 2.5a4 4 0 0 0-4 4v1H5.5A2.5 2.5 0 0 0 3 10v5a2.5 2.5 0 0 0 2.5 2.5h9A2.5 2.5 0 0 0 17 15v-5a2.5 2.5 0 0 0-2.5-2.5H14v-1a4 4 0 0 0-4-4Zm2.5 5v-1a2.5 2.5 0 0 0-5 0v1h5Z" clip-rule="evenodd"/>
                            </svg>
                            <input class="login-input" id="password" name="password" type="password" required placeholder="Enter your password">
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="bg-green-50 border-2 border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-50 border-2 border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div>
                    <button type="submit" class="login-submit">
                        Sign in
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
