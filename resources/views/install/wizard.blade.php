<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup - Inventory System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --bg-start: #eef2ff;
            --bg-end: #f8fafc;
            --border: #dbe3f5;
            --border-focus: #6366f1;
            --text-muted: #64748b;
        }
        body { margin: 0; background: radial-gradient(circle at top left, var(--bg-start), var(--bg-end) 58%); color: #0f172a; }
        .card {
            max-width: 520px;
            border-radius: 1rem;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 16px 40px rgba(79, 70, 229, 0.14);
            border: 1px solid rgba(226, 232, 240, 0.95);
        }
        .step-badge { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #6366f1; }
        .label { display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.4rem; }
        .input {
            width: 100%; height: 2.6rem; border: 1.5px solid var(--border); border-radius: 0.75rem;
            padding: 0 0.9rem; outline: none; transition: border-color 0.2s, box-shadow 0.2s;
        }
        .input:focus { border-color: var(--border-focus); box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.18); }
        .btn-primary {
            width: 100%; border: 0; border-radius: 0.8rem; background: linear-gradient(135deg, #4f46e5, #4338ca);
            color: #fff; font-weight: 700; padding: 0.7rem 1rem; cursor: pointer;
            box-shadow: 0 10px 22px rgba(79, 70, 229, 0.3);
        }
        .btn-primary:hover { filter: brightness(1.05); }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="card w-full space-y-8">
            <div class="text-center">
                <h1 class="text-2xl font-bold text-gray-900">Welcome to Inventory System</h1>
                <p class="mt-1 text-sm text-gray-500">Complete the steps below to set up your application.</p>
            </div>

            <form action="{{ route('install.store') }}" method="POST" class="space-y-8">
                @csrf

                {{-- Step 1: Admin user --}}
                <div class="space-y-4">
                    <span class="step-badge">Step 1 — Admin account</span>
                    <p class="text-sm text-gray-600">Create the first user (administrator). You will use these credentials to sign in.</p>

                    <div>
                        <label for="name" class="label">Full name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" class="input" value="{{ old('name') }}" required placeholder="e.g. Admin User">
                    </div>
                    <div>
                        <label for="email" class="label">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" class="input" value="{{ old('email') }}" required placeholder="admin@example.com">
                        @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="password" class="label">Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password" id="password" class="input" required placeholder="Min 8 characters">
                        @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="label">Confirm password <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="input" required placeholder="Repeat password">
                    </div>
                </div>

                {{-- Step 2: Company (optional) --}}
                <div class="space-y-4 border-t border-gray-200 pt-6">
                    <span class="step-badge">Step 2 — Company (optional)</span>
                    <p class="text-sm text-gray-600">You can set your company details now or later from Settings.</p>

                    <div>
                        <label for="company_name" class="label">Company name</label>
                        <input type="text" name="company_name" id="company_name" class="input" value="{{ old('company_name') }}" placeholder="Your company name">
                    </div>
                    <div>
                        <label for="company_gst_number" class="label">GST number</label>
                        <input type="text" name="company_gst_number" id="company_gst_number" class="input" value="{{ old('company_gst_number') }}" placeholder="GST number">
                    </div>
                    <div>
                        <label for="company_address" class="label">Address</label>
                        <textarea name="company_address" id="company_address" class="input min-h-[80px] py-2" rows="3" placeholder="Company address">{{ old('company_address') }}</textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="company_phone" class="label">Phone</label>
                            <input type="text" name="company_phone" id="company_phone" class="input" value="{{ old('company_phone') }}" placeholder="Phone">
                        </div>
                        <div>
                            <label for="company_email" class="label">Email</label>
                            <input type="email" name="company_email" id="company_email" class="input" value="{{ old('company_email') }}" placeholder="company@example.com">
                        </div>
                    </div>
                </div>

                @if($errors->any() && !$errors->has('email') && !$errors->has('password'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <button type="submit" class="btn-primary">Complete setup & sign in</button>
            </form>
        </div>
    </div>
</body>
</html>
