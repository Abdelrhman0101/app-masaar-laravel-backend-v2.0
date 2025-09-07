@extends('layouts.dashboard')

@section('title', 'محادثات العملاء')

@section('content')

{{-- ========== قسم الأنماط (CSS) الخاص بالصفحة ========== --}}
<style>
    :root {
        --primary-orange: #FC8700;
        --light-gray: #f8f9fa;
        --dark-gray: #343a40;
        --white: #ffffff;
        --shadow-lg: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .chat-container { display: flex; height: calc(100vh - 4rem); background: var(--white); border-radius: 20px; box-shadow: var(--shadow-lg); overflow: hidden;}
    .conversations-list { min-width: 320px; max-width: 320px; border-left: 1px solid #dee2e6; display: flex; flex-direction: column; background-color: var(--light-gray);}
    .list-header { padding: 1.25rem; border-bottom: 1px solid #dee2e6;}
    .list-header h4 { margin: 0; color: var(--primary-orange);}
    .list-body { overflow-y: auto; flex-grow: 1;}
    .conversation-item { display: flex; align-items: center; padding: 1rem 1.25rem; cursor: pointer; transition: background-color 0.2s ease; border-bottom: 1px solid #e9ecef;}
    .conversation-item:hover { background-color: #e9ecef;}
    .conversation-item.active { background-color: var(--primary-orange); color: var(--white);}
    .conversation-item.active .conversation-details h6, .conversation-item.active .conversation-details p { color: var(--white);}
    .profile-pic { width: 50px; height: 50px; border-radius: 50%; margin-left: 1rem; object-fit: cover;}
    .conversation-details { flex-grow: 1; overflow: hidden;}
    .conversation-details h6 { margin: 0; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;}
    .conversation-details p { margin: 0; font-size: 0.9rem; color: #6c757d; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;}
    .chat-area { flex-grow: 1; display: flex; flex-direction: column;}
    .chat-header { display: flex; align-items: center; padding: 1rem 1.5rem; background-color: var(--white); border-bottom: 1px solid #e9ecef;}
    #emptyChatView { display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%; color: #6c757d;}
    #emptyChatView i { font-size: 5rem; color: #e9ecef;}
    .messages-area { flex-grow: 1; overflow-y: auto; padding: 1.5rem; background-color: #f1f2f6;}
    .message-bubble { max-width: 70%; padding: 0.75rem 1.25rem; border-radius: 18px; margin-bottom: 1rem; line-height: 1.5; position: relative;}
    .message-bubble.sent { background-color: var(--white); color: var(--dark-gray); margin-right: auto; border-bottom-right-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);}
    .message-bubble.received { background-color: var(--primary-orange); color: var(--white); margin-left: auto; border-bottom-left-radius: 4px;}
    .chat-footer { padding: 1rem 1.5rem; background-color: var(--white); border-top: 1px solid #e9ecef;}
    #sendMessageForm { display: flex; align-items: center;}
    #messageInput { flex-grow: 1; border: 1px solid #dee2e6; border-radius: 20px; padding: 0.75rem 1.25rem; resize: none; transition: box-shadow 0.2s ease;}
    #messageInput:focus { box-shadow: 0 0 0 0.2rem rgba(252, 135, 0, 0.25); border-color: var(--primary-orange);}
    #sendMessageBtn { min-width: 50px; height: 50px; border-radius: 50%; background-color: var(--primary-orange); color: white; border: none; margin-right: 1rem; font-size: 1.5rem; transition: background-color 0.2s ease;}
    #sendMessageBtn:hover { background-color: #e67600;}
</style>

{{-- ========== قسم HTML الخاص بالصفحة ========== --}}
<audio id="notificationSound" src="{{ asset('sounds/notification.mp3') }}" preload="auto"></audio>

<div class="chat-container">
    <div class="conversations-list">
        <div class="list-header"><h4>جميع المحادثات</h4></div>
        <div class="list-body" id="conversationsContainer"><p class="p-3 text-muted">جاري تحميل المحادثات...</p></div>
    </div>
    <div class="chat-area">
        <div id="chatInterface" class="d-none w-100 h-100 d-flex flex-column">
            <div class="chat-header">
                <img id="chatWithPic" src="" class="profile-pic" alt="Profile Picture">
                <h5 id="chatWithName" class="mb-0"></h5>
            </div>
            <div class="messages-area" id="messagesContainer"></div>
            <div class="chat-footer">
                <form id="sendMessageForm">
                    <textarea id="messageInput" class="form-control" rows="1" placeholder="اكتب رسالتك هنا..."></textarea>
                    <button type="submit" id="sendMessageBtn"><i class="bi bi-send-fill"></i></button>
                </form>
            </div>
        </div>
        <div id="emptyChatView" class="d-flex w-100 h-100 justify-content-center align-items-center flex-column">
             <i class="bi bi-chat-left-dots"></i>
             <h4 class="mt-3">اختر محادثة لعرضها</h4>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- [1] إعدادات وتحققات أساسية ---
    const API_TOKEN = localStorage.getItem('token');
    if (!API_TOKEN) { window.location.href = '/login'; return; }

    const loggedInAdmin = JSON.parse(localStorage.getItem('user'));
    const isAllowedAdmin = loggedInAdmin && loggedInAdmin.user_type === 'admin' && ((loggedInAdmin.email || '').toLowerCase() === 'admin@msar.app');
    if (!isAllowedAdmin) {
        const container = document.querySelector('.chat-container');
        if (container) {
            container.innerHTML = `
                <div class="w-100 p-5 text-center">
                    <h3 class="text-danger mb-3">غير مصرح</h3>
                    <p class="text-muted">ليس لديك صلاحية لعرض محادثات الأدمن مع العملاء.</p>
                </div>
            `;
        }
        return;
    }
    
    if (typeof Echo === 'undefined') {
        console.error('Laravel Echo غير محمّل. تأكد من تضمين app.js/ bootstrap.js');
        // لا نوقف الصفحة بالكامل حتى لو Echo غير متاح
    }
    
    // --- [2] عناصر الصفحة (لا تغيير هنا) ---
    const conversationsContainer = document.getElementById('conversationsContainer');
    const messagesContainer = document.getElementById('messagesContainer');
    const chatInterface = document.getElementById('chatInterface');
    const emptyChatView = document.getElementById('emptyChatView');
    const chatWithName = document.getElementById('chatWithName');
    const chatWithPic = document.getElementById('chatWithPic');
    const sendMessageForm = document.getElementById('sendMessageForm');
    const messageInput = document.getElementById('messageInput');
    const notificationSound = document.getElementById('notificationSound');

    // --- [3] متغيرات الحالة ---
    let activeUser = null; // <-- تعديل: الآن نتتبع المستخدم النشط بدلاً من المحادثة
    let conversations = [];

    // --- [4] دوال مساعدة ---
    const playNotificationSound = () => {
        try { notificationSound.currentTime = 0; notificationSound.play(); } catch (e) { /* تجاهل */ }
    };
    const renderMessages = (messages) => {
        if (!Array.isArray(messages)) { messagesContainer.innerHTML = ''; return; }
        messagesContainer.innerHTML = messages.map(m => {
            const isMine = (m.sender_id === loggedInAdmin.id) || (m.sender && m.sender.id === loggedInAdmin.id);
            const cls = isMine ? 'received' : 'sent';
            const content = ((m.content ?? '') + '').replace(/</g,'&lt;').replace(/>/g,'&gt;');
            return `<div class="message-bubble ${cls}">${content}</div>`;
        }).join('');
        scrollToBottom();
    };
    const appendMessage = (message) => {
        const isMine = (message.sender_id === loggedInAdmin.id) || (message.sender && message.sender.id === loggedInAdmin.id);
        const cls = isMine ? 'received' : 'sent';
        const content = ((message.content ?? '') + '').replace(/</g,'&lt;').replace(/>/g,'&gt;');
        messagesContainer.insertAdjacentHTML('beforeend', `<div class="message-bubble ${cls}">${content}</div>`);
        scrollToBottom();
    };
    const scrollToBottom = () => { messagesContainer.scrollTop = messagesContainer.scrollHeight; };

    const renderConversations = () => {
        conversationsContainer.innerHTML = conversations.map(convo => {
            const lastMessageContent = convo.latest_message?.content ?? 'لا توجد رسائل بعد...';
            // <-- تعديل: الآن data-id يحمل user_id مباشرة لأنه هو المعرف للمحادثة
            return `
            <div class="conversation-item" data-id="${convo.user.id}">
                <img class="profile-pic" src="https://avatar.iran.liara.run/public/boy?username=${convo.user.name}" alt="${convo.user.name}">
                <div class="conversation-details">
                    <h6>${convo.user.name}</h6>
                    <p>${lastMessageContent.substring(0, 30)}${lastMessageContent.length > 30 ? '...' : ''}</p>
                </div>
            </div>`;
        }).join('');
        
        if(activeUser) {
            // <-- تعديل: نبحث عن العنصر النشط باستخدام user.id
            const activeItem = conversationsContainer.querySelector(`.conversation-item[data-id="${activeUser.id}"]`);
            if (activeItem) activeItem.classList.add('active');
        }
    };
    
    // --- [5] دوال الاتصال بالـ API ---
    // <-- تعديل: الـ endpoint أصبح index بدلاً من chats
       async function fetchConversationsAPI() {
        try {
            const response = await fetch('/api/admin/chats', { headers: { 'Authorization': `Bearer ${API_TOKEN}`, 'Accept': 'application/json' }});
            if (!response.ok) {
                console.error('API Error:', response.status, await response.text());
                return;
            }
            const result = await response.json();
            
            // ==============>> أضف هذا السطر للتشخيص <<==============
            console.log("API Response:", result); 
            // =======================================================

            // <-- تعديل محتمل هنا بناءً على شكل الرد
            if (result.status && result.data && Array.isArray(result.data)) {
                conversations = result.data; // إذا كانت result.data هي المصفوفة مباشرة
                renderConversations();
            } else if (result.status && result.data && Array.isArray(result.data.data)) {
                // هذا هو الاحتمال الأكبر إذا كنت تستخدم Pagination
                conversations = result.data.data; 
                renderConversations();
            } else {
                console.error("The API did not return an array of conversations.", result);
            }

        } catch (error) { console.error('Failed to fetch conversations:', error); }
    }

    // --- [6] منطق المحادثات (تعديلات جوهرية هنا) ---
    conversationsContainer.addEventListener('click', async function(e) {
        const item = e.target.closest('.conversation-item');
        if (!item) return;

        const userId = item.dataset.id; // <-- تعديل: نحصل على user_id
        // <-- تعديل: المحادثة النشطة هي محادثة المستخدم المحدد
        const activeConversation = conversations.find(c => c.user.id == userId);
        if (!activeConversation) return;

        activeUser = activeConversation.user; // <-- تعديل: نخزن المستخدم النشط

        document.querySelectorAll('.conversation-item').forEach(el => el.classList.remove('active'));
        item.classList.add('active');
        chatInterface.classList.remove('d-none');
        chatInterface.classList.add('d-flex');
        emptyChatView.style.display = 'none';
        
        chatWithName.textContent = activeUser.name;
        chatWithPic.src = `https://avatar.iran.liara.run/public/boy?username=${activeUser.name}`;
        
        try {
            // <-- تعديل: نستدعي API جلب الرسائل باستخدام user_id
            const response = await fetch(`/api/admin/chats/${userId}`, { headers: { 'Authorization': `Bearer ${API_TOKEN}`, 'Accept': 'application/json' } });
            const result = await response.json();
            renderMessages(result.data.messages);
        } catch (error) { console.error('Failed to load messages:', error); }
    });

    sendMessageForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const content = messageInput.value.trim();
        if (!content || !activeUser) return; // <-- تعديل: نتأكد من وجود مستخدم نشط

        const tempMessage = { content: content, sender_id: loggedInAdmin.id };
        appendMessage(tempMessage);
        
        const originalMessage = messageInput.value;
        messageInput.value = '';
        
        try {
            // <-- تعديل: API إرسال الرسالة الآن يأخذ user_id
            await fetch('/api/admin/chats', {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${API_TOKEN}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ content: content, user_id: activeUser.id }) // <-- تعديل: نرسل user_id
            });
            fetchConversationsAPI();
        } catch (error) {
            messageInput.value = originalMessage;
            if(messagesContainer.lastChild) messagesContainer.lastChild.remove();
        }
    });

    // --- [7] إعداد Echo (تعديل جوهري هنا) ---
    async function initializeRealtime() {
        await fetchConversationsAPI();

        if (conversations.length > 0) {
            conversations.forEach(convo => {
                // <-- تعديل: نستمع الآن على قناة المحادثة باستخدام convo.id
                // الباك إند يرسل لنا convo.id وهو conversation_id الصحيح
                Echo.private(`chat.${convo.id}`)
                    .listen('.new.message', (event) => { // <-- تعديل: اسم الحدث هو new.message
                        const message = event.message;
                        if (message.sender_id === loggedInAdmin.id) return;
                        
                        playNotificationSound();
                        fetchConversationsAPI();

                        // <-- تعديل: التحقق إذا كانت الرسالة تابعة للمستخدم النشط
                        if (activeUser && activeUser.id === message.sender_id) {
                            appendMessage(message);
                        }
                    });
            });
        }
    }

    // --- [8] بدء التشغيل ---
    initializeRealtime();
});
</script>
@endsection