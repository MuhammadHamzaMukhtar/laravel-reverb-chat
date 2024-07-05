<template>
    <div class="flex flex-col h-screen">
        <div ref="messageContainer" class="flex-1 overflow-y-auto">
            <div class="flex flex-col space-y-4 p-4">
                <!-- Chat messages -->
                <div v-for="message in messages" :key="message.id">
                    <div
                        v-if="message.sender_id === currentUser.id"
                        class="flex flex-col items-end"
                    >
                        <div class="bg-blue-500 text-white rounded-lg p-2">
                            {{ message.message }}
                        </div>
                    </div>
                    <div v-else class="flex flex-col items-start">
                        <div class="bg-gray-200 rounded-lg p-2">
                            {{ message.message }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4">
            <!-- Chat input -->
            <div class="flex">
                <input
                    v-model="newMessage"
                    type="text"
                    placeholder="Type your message..."
                    @keydown="sendTypingEvent"
                    @keyup.enter="sendMessage"
                    class="flex-1 border border-gray-300 rounded-lg p-2 mr-2"
                />
                <button
                    @click="sendMessage"
                    class="bg-blue-500 text-white rounded-lg px-4 py-2"
                >
                    Send
                </button>
            </div>
            <small v-if="isFriendTyping" class="text-gray-500"
                >{{ props.friend.name }} is typing...</small
            >
        </div>
    </div>
</template>
<script setup>
import { nextTick, onMounted, ref, watch } from "vue";

const props = defineProps({
    friend: { type: Object, required: true },
    currentUser: { type: Object, required: true },
});

const messages = ref([]);
const newMessage = ref("");
const messageContainer = ref(null);
const isFriendTyping = ref(false);

watch(
    messages,
    () => {
        nextTick(
            // Scroll to bottom when new message is added
            () => {
                messageContainer.value.scrollTo({
                    top: messageContainer.value.scrollHeight,
                    behavior: "smooth",
                });
            }
        );
    },
    { deep: true }
);

const sendMessage = () => {
    if (newMessage.value.trim() !== "") {
        axios
            .post(`/messages/${props.friend.id}`, {
                message: newMessage.value,
            })
            .then((response) => {
                messages.value.push(response.data);
                newMessage.value = "";
            });
    }
};

const sendTypingEvent = () => {
    Echo.private(`chat.${props.friend.id}`).whisper("typing", {
        userId: props.currentUser.id,
    });
};

onMounted(() => {
    axios.get(`/messages/${props.friend.id}`).then((response) => {
        messages.value = response.data;
    });

    Echo.private(`chat.${props.currentUser.id}`)
        .listen("MessageSent", (e) => {
            messages.value.push(e.chatMessage);
        })
        .listenForWhisper("typing", (response) => {
            isFriendTyping.value = response.userId === props.friend.id;
            setTimeout(() => {
                isFriendTyping.value = false;
            }, 2000);
        });
});
</script>
