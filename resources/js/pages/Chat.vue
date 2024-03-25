<!--suppress VueUnrecognizedDirective -->
<script setup>
import { nextTick, ref } from "vue";
import fetchJSON from "../fetchJSON.js";
import { intlFormatDistance } from "date-fns";
import { useUserStore } from "../stores/user.js";
import Echo from "laravel-echo";
import { Mentionable } from "vue-mention";

const userStore = useUserStore();
/** @type {import("vue").Ref<Room[]>} */
const rooms = ref([]);
const selectedRoom = ref();

/** @type {import("vue").Ref<HTMLElement | null>}}>} */
const messagesRef = ref(null);

/** @type {import("vue").Ref<HTMLElement | null>}}>} */
const roomsRef = ref(null);

// Room ids with unread messages
/** @type {import("vue").Ref<number[]>} */
const unreadRooms = ref([]);

const newMessageContent = ref("");

const markAsRead = (room) => {
    unreadRooms.value = unreadRooms.value.filter((id) => id !== room.id);
};

const markAsUnread = (room) => {
    if (!unreadRooms.value.includes(room.id)) {
        unreadRooms.value.push(room.id);
    }
};

const fetchRooms = async () => {
    const { ok, data } = await fetchJSON("/rooms");

    if (!ok) {
        return;
    }

    // Reverse the order of messages, Laravel 返回的是倒序的
    data.forEach((room) => {
        room.messages.reverse();
    });

    // Sort rooms by messages_count, so the room with most messages is on top
    data.sort((a, b) => b.messages_count - a.messages_count);

    rooms.value = data;

    if (data.length > 0) {
        selectedRoom.value = rooms.value[0];
        await scrollToBottom(messagesRef);
    }
};

const selectRoom = (room) => {
    selectedRoom.value = room;
    scrollToBottom(messagesRef);
    markAsRead(room);
};

const addRoom = async (event) => {
    event.preventDefault();
    const title = event.target[0].value;
    if (!title) {
        alert("请输入房间名称");
        return;
    }

    const { ok, data } = await fetchJSON("/rooms", {
        method: "POST",
        body: { title },
    });

    if (!ok) {
        return;
    }

    rooms.value.push(data);
    selectedRoom.value = data;
    event.target.reset();
    await scrollToBottom(roomsRef);
};

const scrollToBottom = async (elRef) => {
    if (!elRef.value) {
        return;
    }

    await nextTick(() => {
        elRef.value.scrollTop = elRef.value.scrollHeight;
    });
};

const sendMessage = async () => {
    const content = newMessageContent.value;
    newMessageContent.value = "";
    if (!content) {
        alert("请输入消息内容");
        return;
    }

    const { ok, data } = await fetchJSON(
        `/rooms/${selectedRoom.value.id}/messages`,
        {
            method: "POST",
            body: { content },
        },
    );

    if (!ok) {
        return;
    }

    // Avoid race condition with websocket
    // if the message is already in the room, ignore it
    if (selectedRoom.value.messages.some((m) => m.id === data.id)) {
        return;
    }

    selectedRoom.value.messages.push(data);
    await scrollToBottom(messagesRef);
};

const setupEcho = () => {
    const echo = new Echo({
        broadcaster: "reverb",
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? "https") === "https",
        enabledTransports: ["ws", "wss"],
    });

    echo.channel("chat")
        .listen(
            "MessageCreated",
            (
                /** @type {{ message: import('../types.js').Message }} */
                event,
            ) => {
                const message = event.message;

                const room = rooms.value.find(
                    (room) => room.id === message.room_id,
                );
                if (!room) {
                    console.error("message room not found", message);
                    return;
                }

                // if the message is already in the room, ignore it
                if (room.messages.some((m) => m.id === message.id)) {
                    return;
                }

                room.messages.push(message);
                // if the room is selected, scroll to the bottom
                if (selectedRoom.value === room) {
                    scrollToBottom(messagesRef);
                } else {
                    markAsUnread(room);
                }
            },
        )
        .listen(
            "RoomCreated",
            (
                /** @type {{ room: import('../types.js').Room }} */
                event,
            ) => {
                const room = event.room;

                // if the room is already in the list, ignore it
                if (rooms.value.some((r) => r.id === room.id)) {
                    return;
                }

                rooms.value.push(room);
            },
        );
};

fetchRooms();
setupEcho();
</script>

<template>
    <aside
        class="fixed hidden h-full w-[230px] flex-col border-r bg-gray-900 text-white/80 shadow-md sm:flex"
    >
        <div>
            <h1
                class="mt-4 text-center font-[cursive] text-2xl font-black tracking-widest drop-shadow-2xl"
            >
                聊天室
            </h1>
            <p class="mb-4 mt-1 px-4 text-center text-xs opacity-60">
                由
                <span class="border-b border-dotted border-b-red-700"
                    >Laravel Reverb</span
                >
                和
                <span class="border-b border-dotted border-b-green-700"
                    >Vue</span
                >
                搭建
            </p>
        </div>
        <div ref="roomsRef" class="flex-grow overflow-auto">
            <div v-if="rooms.length" class="space-y-2" v-auto-animate>
                <button
                    v-for="room in rooms"
                    :key="room.id"
                    class="flex w-full items-center justify-between gap-2 rounded-md px-3 py-2 text-left transition hover:bg-gray-800"
                    :class="{
                        'text-gray-500': selectedRoom !== room,
                        'bg-gray-800 text-blue-500': selectedRoom === room,
                    }"
                    @click="selectRoom(room)"
                >
                    {{ room.title }}
                    <span
                        class="min-w-5 rounded-full p-1 text-center text-xs tabular-nums leading-none text-white/50"
                        :class="
                            unreadRooms.includes(room.id)
                                ? 'bg-rose-800'
                                : 'bg-gray-800'
                        "
                        >{{ room.messages.length }}</span
                    >
                </button>
            </div>
            <div v-else class="mt-4 text-center text-sm opacity-60">
                没有房间
            </div>
        </div>
        <form class="pt-4" @submit.prevent="addRoom">
            <input
                type="text"
                class="w-full rounded border-b border-t border-gray-700 bg-gray-800 p-2 text-sm text-white/80 focus:border-blue-500 focus:outline-none"
                required
                placeholder="添加一间自己的房间..."
            />
        </form>
    </aside>

    <main class="flex h-dvh flex-col sm:ml-[230px]">
        <div
            ref="messagesRef"
            class="flex-grow overflow-auto scroll-smooth px-4 pt-12"
        >
            <div
                v-if="selectedRoom && selectedRoom.messages.length"
                class="space-y-3"
                v-auto-animate
            >
                <div
                    v-for="message in selectedRoom.messages"
                    :key="message.id"
                    class="flex flex-col"
                    :class="{
                        'items-end': message.user_id === userStore.user.id,
                    }"
                >
                    <div
                        class="mb-1 flex gap-2 text-sm leading-none"
                        :class="{
                            'flex-row-reverse':
                                message.user_id === userStore.user.id,
                        }"
                    >
                        {{ message.user.name }}
                        <time class="text-xs opacity-50"
                            >{{
                                intlFormatDistance(
                                    message.created_at,
                                    new Date(),
                                )
                            }}
                        </time>
                    </div>
                    <div
                        class="w-max max-w-full rounded-lg px-3 py-1.5"
                        :class="
                            message.user_id === userStore.user.id
                                ? 'bg-blue-500 text-white'
                                : 'bg-gray-200 text-black'
                        "
                    >
                        {{ message.content }}
                    </div>
                </div>
            </div>
            <div v-else class="mt-4 text-center text-sm opacity-60">
                暂无消息
            </div>
        </div>

        <form class="px-4 py-4" @submit.prevent="sendMessage">
            <Mentionable
                :keys="['@']"
                :items="[
                    {
                        value: 'kimi',
                        label: 'Kimi',
                    },
                ]"
                insert-space
                offset="6"
            >
                <input
                    v-model="newMessageContent"
                    type="text"
                    class="w-full rounded-md border border-gray-300 px-3 py-3 focus:border-blue-500 focus:outline-none"
                    placeholder="输入消息, @kimi 试试..."
                    required
                />
            </Mentionable>
        </form>
    </main>
</template>
