<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$currentUserId = $_SESSION["user_id"];
$currentUserName = $_SESSION["user_name"] ?? "";

// Récupérer l'utilisateur courant
$meStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$meStmt->execute([$currentUserId]);
$me = $meStmt->fetch();

// Utilisateur avec qui on veut chatter (depuis ?user=ID)
$targetUserId = intval($_GET["user"] ?? 0);

$targetUser = null;
if ($targetUserId > 0) {
    $tStmt = $pdo->prepare("SELECT id, name, email, role, avatar_path FROM users WHERE id = ?");
    $tStmt->execute([$targetUserId]);
    $targetUser = $tStmt->fetch();
}

$myInitials = strtoupper(substr($me["name"] ?? "U", 0, 1));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Messages — HireTounsi</title>
<link href="https://fonts.googleapis.com/css2?family=Cabinet+Grotesk:wght@400;700;800;900&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
<style>
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

:root {
  --bg:     #07080d;
  --s:      #0e1018;
  --s2:     #161824;
  --border: rgba(255,255,255,.07);
  --accent: #8b5cf6;
  --a2:     #06b6d4;
  --text:   #f0eeff;
  --muted:  #7a7890;
  --muted2: #4a4860;
  --green:  #34d399;
}

body {
  font-family:'Instrument Sans',sans-serif;
  background:var(--bg); color:var(--text);
  height:100vh; display:flex; flex-direction:column; overflow:hidden;
}

/* ── NAV ── */
nav {
  display:flex; align-items:center; justify-content:space-between;
  padding:0 32px; height:60px; flex-shrink:0;
  border-bottom:1px solid var(--border);
  background:rgba(7,8,13,.9); backdrop-filter:blur(20px);
}
.nav-logo {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:900; font-size:18px; letter-spacing:-.5px;
  color:var(--text); text-decoration:none;
}
.nav-logo span { color:var(--accent); }
.nav-back {
  display:inline-flex; align-items:center; gap:6px;
  color:var(--muted); text-decoration:none; font-size:13px; font-weight:600;
  padding:7px 14px; border-radius:9px; border:1px solid var(--border);
  background:var(--s2); transition:.2s;
}
.nav-back:hover { color:var(--text); border-color:rgba(139,92,246,.4); }

/* ── CHAT LAYOUT ── */
.chat-layout {
  display:flex; flex:1; overflow:hidden;
}

/* ── CONVERSATIONS SIDEBAR ── */
.conv-sidebar {
  width:300px; flex-shrink:0;
  border-right:1px solid var(--border);
  display:flex; flex-direction:column;
  background:var(--s);
}

.conv-header {
  padding:16px 18px; border-bottom:1px solid var(--border); flex-shrink:0;
}
.conv-header h2 {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:800; font-size:16px; letter-spacing:-.3px; margin-bottom:10px;
}
.conv-search {
  width:100%; padding:9px 12px;
  background:var(--s2); border:1px solid var(--border);
  border-radius:9px; color:var(--text);
  font-family:'Instrument Sans',sans-serif; font-size:13px; outline:none;
}
.conv-search::placeholder { color:var(--muted2); }
.conv-search:focus { border-color:rgba(139,92,246,.5); }

.conv-list { flex:1; overflow-y:auto; }

.conv-item {
  display:flex; align-items:center; gap:11px;
  padding:14px 18px; cursor:pointer; transition:.15s;
  border-bottom:1px solid var(--border);
  position:relative;
}
.conv-item:hover { background:var(--s2); }
.conv-item.active { background:rgba(139,92,246,.1); border-left:2px solid var(--accent); }

.conv-avatar {
  width:42px; height:42px; border-radius:12px; flex-shrink:0;
  background:linear-gradient(135deg,var(--accent),#7c3aed);
  display:flex; align-items:center; justify-content:center;
  font-family:'Cabinet Grotesk',sans-serif; font-weight:900; font-size:15px;
  overflow:hidden;
}
.conv-avatar img { width:100%; height:100%; object-fit:cover; }

.conv-info { flex:1; min-width:0; }
.conv-name { font-size:14px; font-weight:700; margin-bottom:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.conv-last { font-size:12px; color:var(--muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

.conv-meta { display:flex; flex-direction:column; align-items:flex-end; gap:4px; flex-shrink:0; }
.conv-time { font-size:11px; color:var(--muted2); }
.conv-badge {
  background:var(--accent); color:#fff;
  font-size:10px; font-weight:700;
  width:18px; height:18px; border-radius:50%;
  display:flex; align-items:center; justify-content:center;
}

.conv-empty {
  text-align:center; padding:40px 20px; color:var(--muted2); font-size:13px;
}

/* ── CHAT AREA ── */
.chat-area {
  flex:1; display:flex; flex-direction:column; min-width:0;
}

/* chat empty state */
.chat-empty {
  flex:1; display:flex; flex-direction:column;
  align-items:center; justify-content:center; gap:14px;
  color:var(--muted2); text-align:center; padding:40px;
}
.chat-empty svg { opacity:.2; }
.chat-empty h3 {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:800; font-size:18px; color:var(--muted);
}
.chat-empty p { font-size:13px; }

/* chat with user */
.chat-header {
  display:flex; align-items:center; gap:12px;
  padding:14px 20px; border-bottom:1px solid var(--border);
  background:var(--s); flex-shrink:0;
}
.chat-header-avatar {
  width:38px; height:38px; border-radius:10px;
  background:linear-gradient(135deg,var(--accent),#7c3aed);
  display:flex; align-items:center; justify-content:center;
  font-family:'Cabinet Grotesk',sans-serif; font-weight:900; font-size:14px;
  overflow:hidden; flex-shrink:0;
}
.chat-header-avatar img { width:100%; height:100%; object-fit:cover; }
.chat-header-name {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:800; font-size:15px; letter-spacing:-.3px;
}
.chat-header-role { font-size:11px; color:var(--accent); font-weight:600; }
.online-dot {
  width:8px; height:8px; border-radius:50%;
  background:var(--green); box-shadow:0 0 6px var(--green);
  margin-left:auto;
}

/* messages */
.messages-wrap {
  flex:1; overflow-y:auto; padding:20px;
  display:flex; flex-direction:column; gap:12px;
}

.msg-group { display:flex; gap:8px; }
.msg-group.mine { flex-direction:row-reverse; }

.msg-avatar {
  width:30px; height:30px; border-radius:8px; flex-shrink:0; align-self:flex-end;
  background:linear-gradient(135deg,var(--accent),#7c3aed);
  display:flex; align-items:center; justify-content:center;
  font-family:'Cabinet Grotesk',sans-serif; font-weight:900; font-size:11px;
  overflow:hidden;
}
.msg-avatar img { width:100%; height:100%; object-fit:cover; }

.msg-content { max-width:65%; display:flex; flex-direction:column; gap:3px; }
.msg-group.mine .msg-content { align-items:flex-end; }

.msg-bubble {
  padding:10px 14px; border-radius:14px;
  font-size:14px; line-height:1.55; word-break:break-word;
}
.msg-group:not(.mine) .msg-bubble {
  background:var(--s2); border:1px solid var(--border);
  border-bottom-left-radius:4px;
}
.msg-group.mine .msg-bubble {
  background:linear-gradient(135deg,var(--accent),#7c3aed);
  color:#fff; border-bottom-right-radius:4px;
}

.msg-time { font-size:10px; color:var(--muted2); padding:0 4px; }

/* date separator */
.date-sep {
  text-align:center; font-size:11px; color:var(--muted2);
  display:flex; align-items:center; gap:10px; margin:6px 0;
}
.date-sep::before, .date-sep::after {
  content:''; flex:1; height:1px; background:var(--border);
}

/* typing indicator */
.typing {
  display:flex; gap:4px; padding:10px 14px;
  background:var(--s2); border:1px solid var(--border);
  border-radius:14px; border-bottom-left-radius:4px;
  width:fit-content;
}
.typing span {
  width:6px; height:6px; border-radius:50%; background:var(--muted);
  animation:bounce .9s infinite;
}
.typing span:nth-child(2){ animation-delay:.15s; }
.typing span:nth-child(3){ animation-delay:.3s; }
@keyframes bounce { 0%,60%,100%{transform:translateY(0)} 30%{transform:translateY(-5px)} }

/* input zone */
.chat-input-zone {
  padding:14px 20px; border-top:1px solid var(--border);
  background:var(--s); flex-shrink:0;
}
.input-row {
  display:flex; gap:10px; align-items:flex-end;
}
.msg-input {
  flex:1; padding:12px 16px;
  background:var(--s2); border:1px solid var(--border);
  border-radius:12px; color:var(--text);
  font-family:'Instrument Sans',sans-serif; font-size:14px;
  outline:none; resize:none; max-height:120px; line-height:1.5;
  transition:.2s;
}
.msg-input::placeholder { color:var(--muted2); }
.msg-input:focus { border-color:rgba(139,92,246,.5); box-shadow:0 0 0 3px rgba(139,92,246,.1); }

.send-btn {
  width:44px; height:44px; border-radius:11px; flex-shrink:0;
  background:linear-gradient(135deg,var(--accent),#7c3aed);
  border:none; cursor:pointer; color:#fff;
  display:flex; align-items:center; justify-content:center;
  transition:.2s; box-shadow:0 4px 12px rgba(139,92,246,.35);
}
.send-btn:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(139,92,246,.5); }
.send-btn:disabled { opacity:.5; cursor:not-allowed; transform:none; }

/* scrollbar */
::-webkit-scrollbar { width:5px; }
::-webkit-scrollbar-track { background:transparent; }
::-webkit-scrollbar-thumb { background:var(--border); border-radius:99px; }
</style>
</head>
<body>

<nav>
  <a href="index.html" class="nav-logo">Hire<span>Tounsi</span></a>
  <a href="javascript:history.back()" class="nav-back">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
    Retour
  </a>
</nav>

<div class="chat-layout">

  <!-- ════ CONVERSATIONS ════ -->
  <div class="conv-sidebar">
    <div class="conv-header">
      <h2>Messages</h2>
      <input class="conv-search" placeholder="Rechercher…" id="convSearch">
    </div>
    <div class="conv-list" id="convList">
      <div class="conv-empty">Chargement…</div>
    </div>
  </div>

  <!-- ════ CHAT AREA ════ -->
  <div class="chat-area" id="chatArea">
    <div class="chat-empty" id="chatEmpty">
      <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
      </svg>
      <h3>Vos messages</h3>
      <p>Sélectionnez une conversation ou contactez un talent.</p>
    </div>
  </div>

</div>

<script>
const ME_ID   = <?= $currentUserId ?>;
const ME_NAME = <?= json_encode($me["name"] ?? "Moi") ?>;
const ME_INIT = <?= json_encode($myInitials) ?>;

let activeUserId   = null;
let activeUserName = "";
let activeUserInit = "";
let lastMsgId      = 0;
let pollTimer      = null;

// ── START : ouvre direct si ?user= ──
<?php if ($targetUser): ?>
openChat(
  <?= $targetUser["id"] ?>,
  <?= json_encode($targetUser["name"]) ?>,
  <?= json_encode(strtoupper(substr($targetUser["name"], 0, 2))) ?>,
  <?= json_encode($targetUser["avatar_path"] ?? null) ?>
);
<?php endif; ?>

// ── LOAD CONVERSATIONS ──
loadConversations();

async function loadConversations() {
  const res  = await fetch("get_conversations.php");
  const data = await res.json();

  const list = document.getElementById("convList");

  if (!data.success || !data.conversations.length) {
    list.innerHTML = '<div class="conv-empty">Aucune conversation</div>';
    return;
  }

  list.innerHTML = data.conversations.map(c => `
    <div class="conv-item ${c.other_user_id == activeUserId ? 'active' : ''}"
         onclick="openChat(${c.other_user_id}, '${escHtml(c.other_user_name)}', '${c.initials}', '${c.other_user_avatar ?? ''}')">
      <div class="conv-avatar">
        ${c.other_user_avatar
          ? `<img src="uploads/${c.other_user_avatar}">`
          : c.initials}
      </div>
      <div class="conv-info">
        <div class="conv-name">${escHtml(c.other_user_name)}</div>
        <div class="conv-last">${c.last_message}</div>
      </div>
      <div class="conv-meta">
        <span class="conv-time">${c.time_ago}</span>
        ${c.unread_count > 0 ? `<span class="conv-badge">${c.unread_count}</span>` : ''}
      </div>
    </div>
  `).join('');
}

// ── OPEN CHAT ──
function openChat(userId, userName, initials, avatarPath) {
  activeUserId   = userId;
  activeUserName = userName;
  activeUserInit = initials;
  lastMsgId = 0;

  // Highlight active conv
  document.querySelectorAll(".conv-item").forEach(el => el.classList.remove("active"));
  const activeEl = [...document.querySelectorAll(".conv-item")].find(el =>
    el.onclick?.toString().includes(`openChat(${userId},`)
  );
  if (activeEl) activeEl.classList.add("active");

  // Build chat UI
  const area = document.getElementById("chatArea");
  const avatarHtml = avatarPath
    ? `<img src="uploads/${avatarPath}">`
    : initials;

  area.innerHTML = `
    <div class="chat-header">
      <div class="chat-header-avatar">${avatarHtml}</div>
      <div>
        <div class="chat-header-name">${escHtml(userName)}</div>
        <div class="chat-header-role">Utilisateur HireTounsi</div>
      </div>
      <span class="online-dot" title="En ligne"></span>
    </div>

    <div class="messages-wrap" id="messagesWrap"></div>

    <div class="chat-input-zone">
      <div class="input-row">
        <textarea class="msg-input" id="msgInput" rows="1"
          placeholder="Écrire un message…"
          onkeydown="handleKey(event)"></textarea>
        <button class="send-btn" id="sendBtn" onclick="sendMessage()">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7z"/>
          </svg>
        </button>
      </div>
    </div>
  `;

  // Auto-resize textarea
  const textarea = document.getElementById("msgInput");
  textarea.addEventListener("input", () => {
    textarea.style.height = "auto";
    textarea.style.height = Math.min(textarea.scrollHeight, 120) + "px";
  });

  loadMessages();
  if (pollTimer) clearInterval(pollTimer);
  pollTimer = setInterval(pollMessages, 3000);
}

// ── LOAD MESSAGES ──
async function loadMessages() {
  if (!activeUserId) return;
  const res  = await fetch(`get_messages.php?user_id=${activeUserId}`);
  const data = await res.json();
  if (!data.success) return;

  const wrap = document.getElementById("messagesWrap");
  if (!wrap) return;
  wrap.innerHTML = "";

  let lastDate = "";
  data.messages.forEach(msg => {
    const d = msg.date;
    if (d !== lastDate) {
      wrap.insertAdjacentHTML("beforeend",
        `<div class="date-sep">${d}</div>`);
      lastDate = d;
    }
    wrap.insertAdjacentHTML("beforeend", renderMsg(msg));
    if (msg.id > lastMsgId) lastMsgId = msg.id;
  });

  scrollBottom();
}

// ── POLL new messages ──
async function pollMessages() {
  if (!activeUserId || lastMsgId === 0) return;
  const res  = await fetch(`get_messages.php?user_id=${activeUserId}&last_id=${lastMsgId}`);
  const data = await res.json();
  if (!data.success || !data.messages.length) return;

  const wrap = document.getElementById("messagesWrap");
  if (!wrap) return;

  data.messages.forEach(msg => {
    wrap.insertAdjacentHTML("beforeend", renderMsg(msg));
    if (msg.id > lastMsgId) lastMsgId = msg.id;
  });
  scrollBottom();
  loadConversations(); // refresh sidebar
}

// ── SEND ──
async function sendMessage() {
  const input = document.getElementById("msgInput");
  const btn   = document.getElementById("sendBtn");
  if (!input) return;

  const content = input.value.trim();
  if (!content || !activeUserId) return;

  btn.disabled = true;
  input.value  = "";
  input.style.height = "auto";

  const form = new FormData();
  form.append("receiver_id", activeUserId);
  form.append("content", content);

  const res  = await fetch("send_message.php", { method:"POST", body:form });
  const data = await res.json();

  btn.disabled = false;

  if (data.success) {
    const wrap = document.getElementById("messagesWrap");
    if (wrap) {
      wrap.insertAdjacentHTML("beforeend", renderMsg(data.message));
      if (data.message.id > lastMsgId) lastMsgId = data.message.id;
      scrollBottom();
    }
    loadConversations();
  }
}

function handleKey(e) {
  if (e.key === "Enter" && !e.shiftKey) {
    e.preventDefault();
    sendMessage();
  }
}

// ── RENDER MESSAGE ──
function renderMsg(msg) {
  const mine = msg.is_mine || msg.sender_id == ME_ID;
  const cls  = mine ? "mine" : "";
  const init = mine ? ME_INIT : activeUserInit;
  const avatarHtml = msg.sender_avatar
    ? `<img src="uploads/${msg.sender_avatar}">`
    : init;

  return `
    <div class="msg-group ${cls}">
      <div class="msg-avatar">${avatarHtml}</div>
      <div class="msg-content">
        <div class="msg-bubble">${msg.content}</div>
        <span class="msg-time">${msg.time || ""}</span>
      </div>
    </div>
  `;
}

function scrollBottom() {
  const wrap = document.getElementById("messagesWrap");
  if (wrap) wrap.scrollTop = wrap.scrollHeight;
}

function escHtml(str) {
  return String(str).replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;");
}

// Search conversations
document.getElementById("convSearch").addEventListener("input", function() {
  const q = this.value.toLowerCase();
  document.querySelectorAll(".conv-item").forEach(el => {
    const name = el.querySelector(".conv-name").textContent.toLowerCase();
    el.style.display = name.includes(q) ? "" : "none";
  });
});
</script>

</body>
</html>