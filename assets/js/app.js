function confirmDelete(message){return window.confirm(message || 'Are you sure you want to delete this item?');}
function togglePassword(id){const el=document.getElementById(id);if(el)el.type=el.type==='password'?'text':'password';}

document.addEventListener('DOMContentLoaded',()=>{
  document.querySelectorAll('[data-confirm]').forEach(link=>link.addEventListener('click',e=>{if(!confirmDelete(link.dataset.confirm)){e.preventDefault();}}));
  document.querySelectorAll('[data-validate-form]').forEach(form=>form.addEventListener('submit',e=>{
    if(!form.checkValidity()){e.preventDefault();form.reportValidity();}
  }));
  setTimeout(()=>document.querySelectorAll('.flash').forEach(x=>x.remove()),4500);

  const search=document.getElementById('productSearch');
  const results=document.getElementById('ajaxResults');
  if(search&&results){let timer;search.addEventListener('input',()=>{clearTimeout(timer);const q=search.value.trim();if(!q){results.innerHTML='';return;}timer=setTimeout(async()=>{try{const response=await fetch((window.STOCKFLOW_BASE||'')+'/ajax/product_search.php?q='+encodeURIComponent(q),{headers:{'Accept':'application/json'}});const json=await response.json();if(!json.success){results.textContent='Search failed.';return;}results.innerHTML=json.data.length?'<div class="ajax-box"><b>AJAX JSON Search Results</b><ul>'+json.data.map(p=>`<li><span>${escapeHtml(p.name)} (${escapeHtml(p.sku)})</span><span>${p.quantity} units · ৳${Number(p.price).toFixed(2)}</span></li>`).join('')+'</ul></div>':'<div class="ajax-box">No matching products.</div>';}catch(err){results.textContent='Could not load search results.';}},250);});}
});
function escapeHtml(value){return String(value).replace(/[&<>'"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[c]));}

document.addEventListener('DOMContentLoaded',()=>{
  document.querySelectorAll('.role-product-search').forEach(search=>{
    const results=document.querySelector(search.dataset.results); let timer;
    search.addEventListener('input',()=>{
      clearTimeout(timer); const q=search.value.trim(); if(!q){results.innerHTML='';return;}
      timer=setTimeout(async()=>{try{const r=await fetch((window.STOCKFLOW_BASE||'')+'/ajax/product_search.php?q='+encodeURIComponent(q),{headers:{Accept:'application/json'}});const j=await r.json();results.innerHTML=j.data&&j.data.length?'<div class="ajax-box"><b>Matching Products</b><ul>'+j.data.map(p=>`<li><span>${escapeHtml(p.name)} (${escapeHtml(p.sku)})</span><span>${p.quantity} units · ৳${Number(p.price).toFixed(2)}</span></li>`).join('')+'</ul></div>':'<div class="ajax-box">No matching products.</div>';}catch(e){results.textContent='Could not load search results.';}},250);
    });
  });
});
