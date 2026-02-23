{{-- Barcode scanner: capture image → user selects/crops barcode area → extract code. --}}
<div id="barcodeScannerModal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog" aria-label="Scan barcode">
    <div class="fixed inset-0 bg-black/60" id="barcodeScannerBackdrop"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4 overflow-y-auto overflow-x-hidden min-h-0">
        <div id="barcodeScannerModalBox" class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col my-auto shrink-0">
            <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center shrink-0">
                <h3 class="text-lg font-semibold text-gray-900" id="barcodeScannerTitle">Scan barcode or QR code</h3>
                <div class="flex items-center gap-1">
                    <a href="{{ route('help.scanner-setup') }}" target="_blank" rel="noopener" data-no-loader class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700" aria-label="Scanner setup help" title="Setup scanner using mobile (webcam)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </a>
                    <button type="button" id="barcodeScannerClose" data-no-loader class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700" aria-label="Close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            <div class="p-4 overflow-y-auto min-h-0 flex-1">
                <div id="barcodeScannerReader" class="w-full rounded-lg overflow-hidden bg-gray-900 min-h-[280px] flex items-center justify-center relative"></div>
                <p id="barcodeScannerHint" class="text-sm text-gray-500 mt-2">Point camera at barcode (auto-scan), or capture an image, or upload an image file to scan.</p>
                <div id="barcodeScannerZoomWrap" class="mt-3 hidden">
                    <label class="text-xs font-medium text-gray-600 block mb-1">Zoom camera</label>
                    <div class="flex items-center gap-2">
                        <button type="button" id="barcodeScannerZoomOut" data-no-loader class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-xl font-medium" aria-label="Zoom out">−</button>
                        <input type="range" id="barcodeScannerZoomSlider" min="1" max="3" step="0.25" value="1" class="flex-1 h-3 rounded-lg appearance-none bg-gray-200 cursor-pointer accent-indigo-600">
                        <button type="button" id="barcodeScannerZoomIn" data-no-loader class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-xl font-medium" aria-label="Zoom in">+</button>
                        <span id="barcodeScannerZoomValue" class="flex-shrink-0 text-sm font-semibold text-gray-700 w-9">1×</span>
                    </div>
                </div>
                <div id="barcodeScannerActions" class="mt-3 flex flex-wrap gap-2 hidden">
                    <button type="button" id="barcodeScannerCapture" data-no-loader class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 font-medium rounded-lg hover:bg-gray-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
                        Capture image (manual)
                    </button>
                    <button type="button" id="barcodeScannerUploadBtn" data-no-loader class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 font-medium rounded-lg hover:bg-gray-50" title="Scan from image file (desktop or when camera unavailable)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Upload image
                    </button>
                </div>
                <input type="file" id="barcodeScannerFileInput" accept="image/*" class="hidden" aria-hidden="true">
                <div id="barcodeScannerCropActions" class="mt-3 flex flex-wrap gap-2 hidden">
                    <button type="button" id="barcodeScannerRotateLeft" data-no-loader class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 font-medium rounded-lg hover:bg-gray-50" title="Rotate left 90°">↶ Rotate left</button>
                    <button type="button" id="barcodeScannerRotateRight" data-no-loader class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 font-medium rounded-lg hover:bg-gray-50" title="Rotate right 90°">↷ Rotate right</button>
                    <button type="button" id="barcodeScannerExtract" data-no-loader class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700">Extract from selection</button>
                    <button type="button" id="barcodeScannerExtractFull" data-no-loader class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700">Extract from full image</button>
                    <button type="button" id="barcodeScannerExtractText" data-no-loader class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-300 bg-amber-50 text-amber-800 font-medium rounded-lg hover:bg-amber-100" title="OCR: extract text from selected area (e.g. serial number below barcode)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Extract text from selection
                    </button>
                    <button type="button" id="barcodeScannerRetake" data-no-loader class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700">Retake photo</button>
                </div>
                <p id="barcodeScannerStatus" class="text-sm mt-2 hidden"></p>
            </div>
        </div>
    </div>
</div>
<div id="barcodeScannerFileReader" class="hidden" aria-hidden="true"></div>
<canvas id="barcodeScannerCropCanvas" class="hidden"></canvas>

<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
<style>
#barcodeScannerReader .barcode-scanner-video-wrap { width: 100%; min-height: 280px; max-height: 360px; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #111827; }
#barcodeScannerReader .barcode-scanner-video-wrap video { max-width: 100%; max-height: 360px; object-fit: contain; display: block; transition: transform 0.15s ease-out; }
#barcodeScannerReader video { width: 100%; max-height: 360px; object-fit: contain; display: block; }
#barcodeScannerReader canvas { max-width: 100%; height: auto; display: block; cursor: crosshair; }
#barcodeScannerReader { min-height: 280px; }
#barcodeScannerStatus.error { color: #b91c1c; }
#barcodeScannerStatus.success { color: #15803d; }
/* Keep modal box on screen when rotated; content scrolls inside */
#barcodeScannerModalBox { max-height: calc(100vh - 2rem); max-height: calc(100dvh - 2rem); }
</style>
<script>
(function() {
    var currentStream = null;
    var videoEl = null;
    var html5QrCode = null;
    var onScanCallback = null;
    var captureCanvas = null;
    var cropCanvasEl = document.getElementById('barcodeScannerCropCanvas');
    var decodeCanvasEl = document.createElement('canvas');
    var selection = { startX: 0, startY: 0, endX: 0, endY: 0, active: false };
    var mode = 'camera';
    var cameraZoomLevel = 1;
    var videoWrapEl = null;
    var autoScanIntervalId = null;
    var autoScanInProgress = false;
    var autoScanCanvas = document.createElement('canvas');
    var captureRotation = 0;
    var barcodeDetector = null;
    var barcodeDetector1D = null;
    (function initBarcodeDetector() {
        if (typeof window.BarcodeDetector === 'undefined') return;
        var formatsWithBarcode = ['qr_code', 'code_128', 'code_39', 'ean_13', 'ean_8', 'upc_a', 'upc_e', 'codabar', 'itf', 'aztec', 'data_matrix', 'pdf417'];
        try {
            barcodeDetector = new window.BarcodeDetector({ formats: formatsWithBarcode });
        } catch (e1) {
            try {
                barcodeDetector = new window.BarcodeDetector({ formats: ['qr_code', 'code_128', 'ean_13', 'upc_a'] });
            } catch (e2) {
                try {
                    barcodeDetector = new window.BarcodeDetector();
                } catch (e3) {}
            }
        }
        try {
            barcodeDetector1D = new window.BarcodeDetector({ formats: ['ean_13', 'ean_8', 'upc_a', 'upc_e', 'code_128', 'code_39'] });
        } catch (e) {
            barcodeDetector1D = null;
        }
    })();

    var modal = document.getElementById('barcodeScannerModal');
    var readerDiv = document.getElementById('barcodeScannerReader');
    var backdrop = document.getElementById('barcodeScannerBackdrop');
    var closeBtn = document.getElementById('barcodeScannerClose');
    var actionsDiv = document.getElementById('barcodeScannerActions');
    var cropActionsDiv = document.getElementById('barcodeScannerCropActions');
    var captureBtn = document.getElementById('barcodeScannerCapture');
    var extractBtn = document.getElementById('barcodeScannerExtract');
    var extractFullBtn = document.getElementById('barcodeScannerExtractFull');
    var extractTextBtn = document.getElementById('barcodeScannerExtractText');
    var retakeBtn = document.getElementById('barcodeScannerRetake');
    var hintEl = document.getElementById('barcodeScannerHint');
    var statusEl = document.getElementById('barcodeScannerStatus');
    var titleEl = document.getElementById('barcodeScannerTitle');
    var zoomWrap = document.getElementById('barcodeScannerZoomWrap');
    var zoomSlider = document.getElementById('barcodeScannerZoomSlider');
    var zoomValueEl = document.getElementById('barcodeScannerZoomValue');
    var zoomOutBtn = document.getElementById('barcodeScannerZoomOut');
    var zoomInBtn = document.getElementById('barcodeScannerZoomIn');
    var uploadBtn = document.getElementById('barcodeScannerUploadBtn');
    var fileInput = document.getElementById('barcodeScannerFileInput');

    function isElectronOrNativePHP() {
        if (typeof navigator === 'undefined') return false;
        var ua = navigator.userAgent || '';
        if (ua.indexOf('Electron') !== -1) return true;
        if (typeof window !== 'undefined' && (window.nativephp || window.NativePHP)) return true;
        return false;
    }

    function isSecureContext() {
        if (typeof window === 'undefined' || !window.location) return false;
        if (isElectronOrNativePHP()) return true;
        if (window.isSecureContext === true) return true;
        if (window.location.protocol === 'https:') return true;
        if (window.location.protocol === 'http:' && /^(localhost|127\.0\.0\.1)$/i.test((window.location.hostname || ''))) return true;
        return false;
    }

    function stopCamera() {
        stopAutoScan();
        if (currentStream) {
            currentStream.getTracks().forEach(function(t) { t.stop(); });
            currentStream = null;
        }
        if (videoEl && videoEl.srcObject) {
            videoEl.srcObject = null;
        }
    }

    function resetScannerState() {
        stopCamera();
        captureCanvas = null;
        mode = 'camera';
        cameraZoomLevel = 1;
        if (readerDiv) {
            readerDiv.innerHTML = '';
            readerDiv.id = 'barcodeScannerReader';
            readerDiv.classList.remove('barcode-scanner-crop-mode');
        }
        if (cropActionsDiv) cropActionsDiv.classList.add('hidden');
        if (actionsDiv) actionsDiv.classList.remove('hidden');
        if (zoomWrap) zoomWrap.classList.remove('hidden');
        if (statusEl) statusEl.classList.add('hidden');
        if (titleEl) titleEl.textContent = 'Scan barcode or QR code';
        if (hintEl) hintEl.textContent = 'Point camera at barcode — it will scan automatically. Or capture an image to select the area manually.';
        if (zoomSlider) zoomSlider.value = 1;
        if (zoomValueEl) zoomValueEl.textContent = '1×';
    }

    function closeModal() {
        stopCamera();
        resetScannerState();
        modal.classList.add('hidden');
        onScanCallback = null;
    }

    function stopAutoScan() {
        if (autoScanIntervalId) {
            clearInterval(autoScanIntervalId);
            autoScanIntervalId = null;
        }
    }

    function runAutoScan() {
        if (mode !== 'camera' || !videoEl || videoEl.readyState < 2 || autoScanInProgress) return;
        var w = videoEl.videoWidth;
        var h = videoEl.videoHeight;
        if (!w || !h) return;
        var maxDim = 640;
        var scale = 1;
        if (w > maxDim || h > maxDim) scale = maxDim / Math.max(w, h);
        var cw = Math.round(w * scale);
        var ch = Math.round(h * scale);
        autoScanCanvas.width = cw;
        autoScanCanvas.height = ch;
        var ctx = autoScanCanvas.getContext('2d');
        try {
            ctx.drawImage(videoEl, 0, 0, w, h, 0, 0, cw, ch);
        } catch (e) {
            return;
        }
        autoScanInProgress = true;
        function done() {
            autoScanInProgress = false;
        }
        function tryDetect(detector, next) {
            if (!detector) {
                next();
                return;
            }
            detector.detect(autoScanCanvas)
                .then(function(detected) {
                    if (detected && detected.length > 0 && detected[0].rawValue) {
                        if (applyDetectedValue(detected[0].rawValue)) {
                            stopAutoScan();
                            stopCamera();
                        }
                        done();
                        return;
                    }
                    next();
                })
                .catch(function() { next(); });
        }
        tryDetect(barcodeDetector, function() {
            tryDetect(barcodeDetector1D, function() {
                done();
            });
        });
    }

    function fillNextEmptySerialInput(value) {
        var inputs = document.querySelectorAll('input[name="serial_numbers[]"]');
        for (var i = 0; i < inputs.length; i++) {
            if (!inputs[i].value.trim()) {
                inputs[i].value = value;
                inputs[i].dispatchEvent(new Event('input', { bubbles: true }));
                return true;
            }
        }
        return false;
    }

    function applyDetectedValue(value) {
        value = String(value || '').trim();
        if (!value || !onScanCallback) return false;
        var result = onScanCallback(value);
        if (result !== false) {
            stopCamera();
            closeModal();
            return true;
        }
        return false;
    }

    function showStatus(msg, isError) {
        statusEl.textContent = msg;
        statusEl.classList.remove('hidden', 'error', 'success');
        statusEl.classList.add(isError ? 'error' : 'success');
    }

    function showCameraError(msg, detail, showSecureTip, showTryAgain) {
        var secureTip = '';
        if (showSecureTip && !isElectronOrNativePHP() && window.location && window.location.protocol === 'http:' && !/^(localhost|127\.0\.0\.1)$/i.test((window.location.hostname || ''))) {
            secureTip = '<p class="px-4 pb-2 text-sm text-amber-800 font-medium">Camera only works on <strong>HTTPS</strong> or <strong>localhost</strong>.</p>';
        }
        var tryAgainBtn = showTryAgain ? '<p class="px-4 pb-2"><button type="button" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700" data-no-loader id="barcodeScannerTryAgain">Try again</button></p>' : '';
        var uploadFallback = '<p class="px-4 pb-4"><button type="button" class="px-4 py-2 border border-gray-300 bg-white text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50" data-no-loader id="barcodeScannerUploadFromError">Choose image file to scan</button></p>';
        readerDiv.innerHTML = '<p class="p-4 text-amber-700">' + (msg || 'Camera access needed.') + '</p>' + secureTip + '<p class="px-4 pb-2 text-sm text-gray-600">' + (detail || '') + '</p>' + tryAgainBtn + uploadFallback;
        if (showTryAgain) {
            var btn = document.getElementById('barcodeScannerTryAgain');
            if (btn) btn.addEventListener('click', function() { window.openBarcodeScanner(onScanCallback); });
        }
        var uploadFromErrorBtn = document.getElementById('barcodeScannerUploadFromError');
        if (uploadFromErrorBtn && fileInput) uploadFromErrorBtn.addEventListener('click', function() { fileInput.click(); });
        actionsDiv.classList.add('hidden');
        cropActionsDiv.classList.add('hidden');
    }

    function getNotAllowedMessage() {
        if (isElectronOrNativePHP()) return 'Allow camera in Windows Settings → Privacy & security → Camera, or use "Choose image file to scan" below.';
        return 'Allow camera for this site (lock/camera icon in address bar), or use "Upload image" to scan from a file.';
    }

    function eventToCanvasCoords(canvas, ev) {
        var rect = canvas.getBoundingClientRect();
        var scaleX = canvas.width / rect.width;
        var scaleY = canvas.height / rect.height;
        return {
            x: Math.round((ev.clientX - rect.left) * scaleX),
            y: Math.round((ev.clientY - rect.top) * scaleY)
        };
    }

    function redrawCaptureWithSelection() {
        if (!captureCanvas || !captureCanvas._capturedImage) return;
        var img = captureCanvas._capturedImage;
        var imgW = img.naturalWidth || img.width;
        var imgH = img.naturalHeight || img.height;
        var rot = ((captureRotation % 360) + 360) % 360;
        var cw = (rot === 90 || rot === 270) ? imgH : imgW;
        var ch = (rot === 90 || rot === 270) ? imgW : imgH;
        captureCanvas.width = cw;
        captureCanvas.height = ch;
        var ctx = captureCanvas.getContext('2d');
        ctx.save();
        if (rot === 90) {
            ctx.translate(cw, 0);
            ctx.rotate(Math.PI / 2);
            ctx.drawImage(img, 0, 0, imgW, imgH, 0, 0, imgW, imgH);
        } else if (rot === 180) {
            ctx.translate(cw, ch);
            ctx.rotate(Math.PI);
            ctx.drawImage(img, 0, 0, imgW, imgH, 0, 0, imgW, imgH);
        } else if (rot === 270) {
            ctx.translate(0, ch);
            ctx.rotate(-Math.PI / 2);
            ctx.drawImage(img, 0, 0, imgW, imgH, 0, 0, imgW, imgH);
        } else {
            ctx.drawImage(img, 0, 0, imgW, imgH, 0, 0, cw, ch);
        }
        ctx.restore();
        var sx = Math.min(selection.startX, selection.endX);
        var sy = Math.min(selection.startY, selection.endY);
        var sw = Math.abs(selection.endX - selection.startX);
        var sh = Math.abs(selection.endY - selection.startY);
        if (sw > 2 && sh > 2) {
            ctx.strokeStyle = '#22c55e';
            ctx.lineWidth = 3;
            ctx.strokeRect(sx, sy, sw, sh);
            ctx.fillStyle = 'rgba(34, 197, 94, 0.2)';
            ctx.fillRect(sx, sy, sw, sh);
        }
    }

    function sharpenCanvas(ctx, w, h) {
        try {
            var imgData = ctx.getImageData(0, 0, w, h);
            var d = imgData.data;
            var stride = w * 4;
            var kernel = [0, -1, 0, -1, 5, -1, 0, -1, 0];
            var side = Math.floor(Math.sqrt(kernel.length));
            var half = (side - 1) / 2;
            var out = new Uint8ClampedArray(d.length);
            for (var y = 0; y < h; y++) {
                for (var x = 0; x < w; x++) {
                    for (var c = 0; c < 4; c++) {
                        var sum = 0;
                        for (var ky = -half; ky <= half; ky++) {
                            for (var kx = -half; kx <= half; kx++) {
                                var ny = y + ky;
                                var nx = x + kx;
                                if (ny >= 0 && ny < h && nx >= 0 && nx < w) {
                                    sum += d[(ny * w + nx) * 4 + c] * kernel[(ky + half) * side + (kx + half)];
                                }
                            }
                        }
                        out[(y * w + x) * 4 + c] = Math.max(0, Math.min(255, c < 3 ? sum : d[(y * w + x) * 4 + c]));
                    }
                }
            }
            imgData.data.set(out);
            ctx.putImageData(imgData, 0, 0);
        } catch (e) {}
    }

    function getSelectedRegion() {
        var sx = Math.min(selection.startX, selection.endX);
        var sy = Math.min(selection.startY, selection.endY);
        var sw = Math.abs(selection.endX - selection.startX);
        var sh = Math.abs(selection.endY - selection.startY);
        if (sw < 10 || sh < 10) return null;
        return { x: sx, y: sy, w: sw, h: sh };
    }

    function ensureMinSize(srcCanvas, minDim) {
        minDim = minDim || 400;
        var w = srcCanvas.width;
        var h = srcCanvas.height;
        if (!w || !h) return srcCanvas;
        var minH = h;
        var minW = w;
        if (w < minDim || h < minDim) {
            var scale = Math.max(minDim / w, minDim / h, 1);
            minW = Math.round(w * scale);
            minH = Math.round(h * scale);
        }
        if (minW > 2 * minH) minH = Math.max(minH, 320);
        if (minH > 2 * minW) minW = Math.max(minW, 320);
        if (minW === w && minH === h) return srcCanvas;
        decodeCanvasEl.width = minW;
        decodeCanvasEl.height = minH;
        var ctx = decodeCanvasEl.getContext('2d');
        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = 'high';
        ctx.drawImage(srcCanvas, 0, 0, w, h, 0, 0, minW, minH);
        return decodeCanvasEl;
    }

    function toGrayscaleContrastCanvas(src) {
        try {
            if (!src || !src.width || !src.height) return null;
            var c = document.createElement('canvas');
            c.width = src.width;
            c.height = src.height;
            var ctx = c.getContext('2d');
            ctx.drawImage(src, 0, 0);
            var imgData = ctx.getImageData(0, 0, c.width, c.height);
            var d = imgData.data;
            var brightness = 22;
            for (var i = 0; i < d.length; i += 4) {
                var g = (0.299 * d[i] + 0.587 * d[i+1] + 0.114 * d[i+2]);
                g = g < 128 ? Math.max(0, g - 20) : Math.min(255, g + 20);
                g = Math.max(0, Math.min(255, Math.round(g) + brightness));
                d[i] = d[i+1] = d[i+2] = g;
            }
            ctx.putImageData(imgData, 0, 0);
            return c;
        } catch (e) {
            return null;
        }
    }

    /** Black/white binarized canvas. threshold 0-255 (default 128). invert=true => white bars on black. */
    function toBinarizedCanvas(src, invert, threshold) {
        if (threshold == null) threshold = 128;
        try {
            if (!src || !src.width || !src.height) return null;
            var c = document.createElement('canvas');
            c.width = src.width;
            c.height = src.height;
            var ctx = c.getContext('2d');
            ctx.drawImage(src, 0, 0);
            var imgData = ctx.getImageData(0, 0, c.width, c.height);
            var d = imgData.data;
            for (var i = 0; i < d.length; i += 4) {
                var g = (0.299 * d[i] + 0.587 * d[i+1] + 0.114 * d[i+2]);
                var v = (g <= threshold) ? 0 : 255;
                if (invert) v = 255 - v;
                d[i] = d[i+1] = d[i+2] = v;
            }
            ctx.putImageData(imgData, 0, 0);
            return c;
        } catch (e) {
            return null;
        }
    }

    /** Try decoding with several binarized variants (helps smudged/damaged or low-contrast labels). */
    function tryBinarizedVariants(workCanvas, tryOne, onAllFailed) {
        var thresholds = [105, 128, 150];
        var idx = 0;
        function next() {
            if (idx >= thresholds.length * 2) {
                onAllFailed();
                return;
            }
            var inv = (idx % 2) === 1;
            var th = thresholds[Math.floor(idx / 2)];
            idx++;
            var c = toBinarizedCanvas(workCanvas, inv, th);
            if (c) tryOne(c, next);
            else next();
        }
        next();
    }

    function decodeImageCanvas(srcCanvas, onDone) {
        var doneOnce = false;
        function finish(ok, value, errorMsg) {
            if (doneOnce) return;
            doneOnce = true;
            try {
                onDone(ok, value, errorMsg);
            } catch (e) {}
        }
        try {
            if (!srcCanvas || srcCanvas.width === 0 || srcCanvas.height === 0) {
                finish(false, null, 'Image area is too small. Draw a larger rectangle around the barcode.');
                return;
            }
            var workCanvas = ensureMinSize(srcCanvas, 560);
            function succeed(value) {
                finish(true, value);
            }
            function fail() {
                tryScanFileWithUpscale(srcCanvas, 600, succeed, function(ok) {
                    if (ok) return;
                    tryScanFileWithUpscale(srcCanvas, 900, succeed, function(ok2) {
                        if (ok2) return;
                        tryScanFileWithUpscale(srcCanvas, 1200, succeed, function(ok3) {
                            if (!ok3) finish(false, null, null);
                        });
                    });
                });
            }
            function tryBarcodeThenHtml5(canvasToTry, thenFail) {
                if (!canvasToTry || canvasToTry.width === 0 || canvasToTry.height === 0) {
                    thenFail();
                    return;
                }
                if (!barcodeDetector) {
                    tryScanFile(canvasToTry, succeed, thenFail);
                    return;
                }
                barcodeDetector.detect(canvasToTry)
                    .then(function(detected) {
                        if (detected && detected.length > 0 && detected[0].rawValue) {
                            succeed(detected[0].rawValue);
                            return;
                        }
                        tryScanFile(canvasToTry, succeed, thenFail);
                    })
                    .catch(function() {
                        tryScanFile(canvasToTry, succeed, thenFail);
                    });
            }
            function tryBinarizedThenFail() {
                tryBinarizedVariants(workCanvas, function(canvas, next) {
                    tryBarcodeThenHtml5(canvas, next);
                }, fail);
            }
            tryBarcodeThenHtml5(workCanvas, function() {
                var grayCanvas = toGrayscaleContrastCanvas(workCanvas);
                if (grayCanvas) {
                    tryBarcodeThenHtml5(grayCanvas, tryBinarizedThenFail);
                } else {
                    tryBinarizedThenFail();
                }
            });
        } catch (e) {
            finish(false, null, 'Extraction failed. Please try again or enter the code manually.');
        }
    }

    function tryScanFileWithUpscale(srcCanvas, minShortSide, succeed, onDone) {
        minShortSide = minShortSide || 600;
        try {
            var w = srcCanvas.width;
            var h = srcCanvas.height;
            if (!w || !h) {
                onDone(false);
                return;
            }
            var maxLongSide = 3600;
            var scale = 1;
            if (w < minShortSide || h < minShortSide) {
                scale = Math.max(minShortSide / w, minShortSide / h, 2);
            }
            var bigW = Math.round(w * scale);
            var bigH = Math.round(h * scale);
            if (Math.max(bigW, bigH) > maxLongSide) {
                var s = maxLongSide / Math.max(bigW, bigH);
                bigW = Math.round(bigW * s);
                bigH = Math.round(bigH * s);
            }
            bigW = Math.max(bigW, 200);
            bigH = Math.max(bigH, 200);
            decodeCanvasEl.width = bigW;
            decodeCanvasEl.height = bigH;
            var ctx = decodeCanvasEl.getContext('2d');
            ctx.imageSmoothingEnabled = true;
            ctx.imageSmoothingQuality = 'high';
            ctx.drawImage(srcCanvas, 0, 0, w, h, 0, 0, bigW, bigH);
            function upscaleFail() {
                tryBinarizedVariants(decodeCanvasEl, function(canvas, next) {
                    var det = barcodeDetector || barcodeDetector1D;
                    if (det) {
                        det.detect(canvas).then(function(detected) {
                            if (detected && detected.length > 0 && detected[0].rawValue) {
                                succeed(detected[0].rawValue);
                                return;
                            }
                            tryScanFile(canvas, succeed, next);
                        }).catch(function() { tryScanFile(canvas, succeed, next); });
                    } else {
                        tryScanFile(canvas, succeed, next);
                    }
                }, function() { onDone(false); });
            }
            function upscaleDone() {
                if (barcodeDetector1D) {
                    barcodeDetector1D.detect(decodeCanvasEl)
                        .then(function(detected) {
                            if (detected && detected.length > 0 && detected[0].rawValue) {
                                succeed(detected[0].rawValue);
                                return;
                            }
                            tryScanFile(decodeCanvasEl, succeed, upscaleFail);
                        })
                        .catch(function() {
                            tryScanFile(decodeCanvasEl, succeed, upscaleFail);
                        });
                } else {
                    tryScanFile(decodeCanvasEl, succeed, upscaleFail);
                }
            }
            if (barcodeDetector) {
                barcodeDetector.detect(decodeCanvasEl)
                    .then(function(detected) {
                        if (detected && detected.length > 0 && detected[0].rawValue) {
                            succeed(detected[0].rawValue);
                            return;
                        }
                        upscaleDone();
                    })
                    .catch(upscaleDone);
            } else {
                upscaleDone();
            }
        } catch (e) {
            onDone(false);
        }
    }

    function tryScanFile(canvas, succeed, fail) {
        try {
            if (!window.Html5Qrcode) {
                fail();
                return;
            }
            if (!canvas || canvas.width === 0 || canvas.height === 0) {
                fail();
                return;
            }
            canvas.toBlob(function(blob) {
                try {
                    if (!blob) {
                        fail();
                        return;
                    }
                    var file = new File([blob], 'scan.png', { type: 'image/png' });
                    if (!html5QrCode) html5QrCode = new window.Html5Qrcode('barcodeScannerFileReader');
                    html5QrCode.scanFile(file, false)
                        .then(function(decodedText) {
                            try {
                                if (decodedText && String(decodedText).trim()) succeed(String(decodedText).trim());
                                else fail();
                            } catch (e) { fail(); }
                        })
                        .catch(function() { fail(); });
                } catch (e) {
                    fail();
                }
            }, 'image/png', 1);
        } catch (e) {
            fail();
        }
    }

    function extractFromSelection() {
        var region = getSelectedRegion();
        if (!region) {
            showStatus('Draw a rectangle around the barcode first (drag on the image).', true);
            return;
        }
        if (!captureCanvas || !captureCanvas._capturedImage) return;
        cropCanvasEl.width = region.w;
        cropCanvasEl.height = region.h;
        var ctx = cropCanvasEl.getContext('2d');
        ctx.drawImage(captureCanvas, region.x, region.y, region.w, region.h, 0, 0, region.w, region.h);
        extractBtn.disabled = true;
        if (extractFullBtn) extractFullBtn.disabled = true;
        statusEl.classList.add('hidden');
        try {
            decodeImageCanvas(cropCanvasEl, function(ok, value, errorMsg) {
                extractBtn.disabled = false;
                if (extractFullBtn) extractFullBtn.disabled = false;
                if (ok && applyDetectedValue(value)) return;
                showStatus(errorMsg || 'No barcode or QR code found. Try "Extract from full image" or draw a tighter rectangle.', true);
            });
        } catch (e) {
            extractBtn.disabled = false;
            if (extractFullBtn) extractFullBtn.disabled = false;
            showStatus('Extraction failed. Please try again or enter the code manually.', true);
        }
    }

    function extractFromFullImage() {
        if (!captureCanvas || !captureCanvas._capturedImage) return;
        cropCanvasEl.width = captureCanvas.width;
        cropCanvasEl.height = captureCanvas.height;
        var ctx = cropCanvasEl.getContext('2d');
        ctx.drawImage(captureCanvas, 0, 0, captureCanvas.width, captureCanvas.height, 0, 0, captureCanvas.width, captureCanvas.height);
        extractBtn.disabled = true;
        if (extractFullBtn) extractFullBtn.disabled = true;
        statusEl.classList.add('hidden');
        try {
            decodeImageCanvas(cropCanvasEl, function(ok, value, errorMsg) {
                extractBtn.disabled = false;
                if (extractFullBtn) extractFullBtn.disabled = false;
                if (ok && applyDetectedValue(value)) return;
                showStatus(errorMsg || 'No barcode or QR code found in the image. Retake and ensure the code is clear.', true);
            });
        } catch (e) {
            extractBtn.disabled = false;
            if (extractFullBtn) extractFullBtn.disabled = false;
            showStatus('Extraction failed. Please try again or enter the code manually.', true);
        }
    }

    function extractTextFromSelection() {
        var region = getSelectedRegion();
        if (!region) {
            showStatus('Draw a rectangle around the text first (e.g. serial number below the barcode).', true);
            return;
        }
        if (!captureCanvas || !captureCanvas._capturedImage) return;
        if (typeof window.Tesseract === 'undefined') {
            showStatus('Text recognition is not available. Please refresh the page.', true);
            return;
        }
        var w = region.w;
        var h = region.h;
        var scale = 1;
        var minTextSize = 180;
        if (w < minTextSize || h < minTextSize) {
            scale = Math.max(minTextSize / w, minTextSize / h, 1.5);
            w = Math.round(region.w * scale);
            h = Math.round(region.h * scale);
        }
        cropCanvasEl.width = w;
        cropCanvasEl.height = h;
        var ctx = cropCanvasEl.getContext('2d');
        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = 'high';
        ctx.drawImage(captureCanvas, region.x, region.y, region.w, region.h, 0, 0, w, h);
        if (extractTextBtn) extractTextBtn.disabled = true;
        statusEl.classList.remove('hidden');
        showStatus('Extracting text…', false);
        var Tesseract = window.Tesseract;
        Tesseract.createWorker('eng')
            .then(function(worker) {
                return worker.recognize(cropCanvasEl).then(function(result) {
                    return worker.terminate().then(function() { return result; });
                });
            })
            .then(function(result) {
                if (extractTextBtn) extractTextBtn.disabled = false;
                var text = (result && result.data && result.data.text) ? String(result.data.text).trim() : ((result && result.text) ? String(result.text).trim() : '');
                if (!text) {
                    showStatus('No text found in selection. Try a tighter rectangle or ensure the text is clear.', true);
                    return;
                }
                if (applyDetectedValue(text)) return;
                showStatus('Text extracted: ' + text.substring(0, 50) + (text.length > 50 ? '…' : ''), false);
            })
            .catch(function(err) {
                if (extractTextBtn) extractTextBtn.disabled = false;
                showStatus('Text extraction failed. Please try again.', true);
            });
    }

    function applyZoomToVideo() {
        if (!videoEl) return;
        var scale = Math.max(1, Math.min(3, cameraZoomLevel));
        videoEl.style.transform = 'scale(' + scale + ')';
        videoEl.style.transformOrigin = 'center center';
        if (zoomSlider) zoomSlider.value = scale;
        if (zoomValueEl) zoomValueEl.textContent = scale.toFixed(1) + '×';
    }

    function switchToCropPhase() {
        if (!videoEl || videoEl.readyState < 2) return;
        var w = videoEl.videoWidth;
        var h = videoEl.videoHeight;
        if (!w || !h) return;
        captureBtn.disabled = true;
        showStatus('Hold steady…', false);
        statusEl.classList.remove('hidden');
        setTimeout(function() {
            if (!videoEl || videoEl.readyState < 2) {
                captureBtn.disabled = false;
                statusEl.classList.add('hidden');
                return;
            }
            w = videoEl.videoWidth;
            h = videoEl.videoHeight;
            if (!w || !h) {
                captureBtn.disabled = false;
                statusEl.classList.add('hidden');
                return;
            }
            var zoom = Math.max(1, Math.min(3, cameraZoomLevel));
            var cropW = Math.round(w / zoom);
            var cropH = Math.round(h / zoom);
            var sx = Math.round((w - cropW) / 2);
            var sy = Math.round((h - cropH) / 2);
            captureCanvas = document.createElement('canvas');
            captureCanvas.width = cropW;
            captureCanvas.height = cropH;
            var ctx = captureCanvas.getContext('2d');
            try {
                ctx.drawImage(videoEl, sx, sy, cropW, cropH, 0, 0, cropW, cropH);
                sharpenCanvas(ctx, cropW, cropH);
            } catch (e) {
                captureCanvas = null;
                captureBtn.disabled = false;
                statusEl.classList.add('hidden');
                stopCamera();
                showStatus('Capture failed. Please try again.', true);
                return;
            }
            captureBtn.disabled = false;
            statusEl.classList.add('hidden');
            stopCamera();
            readerDiv.innerHTML = '';
            readerDiv.classList.add('barcode-scanner-crop-mode');
            var img = new Image();
            img.onload = function() {
                captureCanvas._capturedImage = img;
                showStatus('Scanning…', false);
                statusEl.classList.remove('hidden');
                decodeImageCanvas(captureCanvas, function(ok, value, errorMsg) {
                    statusEl.classList.add('hidden');
                    if (ok && value && applyDetectedValue(value)) {
                        return;
                    }
                    showCropPhaseUI();
                });
            };
            img.src = captureCanvas.toDataURL('image/png');
        }, 600);
    }

    function showCropPhaseUI() {
        if (!captureCanvas || !readerDiv) return;
        captureRotation = 0;
        readerDiv.innerHTML = '';
        readerDiv.appendChild(captureCanvas);
        captureCanvas.style.width = '100%';
        captureCanvas.style.maxWidth = '100%';
        captureCanvas.style.height = 'auto';
        captureCanvas.style.display = 'block';
        selection = { startX: 0, startY: 0, endX: 0, endY: 0, active: false };
        mode = 'crop';
        actionsDiv.classList.add('hidden');
        if (zoomWrap) zoomWrap.classList.add('hidden');
        cropActionsDiv.classList.remove('hidden');
        hintEl.textContent = 'Drag to select an area: use "Extract from selection" for barcode/QR, or "Extract text from selection" for serial number or other text.';
        titleEl.textContent = 'Select barcode area';

        captureCanvas.onmousedown = function(ev) {
            var p = eventToCanvasCoords(captureCanvas, ev);
            selection.startX = p.x;
            selection.startY = p.y;
            selection.endX = p.x;
            selection.endY = p.y;
            selection.active = true;
        };
        captureCanvas.onmousemove = function(ev) {
            if (!selection.active) return;
            var p = eventToCanvasCoords(captureCanvas, ev);
            selection.endX = Math.max(0, Math.min(captureCanvas.width, p.x));
            selection.endY = Math.max(0, Math.min(captureCanvas.height, p.y));
            redrawCaptureWithSelection();
        };
        captureCanvas.onmouseup = function() {
            selection.active = false;
            redrawCaptureWithSelection();
        };
        captureCanvas.onmouseleave = function() {
            selection.active = false;
        };
        redrawCaptureWithSelection();
    }

    function switchBackToCamera() {
        mode = 'camera';
        captureCanvas = null;
        cropActionsDiv.classList.add('hidden');
        readerDiv.innerHTML = '';
        readerDiv.id = 'barcodeScannerReader';
        readerDiv.classList.remove('barcode-scanner-crop-mode');
        titleEl.textContent = 'Scan barcode or QR code';
        hintEl.textContent = 'Zoom to focus on the code, then capture. Draw a rectangle around the barcode to extract.';
        if (zoomWrap) zoomWrap.classList.add('hidden');
        requestCameraAndStart();
    }

    function requestCameraAndStart() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showCameraError('Camera not supported.', 'Use a modern browser or desktop app. Use HTTPS or localhost.', !isElectronOrNativePHP(), false);
            return;
        }
        readerDiv.innerHTML = '<p class="p-4 text-gray-500">Requesting camera…</p>';
        actionsDiv.classList.add('hidden');
        statusEl.classList.add('hidden');

        navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: 'environment',
                width: { ideal: 1280, min: 640 },
                height: { ideal: 720, min: 480 }
            }
        }).catch(function() {
            return navigator.mediaDevices.getUserMedia({
                video: {
                    width: { ideal: 1280, min: 640 },
                    height: { ideal: 720, min: 480 }
                }
            });
        }).catch(function() { return navigator.mediaDevices.getUserMedia({ video: true }); })
            .then(function(stream) {
                currentStream = stream;
                readerDiv.innerHTML = '';
                videoWrapEl = document.createElement('div');
                videoWrapEl.className = 'barcode-scanner-video-wrap';
                videoEl = document.createElement('video');
                videoEl.setAttribute('playsinline', '');
                videoEl.setAttribute('autoplay', '');
                videoEl.setAttribute('muted', '');
                videoEl.srcObject = stream;
                videoWrapEl.appendChild(videoEl);
                readerDiv.appendChild(videoWrapEl);
                actionsDiv.classList.remove('hidden');
                if (zoomWrap) zoomWrap.classList.remove('hidden');
                cameraZoomLevel = parseFloat(zoomSlider ? zoomSlider.value : 1) || 1;
                applyZoomToVideo();
                videoEl.onloadedmetadata = function() {
                    videoEl.play().catch(function() {});
                    stopAutoScan();
                    autoScanIntervalId = setInterval(runAutoScan, 550);
                };
            })
            .catch(function(err) {
                var detail = (err && err.message) ? err.message : '';
                if (err && err.name === 'NotAllowedError') showCameraError('Camera permission denied.', getNotAllowedMessage(), false, true);
                else if (err && err.name === 'NotFoundError') showCameraError('No camera found.', detail, false, true);
                else showCameraError('Camera access needed.', detail, !isElectronOrNativePHP(), true);
            });
    }

    function onZoomChange() {
        cameraZoomLevel = parseFloat(zoomSlider.value) || 1;
        applyZoomToVideo();
    }
    if (zoomSlider) {
        zoomSlider.addEventListener('input', onZoomChange);
        zoomSlider.addEventListener('change', onZoomChange);
    }
    if (zoomOutBtn) {
        zoomOutBtn.addEventListener('click', function() {
            var v = parseFloat(zoomSlider.value) || 1;
            v = Math.max(1, v - 0.25);
            zoomSlider.value = v;
            cameraZoomLevel = v;
            applyZoomToVideo();
        });
    }
    if (zoomInBtn) {
        zoomInBtn.addEventListener('click', function() {
            var v = parseFloat(zoomSlider.value) || 1;
            v = Math.min(3, v + 0.25);
            zoomSlider.value = v;
            cameraZoomLevel = v;
            applyZoomToVideo();
        });
    }

    captureBtn.addEventListener('click', switchToCropPhase);
    extractBtn.addEventListener('click', extractFromSelection);
    if (extractFullBtn) extractFullBtn.addEventListener('click', extractFromFullImage);
    if (extractTextBtn) extractTextBtn.addEventListener('click', extractTextFromSelection);
    retakeBtn.addEventListener('click', switchBackToCamera);

    var rotateLeftBtn = document.getElementById('barcodeScannerRotateLeft');
    var rotateRightBtn = document.getElementById('barcodeScannerRotateRight');
    if (rotateLeftBtn) {
        rotateLeftBtn.addEventListener('click', function() {
            if (!captureCanvas || !captureCanvas._capturedImage) return;
            captureRotation = ((captureRotation - 90) + 360) % 360;
            selection = { startX: 0, startY: 0, endX: 0, endY: 0, active: false };
            redrawCaptureWithSelection();
        });
    }
    if (rotateRightBtn) {
        rotateRightBtn.addEventListener('click', function() {
            if (!captureCanvas || !captureCanvas._capturedImage) return;
            captureRotation = (captureRotation + 90) % 360;
            selection = { startX: 0, startY: 0, endX: 0, endY: 0, active: false };
            redrawCaptureWithSelection();
        });
    }

    function scanFromFile(file) {
        if (!file || !file.type.match(/^image\//)) return;
        stopCamera();
        var img = new Image();
        var url = (window.URL || window.webkitURL).createObjectURL(file);
        img.onload = function() {
            (window.URL || window.webkitURL).revokeObjectURL(url);
            var w = img.naturalWidth || img.width;
            var h = img.naturalHeight || img.height;
            if (!w || !h) {
                showStatus('Could not load image dimensions.', true);
                return;
            }
            statusEl.classList.add('hidden');
            captureCanvas = document.createElement('canvas');
            captureCanvas.width = w;
            captureCanvas.height = h;
            var ctx = captureCanvas.getContext('2d');
            ctx.drawImage(img, 0, 0, w, h);
            captureCanvas._capturedImage = img;
            readerDiv.innerHTML = '';
            readerDiv.classList.add('barcode-scanner-crop-mode');
            showCropPhaseUI();
        };
        img.onerror = function() {
            (window.URL || window.webkitURL).revokeObjectURL(url);
            showStatus('Could not load image.', true);
        };
        img.src = url;
    }

    if (uploadBtn && fileInput) {
        uploadBtn.addEventListener('click', function() { fileInput.click(); });
        fileInput.addEventListener('change', function() {
            var file = fileInput.files && fileInput.files[0];
            if (file) {
                scanFromFile(file);
                fileInput.value = '';
            }
        });
    }

    window.openBarcodeScanner = function(callback) {
        onScanCallback = typeof callback === 'function' ? callback : fillNextEmptySerialInput;
        resetScannerState();
        modal.classList.remove('hidden');
        scannerOpenedAt = Date.now();
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showCameraError('Camera not supported.', 'Use a modern browser or desktop app.', !isElectronOrNativePHP(), false);
            return;
        }
        if (!isSecureContext() && !isElectronOrNativePHP()) {
            showCameraError('Camera requires a secure context.', 'Open via HTTPS or localhost.', true, false);
            return;
        }
        requestCameraAndStart();
    };

    closeBtn.addEventListener('click', closeModal);
    backdrop.addEventListener('click', function(ev) {
        if (Date.now() - scannerOpenedAt < 400) return;
        closeModal();
    });
})();
</script>
