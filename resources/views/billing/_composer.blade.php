<div id="composer" class="modal" hidden>
    <div class="modal-box">
        <div class="modal-head">
            <span id="cmp-title">Add Item</span>
            <button type="button" id="cmp-close" class="xbtn">&times;</button>
        </div>
        <div class="cmp-body">
            <div class="prow" id="cmp-product"></div>
            <div id="cmp-dup" class="status" style="margin-top:10px;" hidden>Already in cart &mdash; saving will update the line.</div>
            <div id="cmp-slabs"></div>
            <div class="slabrow" style="margin-top:10px;">
                <div class="fld"><span>Qty</span><input type="number" id="cmp-qty" min="1" step="1"></div>
                <div class="fld"><span>Free qty</span><input type="number" id="cmp-free" min="0" step="1"></div>
            </div>
            <div class="slabrow" style="margin-top:8px;" id="cmp-derived" hidden>
                <div class="fld"><span>Factor</span><input type="number" id="cmp-factor" min="0.01" step="0.01" title="Rate = MRP / factor"></div>
                <div class="fld"><span>Flat %</span><input type="number" id="cmp-flat" step="0.01" title="Rate = MRP minus this percent"></div>
            </div>
            <div class="slabrow" style="margin-top:8px;">
                <div class="fld"><span>Scheme %</span><input type="number" id="cmp-scheme" min="0" max="100" step="0.01"></div>
                <div class="fld"><span>Rate &#8377;</span><input type="number" id="cmp-rate" min="0.01" step="0.01"></div>
            </div>
            <div id="cmp-amount" class="linehint"></div>
            <div style="margin-top:14px;">
                <button type="button" id="cmp-save" class="btn">ADD TO CART</button>
            </div>
            <div style="margin-top:8px;">
                <button type="button" id="cmp-remove" class="btn btn-outline" hidden>REMOVE FROM CART</button>
            </div>
        </div>
    </div>
</div>