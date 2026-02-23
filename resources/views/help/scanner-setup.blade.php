@extends('layouts.app')

@section('title', 'Setup Scanner Using Mobile (Webcam)')

@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ url()->previous() ?? route('dashboard') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">&larr; Back</a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900 mb-2">Setup scanner using mobile as webcam</h1>
    <p class="text-gray-600 mb-6">Use <strong>DroidCam</strong> to turn your phone into a webcam so the barcode scanner can use it. Install the app on your phone and the client on your Windows PC, then connect via <strong>Wi‑Fi</strong> or <strong>USB</strong> (USB is usually more stable and works well for Android).</p>

    <p class="text-gray-700 font-medium mb-2">Install the apps</p>
    <ul class="list-disc list-inside space-y-1 text-gray-700 mb-6">
        <li><strong>On your phone:</strong> <a href="https://play.google.com/store/apps/details?id=com.dev47apps.obsdroidcam" target="_blank" rel="noopener" class="text-indigo-600 hover:underline">Android (Google Play)</a> or <a href="https://apps.apple.com/us/app/droidcam-webcam-sharing/id1472440573" target="_blank" rel="noopener" class="text-indigo-600 hover:underline">iOS (App Store)</a></li>
        <li><strong>On your PC:</strong> <a href="https://droidcam.app/windows/" target="_blank" rel="noopener" class="text-indigo-600 hover:underline">DroidCam for Windows</a></li>
    </ul>

    <p class="text-gray-700 font-medium mb-2">Connect: choose one</p>

    <div class="space-y-6 mb-6">
        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Option A — Wi‑Fi</h2>
            <ol class="list-decimal list-inside space-y-1 text-gray-700 text-sm">
                <li>Put phone and PC on the <strong>same Wi‑Fi network</strong>.</li>
                <li>Open DroidCam on the phone and note the IP and port (e.g. <code class="bg-gray-100 px-1 rounded">192.168.1.x:4747</code>).</li>
                <li>In the Windows client, choose <strong>Wi-Fi</strong>, enter that address (or use the in-app Connect), then click <strong>Start</strong>.</li>
            </ol>
        </div>

        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Option B — USB (Android; often more stable)</h2>
            <ol class="list-decimal list-inside space-y-1 text-gray-700 text-sm">
                <li>On your Android phone: enable <strong>Developer options</strong> (tap <em>Build number</em> 7 times under Settings → About), then turn on <strong>USB debugging</strong> in Developer options.</li>
                <li>Connect the phone to the PC with a <strong>USB cable</strong>. If Windows asks to install drivers, allow it (or install your phone’s USB drivers if needed).</li>
                <li>Open DroidCam on the phone. On the PC client, select <strong>USB</strong>, click the refresh button, and allow <strong>USB debugging</strong> on the phone when prompted. Then click <strong>Start</strong>.</li>
                <li>If the device is not detected, try changing the USB mode on the phone (notification → USB options) to <strong>PTP</strong> or <strong>File transfer</strong>.</li>
            </ol>
            <p class="text-gray-500 text-xs mt-2">iOS: USB is not supported by DroidCam; use Wi‑Fi for iPhones.</p>
        </div>
    </div>

    <p class="text-gray-700">Once connected (Wi‑Fi or USB), the phone camera appears as a <strong>webcam</strong> in Windows. In this app, open the barcode scanner and choose that camera from the device list if more than one is available.</p>

    <div class="mt-8 p-4 bg-gray-50 rounded-lg border border-gray-200">
        <p class="text-sm text-gray-600 mb-2"><strong>More info</strong></p>
        <a href="https://droidcam.app/" target="_blank" rel="noopener" class="text-indigo-600 hover:underline">droidcam.app</a>
    </div>
</div>
@endsection
