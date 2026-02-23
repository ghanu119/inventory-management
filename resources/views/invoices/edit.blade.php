@extends('layouts.app')

@section('title', 'Edit Invoice')

@section('content')
<div class="space-y-6">
    <h1 class="text-3xl font-bold text-gray-900">Edit Invoice: {{ $invoice->invoice_number }}</h1>

    <form action="{{ route('invoices.update', $invoice) }}" method="POST" class="bg-white shadow rounded-lg p-6" id="invoiceForm">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="invoice_date" class="block text-sm font-semibold text-gray-700 mb-2">Invoice Date</label>
                    <input type="date" name="invoice_date" id="invoice_date" value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d')) }}" required>
                </div>
                <div>
                    <label for="payment_mode" class="block text-sm font-semibold text-gray-700 mb-2">Payment Mode</label>
                    <select name="payment_mode" id="payment_mode" class="select2" required>
                        @foreach(['Cash','UPI','Card','Bank'] as $mode)
                            <option value="{{ $mode }}" {{ old('payment_mode', $invoice->payment_mode) === $mode ? 'selected' : '' }}>{{ $mode }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold mb-4">Customer Details</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-semibold text-gray-700 mb-2">Name</label><input type="text" name="customer_name" value="{{ old('customer_name', $invoice->customer->name) }}" required class="w-full px-4 py-2 border rounded"></div>
                    <div><label class="block text-sm font-semibold text-gray-700 mb-2">Phone</label><input type="text" name="customer_phone" value="{{ old('customer_phone', $invoice->customer->phone) }}" class="w-full px-4 py-2 border rounded"></div>
                    <div><label class="block text-sm font-semibold text-gray-700 mb-2">Email</label><input type="email" name="customer_email" value="{{ old('customer_email', $invoice->customer->email) }}" class="w-full px-4 py-2 border rounded"></div>
                    <div><label class="block text-sm font-semibold text-gray-700 mb-2">GST Number</label><input type="text" name="customer_gst_number" value="{{ old('customer_gst_number', $invoice->customer->gst_number) }}" class="w-full px-4 py-2 border rounded"></div>
                </div>
                <div class="mt-4"><label class="block text-sm font-semibold text-gray-700 mb-2">Address</label><textarea name="customer_address" rows="2" class="w-full px-4 py-2 border rounded">{{ old('customer_address', $invoice->customer->address) }}</textarea></div>
            </div>

            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold mb-4">Items</h3>
                <div id="itemsContainer">
                    @foreach($invoice->items as $idx => $item)
                    <div class="item-row border-b pb-4 mb-4">
                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-5">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Product</label>
                                <select name="items[{{ $idx }}][product_id]" class="product-select select2" required data-selected="{{ $item->product_id }}">
                                    <option value="">Select Product</option>
                                </select>
                                <p class="hsn-display mt-1 text-xs text-gray-500">HSN: {{ $item->product->hsn_code ?: 'Not set' }}</p>
                            </div>
                            <div class="col-span-1"><label class="block text-sm font-semibold text-gray-700 mb-2">Qty</label><input type="number" name="items[{{ $idx }}][quantity]" class="quantity-input" min="1" value="{{ old("items.{$idx}.quantity", $item->quantity) }}" required class="w-full px-2 py-1.5 border rounded"></div>
                            <div class="col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Serial No</label>
                                <select class="serial-dropdown select2 w-full px-2 py-1.5 border rounded text-sm mb-1" style="display:none;"><option value="">Select or type below</option><option value="__manual__">Manual entry</option></select>
                                <div class="flex items-center gap-1">
                                    <input type="text" name="items[{{ $idx }}][serial_no]" class="serial-input flex-1 min-w-0 px-2 py-1.5 border rounded text-sm" value="{{ old("items.{$idx}.serial_no", $item->serial_no) }}">
                                    <button type="button" class="invoice-item-scan-btn inline-flex items-center p-2 border border-gray-300 rounded-md text-gray-600 hover:bg-gray-100 flex-shrink-0" data-no-loader title="Scan barcode for this item"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg></button>
                                </div>
                            </div>
                            <div class="col-span-2"><label class="block text-sm font-semibold text-gray-700 mb-2">Price</label><input type="text" class="price-display bg-gray-100 w-full px-2 py-1.5 border rounded" readonly value="Rs {{ number_format($item->price, 2) }}"></div>
                            <div class="col-span-1"><label class="block text-sm font-semibold text-gray-700 mb-2">GST</label><input type="text" class="gst-display bg-gray-100 w-full px-2 py-1.5 border rounded" readonly value="{{ $item->gst_rate }}%"></div>
                            <div class="col-span-1"><label class="block text-sm font-semibold text-gray-700 mb-2">&nbsp;</label><button type="button" class="remove-item w-full px-3 py-2.5 bg-red-600 text-white rounded" data-no-loader>Remove</button></div>
                        </div>
                        <div class="grid grid-cols-12 gap-4 mt-2">
                            <div class="col-span-2"><label class="block text-sm font-semibold text-gray-700 mb-1">Warranty (yr)</label><input type="number" step="0.01" name="items[{{ $idx }}][warranty_years]" class="warranty-input w-full px-2 py-1.5 border rounded text-sm" min="0" value="{{ old("items.{$idx}.warranty_years", $item->warranty_years) ?? '' }}"></div>
                            <div class="col-span-10"><label class="block text-sm font-semibold text-gray-700 mb-1">Custom short text</label><textarea name="items[{{ $idx }}][custom_short_text]" class="custom-text-input w-full px-2 py-1.5 border rounded text-sm" rows="2">{{ old("items.{$idx}.custom_short_text", $item->custom_short_text) }}</textarea></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button type="button" id="addItem" class="px-4 py-2.5 bg-gray-600 text-white rounded font-medium mt-2" data-no-loader>Add Item</button>
            </div>

            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold mb-4">Discount</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div><label class="block text-sm font-semibold text-gray-700 mb-2">Type</label><select name="discount_type" id="discount_type" class="select2 w-full px-4 py-2 border rounded"><option value="">None</option><option value="flat" {{ old('discount_type', $invoice->discount_type) === 'flat' ? 'selected' : '' }}>Flat</option><option value="percentage" {{ old('discount_type', $invoice->discount_type) === 'percentage' ? 'selected' : '' }}>Percentage</option></select></div>
                    <div id="discount_amount_wrapper"><label class="block text-sm font-semibold text-gray-700 mb-2">Amount</label><input type="number" step="0.01" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', $invoice->discount_amount) }}" min="0" class="w-full px-4 py-2 border rounded"></div>
                </div>
            </div>

            <div class="border-t pt-6">
                <div class="max-w-sm space-y-1 text-sm">
                    <div class="flex justify-between"><span class="text-gray-600">Subtotal:</span><span class="font-medium" id="summarySubtotal">Rs 0.00</span></div>
                    <div class="flex justify-between"><span class="text-gray-600">Total GST:</span><span class="font-medium" id="summaryTotalGst">Rs 0.00</span></div>
                    <div class="flex justify-between"><span class="text-gray-600">Discount:</span><span class="font-medium" id="summaryDiscount">Rs 0.00</span></div>
                    <div class="flex justify-between border-t pt-2"><span class="font-bold">Grand Total:</span><span class="font-bold" id="summaryGrandTotal">Rs 0.00</span></div>
                </div>
            </div>

            <div><label class="block text-sm font-semibold text-gray-700 mb-2">Notes</label><textarea name="notes" id="notes" rows="3" class="w-full px-4 py-2 border rounded">{{ old('notes', $invoice->notes) }}</textarea></div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('invoices.show', $invoice) }}" class="px-4 py-2 border border-gray-300 rounded">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Update Invoice</button>
            </div>
        </div>
    </form>
</div>

@include('components.barcode-scanner')

<script>
let itemIndex = {{ $invoice->items->count() }};
const products = @json($products);
const availableSerialsBaseUrl = "{{ url('products') }}";
function formatMoney(a){return 'Rs '+a.toFixed(2);}
function recalcInvoiceSummary(){
    var r=document.querySelectorAll('#itemsContainer .item-row'),st=0,tg=0;
    r.forEach(function(row){
        var sel=row.querySelector('.product-select'),q=row.querySelector('.quantity-input');
        if(!sel||!q)return;
        var opt=sel.options[sel.selectedIndex],qty=parseFloat(q.value||0);
        if(!opt||!opt.value||qty<=0)return;
        var price=parseFloat(opt.dataset.price||0),gst=parseFloat(opt.dataset.gst||0),inc=parseInt(opt.getAttribute('data-gst-included')||'0')===1;
        var lt=inc?price*qty:(price*qty),lg=inc&&gst>0?lt/(1+gst/100):0;lg=inc?lt-lg:(lt*gst/100);if(inc&&gst>0){var tx=lt/(1+gst/100);lg=lt-tx;}
        else{var tx=price*qty;lg=(tx*gst)/100;lt=tx+lg;}
        st+=tx;tg+=lg;
    });
    var dt=document.getElementById('discount_type').value,da=parseFloat(document.getElementById('discount_amount').value||0),dm=0;
    if(dt==='percentage'&&da>0)dm=(st*da)/100;else if(dt==='flat'&&da>0)dm=da;
    document.getElementById('summarySubtotal').textContent=formatMoney(st);
    document.getElementById('summaryTotalGst').textContent=formatMoney(tg);
    document.getElementById('summaryDiscount').textContent=formatMoney(dm);
    document.getElementById('summaryGrandTotal').textContent=formatMoney(st+tg-dm);
}
function populateProductSelect(sel){
    if(!Array.isArray(products))return;
    while(sel.firstChild)sel.removeChild(sel.firstChild);
    var ph=document.createElement('option');ph.value='';ph.textContent='Select Product';sel.appendChild(ph);
    products.forEach(function(p){
        var o=document.createElement('option');o.value=p.id;
        var pr=parseFloat(p.price||0),gi=!!p.is_gst_included,st=p.stock_quantity??0,gr=p.gst_rate??0,hs=p.hsn_code||'',wy=p.warranty_years!=null?p.warranty_years:'',ct=p.custom_short_text||'';
        o.textContent=p.name+' (Rs '+pr.toFixed(2)+' | Stock: '+st+')';
        o.dataset.price=pr;o.dataset.gst=gr;o.dataset.stock=st;o.dataset.hsn=hs;o.dataset.warranty=wy;o.dataset.customText=ct;o.setAttribute('data-gst-included',gi?'1':'0');
        sel.appendChild(o);
    });
}
function syncSerialDropdownToInput(sel){var row=sel.closest('.item-row'),si=row&&row.querySelector('.serial-input');if(si){var v=sel.value;si.value=(v&&v!=='__manual__')?v:'';}}
function initSelect2(el){
    if(!window.$||!$.fn.select2)return;
    $(el).find('.product-select').each(function(){
        populateProductSelect(this);
        var sid=$(this).data('selected');
        if(sid)$(this).val(sid).trigger('change');
    });
    $(el).find('.select2').each(function(){
        if($(this).hasClass('select2-hidden-accessible'))return;
        if($(this).hasClass('serial-dropdown')){
            $(this).select2({placeholder:'Select or type below',allowClear:true});
            $(this).off('select2:select select2:clear').on('select2:select',function(e){var r=this.closest('.item-row'),si=r&&r.querySelector('.serial-input');if(si){var v=e.params.data.id;si.value=(v&&v!=='__manual__')?v:'';}}).on('select2:clear',function(){syncSerialDropdownToInput(this);});
        }
        else $(this).select2();
        if($(this).hasClass('product-select')){
            $(this).on('select2:select',function(){
                var opt=this.options[this.selectedIndex],row=this.closest('.item-row');
                if(opt&&row&&opt.value){
                    var price=parseFloat(opt.dataset.price||0),gst=parseFloat(opt.dataset.gst||0),hsn=opt.dataset.hsn||'';
                    if(row.querySelector('.price-display'))row.querySelector('.price-display').value='Rs '+price.toFixed(2);
                    if(row.querySelector('.gst-display'))row.querySelector('.gst-display').value=gst+'%';
                    var hd=row.querySelector('.hsn-display');if(hd)hd.textContent='HSN: '+(hsn||'Not set');
                    row.querySelector('.warranty-input').value=opt.dataset.warranty||'';
                    row.querySelector('.custom-text-input').value=opt.dataset.customText||'';
                    row.querySelector('.quantity-input').setAttribute('max',opt.dataset.stock||'');
                    var sd=row.querySelector('.serial-dropdown'),si=row.querySelector('.serial-input');
                    if(si)si.value='';
                    if(sd){if(window.$&&$.fn.select2&&$(sd).hasClass('select2-hidden-accessible'))$(sd).select2('destroy');
                        sd.innerHTML='<option value="">Select or type below</option><option value="__manual__">Manual entry</option>';sd.style.display='none';
                        fetch(availableSerialsBaseUrl+'/'+opt.value+'/available-serials',{headers:{'Accept':'application/json'}}).then(function(r){return r.json();}).then(function(serials){
                            if(serials&&serials.length>0){serials.forEach(function(s){var o=document.createElement('option');o.value=s;o.textContent=s;sd.appendChild(o);});sd.style.display='block';if(window.$&&$.fn.select2){$(sd).select2({placeholder:'Select or type below',allowClear:true});$(sd).off('select2:select select2:clear').on('select2:select',function(e){var r=this.closest('.item-row'),si=r&&r.querySelector('.serial-input');if(si){var v=e.params.data.id;si.value=(v&&v!=='__manual__')?v:'';}}).on('select2:clear',function(){syncSerialDropdownToInput(this);});}}
                        });
                    }
                }
                recalcInvoiceSummary();
            });
        }
    });
}
document.getElementById('addItem').addEventListener('click',function(){
    var cont=document.getElementById('itemsContainer'),tpl=cont.querySelector('.item-row'),row=tpl.cloneNode(true);
    row.querySelectorAll('.select2-container').forEach(function(e){e.remove();});
    row.querySelectorAll('input,select,textarea').forEach(function(inp){
        if(inp.name)inp.name=inp.name.replace(/\[\d+\]/,'['+itemIndex+']');
        if(inp.classList.contains('product-select'))inp.value='';inp.removeAttribute('data-selected');
        if(inp.classList.contains('quantity-input'))inp.value='1';
        if(inp.classList.contains('serial-input'))inp.value='';
        if(inp.classList.contains('warranty-input'))inp.value='';
        if(inp.classList.contains('custom-text-input'))inp.value='';
        if(inp.classList.contains('price-display')||inp.classList.contains('gst-display'))inp.value='';
    });
    var sd0=row.querySelector('.serial-dropdown');if(sd0){sd0.innerHTML='<option value="">Select or type below</option><option value="__manual__">Manual entry</option>';sd0.style.display='none';sd0.classList.remove('select2-hidden-accessible');sd0.removeAttribute('data-select2-id');}
    var ps=row.querySelector('.product-select');if(ps){ps.classList.remove('select2-hidden-accessible');ps.removeAttribute('data-select2-id');ps.style.display='';}
    cont.appendChild(row);initSelect2(row);itemIndex++;recalcInvoiceSummary();
});
function reindexItemRows(){var cont=document.getElementById('itemsContainer'),rows=cont.querySelectorAll('.item-row');rows.forEach(function(row,index){row.querySelectorAll('input,select,textarea').forEach(function(inp){if(inp.name&&inp.name.indexOf('items[')===0)inp.name=inp.name.replace(/^items\[\d+\]/,'items['+index+']');});});itemIndex=rows.length;}
document.getElementById('itemsContainer').addEventListener('click',function(e){
    if(e.target.classList.contains('remove-item')&&document.querySelectorAll('.item-row').length>1){
        var row=e.target.closest('.item-row');
        if(row.querySelector('.product-select')&&window.$&&$.fn.select2)$(row.querySelector('.product-select')).select2('destroy');
        row.remove();reindexItemRows();recalcInvoiceSummary();
    }
});
document.getElementById('itemsContainer').addEventListener('change',function(e){
    if(e.target.classList.contains('serial-dropdown'))syncSerialDropdownToInput(e.target);
});
document.getElementById('itemsContainer').addEventListener('mousedown',function(e){
    if(e.target.closest('.invoice-item-scan-btn')){e.preventDefault();e.stopPropagation();var row=e.target.closest('.item-row'),si=row&&row.querySelector('.serial-input');if(si&&window.openBarcodeScanner){window.openBarcodeScanner(function(value){si.value=value;si.dispatchEvent(new Event('input',{bubbles:true}));return true;});}}
});
document.getElementById('itemsContainer').addEventListener('input',function(e){if(e.target.classList.contains('quantity-input'))recalcInvoiceSummary();});
function updateDiscountAmountVisibility(){var type=document.getElementById('discount_type').value,wrapper=document.getElementById('discount_amount_wrapper'),amountInput=document.getElementById('discount_amount');if(!wrapper||!amountInput)return;if(type===''||type==='None'){amountInput.value='';wrapper.style.display='none';}else{wrapper.style.display='block';amountInput.placeholder=type==='percentage'?'0':'0.00';}recalcInvoiceSummary();}
if(window.$)$(document).on('change','#discount_type',function(){document.getElementById('discount_amount').value='';updateDiscountAmountVisibility();});
document.getElementById('discount_type').addEventListener('change',function(){document.getElementById('discount_amount').value='';updateDiscountAmountVisibility();});
document.getElementById('discount_amount').addEventListener('input',recalcInvoiceSummary);
initSelect2(document);
updateDiscountAmountVisibility();
recalcInvoiceSummary();
</script>
@endsection
