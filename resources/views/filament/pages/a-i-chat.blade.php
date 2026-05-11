<x-filament-panels::page>

@push('styles')
<style>
    :root {
        --chat-bg-light: #f9fafb;
        --chat-bg-dark: #09090b;
        --chat-card-light: rgba(255, 255, 255, 0.8);
        --chat-card-dark: rgba(24, 24, 27, 0.8);
        --chat-accent: #6366f1;
        --chat-accent-hover: #4f46e5;
        --chat-text-light: #1f2937;
        --chat-text-dark: #f3f4f6;
        --chat-border-light: rgba(229, 231, 235, 0.5);
        --chat-border-dark: rgba(63, 63, 70, 0.5);
    }

    #gpt-shell {
        position: relative;
        display: flex;
        flex-direction: column;
        width: 100%;
        height: calc(100vh - 180px);
        min-height: 600px;
        background: var(--chat-bg-light);
        color: var(--chat-text-light);
        overflow: hidden;
        border-radius: 24px;
        border: 1px solid var(--chat-border-light);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .dark #gpt-shell {
        background: var(--chat-bg-dark);
        color: var(--chat-text-dark);
        border-color: var(--chat-border-dark);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
    }

    #gpt-shell::before {
        content: '';
        position: absolute;
        top: -10%;
        right: -10%;
        width: 40%;
        height: 40%;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
        z-index: 0;
        pointer-events: none;
    }

    #gpt-messages {
        flex: 1;
        overflow-y: auto;
        padding: 2rem 1rem 8rem;
        scroll-behavior: smooth;
        z-index: 1;
    }

    #gpt-messages::-webkit-scrollbar { width: 5px; }
    #gpt-messages::-webkit-scrollbar-track { background: transparent; }
    #gpt-messages::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }
    .dark #gpt-messages::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); }

    #gpt-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        text-align: center;
        animation: fadeIn 0.8s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    #gpt-empty h1 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 1rem;
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        letter-spacing: -0.025em;
    }

    .gpt-chips {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
        max-width: 500px;
        margin-top: 2rem;
    }

    .gpt-chip {
        padding: 0.875rem 1.25rem;
        border-radius: 16px;
        background: var(--chat-card-light);
        border: 1px solid var(--chat-border-light);
        color: #4b5563;
        font-size: 0.9375rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        text-align: left;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .dark .gpt-chip {
        background: var(--chat-card-dark);
        border-color: var(--chat-border-dark);
        color: #9ca3af;
    }

    .gpt-chip:hover {
        transform: translateY(-2px);
        border-color: var(--chat-accent);
        color: var(--chat-accent);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
    }

    .gpt-row {
        width: 100%;
        margin-bottom: 2rem;
        animation: slideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateX(type === 'user' ? 20px : -20px); }
        to { opacity: 1; transform: translateX(0); }
    }

    .gpt-row-inner {
        max-width: 850px;
        margin: 0 auto;
        display: flex;
        gap: 1.25rem;
        padding: 0 1rem;
    }

    .gpt-row.user .gpt-row-inner {
        flex-direction: row-reverse;
    }

    .gpt-avatar {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .gpt-avatar.user { background: var(--chat-accent); color: white; }
    .gpt-avatar.ai { background: #10b981; color: white; }

    .gpt-content-wrap {
        flex: 1;
        display: flex;
        flex-direction: column;
        max-width: 80%;
    }

    .gpt-row.user .gpt-content-wrap { align-items: flex-end; }

    .gpt-sender {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.375rem;
        color: #6b7280;
    }

    .gpt-bubble {
        padding: 1rem 1.25rem;
        border-radius: 20px;
        font-size: 1rem;
        line-height: 1.6;
        position: relative;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .gpt-row.ai .gpt-bubble {
        background: var(--chat-card-light);
        border: 1px solid var(--chat-border-light);
        border-top-left-radius: 4px;
        color: var(--chat-text-light);
    }

    .dark .gpt-row.ai .gpt-bubble {
        background: var(--chat-card-dark);
        border-color: var(--chat-border-dark);
        color: var(--chat-text-dark);
    }

    .gpt-row.user .gpt-bubble {
        background: var(--chat-accent);
        color: white;
        border-top-right-radius: 4px;
    }

    /* Typing Animation */
    .typing {
        display: flex;
        gap: 4px;
        padding: 4px 0;
    }
    .typing span {
        width: 8px;
        height: 8px;
        background: var(--chat-accent);
        border-radius: 50%;
        animation: typing 1s infinite ease-in-out;
    }
    .typing span:nth-child(2) { animation-delay: 0.2s; }
    .typing span:nth-child(3) { animation-delay: 0.4s; }

    @keyframes typing {
        0%, 100% { transform: translateY(0); opacity: 0.4; }
        50% { transform: translateY(-4px); opacity: 1; }
    }

    #gpt-input-area {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 1.5rem;
        background: linear-gradient(to top, var(--chat-bg-light) 60%, transparent);
        z-index: 10;
    }

    .dark #gpt-input-area {
        background: linear-gradient(to top, var(--chat-bg-dark) 60%, transparent);
    }

    .gpt-input-container {
        max-width: 850px;
        margin: 0 auto;
        background: var(--chat-card-light);
        border: 1px solid var(--chat-border-light);
        border-radius: 20px;
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        gap: 0.75rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        transition: border-color 0.2s, box-shadow 0.2s;
        backdrop-filter: blur(12px);
    }

    .dark .gpt-input-container {
        background: var(--chat-card-dark);
        border-color: var(--chat-border-dark);
    }

    .gpt-input-container:focus-within {
        border-color: var(--chat-accent);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    #gpt-prompt {
        flex: 1;
        background: transparent;
        border: none;
        outline: none;
        color: inherit;
        font-size: 1rem;
        resize: none;
        max-height: 150px;
        padding: 0.25rem 0;
    }

    #gpt-send {
        background: var(--chat-accent);
        color: white;
        border: none;
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    #gpt-send:hover {
        background: var(--chat-accent-hover);
        transform: scale(1.05);
    }

    #gpt-send:active { transform: scale(0.95); }

    #gpt-send:disabled {
        background: #9ca3af;
        cursor: not-allowed;
        transform: none;
    }

    #gpt-error {
        display: none;
        max-width: 850px;
        margin: 0 auto 0.75rem;
        padding: 0.75rem 1rem;
        background: #fee2e2;
        border: 1px solid #fecaca;
        border-radius: 12px;
        color: #b91c1c;
        font-size: 0.875rem;
        animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
    }

    .dark #gpt-error {
        background: #450a0a;
        border-color: #7f1d1d;
        color: #fca5a5;
    }

    @keyframes shake {
        10%, 90% { transform: translate3d(-1px, 0, 0); }
        20%, 80% { transform: translate3d(2px, 0, 0); }
        30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
        40%, 60% { transform: translate3d(4px, 0, 0); }
    }

    .gpt-footer {
        text-align: center;
        font-size: 0.75rem;
        color: #9ca3af;
        margin-top: 0.75rem;
    }
</style>
@endpush

<div id="gpt-shell">
    <div id="gpt-messages">
        <div id="gpt-empty">
            <div class="mb-4">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-500/10 text-indigo-500 mb-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
            <h1>¿Cómo puedo ayudarte hoy?</h1>
            <p class="text-gray-500 dark:text-gray-400 max-width-md">Tu asistente personal potenciado por IA está listo para colaborar contigo.</p>
            
            <div class="gpt-chips">
                <button class="gpt-chip" onclick="useChip(this)">
                    <span>✨</span> Explícame cómo funciona...
                </button>
                <button class="gpt-chip" onclick="useChip(this)">
                    <span>📝</span> Resume este texto...
                </button>
                <button class="gpt-chip" onclick="useChip(this)">
                    <span>💡</span> Dame ideas creativas para...
                </button>
                <button class="gpt-chip" onclick="useChip(this)">
                    <span>📧</span> Redacta un correo profesional...
                </button>
            </div>
        </div>
    </div>

    <div id="gpt-input-area">
        <div id="gpt-error"></div>
        <div class="gpt-input-container">
            <textarea id="gpt-prompt" placeholder="Escribe un mensaje..." rows="1"></textarea>
            <button id="gpt-send" onclick="gptSend()" title="Enviar mensaje">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
            </button>
        </div>
        <p class="gpt-footer">Impulsado por IA. Verifique la información importante.</p>
    </div>

    @push('scripts')
    <script>
    (function () {
        const messagesEl = document.getElementById('gpt-messages');
        const promptEl   = document.getElementById('gpt-prompt');
        const sendBtn    = document.getElementById('gpt-send');
        const errorEl    = document.getElementById('gpt-error');

        let loading = false;

        promptEl.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 150) + 'px';
        });

        promptEl.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                gptSend();
            }
        });

        function clearEmpty() {
            const el = document.getElementById('gpt-empty');
            if (el) el.style.display = 'none';
        }

        function addRow(type, text) {
            clearEmpty();

            const row = document.createElement('div');
            row.className = 'gpt-row ' + type;

            const inner = document.createElement('div');
            inner.className = 'gpt-row-inner';

            const avatar = document.createElement('div');
            avatar.className = 'gpt-avatar ' + type;
            avatar.innerHTML = type === 'user' ? 
                '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>' : 
                '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>';

            const contentWrap = document.createElement('div');
            contentWrap.className = 'gpt-content-wrap';

            const sender = document.createElement('div');
            sender.className = 'gpt-sender';
            sender.textContent = type === 'user' ? 'Tú' : 'Asistente';

            const bubble = document.createElement('div');
            bubble.className = 'gpt-bubble';
            bubble.textContent = text;

            contentWrap.appendChild(sender);
            contentWrap.appendChild(bubble);
            inner.appendChild(avatar);
            inner.appendChild(contentWrap);
            row.appendChild(inner);
            messagesEl.appendChild(row);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        function addTyping() {
            clearEmpty();
            const row = document.createElement('div');
            row.className = 'gpt-row ai';
            row.id = 'gpt-typing';
            row.innerHTML = `
                <div class="gpt-row-inner">
                    <div class="gpt-avatar ai">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="gpt-content-wrap">
                        <div class="gpt-sender">Asistente</div>
                        <div class="gpt-bubble">
                            <div class="typing"><span></span><span></span><span></span></div>
                        </div>
                    </div>
                </div>
            `;
            messagesEl.appendChild(row);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        function removeTyping() {
            const el = document.getElementById('gpt-typing');
            if (el) el.remove();
        }

        function showError(msg) {
            errorEl.textContent = msg;
            errorEl.style.display = 'block';
            setTimeout(() => { errorEl.style.display = 'none'; }, 5000);
        }

        function setLoading(val) {
            loading = val;
            sendBtn.disabled = val;
            promptEl.disabled = val;
        }

        window.gptSend = async function () {
            const text = promptEl.value.trim();
            if (!text || loading) return;

            errorEl.style.display = 'none';
            addRow('user', text);
            promptEl.value = '';
            promptEl.style.height = 'auto';

            setLoading(true);
            addTyping();

            try {
                const res = await fetch('/ia-test', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ pregunta: text })
                });

                removeTyping();

                if (!res.ok) throw new Error('Error del servidor (' + res.status + ')');

                const data = await res.json();

                if (data.respuesta) {
                    addRow('ai', data.respuesta);
                } else {
                    showError('La respuesta llegó vacía. Intenta de nuevo.');
                }
            } catch (err) {
                removeTyping();
                showError(err.message || 'No se pudo conectar con el asistente.');
            } finally {
                setLoading(false);
                promptEl.focus();
            }
        };

        window.useChip = function (btn) {
            const text = btn.innerText.replace(/^[^\s]+\s/, ''); // Remove emoji
            promptEl.value = text;
            promptEl.dispatchEvent(new Event('input'));
            promptEl.focus();
        };

        promptEl.focus();
    })();
    </script>
    @endpush

</div>

</x-filament-panels::page>