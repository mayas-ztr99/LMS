@extends('layouts.app')

@section('content')
    @vite(['resources/js/chat.js'])
    <div class="container">
        <h1>المحادثة الفورية</h1>

        <div class="chat-container">
            <!-- قائمة المحادثات -->
            <div class="conversations-list" id="conversationsList">
                <h3>محادثاتي</h3>
                <ul id="conversations"></ul>
            </div>

            <!-- نافذة المحادثة -->
            <div class="chat-window">
                <div id="chatHeader">
                    <h3 id="chatPartner"></h3>
                </div>
                <div id="messagesContainer">
                    <ul id="messages"></ul>
                </div>
                <form id="messageForm">
                    <input type="hidden" id="conversationId" value="{{ $conversationId ?? '' }}">
                    <input type="text" id="messageInput" placeholder="اكتب رسالة..." required>
                    <button type="submit">إرسال</button>
                </form>
            </div>
        </div>
    </div>

    <style>
        .chat-container {
            display: flex;
            gap: 20px;
            height: 500px;
        }
        .conversations-list {
            width: 30%;
            border-right: 1px solid #ccc;
            overflow-y: auto;
        }
        .chat-window {
            width: 70%;
            display: flex;
            flex-direction: column;
        }
        #messagesContainer {
            flex: 1;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
        }
        #messages {
            list-style: none;
            padding: 0;
        }
        #messages li {
            padding: 8px;
            margin-bottom: 5px;
            background: #f1f1f1;
            border-radius: 5px;
        }
        #messageForm {
            display: flex;
            gap: 10px;
        }
        #messageInput {
            flex: 1;
        }
    </style>
@endsection
