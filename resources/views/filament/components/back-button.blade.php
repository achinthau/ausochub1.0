<div style="position: fixed; top: 14px; left: 80px; z-index: 9999; display: flex; align-items: center;">
    <a href="{{ $url }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.75rem; font-size: 0.875rem; font-weight: 700; transition: all 0.2s; border-radius: 0.5rem; color: #374151; text-decoration: none;" 
       onmouseover="this.style.color='#111827'; this.style.backgroundColor='#f3f4f6'" 
       onmouseout="this.style.color='#374151'; this.style.backgroundColor='transparent'">
        <svg style="width: 1.25rem; height: 1.25rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>{{ $label }}</span>
    </a>
</div>
