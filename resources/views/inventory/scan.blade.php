@extends('layouts.app')

@section('title', 'Inventory – Barcode Scanner')

@push('styles')
<style>
/* ── Scanner Page Styles ── */
.scan-hero {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    border-radius: 20px;
    padding: 2rem;
    color: white;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
}
.scan-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 200px; height: 200px;
    background: rgba(255,255,255,0.04);
    border-radius: 50%;
}
.scan-hero::after {
    content: '';
    position: absolute;
    bottom: -40px; left: 50%;
    width: 300px; height: 300px;
    background: rgba(255,255,255,0.02);
    border-radius: 50%;
}

/* Camera viewport */
#scanner-container {
    position: relative;
    width: 100%;
    max-width: 520px;
    margin: 0 auto;
    border-radius: 16px;
    overflow: hidden;
    background: #000;
    aspect-ratio: 4/3;
    border: 3px solid rgba(255,255,255,0.15);
    box-shadow: 0 20px 60px rgba(0,0,0,0.5);
}
#scanner-video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.scan-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: none;
}
.scan-frame {
    width: 65%;
    height: 45%;
    position: relative;
}
.scan-frame::before,
.scan-frame::after,
.scan-frame span::before,
.scan-frame span::after {
    content: '';
    position: absolute;
    width: 28px;
    height: 28px;
    border-color: #f59e0b;
    border-style: solid;
}
.scan-frame::before  { top: 0;    left: 0;    border-width: 3px 0 0 3px; border-radius: 6px 0 0 0; }
.scan-frame::after   { top: 0;    right: 0;   border-width: 3px 3px 0 0; border-radius: 0 6px 0 0; }
.scan-frame span::before { bottom: 0; left: 0;  border-width: 0 0 3px 3px; border-radius: 0 0 0 6px; }
.scan-frame span::after  { bottom: 0; right: 0; border-width: 0 3px 3px 0; border-radius: 0 0 6px 0; }

/* Scan line animation */
.scan-line {
    position: absolute;
    left: 0; right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, #f59e0b, transparent);
    animation: scanMove 2.2s ease-in-out infinite;
    box-shadow: 0 0 8px #f59e0b;
}
@keyframes scanMove {
    0%   { top: 15%; }
    50%  { top: 80%; }
    100% { top: 15%; }
}

/* Scanner placeholder */
.scanner-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    height: 100%;
    color: #6b7280;
    padding: 2rem;
    text-align: center;
}

/* Result cards */
.scanned-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 10px;
    margin-bottom: 8px;
    animation: slideIn 0.3s ease;
}
.scanned-item .serial-badge {
    font-family: 'Courier New', monospace;
    font-weight: 700;
    font-size: 0.95rem;
    color: #166534;
    background: #dcfce7;
    padding: 4px 10px;
    border-radius: 6px;
    letter-spacing: 1px;
}
.scanned-item.duplicate {
    background: #fef2f2;
    border-color: #fecaca;
}
.scanned-item.duplicate .serial-badge {
    color: #991b1b;
    background: #fee2e2;
}
@keyframes slideIn {
    from { opacity: 0; transform: translateX(-10px); }
    to   { opacity: 1; transform: translateX(0); }
}

/* Pulse success ring */
.pulse-success {
    animation: pulse 0.5s ease;
}
@keyframes pulse {
    0%   { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
    70%  { box-shadow: 0 0 0 16px rgba(34, 197, 94, 0); }
    100% { box-shadow: none; }
}

/* Status bar */
.status-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 500;
}
.status-bar.scanning { background: rgba(245,158,11,0.1); color: #d97706; }
.status-bar.ready    { background: rgba(34,197,94,0.1);  color: #16a34a; }
.status-bar.inactive { background: rgba(107,114,128,0.1); color: #6b7280; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Hero Header --}}
    <div class="scan-hero">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h4 class="fw-bold mb-1">
                    <i class="bi bi-upc-scan me-2" style="color:#f59e0b"></i>
                    Barcode Scanner — Inventory Registration
                </h4>
                <p class="mb-0 opacity-75">Point the laptop camera at a barcode — the serial number will be registered automatically.</p>
            </div>
            <a href="{{ route('inventory.index') }}" class="btn btn-light btn-sm">
                <i class="bi bi-list-ul me-1"></i> Inventory List
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left: Camera + Controls --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius:16px">
                <div class="card-body p-4">

                    {{-- Item selector --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-box-seam me-1 text-primary"></i>
                            Walkie Model (optional)
                        </label>
                        <select id="item_id" class="form-select">
                            <option value="">— No model selected —</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}{{ $item->type ? ' ('.$item->type.')' : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Camera viewer --}}
                    <div id="scanner-container">
                        <div class="scanner-placeholder" id="scanner-placeholder">
                            <i class="bi bi-camera-video" style="font-size:3rem; color:#9ca3af"></i>
                            <div>
                                <p class="fw-semibold mb-1" style="color:#374151">Camera Off</p>
                                <p class="small text-muted mb-0">Press the Start button below</p>
                            </div>
                        </div>
                        <video id="scanner-video" style="display:none" playsinline></video>
                        <div class="scan-overlay" id="scan-overlay" style="display:none">
                            <div class="scan-frame">
                                <span></span>
                                <div class="scan-line"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Status + Controls --}}
                    <div class="d-flex align-items-center justify-content-between mt-3 flex-wrap gap-2">
                        <div class="status-bar inactive" id="status-bar">
                            <span class="status-dot" style="width:8px;height:8px;border-radius:50%;background:currentColor;display:inline-block"></span>
                            <span id="status-text">Camera is off</span>
                        </div>
                        <div class="d-flex gap-2">
                            <button id="start-btn" class="btn btn-success btn-sm px-3">
                                <i class="bi bi-camera-video-fill me-1"></i> Start
                            </button>
                            <button id="stop-btn" class="btn btn-danger btn-sm px-3" style="display:none">
                                <i class="bi bi-stop-fill me-1"></i> Stop
                            </button>
                        </div>
                    </div>

                    {{-- Manual entry fallback --}}
                    <hr class="my-3">
                    <div>
                        <label class="form-label fw-semibold small text-muted">
                            <i class="bi bi-keyboard me-1"></i> Manual Entry
                        </label>
                        <div class="input-group">
                            <input type="text"
                                   id="manual-serial"
                                   class="form-control font-monospace text-uppercase"
                                   placeholder="Type or paste serial number..."
                                   style="letter-spacing:1px">
                            <button id="manual-save-btn" class="btn btn-warning px-3">
                                <i class="bi bi-plus-circle me-1"></i> Add
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Scanned List --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius:16px">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-check2-all me-2 text-success"></i>
                            Scanned Items
                        </h6>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary rounded-pill" id="scan-count">0</span>
                            <button id="clear-btn" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:.75rem">Clear</button>
                        </div>
                    </div>

                    <div id="scanned-list" style="max-height: 420px; overflow-y:auto;">
                        <div class="text-center text-muted py-5" id="empty-state">
                            <i class="bi bi-upc" style="font-size:3rem; opacity:.3"></i>
                            <p class="mt-2 small">No items scanned yet</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary row --}}
    <div class="row g-3 mt-1">
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm text-center p-3" style="border-radius:12px">
                <div class="fw-bold fs-4 text-success" id="success-count">0</div>
                <div class="small text-muted">Saved</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm text-center p-3" style="border-radius:12px">
                <div class="fw-bold fs-4 text-danger" id="dup-count">0</div>
                <div class="small text-muted">Duplicates</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm text-center p-3" style="border-radius:12px">
                <div class="fw-bold fs-4 text-primary" id="total-count">0</div>
                <div class="small text-muted">Total Attempts</div>
            </div>
        </div>
    </div>
</div>

{{-- Audio beep (success) --}}
<audio id="beep-sound" preload="auto">
    <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2YiCSuZ4+rIgC0LLpXk9dqxdyoVMJHl+OjKhzQPN5jr+PPlkD4dRqvz/v7zoFcpU7v9//3pnFsnX8H+//7toGAqWcT9//7zoWAqWcP+//3toGErX8f9//3spWUyXsf9//3rp2g2Y87+/v3poGQwWcz+//3ppnAwZtH+/f3sp2g5YNj///3lnFsiUMH8//3vnmo1at3///3qo205adz//v3qo285b9z///boonc8cOL///vmon0/ceL///vmonw/cuL///vmonw/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+//3non0/ct/+@endsection

@push('scripts')
{{-- QuaggaJS for barcode detection --}}
<script src="https://cdn.jsdelivr.net/npm/@ericblade/quagga2@1.2.6/dist/quagga.min.js"></script>
<script>
(function () {
    'use strict';

    // ── State ──
    let successCount = 0;
    let dupCount = 0;
    let totalCount = 0;
    let lastDetected = '';
    let lastDetectedTime = 0;
    let isScanning = false;
    let detectionBuffer   = [];   // rolling window of last N detections
    const REQUIRED_HITS   = 2;    // same code must appear this many times to confirm
    const BUFFER_SIZE     = 5;
    const MIN_CONFIDENCE  = 0.55; // 55% confidence minimum

    const csrfToken  = document.querySelector('meta[name="csrf-token"]').content;
    const storeUrl   = '{{ route("inventory.storeScan") }}';

    // ── DOM refs ──
    const startBtn       = document.getElementById('start-btn');
    const stopBtn        = document.getElementById('stop-btn');
    const clearBtn       = document.getElementById('clear-btn');
    const statusBar      = document.getElementById('status-bar');
    const statusText     = document.getElementById('status-text');
    const scanList       = document.getElementById('scanned-list');
    const emptyState     = document.getElementById('empty-state');
    const scanCount      = document.getElementById('scan-count');
    const successCountEl = document.getElementById('success-count');
    const dupCountEl     = document.getElementById('dup-count');
    const totalCountEl   = document.getElementById('total-count');
    const manualInput    = document.getElementById('manual-serial');
    const manualSaveBtn  = document.getElementById('manual-save-btn');
    const placeholder    = document.getElementById('scanner-placeholder');
    const scanOverlay    = document.getElementById('scan-overlay');

    // ── Beep ──
    function playBeep(success = true) {
        try {
            const ctx  = new (window.AudioContext || window.webkitAudioContext)();
            const osc  = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.type = 'sine';
            osc.frequency.value = success ? 1000 : 400;
            gain.gain.setValueAtTime(0.4, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.25);
            osc.start(ctx.currentTime);
            osc.stop(ctx.currentTime + 0.25);
        } catch(e) {}
    }

    function setStatus(state, text) {
        statusBar.className = 'status-bar ' + state;
        statusText.textContent = text;
    }

    // ── Confidence calculator ──
    // QuaggaJS decodedCodes.error is per-character error (0=perfect, 1=terrible)
    function calcConfidence(result) {
        const codes = result.codeResult?.decodedCodes || [];
        const valid = codes.filter(c => typeof c.error === 'number');
        if (!valid.length) return 1;
        const avgError = valid.reduce((s, c) => s + c.error, 0) / valid.length;
        return 1 - avgError;
    }

    // ── Start Scanner ──
    startBtn.addEventListener('click', startScanner);

    function startScanner() {
        if (isScanning) return;
        setStatus('scanning', 'Starting camera...');

        Quagga.init({
            inputStream: {
                name: 'Live',
                type: 'LiveStream',
                target: document.getElementById('scanner-container'),
                constraints: {
                    width:  { ideal: 1280 },
                    height: { ideal: 720 },
                    facingMode: 'environment',
                },
            },
            locator: {
                patchSize: 'large',
                halfSample: false,   // false = higher decode quality
            },
            numOfWorkers: Math.min(navigator.hardwareConcurrency || 2, 4),
            frequency: 20,
            decoder: {
                readers: [
                    { format: 'code_128_reader', config: {} },
                    { format: 'code_39_reader',  config: {} },
                    { format: 'code_93_reader',  config: {} },
                    { format: 'ean_reader',       config: {} },
                    { format: 'ean_8_reader',     config: {} },
                    { format: 'upc_reader',       config: {} },
                ],
                multiple: false,
            },
            locate: true,
        }, function (err) {
            if (err) {
                console.error(err);
                setStatus('inactive', 'Camera error: ' + err.message);
                return;
            }
            Quagga.start();
            isScanning = true;

            const liveVideo = document.querySelector('#scanner-container video');
            if (liveVideo) liveVideo.style.cssText = 'width:100%;height:100%;object-fit:cover;position:absolute;top:0;left:0;';
            const canvas = document.querySelector('#scanner-container canvas.drawingBuffer');
            if (canvas) canvas.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;';

            placeholder.style.display = 'none';
            scanOverlay.style.display = 'flex';
            startBtn.style.display    = 'none';
            stopBtn.style.display     = 'inline-block';
            setStatus('scanning', 'Scanning — hold the barcode inside the frame');
        });

        Quagga.onDetected(handleDetected);
        Quagga.onProcessed(drawResult);
    }

    // ── Draw detection box on canvas ──
    function drawResult(result) {
        const ctx    = Quagga.canvas.ctx.overlay;
        const canvas = Quagga.canvas.dom.overlay;
        if (!ctx || !canvas) return;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        if (result?.box) {
            Quagga.ImageDebug.drawPath(result.box, {x:0,y:1}, ctx, {color:'#f59e0b', lineWidth:3});
        }
    }

    // ── Stop Scanner ──
    stopBtn.addEventListener('click', stopScanner);
    function stopScanner() {
        if (!isScanning) return;
        Quagga.stop();
        isScanning = false;
        detectionBuffer = [];
        placeholder.style.display = 'flex';
        scanOverlay.style.display = 'none';
        startBtn.style.display    = 'inline-block';
        stopBtn.style.display     = 'none';
        setStatus('inactive', 'Camera is off');
    }

    // ── Detection handler with confidence + 3x buffer ──
    function handleDetected(result) {
        const code       = result.codeResult?.code?.trim();
        const confidence = calcConfidence(result);

        if (!code || code.length < 3) return;

        // Skip low confidence reads
        if (confidence < MIN_CONFIDENCE) {
            console.log(`[SKIP] conf=${(confidence*100).toFixed(0)}% code="${code}"`);
            return;
        }

        // Push into rolling buffer
        detectionBuffer.push(code);
        if (detectionBuffer.length > BUFFER_SIZE) detectionBuffer.shift();

        // Count how many recent detections match this code
        const hits = detectionBuffer.filter(c => c === code).length;
        console.log(`[DETECT] "${code}" | conf:${(confidence*100).toFixed(0)}% | hits:${hits}/${REQUIRED_HITS}`);

        if (hits < REQUIRED_HITS) return;

        // Debounce: same code within 3s = ignore
        const now = Date.now();
        if (code === lastDetected && (now - lastDetectedTime) < 3000) return;

        lastDetected     = code;
        lastDetectedTime = now;
        detectionBuffer  = []; // reset buffer after confirmed detection

        showConfirmModal(code, confidence);
    }

    // ── Confirm modal before saving ──
    function showConfirmModal(code, confidence) {
        playBeep(true);
        setStatus('ready', `Detected: ${code}`);

        Swal.fire({
            title: '🎯 Barcode Detected!',
            html: `
                <div style="margin:8px 0 16px">
                    <div style="font-family:'Courier New',monospace;font-size:1.4rem;font-weight:700;
                                letter-spacing:3px;color:#166534;background:#dcfce7;
                                padding:12px 20px;border-radius:10px;display:inline-block;
                                word-break:break-all;max-width:280px">
                        ${code}
                    </div>
                    <div style="margin-top:10px;font-size:.82rem;color:#6b7280">
                        <i class="bi bi-shield-check"></i>
                        Confidence: <strong>${(confidence*100).toFixed(0)}%</strong> &nbsp;|&nbsp;
                        Characters: <strong>${code.length}</strong>
                    </div>
                </div>
                <p style="color:#374151;font-size:.9rem;margin:0">
                    Is this serial number correct? Save it?
                </p>
            `,
            icon: 'success',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-check-circle"></i> Yes, Save',
            cancelButtonText:  '<i class="bi bi-arrow-repeat"></i> Scan Again',
            confirmButtonColor: '#16a34a',
            cancelButtonColor:  '#6b7280',
            allowOutsideClick: false,
        }).then(result => {
            if (result.isConfirmed) {
                saveSerial(code);
            } else {
                lastDetected = ''; // allow re-scan immediately
                setStatus('scanning', 'Scanning — hold the barcode inside the frame');
            }
        });
    }

    // ── Manual entry ──
    manualSaveBtn.addEventListener('click', () => {
        const val = manualInput.value.trim().toUpperCase();
        if (!val) { manualInput.focus(); return; }
        saveSerial(val, () => { manualInput.value = ''; manualInput.focus(); });
    });
    manualInput.addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); manualSaveBtn.click(); }
    });

    // ── Save to DB ──
    function saveSerial(serial, onDone) {
        totalCount++;
        totalCountEl.textContent = totalCount;
        scanCount.textContent    = totalCount;

        fetch(storeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                serial_number: serial,
                item_id: document.getElementById('item_id').value || null,
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                successCount++;
                successCountEl.textContent = successCount;
                playBeep(true);
                addToList(serial, data.item_name && data.item_name !== '—' ? data.item_name : 'Saved successfully', false);
            } else {
                const rawMsg = data.errors?.serial_number?.[0] || data.message || 'Error';
                const isDup  = rawMsg.includes('already been taken') || rawMsg.includes('unique') || rawMsg.includes('पहले से');
                const msg    = isDup ? 'Serial number already exists in inventory' : rawMsg;
                dupCount++;
                dupCountEl.textContent = dupCount;
                playBeep(false);
                addToList(serial, msg, true);
            }
            setStatus('scanning', 'Scanning — hold the barcode inside the frame');
            if (onDone) onDone();
        })
        .catch(err => {
            console.error(err);
            addToList(serial, 'Network error — check your connection', true);
            if (onDone) onDone();
        });
    }

    // ── Add row to list ──
    function addToList(serial, info, isErr) {
        emptyState.style.display = 'none';
        const div = document.createElement('div');
        div.className = 'scanned-item' + (isErr ? ' duplicate' : '');
        div.innerHTML = `
            <div class="${isErr ? 'text-danger' : 'text-success'}" style="font-size:1.2rem">
                <i class="bi bi-${isErr ? 'exclamation-circle' : 'check-circle-fill'}"></i>
            </div>
            <div class="flex-grow-1">
                <div class="serial-badge">${serial}</div>
                <div class="small mt-1 ${isErr ? 'text-danger' : 'text-muted'}">${info}</div>
            </div>
            <div class="small text-muted">${new Date().toLocaleTimeString('en-IN',{hour:'2-digit',minute:'2-digit'})}</div>
        `;
        scanList.insertBefore(div, scanList.firstChild);
    }

    // ── Clear ──
    clearBtn.addEventListener('click', () => {
        scanList.innerHTML = '';
        scanList.appendChild(emptyState);
        emptyState.style.display = 'block';
        successCount = dupCount = totalCount = 0;
        [successCountEl, dupCountEl, totalCountEl, scanCount].forEach(el => el.textContent = '0');
    });

})();
</script>
@endpush
