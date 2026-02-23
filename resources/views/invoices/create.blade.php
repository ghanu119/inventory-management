@extends('layouts.app')

@section('title', 'Create Invoice')

@section('content')
<div class="space-y-6">
    <h1 class="text-3xl font-bold text-gray-900">Create Invoice</h1>

    <form action="{{ route('invoices.store') }}" method="POST" class="bg-white shadow rounded-lg p-6" id="invoiceForm">
        @csrf

        <div class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="invoice_date" class="block text-sm font-semibold text-gray-700 mb-2">Invoice Date <span class="text-red-500">*</span></label>
                    <input type="date" name="invoice_date" id="invoice_date" value="{{ old('invoice_date', now()->format('Y-m-d')) }}" required>
                </div>

                <div>
                    <label for="payment_mode" class="block text-sm font-semibold text-gray-700 mb-2">Payment Mode <span class="text-red-500">*</span></label>
                        <select name="payment_mode" id="payment_mode" class="select2" required>
                        <option value="Cash">Cash</option>
                        <option value="UPI">UPI</option>
                        <option value="Card">Card</option>
                        <option value="Bank">Bank</option>
                    </select>
                </div>
            </div>

            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Customer Details</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="customer_name" class="block text-sm font-semibold text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" required placeholder="Enter customer name">
                    </div>
                    <div>
                        <label for="customer_phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone</label>
                        <input type="text" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}" placeholder="Enter phone number">
                    </div>
                    <div>
                        <label for="customer_email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email') }}" placeholder="Enter email address">
                    </div>
                    <div>
                        <label for="customer_gst_number" class="block text-sm font-semibold text-gray-700 mb-2">GST Number</label>
                        <input type="text" name="customer_gst_number" id="customer_gst_number" value="{{ old('customer_gst_number') }}" placeholder="Enter GST number">
                    </div>
                </div>
                <div class="mt-4">
                    <label for="customer_address" class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                    <textarea name="customer_address" id="customer_address" rows="2" placeholder="Enter customer address">{{ old('customer_address') }}</textarea>
                </div>
            </div>

            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Items</h3>
                <div id="itemsContainer">
                    <div class="item-row border-b pb-4 mb-4">
                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-5">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Product <span class="text-red-500">*</span></label>
                                <select name="items[0][product_id]" class="product-select select2" required>
                                    <option value="">Select Product</option>
                                </select>
                                <p class="hsn-display mt-1 text-xs text-gray-500">HSN: Not set</p>
                            </div>
                            <div class="col-span-1">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Quantity <span class="text-red-500">*</span></label>
                                <input type="number" name="items[0][quantity]" class="quantity-input" min="1" required value="{{ old('items.0.quantity', 1) }}" placeholder="1">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Serial No</label>
                                <select class="serial-dropdown select2 w-full px-2 py-1.5 border rounded text-sm mb-1" style="display:none;">
                                    <option value="">Select or type below</option>
                                    <option value="__manual__">Manual entry</option>
                                </select>
                                <div class="flex items-center gap-1">
                                    <input type="text" name="items[0][serial_no]" class="serial-input flex-1 min-w-0 px-2 py-1.5 border rounded text-sm" placeholder="Enter or select above">
                                    <button type="button" class="invoice-item-scan-btn inline-flex items-center p-2 border border-gray-300 rounded-md text-gray-600 hover:bg-gray-100 flex-shrink-0" data-no-loader title="Scan barcode for this item"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg></button>
                                </div>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Price</label>
                                <input type="text" class="price-display bg-gray-100 cursor-not-allowed" readonly placeholder="0.00">
                            </div>
                            <div class="col-span-1">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">GST Rate</label>
                                <input type="text" class="gst-display bg-gray-100 cursor-not-allowed" readonly placeholder="0%">
                            </div>
                            <div class="col-span-1">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">&nbsp;</label>
                                <button type="button" class="remove-item w-full px-3 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors" data-no-loader>Remove</button>
                            </div>
                        </div>
                        <div class="grid grid-cols-12 gap-4 mt-2">
                            <div class="col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Warranty (yr)</label>
                                <input type="number" step="0.01" name="items[0][warranty_years]" class="warranty-input w-full px-2 py-1.5 border rounded text-sm" min="0" placeholder="0">
                            </div>
                            <div class="col-span-10">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Custom short text</label>
                                <textarea name="items[0][custom_short_text]" class="custom-text-input w-full px-2 py-1.5 border rounded text-sm" rows="2" placeholder="Optional line text (prefilled from product)"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" id="addItem" class="px-4 py-2.5 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium" data-no-loader>Add Item</button>
            </div>

            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Discount</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label for="discount_type" class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
                        <select name="discount_type" id="discount_type" class="select2">
                            <option value="">None</option>
                            <option value="flat">Flat</option>
                            <option value="percentage">Percentage</option>
                        </select>
                    </div>
                    <div id="discount_amount_wrapper">
                        <label for="discount_amount" class="block text-sm font-semibold text-gray-700 mb-2">Amount</label>
                        <input type="number" step="0.01" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', 0) }}" min="0" placeholder="0.00">
                    </div>
                </div>
            </div>

            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Invoice Summary (Preview)</h3>
                <div class="max-w-sm space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal (Taxable):</span>
                        <span class="font-medium" id="summarySubtotal">₹0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total GST:</span>
                        <span class="font-medium" id="summaryTotalGst">₹0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Discount:</span>
                        <span class="font-medium" id="summaryDiscount">₹0.00</span>
                    </div>
                    <div class="flex justify-between border-t pt-2 mt-1">
                        <span class="text-base font-bold">Grand Total:</span>
                        <span class="text-base font-bold" id="summaryGrandTotal">₹0.00</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Values are calculated from selected products, GST (inclusive/exclusive), and discount. Final numbers are stored on save.
                    </p>
                </div>
            </div>

            <div>
                <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                <textarea name="notes" id="notes" rows="3" placeholder="Enter any additional notes">{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('invoices.index') }}" class="px-4 py-2 border border-gray-300 rounded-md">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Create Invoice</button>
            </div>
        </div>
    </form>
</div>

@include('components.barcode-scanner')

<script>
let itemIndex = 1;
const products = @json($products);

function formatMoney(amount) {
    return '₹' + amount.toFixed(2);
}

function recalcInvoiceSummary() {
    const rows = document.querySelectorAll('#itemsContainer .item-row');
    let subtotal = 0;
    let totalGst = 0;

    rows.forEach(row => {
        const productSelect = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity-input');

        if (!productSelect || !quantityInput) {
            return;
        }

        const option = productSelect.options[productSelect.selectedIndex];
        const quantity = parseFloat(quantityInput.value || '0');

        if (!option || !option.value || quantity <= 0) {
            return;
        }

        const price = parseFloat(option.dataset.price || '0');
        const gstRate = parseFloat(option.dataset.gst || '0');
        const isGstIncluded = parseInt(option.dataset.gstIncluded || option.dataset.gstIncluded === '0' ? option.dataset.gstIncluded : option.dataset['gst-included']) === 1
            || option.getAttribute('data-gst-included') === '1';

        let lineTaxable = 0;
        let lineGst = 0;
        let lineTotal = 0;

        if (isNaN(price) || isNaN(gstRate)) {
            return;
        }

        if (isGstIncluded) {
            // Price already includes GST
            lineTotal = price * quantity;

            if (gstRate > 0) {
                lineTaxable = lineTotal / (1 + (gstRate / 100));
                lineGst = lineTotal - lineTaxable;
            } else {
                lineTaxable = lineTotal;
                lineGst = 0;
            }
        } else {
            // Price is without GST
            lineTaxable = price * quantity;
            lineGst = (lineTaxable * gstRate) / 100;
            lineTotal = lineTaxable + lineGst;
        }

        subtotal += lineTaxable;
        totalGst += lineGst;
    });

    // Discount
    const discountType = document.getElementById('discount_type').value;
    const discountInput = parseFloat(document.getElementById('discount_amount').value || '0');
    let discountAmount = 0;

    if (discountType === 'percentage' && discountInput > 0) {
        discountAmount = (subtotal * discountInput) / 100;
    } else if (discountType === 'flat' && discountInput > 0) {
        discountAmount = discountInput;
    }

    const grandTotal = subtotal + totalGst - discountAmount;

    document.getElementById('summarySubtotal').textContent = formatMoney(subtotal || 0);
    document.getElementById('summaryTotalGst').textContent = formatMoney(totalGst || 0);
    document.getElementById('summaryDiscount').textContent = formatMoney(discountAmount || 0);
    document.getElementById('summaryGrandTotal').textContent = formatMoney(grandTotal > 0 ? grandTotal : 0);
}

function populateProductSelect(selectEl) {
    if (!Array.isArray(products)) {
        return;
    }

    // Clear existing options
    while (selectEl.firstChild) {
        selectEl.removeChild(selectEl.firstChild);
    }

    // Placeholder
    const placeholder = document.createElement('option');
    placeholder.value = '';
    placeholder.textContent = 'Select Product';
    selectEl.appendChild(placeholder);

    products.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.id;
        const price = parseFloat(p.price || 0);
        const gstIncluded = !!p.is_gst_included;
        const stock = p.stock_quantity ?? 0;
        const gstRate = p.gst_rate ?? 0;
        const hsnCode = p.hsn_code || '';
        const warranty = p.warranty_years != null ? p.warranty_years : '';
        const customText = p.custom_short_text || '';

        opt.textContent = p.name + ' (₹' + price.toFixed(2) + ' | HSN: ' + (hsnCode || '-') + ' | ' + (gstIncluded ? 'Price incl. GST' : 'Price excl. GST') + ' | Stock: ' + stock + ')';
        opt.dataset.price = price;
        opt.dataset.gst = gstRate;
        opt.dataset.stock = stock;
        opt.dataset.hsn = hsnCode;
        opt.dataset.warranty = warranty;
        opt.dataset.customText = customText;
        opt.dataset.gstIncluded = gstIncluded ? 1 : 0;
        opt.setAttribute('data-gst-included', gstIncluded ? '1' : '0');

        selectEl.appendChild(opt);
    });
}

function syncSerialDropdownToInput(selectEl) {
    const row = selectEl.closest('.item-row');
    const serialInput = row && row.querySelector('.serial-input');
    if (serialInput) {
        const val = selectEl.value;
        serialInput.value = (val && val !== '__manual__') ? val : '';
    }
}

function initSelect2InElement(element) {
    if (window.$ && $.fn.select2) {
        // Populate product dropdowns from JS variable
        $(element).find('.product-select').each(function () {
            populateProductSelect(this);
        });

        $(element).find('.select2').each(function () {
            // Avoid re-initializing if already has Select2
            if (!$(this).hasClass('select2-hidden-accessible')) {
                if ($(this).hasClass('serial-dropdown')) {
                    $(this).select2({ placeholder: 'Select or type below', allowClear: true });
                    $(this).off('select2:select select2:clear').on('select2:select', function (e) {
                        const row = this.closest('.item-row');
                        const serialInput = row && row.querySelector('.serial-input');
                        if (serialInput) {
                            const val = e.params.data.id;
                            serialInput.value = (val && val !== '__manual__') ? val : '';
                        }
                    }).on('select2:clear', function () {
                        syncSerialDropdownToInput(this);
                    });
                } else {
                    $(this).select2();
                }
                // Setup change handler for product selects
                if ($(this).hasClass('product-select')) {
                    setupProductSelectChange(this);
                }
            }
        });
    }
}

document.getElementById('addItem').addEventListener('click', function() {
    const container = document.getElementById('itemsContainer');
    const templateRow = container.querySelector('.item-row');
    const newRow = templateRow.cloneNode(true);

    // Clean up any Select2 artefacts from cloned row
    newRow.querySelectorAll('.select2-container').forEach(el => el.remove());

    newRow.querySelectorAll('input, select').forEach(input => {
        if (input.name) {
            input.name = input.name.replace(/\[0\]/, `[${itemIndex}]`);
        }
        if (input.classList.contains('product-select')) {
            input.value = '';
        }
        if (input.classList.contains('quantity-input')) {
            input.value = '1';
        }
        if (input.classList.contains('serial-input')) {
            input.value = '';
        }
        if (input.classList.contains('warranty-input')) {
            input.value = '';
        }
        if (input.classList.contains('custom-text-input')) {
            input.value = '';
        }
        if (input.classList.contains('price-display') || input.classList.contains('gst-display')) {
            input.value = '';
        }
    });
    const serialDropdown = newRow.querySelector('.serial-dropdown');
    if (serialDropdown) {
        serialDropdown.innerHTML = '<option value="">Select or type below</option><option value="__manual__">Manual entry</option>';
        serialDropdown.style.display = 'none';
        serialDropdown.classList.remove('select2-hidden-accessible');
        serialDropdown.removeAttribute('data-select2-id');
    }
    const hsnDisplay = newRow.querySelector('.hsn-display');
    if (hsnDisplay) {
        hsnDisplay.textContent = 'HSN: Not set';
    }

    // Reset cloned product select so Select2 can be re-initialized cleanly
    const clonedProductSelect = newRow.querySelector('.product-select');
    if (clonedProductSelect) {
        clonedProductSelect.classList.remove('select2-hidden-accessible');
        clonedProductSelect.removeAttribute('data-select2-id');
        clonedProductSelect.style.display = '';
    }

    container.appendChild(newRow);
    initSelect2InElement(newRow);
    itemIndex++;
    recalcInvoiceSummary();
});

function reindexItemRows() {
    const container = document.getElementById('itemsContainer');
    const rows = container.querySelectorAll('.item-row');
    rows.forEach(function(row, index) {
        row.querySelectorAll('input, select, textarea').forEach(function(input) {
            if (input.name && input.name.indexOf('items[') === 0) {
                input.name = input.name.replace(/^items\[\d+\]/, 'items[' + index + ']');
            }
        });
    });
    itemIndex = rows.length;
}

document.getElementById('itemsContainer').addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-item')) {
        const row = e.target.closest('.item-row');
        if (row && document.querySelectorAll('.item-row').length > 1) {
            // Destroy Select2 instance if it exists
            const productSelect = row.querySelector('.product-select');
            if (productSelect && window.$ && $.fn.select2) {
                $(productSelect).select2('destroy');
            }
            // Remove the row
            row.remove();
            // Reindex so submitted array is always items[0], items[1], ... (no gaps)
            reindexItemRows();
            // Recalculate totals after removal
            recalcInvoiceSummary();
        }
    }
});

const availableSerialsBaseUrl = "{{ url('products') }}";

// Handle product select change using jQuery/Select2 events
function setupProductSelectChange(selectEl) {
    if (window.$ && $.fn.select2) {
        $(selectEl).on('select2:select', function(e) {
            const select = this;
            const selectedValue = $(this).val();
            const option = select.querySelector(`option[value="${selectedValue}"]`);
            const row = select.closest('.item-row');

            if (option && row) {
                const price = parseFloat(option.dataset.price || 0);
                const gst = parseFloat(option.dataset.gst || 0);
                const stock = parseFloat(option.dataset.stock || 0);
                const hsn = option.dataset.hsn || '-';
                const warranty = option.dataset.warranty ?? '';
                const customText = option.dataset.customText ?? '';

                row.querySelector('.price-display').value = '₹' + price.toFixed(2);
                row.querySelector('.gst-display').value = gst + '%';
                const hsnDisplay = row.querySelector('.hsn-display');
                if (hsnDisplay) {
                    hsnDisplay.textContent = 'HSN: ' + hsn;
                }
                const warrantyInput = row.querySelector('.warranty-input');
                if (warrantyInput) {
                    warrantyInput.value = warranty;
                }
                const customTextInput = row.querySelector('.custom-text-input');
                if (customTextInput) {
                    customTextInput.value = customText;
                }
                const quantityInput = row.querySelector('.quantity-input');
                if (quantityInput) {
                    quantityInput.setAttribute('max', stock);
                }
                const serialInput = row.querySelector('.serial-input');
                const serialDropdown = row.querySelector('.serial-dropdown');
                if (serialInput) serialInput.value = '';
                if (serialDropdown) {
                    if (window.$ && $.fn.select2 && $(serialDropdown).hasClass('select2-hidden-accessible')) {
                        $(serialDropdown).select2('destroy');
                    }
                    serialDropdown.innerHTML = '<option value="">Select or type below</option><option value="__manual__">Manual entry</option>';
                    serialDropdown.style.display = 'none';
                    const productId = selectedValue;
                    if (productId) {
                        fetch(availableSerialsBaseUrl + '/' + productId + '/available-serials', {
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                        }).then(r => r.json()).then(serials => {
                            if (serials && serials.length > 0) {
                                serials.forEach(s => {
                                    const o = document.createElement('option');
                                    o.value = s;
                                    o.textContent = s;
                                    serialDropdown.appendChild(o);
                                });
                                serialDropdown.style.display = 'block';
                                if (window.$ && $.fn.select2) {
                                    $(serialDropdown).select2({ placeholder: 'Select or type below', allowClear: true });
                                    $(serialDropdown).off('select2:select select2:clear').on('select2:select', function (e) {
                                        const r = this.closest('.item-row');
                                        const si = r && r.querySelector('.serial-input');
                                        if (si) {
                                            const v = e.params.data.id;
                                            si.value = (v && v !== '__manual__') ? v : '';
                                        }
                                    }).on('select2:clear', function () {
                                        syncSerialDropdownToInput(this);
                                    });
                                }
                            }
                        }).catch(() => {});
                    }
                }
            }
            recalcInvoiceSummary();
        });
    }
}

document.getElementById('itemsContainer').addEventListener('input', function(e) {
    if (e.target.classList.contains('quantity-input')) {
        recalcInvoiceSummary();
    }
});

// Serial value is synced to .serial-input via select2:select / select2:clear in initSelect2InElement
document.getElementById('itemsContainer').addEventListener('change', function(e) {
    if (e.target.classList.contains('serial-dropdown')) {
        syncSerialDropdownToInput(e.target);
    }
});

document.getElementById('itemsContainer').addEventListener('mousedown', function(e) {
    if (e.target.closest('.invoice-item-scan-btn')) {
        e.preventDefault();
        e.stopPropagation();
        const row = e.target.closest('.item-row');
        const serialInput = row && row.querySelector('.serial-input');
        if (serialInput && window.openBarcodeScanner) {
            window.openBarcodeScanner(function(value) {
                serialInput.value = value;
                serialInput.dispatchEvent(new Event('input', { bubbles: true }));
                return true;
            });
        }
    }
});

function updateDiscountAmountVisibility() {
    const type = document.getElementById('discount_type').value;
    const wrapper = document.getElementById('discount_amount_wrapper');
    const amountInput = document.getElementById('discount_amount');
    if (!wrapper || !amountInput) return;
    if (type === '' || type === 'None') {
        amountInput.value = '';
        wrapper.style.display = 'none';
    } else {
        wrapper.style.display = 'block';
        amountInput.placeholder = type === 'percentage' ? '0' : '0.00';
    }
    recalcInvoiceSummary();
}
// Use jQuery so change fires when Select2 updates the dropdown
if (window.$) {
    $(document).on('change', '#discount_type', function() {
        document.getElementById('discount_amount').value = '';
        updateDiscountAmountVisibility();
    });
}
document.getElementById('discount_type').addEventListener('change', function() {
    document.getElementById('discount_amount').value = '';
    updateDiscountAmountVisibility();
});
document.getElementById('discount_amount').addEventListener('input', recalcInvoiceSummary);

// Initial setup
initSelect2InElement(document);
updateDiscountAmountVisibility();
recalcInvoiceSummary();
</script>
@endsection


